<?php

namespace App\Http\Controllers;

use App\Models\HomeParty;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HomePartyController extends Controller
{
    public function show(): View
    {
        $products = Product::with('category:id,name')
            ->where('status', 'available')
            ->orderBy('name')
            ->get();

        return view('home-party', [
            'products' => $products,
            'combos' => $this->combos($products),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['required', 'date_format:H:i'],
            'guest_quantity' => ['required', 'integer', 'min:5', 'max:500'],
            'party_type' => ['required', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:2000'],
            'selected_products' => ['required', 'array', 'min:1'],
            'selected_products.*' => ['nullable', 'integer', 'min:0', 'max:99'],
        ], $this->messages());

        $selectedQuantities = collect($data['selected_products'])
            ->mapWithKeys(fn ($quantity, $id) => [(int) $id => max(0, (int) $quantity)])
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($selectedQuantities->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['selected_products' => 'Vui lòng chọn ít nhất một món ăn cho buổi tiệc.']);
        }

        $products = Product::whereIn('id', $selectedQuantities->keys())
            ->where('status', 'available')
            ->get()
            ->keyBy('id');

        if ($products->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['selected_products' => 'Các món đã chọn hiện không khả dụng. Vui lòng chọn lại thực đơn.']);
        }

        $totalPrice = $products->sum(function (Product $product) use ($selectedQuantities): float {
            return (float) $product->price * $selectedQuantities->get($product->id, 0);
        });

        DB::transaction(function () use ($data, $products, $selectedQuantities, $totalPrice): void {
            $party = HomeParty::create([
                'customer_id' => Auth::user()?->customer?->id,
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'event_date' => $data['event_date'],
                'event_time' => $data['event_time'],
                'guest_quantity' => $data['guest_quantity'],
                'party_type' => $data['party_type'],
                'note' => $data['note'] ?? null,
                'total_price' => $totalPrice,
                'status' => 'chờ xác nhận',
            ]);

            foreach ($products as $product) {
                $quantity = $selectedQuantities->get($product->id, 0);
                $price = (float) $product->price;

                $party->details()->create([
                    'food_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $price * $quantity,
                ]);
            }
        });

        return back()->with('status', 'Yêu cầu đặt tiệc tại nhà đã được gửi. Nhà hàng Hoa Sen sẽ liên hệ xác nhận sớm nhất.');
    }

    public function update(Request $request, HomeParty $homeParty): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(HomeParty::STATUSES)],
            'assigned_employee_id' => ['nullable', 'exists:employees,id'],
        ], [
            'required' => ':attribute là bắt buộc.',
            'in' => ':attribute không hợp lệ.',
            'exists' => ':attribute không tồn tại.',
            'attributes.status' => 'Trạng thái',
            'attributes.assigned_employee_id' => 'Nhân viên phụ trách',
        ]);

        $homeParty->update($data);

        return back()->with('status', 'Đã cập nhật đơn đặt tiệc tại nhà.');
    }

    private function combos($products): array
    {
        $ids = $products->pluck('id')->values();

        return [
            [
                'name' => 'Combo Gia đình ấm cúng',
                'description' => 'Phù hợp 10-15 khách, ưu tiên món dễ ăn và món chính đậm vị.',
                'product_ids' => $ids->take(4)->all(),
                'guests' => '10-15 khách',
            ],
            [
                'name' => 'Combo Liên hoan công ty',
                'description' => 'Gợi ý cho nhóm đông, nhiều món dùng chung và phục vụ nhanh.',
                'product_ids' => $ids->skip(2)->take(5)->values()->all(),
                'guests' => '20-40 khách',
            ],
            [
                'name' => 'Combo Tiệc cưới nhỏ',
                'description' => 'Các món trình bày trang trọng cho buổi tiệc thân mật tại nhà.',
                'product_ids' => $ids->take(6)->all(),
                'guests' => '30-60 khách',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'email' => ':attribute không đúng định dạng.',
            'date' => ':attribute không hợp lệ.',
            'date_format' => ':attribute không hợp lệ.',
            'after_or_equal' => ':attribute không được nhỏ hơn hôm nay.',
            'integer' => ':attribute phải là số nguyên.',
            'min' => ':attribute phải tối thiểu :min.',
            'max' => ':attribute không được vượt quá :max.',
            'array' => ':attribute không hợp lệ.',
            'attributes.full_name' => 'Họ và tên',
            'attributes.phone' => 'Số điện thoại',
            'attributes.email' => 'Email',
            'attributes.address' => 'Địa chỉ tổ chức tiệc',
            'attributes.event_date' => 'Ngày tổ chức',
            'attributes.event_time' => 'Giờ tổ chức',
            'attributes.guest_quantity' => 'Số lượng khách',
            'attributes.party_type' => 'Loại tiệc',
            'attributes.note' => 'Ghi chú',
            'attributes.selected_products' => 'Thực đơn',
        ];
    }
}
