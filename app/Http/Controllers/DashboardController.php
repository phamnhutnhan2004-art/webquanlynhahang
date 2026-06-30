<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmationMail;
use App\Models\Category;
use App\Models\ChatbotLog;
use App\Models\AiChatbotSetting;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\GalleryImage;
use App\Models\HomeParty;
use App\Models\KitchenOrder;
use App\Models\MenuGallery;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\ThemeSetting;
use App\Models\Role;
use App\Models\User;
use App\Models\WebsitePageSetting;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'products' => Product::with('category:id,name')
                ->where('status', 'available')
                ->whereHas('category', fn ($query) => $query->where('status', 'hiển thị'))
                ->latest()
                ->limit(6)
                ->get(),
            'categories' => $this->visibleMenuCategories(),
            'totalProducts' => Product::where('status', 'available')->count(),
            'availableTables' => RestaurantTable::where('status', 'trống')->count(),
            'totalReservations' => Reservation::count(),
            'menuGalleries' => MenuGallery::latest()->limit(4)->get(),
            'galleryImages' => GalleryImage::latest()->limit(6)->get(),
        ]);
    }

    public function menu(Request $request): View
    {
        $categories = $this->visibleMenuCategories();

        $products = Product::with('category:id,name')
            ->where('status', 'available')
            ->whereHas('category', fn ($query) => $query->where('status', 'hiển thị'))
            ->when($request->filled('category'), function ($query) use ($request, $categories) {
                $category = $categories->first(fn (Category $item) => $item->menu_slug === $request->query('category'));

                if ($category) {
                    $query->where('category_id', $category->id);
                }
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = trim((string) $request->query('q'));

                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhereHas('category', fn ($query) => $query->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->when($request->query('sort') === 'price_asc', fn ($query) => $query->orderBy('price'))
            ->when($request->query('sort') === 'price_desc', fn ($query) => $query->orderByDesc('price'))
            ->when($request->query('sort') === 'name', fn ($query) => $query->orderBy('name'))
            ->when(! in_array($request->query('sort'), ['price_asc', 'price_desc', 'name'], true), fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        return view('menu', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $request->query('category', ''),
            'selectedSort' => $request->query('sort', 'latest'),
            'searchTerm' => $request->query('q', ''),
        ]);
    }

    public function gallery(): View
    {
        return view('gallery', [
            'menuGalleries' => MenuGallery::latest()->get(),
            'galleryImages' => GalleryImage::latest()->get(),
        ]);
    }

    public function booking(): View
    {
        return view('reservations.create', [
            'tables' => RestaurantTable::where('status', 'trống')->orderBy('area')->orderBy('table_code')->get(),
            'reservationEmailRequired' => $this->reservationEmailRequired(),
        ]);
    }

    public function menuCategory(Request $request, string $categorySlug): View
    {
        $categories = $this->visibleMenuCategories();
        $category = $categories->first(fn (Category $item) => $item->menu_slug === $categorySlug);

        abort_unless($category, 404);

        $products = $category->products()
            ->with('category:id,name')
            ->where('status', 'available')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = trim((string) $request->query('q'));

                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->when($request->query('sort') === 'price_asc', fn ($query) => $query->orderBy('price'))
            ->when($request->query('sort') === 'price_desc', fn ($query) => $query->orderByDesc('price'))
            ->when($request->query('sort') === 'name', fn ($query) => $query->orderBy('name'))
            ->when(! in_array($request->query('sort'), ['price_asc', 'price_desc', 'name'], true), fn ($query) => $query->latest())
            ->paginate(9)
            ->withQueryString();

        $bannerProduct = (clone $category->products())
            ->where('status', 'available')
            ->whereNotNull('image')
            ->latest()
            ->first();

        return view('menu-category', [
            'category' => $category,
            'categories' => $categories,
            'products' => $products,
            'bannerImage' => $bannerProduct?->image_url ?? asset('images/restaurant-interior.png'),
            'selectedSort' => $request->query('sort', 'latest'),
            'searchTerm' => $request->query('q', ''),
        ]);
    }

    public function productDetail(Product $product): View
    {
        abort_unless($product->status === 'available' && $product->category?->status === 'hiển thị', 404);

        $product->load('category');

        return view('product-detail', [
            'product' => $product,
            'relatedProducts' => Product::with('category:id,name')
                ->where('status', 'available')
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }

    public function about(): View
    {
        return view('about');
    }

    public function admin(): View
    {
        $monthlyRevenue = collect(range(5, 0))->map(function (int $monthsAgo) {
            $month = now()->subMonths($monthsAgo);
            $revenue = Payment::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');

            return [
                'label' => 'T'.$month->format('m'),
                'value' => (float) $revenue,
            ];
        });

        $dailyOrders = collect(range(6, 0))->map(function (int $daysAgo) {
            $day = now()->subDays($daysAgo);

            return [
                'label' => $day->format('d/m'),
                'value' => Order::whereDate('created_at', $day)->count(),
            ];
        });

        $topProducts = OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(fn (OrderItem $item) => [
                'label' => $item->product?->name ?? 'Món đã xóa',
                'value' => (int) $item->total_quantity,
            ]);

        $tableStatuses = RestaurantTable::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn (RestaurantTable $table) => [
                'label' => $table->status,
                'value' => (int) $table->total,
            ]);

        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Payment::sum('total_amount'),
            'totalCustomers' => Customer::count(),
            'totalTables' => RestaurantTable::count(),
            'totalReservations' => Reservation::count(),
            'totalHomeParties' => HomeParty::count(),
            'homePartyRevenue' => HomeParty::where('status', 'hoàn thành')->sum('total_price'),
            'homePartyGuests' => HomeParty::whereIn('status', ['đã xác nhận', 'đang chuẩn bị', 'đang phục vụ', 'hoàn thành'])->sum('guest_quantity'),
            'activeHomeParties' => HomeParty::whereIn('status', ['đang chuẩn bị', 'đang phục vụ'])->count(),
            'activeTables' => RestaurantTable::whereNotIn('status', ['trống'])->count(),
            'pendingReservations' => Reservation::where('status', 'chờ xác nhận')->count(),
            'recentOrders' => Order::with(['customer', 'table'])->latest()->limit(5)->get(),
            'recentHomeParties' => HomeParty::with('assignedEmployee.user')->latest()->limit(5)->get(),
            'monthlyRevenue' => $monthlyRevenue,
            'dailyOrders' => $dailyOrders,
            'topProducts' => $topProducts,
            'tableStatuses' => $tableStatuses,
        ]);
    }

    public function adminSection(string $section): View
    {
        abort_unless(in_array($section, [
            'employees',
            'accounts',
            'products',
            'categories',
            'tables',
            'orders',
            'reservations',
            'home-parties',
            'customers',
            'payments',
            'payment-methods',
            'chatbot',
            'ai-chatbot',
            'theme-settings',
            'auth-interface',
            'menu-galleries',
            'gallery-images',
            'news',
            'stats',
        ], true), 404);

        $data = match ($section) {
            'accounts' => [
                'items' => User::with(['role', 'customer.reservations', 'customer.orders.bill', 'customer.bills', 'employee.orders', 'employee.bills'])->latest()->get(),
                'roles' => Role::orderBy('id')->get(),
            ],
            'employees' => ['items' => Employee::with('user')->get()],
            'products' => [
                'items' => Product::with('category')->latest()->get(),
                'categories' => Category::where('status', 'hiển thị')->orderBy('name')->get(),
            ],
            'categories' => ['items' => Category::withCount('products')->latest()->get()],
            'tables' => ['items' => RestaurantTable::with(['activeOrders.items.product'])->orderBy('area')->orderBy('table_code')->get()],
            'orders' => ['items' => Order::with(['customer', 'table', 'employee', 'items.product', 'kitchenOrder.items.food'])->latest()->get()],
            'reservations' => ['items' => Reservation::with(['customer', 'table', 'employee'])->latest()->get()],
            'customers' => ['items' => Customer::withCount('reservations')->latest()->get()],
            'payments' => ['items' => Payment::with(['reservation.customer', 'employee.user'])->latest()->get()],
            'payment-methods' => ['items' => PaymentMethod::withCount('bills')->orderBy('sort_order')->orderBy('id')->get()],
            'chatbot' => ['items' => ChatbotLog::with(['customer', 'reservation', 'order'])->latest()->get()],
            'ai-chatbot' => ['items' => collect(), 'aiSetting' => AiChatbotSetting::current()],
            'theme-settings' => [
                'items' => collect(),
                'themeSetting' => ThemeSetting::current(),
                'pageThemeOptions' => WebsitePageSetting::PAGE_OPTIONS,
            ],
            'auth-interface' => [
                'items' => collect(),
                'authPageSetting' => WebsitePageSetting::current('auth'),
            ],
            'home-parties' => [
                'items' => HomeParty::with(['details.food.category', 'assignedEmployee.user'])->latest()->get(),
                'employees' => Employee::with('user')->where('status', 'đang làm')->orderBy('employee_code')->get(),
                'statuses' => HomeParty::STATUSES,
            ],
            'menu-galleries' => ['items' => MenuGallery::latest()->get()],
            'gallery-images' => ['items' => GalleryImage::latest()->get()],
            'news' => ['items' => collect()],
            'stats' => [
                'revenueByDay' => Payment::query()
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'))
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date')
                    ->get(),
            ],
        };

        return view('admin.section', ['section' => $section] + $data);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['hiển thị', 'ẩn'])],
        ]);

        Category::create($data);

        return back()->with('status', 'Đã thêm danh mục mới.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category)],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['hiển thị', 'ẩn'])],
        ]);

        $category->update($data);

        return back()->with('status', 'Đã cập nhật danh mục.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Danh mục đang có món ăn nên chưa thể xóa.');
        }

        $category->delete();

        return back()->with('status', 'Đã xóa danh mục.');
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data + [
            'slug' => $this->uniqueProductSlug($data['name']),
        ]);

        return back()->with('status', 'Đã thêm món ăn mới.');
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, $product);

        if ($request->hasFile('image')) {
            $this->deleteStoredFile($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data + [
            'slug' => $product->name !== $data['name'] ? $this->uniqueProductSlug($data['name'], $product) : $product->slug,
        ]);

        return back()->with('status', 'Đã cập nhật món ăn.');
    }

    public function destroyProduct(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists() || $product->kitchenItems()->exists()) {
            $product->update(['status' => 'inactive']);

            return back()->with('error', 'Món đã có trong đơn hàng nên được chuyển sang trạng thái ẩn thay vì xóa.');
        }

        $this->deleteStoredFile($product->image);
        $product->delete();

        return back()->with('status', 'Đã xóa món ăn.');
    }

    public function storeMenuGallery(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $data['image'] = $request->file('image')->store('menu-galleries', 'public');

        MenuGallery::create($data);

        return back()->with('status', 'Đã tải lên menu hình ảnh.');
    }

    public function destroyMenuGallery(MenuGallery $menuGallery): RedirectResponse
    {
        $this->deleteStoredFile($menuGallery->image);
        $menuGallery->delete();

        return back()->with('status', 'Đã xóa menu hình ảnh.');
    }

    public function storeGalleryImage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $data['image'] = $request->file('image')->store('gallery-images', 'public');

        GalleryImage::create($data);

        return back()->with('status', 'Đã tải lên ảnh nhà hàng.');
    }

    public function destroyGalleryImage(GalleryImage $galleryImage): RedirectResponse
    {
        $this->deleteStoredFile($galleryImage->image);
        $galleryImage->delete();

        return back()->with('status', 'Đã xóa ảnh nhà hàng.');
    }

    public function storeTable(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_code' => ['required', 'string', 'max:30', 'unique:tables,table_code'],
            'table_name' => ['required', 'string', 'max:80'],
            'area' => ['required', 'string', 'max:80'],
            'seats' => ['required', 'integer', 'min:1', 'max:30'],
            'status' => ['required', Rule::in(['trống', 'đã đặt', 'đang sử dụng', 'đang dọn dẹp'])],
        ]);

        RestaurantTable::create($data);

        return back()->with('status', 'Đã thêm bàn ăn mới.');
    }

    public function updateTable(Request $request, RestaurantTable $table): RedirectResponse
    {
        $data = $request->validate([
            'table_code' => ['required', 'string', 'max:30', Rule::unique('tables', 'table_code')->ignore($table)],
            'table_name' => ['required', 'string', 'max:80'],
            'area' => ['required', 'string', 'max:80'],
            'seats' => ['required', 'integer', 'min:1', 'max:30'],
            'status' => ['required', Rule::in(['trống', 'đã đặt', 'đang sử dụng', 'đang dọn dẹp'])],
        ]);

        $table->update($data);

        return back()->with('status', 'Đã cập nhật bàn ăn.');
    }

    public function updateTableStatus(Request $request, RestaurantTable $table): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['trống', 'đã đặt', 'đang sử dụng', 'đang dọn dẹp'])],
        ]);

        $table->update(['status' => $data['status']]);

        return back()->with('status', 'Đã cập nhật trạng thái bàn.');
    }

    public function destroyTable(RestaurantTable $table): RedirectResponse
    {
        if ($table->reservations()->exists() || $table->orders()->exists()) {
            return back()->with('error', 'Bàn ăn đang có dữ liệu liên quan nên chưa thể xóa.');
        }

        $table->delete();

        return back()->with('status', 'Đã xóa bàn ăn.');
    }

    public function staff(): View
    {
        return view('staff.dashboard', [
            'orders' => Order::with(['customer', 'table', 'items.product', 'kitchenOrder.items.food'])
                ->latest()
                ->limit(10)
                ->get(),
            'reservations' => Reservation::with(['customer', 'table'])->latest()->limit(10)->get(),
            'customers' => Customer::latest()->limit(10)->get(),
            'products' => Product::with('category')->limit(10)->get(),
            'tables' => RestaurantTable::with(['activeOrders.items.product'])
                ->orderBy('area')
                ->orderBy('table_code')
                ->limit(10)
                ->get(),
            'completedKitchenOrders' => KitchenOrder::with(['order.table', 'items.food'])
                ->where('status', 'completed')
                ->latest('updated_at')
                ->limit(6)
                ->get(),
        ]);
    }

    public function updateReservationStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['chờ xác nhận', 'đã xác nhận', 'đã hủy', 'hoàn thành'])],
        ]);

        $employeeId = $request->user()?->employee?->id ?? $reservation->employee_id;

        DB::transaction(function () use ($reservation, $data, $employeeId): void {
            $reservation->update([
                'status' => $data['status'],
                'employee_id' => $employeeId,
            ]);

            if ($reservation->table) {
                $reservation->table->update([
                    'status' => match ($data['status']) {
                        'chờ xác nhận', 'đã xác nhận' => 'đã đặt',
                        'đã hủy', 'hoàn thành' => 'trống',
                    },
                ]);
            }
        });

        return back()->with('status', 'Đã cập nhật trạng thái đặt bàn.');
    }

    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'serving', 'completed', 'cancelled'])],
        ]);

        DB::transaction(function () use ($order, $data, $request): void {
            $order->update([
                'status' => $data['status'],
                'employee_id' => $request->user()?->employee?->id ?? $order->employee_id,
            ]);

            if ($order->table) {
                $tableStatus = match ($data['status']) {
                    'serving' => 'đang sử dụng',
                    'completed', 'cancelled' => 'trống',
                    default => $order->table->status,
                };

                $order->table->update(['status' => $tableStatus]);
            }
        });

        return back()->with('status', 'Đã cập nhật trạng thái đơn hàng.');
    }

    public function customer(): View
    {
        return view('customer.dashboard', [
            'products' => Product::with('category')->where('status', 'available')->latest()->get(),
            'tables' => RestaurantTable::where('status', 'trống')->orderBy('area')->orderBy('table_code')->get(),
            'menuGalleries' => MenuGallery::latest()->get(),
            'galleryImages' => GalleryImage::latest()->limit(8)->get(),
            'reservations' => Reservation::with('table')
                ->where('customer_id', auth()->user()?->customer?->id)
                ->latest()
                ->get(),
            'orders' => Order::with(['table', 'items.product', 'bill'])
                ->where('customer_id', auth()->user()?->customer?->id)
                ->latest('ordered_at')
                ->get(),
        ]);
    }

    public function reserve(Request $request): RedirectResponse
    {
        $user = $request->user();
        $emailRequired = $this->reservationEmailRequired();

        $data = $request->validate([
            'full_name' => [$user ? 'nullable' : 'required', 'string', 'max:150'],
            'phone' => [$user ? 'nullable' : 'required', 'string', 'max:30'],
            'email' => [Rule::requiredIf(! $user && $emailRequired), 'nullable', 'email', 'max:150'],
            'table_id' => ['nullable', Rule::exists('tables', 'id')->where('status', 'trống')],
            'reservation_date' => ['nullable', 'required_without:reservation_time', 'date'],
            'reservation_hour' => ['nullable', 'required_without:reservation_time', 'date_format:H:i'],
            'reservation_time' => ['nullable', 'date'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:30'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [], [
            'full_name' => 'họ và tên',
            'phone' => 'số điện thoại',
            'email' => 'email',
            'reservation_date' => 'ngày đặt',
            'reservation_hour' => 'giờ đặt',
            'reservation_time' => 'thời gian đặt',
            'number_of_guests' => 'số lượng khách',
        ]);

        $reservationTime = $this->reservationTimeFromRequest($data);

        if ($reservationTime->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages([
                'reservation_date' => 'Thời gian đặt bàn cần ở tương lai.',
            ]);
        }

        $reservation = DB::transaction(function () use ($data, $user, $reservationTime): Reservation {
            $customer = null;

            if ($user?->isCustomer()) {
                $customer = $user->customer ?: Customer::create([
                    'user_id' => $user->id,
                    'full_name' => $user->name ?? $user->full_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                ]);
            }

            $guestName = $customer?->full_name ?: trim((string) ($data['full_name'] ?? ($user?->name ?? $user?->full_name ?? '')));
            $guestPhone = $customer?->phone ?: trim((string) ($data['phone'] ?? $user?->phone ?? ''));
            $guestEmail = $customer?->email ?: trim((string) ($data['email'] ?? $user?->email ?? '')) ?: null;

            $table = null;
            if (! empty($data['table_id'])) {
                $table = RestaurantTable::whereKey($data['table_id'])
                    ->where('status', 'trống')
                    ->lockForUpdate()
                    ->first();

                if (! $table) {
                    throw ValidationException::withMessages([
                        'table_id' => 'Bàn đã được đặt hoặc không còn trống. Vui lòng chọn bàn khác.',
                    ]);
                }
            }

            $reservation = Reservation::create([
                'customer_id' => $customer?->id,
                'guest_name' => $guestName,
                'guest_phone' => $guestPhone,
                'guest_email' => $guestEmail,
                'customer_type' => $customer ? 'khách thành viên' : 'khách tiềm năng',
                'table_id' => $table?->id,
                'reservation_code' => 'DB'.now()->format('YmdHis').Str::upper(Str::random(4)),
                'reservation_time' => $reservationTime,
                'number_of_guests' => $data['number_of_guests'],
                'note' => $data['note'] ?? null,
                'source' => 'website',
                'status' => 'chờ xác nhận',
            ]);

            if ($table) {
                $table->update(['status' => 'đã đặt']);
            }

            return $reservation->load(['customer', 'table']);
        });

        $this->sendReservationConfirmation($reservation);

        return back()->with('status', 'Đặt bàn thành công. Nhân viên sẽ xác nhận sớm.');
    }

    private function reservationEmailRequired(): bool
    {
        return filter_var(env('RESERVATION_EMAIL_REQUIRED', false), FILTER_VALIDATE_BOOL);
    }

    private function reservationTimeFromRequest(array $data): Carbon
    {
        if (! empty($data['reservation_time'])) {
            return Carbon::parse($data['reservation_time']);
        }

        return Carbon::parse($data['reservation_date'].' '.$data['reservation_hour']);
    }

    private function sendReservationConfirmation(Reservation $reservation): void
    {
        $email = $reservation->customerEmail();

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new ReservationConfirmationMail($reservation));
            $reservation->update(['confirmation_sent_at' => now()]);
        } catch (\Throwable $exception) {
            Log::warning('Khong gui duoc email xac nhan dat ban.', [
                'reservation_id' => $reservation->id,
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function reserveLegacy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'table_id' => ['nullable', Rule::exists('tables', 'id')->where('status', 'trống')],
            'reservation_time' => ['required', 'date', 'after:now'],
            'number_of_guests' => ['required', 'integer', 'min:1', 'max:30'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($data, $user): void {
            $customer = $user->customer ?: Customer::create([
                'user_id' => $user->id,
                'full_name' => $user->name ?? $user->full_name,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $user->address,
            ]);

            Reservation::create([
                'customer_id' => $customer->id,
                'table_id' => $data['table_id'] ?? null,
                'reservation_code' => 'DB'.now()->format('YmdHis').Str::upper(Str::random(4)),
                'reservation_time' => $data['reservation_time'],
                'number_of_guests' => $data['number_of_guests'],
                'note' => $data['note'] ?? null,
                'source' => 'website',
                'status' => 'chờ xác nhận',
            ]);

            if (! empty($data['table_id'])) {
                RestaurantTable::whereKey($data['table_id'])->update(['status' => 'đã đặt']);
            }
        });

        return back()->with('status', 'Đã gửi yêu cầu đặt bàn. Nhân viên sẽ xác nhận sớm.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150', Rule::unique('products', 'name')->ignore($product)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['available', 'out_of_stock', 'inactive'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
    }

    private function uniqueProductSlug(string $name, ?Product $product = null): string
    {
        $base = Str::slug($name) ?: Str::lower(Str::random(8));
        $slug = $base;
        $index = 2;

        while (Product::where('slug', $slug)->when($product, fn ($query) => $query->whereKeyNot($product->id))->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }

    private function visibleMenuCategories()
    {
        return Category::query()
            ->where('status', 'hiển thị')
            ->whereHas('products', fn ($query) => $query->where('status', 'available'))
            ->withCount(['products as products_count' => fn ($query) => $query->where('status', 'available')])
            ->orderBy('name')
            ->get();
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
