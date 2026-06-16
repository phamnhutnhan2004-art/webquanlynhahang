@extends('layouts.admin')

@php
    $titles = [
        'employees' => 'Quản lý nhân viên',
        'products' => 'Quản lý món ăn',
        'categories' => 'Quản lý danh mục',
        'tables' => 'Quản lý bàn ăn',
        'orders' => 'Quản lý đơn hàng',
        'reservations' => 'Quản lý đặt bàn',
        'home-parties' => 'Quản lý đặt tiệc tại nhà',
        'customers' => 'Quản lý khách hàng',
        'payments' => 'Quản lý thanh toán',
        'chatbot' => 'Quản lý Chatbot',
        'menu-galleries' => 'Quản lý menu hình ảnh',
        'gallery-images' => 'Quản lý thư viện ảnh',
        'news' => 'Quản lý tin tức',
        'settings' => 'Cài đặt hệ thống',
        'stats' => 'Thống kê và báo cáo',
    ];

    $descriptions = [
        'products' => 'Quản lý danh sách món ăn, giá bán, danh mục và trạng thái hiển thị.',
        'categories' => 'Sắp xếp nhóm món ăn để khách hàng và nhân viên dễ tra cứu.',
        'tables' => 'Theo dõi mã bàn, khu vực, số ghế và trạng thái phục vụ.',
        'orders' => 'Quan sát đơn hàng, khách hàng, bàn ăn và giá trị thanh toán.',
        'reservations' => 'Theo dõi lịch đặt bàn từ website và chatbot.',
        'home-parties' => 'Quản lý lịch tiệc tại nhà, trạng thái xử lý và nhân viên phụ trách.',
        'customers' => 'Tổng hợp thông tin khách hàng và lịch sử đặt bàn.',
        'employees' => 'Theo dõi hồ sơ nhân viên, vị trí, ca làm và lương.',
        'payments' => 'Kiểm tra giao dịch, phương thức thanh toán và trạng thái thu tiền.',
        'chatbot' => 'Xem lịch sử hội thoại, ý định xử lý và phiên tương tác.',
        'menu-galleries' => 'Quản lý hình ảnh hoặc PDF menu nhà hàng.',
        'gallery-images' => 'Quản lý thư viện ảnh hiển thị trên website.',
        'news' => 'Khu vực chuẩn bị cho bài viết, tin tức và thông báo nhà hàng.',
        'settings' => 'Cấu hình hồ sơ quản trị và các thông tin hệ thống.',
        'stats' => 'Bảng báo cáo doanh thu theo ngày.',
    ];

    $money = fn ($value) => number_format((float) $value, 0, ',', '.').' VNĐ';
    $detail = fn (array $data) => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $statusOptions = match ($section) {
        'products' => ['available' => 'Đang bán', 'out_of_stock' => 'Tạm hết', 'inactive' => 'Ẩn'],
        'categories' => ['hiển thị' => 'Hiển thị', 'ẩn' => 'Ẩn'],
        'tables' => ['trống' => 'Trống', 'đang phục vụ' => 'Đang phục vụ', 'đã đặt' => 'Đã đặt', 'bảo trì' => 'Bảo trì'],
        default => [],
    };

    $statusField = match ($section) {
        'orders', 'reservations', 'home-parties', 'employees' => 'status',
        'payments' => 'payment_status',
        'chatbot' => 'intent',
        default => null,
    };

    if ($statusField && empty($statusOptions) && isset($items)) {
        $statusOptions = collect($items)
            ->pluck($statusField)
            ->filter()
            ->unique()
            ->mapWithKeys(fn ($status) => [$status => ucfirst($status)])
            ->all();
    }

    $revenueDetail = fn ($row) => $detail(['Ngày' => $row->date, 'Doanh thu' => $money($row->revenue)]);
    $productDetail = fn ($item) => $detail(['Tên món' => $item->name, 'Danh mục' => $item->category?->name, 'Giá' => $money($item->price), 'Trạng thái' => $statusOptions[$item->status] ?? $item->status]);
    $categoryDetail = fn ($item) => $detail(['Tên danh mục' => $item->name, 'Mô tả' => $item->description, 'Số món' => $item->products_count, 'Trạng thái' => $item->status]);
    $tableDetail = fn ($item) => $detail(['Mã bàn' => $item->table_code, 'Tên bàn' => $item->table_name, 'Khu vực' => $item->area, 'Số ghế' => $item->seats, 'Trạng thái' => $item->status]);
    $orderDetail = fn ($item) => $detail(['Mã đơn' => $item->order_code, 'Khách hàng' => $item->customer?->full_name, 'Bàn' => $item->table?->table_name, 'Tổng tiền' => $money($item->total_amount), 'Trạng thái' => $item->status]);
    $reservationDetail = fn ($item) => $detail(['Mã đặt bàn' => $item->reservation_code, 'Khách hàng' => $item->customer?->full_name, 'Bàn' => $item->table?->table_name, 'Thời gian' => $item->reservation_time?->format('d/m/Y H:i'), 'Ghi chú' => $item->note, 'Trạng thái' => $item->status]);
    $homePartyDetail = fn ($item) => $detail(['Khách hàng' => $item->full_name, 'Điện thoại' => $item->phone, 'Địa chỉ' => $item->address, 'Số khách' => $item->guest_quantity, 'Tổng tiền' => $money($item->total_price), 'Ghi chú' => $item->note]);
    $customerDetail = fn ($item) => $detail(['Khách hàng' => $item->full_name, 'Điện thoại' => $item->phone, 'Email' => $item->email, 'Địa chỉ' => $item->address, 'Ghi chú' => $item->note]);
    $employeeDetail = fn ($item) => $detail(['Mã NV' => $item->employee_code, 'Họ tên' => $item->user?->name ?? $item->user?->full_name, 'Vị trí' => $item->position, 'Ca làm' => $item->shift, 'Lương' => $money($item->salary), 'Trạng thái' => $item->status]);
    $paymentDetail = fn ($item) => $detail(['Mã thanh toán' => $item->payment_code, 'Khách hàng' => $item->reservation?->customer?->full_name, 'Phương thức' => $item->payment_method, 'Tổng tiền' => $money($item->total_amount), 'Trạng thái' => $item->payment_status]);
    $chatbotDetail = fn ($item) => $detail(['Phiên' => $item->session_id, 'Người gửi' => $item->sender, 'Tin nhắn' => $item->message, 'Ý định' => $item->intent, 'Độ tin cậy' => $item->confidence]);
    $menuGalleryDetail = fn ($item) => $detail(['Tiêu đề' => $item->title, 'Mô tả' => $item->description, 'Ngày tạo' => $item->created_at?->format('d/m/Y')]);
    $galleryImageDetail = fn ($item) => $detail(['Tiêu đề' => $item->title, 'Ngày tạo' => $item->created_at?->format('d/m/Y')]);
@endphp

@section('title', ($titles[$section] ?? 'Quản trị') . ' - Admin')

@section('content')
<section class="admin-page-head">
    <div>
        <div class="admin-kicker">Khu vực Admin</div>
        <h1 class="admin-title">{{ $titles[$section] ?? 'Quản trị dữ liệu' }}</h1>
        <p class="admin-subtitle">{{ $descriptions[$section] ?? 'Xem và quản lý dữ liệu của hệ thống nhà hàng.' }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}"><i class="bi bi-arrow-left me-1"></i>Về dashboard</a>
</section>

@if($section === 'categories')
    <div class="admin-card mb-3">
        <div class="admin-card-header"><h2 class="h5 mb-0">Thêm danh mục</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Tên danh mục</label>
                    <input class="form-control" name="name" required maxlength="100">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Mô tả</label>
                    <input class="form-control" name="description" maxlength="255">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="hiển thị">Hiển thị</option>
                        <option value="ẩn">Ẩn</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid align-items-end">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg"></i></button>
                </div>
            </form>
        </div>
    </div>
@elseif($section === 'products')
    <div class="admin-card mb-3">
        <div class="admin-card-header"><h2 class="h5 mb-0">Thêm món ăn</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-lg-3">
                    <label class="form-label">Tên món</label>
                    <input class="form-control" name="name" required maxlength="150">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Danh mục</label>
                    <select class="form-select" name="category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Giá</label>
                    <input class="form-control" type="number" name="price" min="0" step="1000" required>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="available">Đang bán</option>
                        <option value="out_of_stock">Tạm hết</option>
                        <option value="inactive">Ẩn</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Ảnh món</label>
                    <input class="form-control" type="file" name="image" accept="image/png,image/jpeg">
                </div>
                <div class="col-12">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control" name="description" rows="2"></textarea>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg me-1"></i>Thêm món ăn</button>
                </div>
            </form>
        </div>
    </div>
@elseif($section === 'tables')
    <div class="admin-card mb-3">
        <div class="admin-card-header"><h2 class="h5 mb-0">Thêm bàn ăn</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.tables.store') }}" class="row g-3">
                @csrf
                <div class="col-md-2">
                    <label class="form-label">Mã bàn</label>
                    <input class="form-control" name="table_code" required maxlength="30">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tên bàn</label>
                    <input class="form-control" name="table_name" required maxlength="80">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Khu vực</label>
                    <input class="form-control" name="area" required maxlength="80">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Số ghế</label>
                    <input class="form-control" type="number" name="seats" min="1" max="30" value="4" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="trống">Trống</option>
                        <option value="đang phục vụ">Đang phục vụ</option>
                        <option value="đã đặt">Đã đặt</option>
                        <option value="bảo trì">Bảo trì</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg me-1"></i>Thêm bàn</button>
                </div>
            </form>
        </div>
    </div>
@elseif($section === 'menu-galleries')
    <div class="admin-card mb-3">
        <div class="admin-card-header"><h2 class="h5 mb-0">Tải lên menu nhà hàng</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.menu-galleries.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-3"><label class="form-label">Tiêu đề</label><input class="form-control" name="title" required maxlength="150"></div>
                <div class="col-md-5"><label class="form-label">Mô tả</label><input class="form-control" name="description" maxlength="255"></div>
                <div class="col-md-3"><label class="form-label">Ảnh/PDF</label><input class="form-control" type="file" name="image" accept="image/png,image/jpeg,application/pdf" required></div>
                <div class="col-md-1 d-grid align-items-end"><button class="btn btn-primary" type="submit"><i class="bi bi-upload"></i></button></div>
            </form>
        </div>
    </div>
@elseif($section === 'gallery-images')
    <div class="admin-card mb-3">
        <div class="admin-card-header"><h2 class="h5 mb-0">Tải lên ảnh nhà hàng</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.gallery-images.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-5"><label class="form-label">Tiêu đề ảnh</label><input class="form-control" name="title" required maxlength="150"></div>
                <div class="col-md-5"><label class="form-label">Ảnh JPG/PNG</label><input class="form-control" type="file" name="image" accept="image/png,image/jpeg" required></div>
                <div class="col-md-2 d-grid align-items-end"><button class="btn btn-primary" type="submit"><i class="bi bi-upload me-1"></i>Tải ảnh</button></div>
            </form>
        </div>
    </div>
@endif

@if($section === 'settings')
    <div class="row g-3">
        <section class="col-lg-6">
            <div class="admin-card h-100" id="ho-so">
                <div class="admin-card-header"><h2 class="h5 mb-0">Hồ sơ cá nhân</h2></div>
                <div class="admin-card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                        <div>
                            <div class="fw-bold">{{ auth()->user()->name ?? 'Quản trị viên' }}</div>
                            <div class="text-muted small">Vai trò: Admin</div>
                        </div>
                    </div>
                    <div class="soft-note">Thông tin hồ sơ đang được hiển thị trong thanh người dùng. Có thể mở rộng thêm form cập nhật khi bổ sung route hồ sơ.</div>
                </div>
            </div>
        </section>
        <section class="col-lg-6">
            <div class="admin-card h-100" id="doi-mat-khau">
                <div class="admin-card-header"><h2 class="h5 mb-0">Đổi mật khẩu</h2></div>
                <div class="admin-card-body">
                    <div class="soft-note">Khu vực đổi mật khẩu đã được bố trí trong giao diện cài đặt. Backend đổi mật khẩu có thể nối vào form này khi cần.</div>
                </div>
            </div>
        </section>
    </div>
@elseif($section === 'news')
    <div class="admin-card">
        <div class="admin-card-body">
            <div class="soft-note">Chưa có model tin tức trong hệ thống hiện tại. Giao diện đã có menu và khu vực quản lý để sẵn sàng bổ sung bài viết, chuyên mục và trạng thái xuất bản.</div>
        </div>
    </div>
@elseif($section === 'stats')
    <div class="admin-card" data-admin-table>
        <div class="admin-card-header">
            <h2 class="h5 mb-0">Doanh thu theo ngày</h2>
        </div>
        <div class="admin-card-body">
            <div class="admin-table-toolbar">
                <input class="form-control" data-table-search placeholder="Tìm theo ngày hoặc doanh thu">
                <select class="form-select" data-table-filter><option value="">Tất cả dữ liệu</option></select>
                <select class="form-select" data-table-size><option>10</option><option>25</option><option>50</option></select>
            </div>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead><tr><th>Ngày</th><th>Doanh thu</th><th data-no-sort>Thao tác</th></tr></thead>
                    <tbody>
                    @forelse($revenueByDay as $row)
                        <tr>
                            <td>{{ $row->date }}</td>
                            <td>{{ $money($row->revenue) }}</td>
                            <td class="action-cell">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $revenueDetail($row) }}"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">Chưa có dữ liệu thống kê.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3" data-table-pager></div>
        </div>
    </div>
@else
    <div class="admin-card" data-admin-table>
        <div class="admin-card-header">
            <h2 class="h5 mb-0">Danh sách dữ liệu</h2>
            @if(! in_array($section, ['products', 'categories', 'tables', 'menu-galleries', 'gallery-images'], true))
                <button class="btn btn-primary btn-sm" type="button" disabled><i class="bi bi-plus-lg me-1"></i>Thêm</button>
            @endif
        </div>
        <div class="admin-card-body">
            <div class="admin-table-toolbar">
                <input class="form-control" data-table-search placeholder="Tìm kiếm dữ liệu">
                <select class="form-select" data-table-filter>
                    <option value="">Tất cả trạng thái</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select class="form-select" data-table-size>
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table mb-0">
                    @if($section === 'products')
                        <thead><tr><th>Ảnh</th><th>Tên món</th><th>Danh mục</th><th>Giá</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><img class="thumb" src="{{ $item->image_url }}" alt="{{ $item->name }}"></td>
                                <td><input class="form-control form-control-sm" form="update-product-{{ $item->id }}" name="name" value="{{ $item->name }}" required></td>
                                <td>
                                    <select class="form-select form-select-sm" form="update-product-{{ $item->id }}" name="category_id">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected($item->category_id === $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input class="form-control form-control-sm" form="update-product-{{ $item->id }}" type="number" name="price" min="0" step="1000" value="{{ (float) $item->price }}" required></td>
                                <td>
                                    <select class="form-select form-select-sm" form="update-product-{{ $item->id }}" name="status">
                                        <option value="available" @selected($item->status === 'available')>Đang bán</option>
                                        <option value="out_of_stock" @selected($item->status === 'out_of_stock')>Tạm hết</option>
                                        <option value="inactive" @selected($item->status === 'inactive')>Ẩn</option>
                                    </select>
                                    <input type="hidden" form="update-product-{{ $item->id }}" name="description" value="{{ $item->description }}">
                                </td>
                                <td class="action-cell">
                                    <form id="update-product-{{ $item->id }}" method="POST" action="{{ route('admin.products.update', $item) }}">@csrf @method('PUT')</form>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $productDetail($item) }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-primary" form="update-product-{{ $item->id }}" type="submit"><i class="bi bi-pencil-square"></i></button>
                                    <form method="POST" action="{{ route('admin.products.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'categories')
                        <thead><tr><th>Tên danh mục</th><th>Mô tả</th><th>Số món</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><input class="form-control form-control-sm" form="update-category-{{ $item->id }}" name="name" value="{{ $item->name }}" required></td>
                                <td><input class="form-control form-control-sm" form="update-category-{{ $item->id }}" name="description" value="{{ $item->description }}"></td>
                                <td>{{ $item->products_count }}</td>
                                <td>
                                    <select class="form-select form-select-sm" form="update-category-{{ $item->id }}" name="status">
                                        <option value="hiển thị" @selected($item->status === 'hiển thị')>Hiển thị</option>
                                        <option value="ẩn" @selected($item->status === 'ẩn')>Ẩn</option>
                                    </select>
                                </td>
                                <td class="action-cell">
                                    <form id="update-category-{{ $item->id }}" method="POST" action="{{ route('admin.categories.update', $item) }}">@csrf @method('PUT')</form>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $categoryDetail($item) }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-primary" form="update-category-{{ $item->id }}" type="submit"><i class="bi bi-pencil-square"></i></button>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'tables')
                        <thead><tr><th>Mã bàn</th><th>Tên bàn</th><th>Khu vực</th><th>Số ghế</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><input class="form-control form-control-sm" form="update-table-{{ $item->id }}" name="table_code" value="{{ $item->table_code }}" required></td>
                                <td><input class="form-control form-control-sm" form="update-table-{{ $item->id }}" name="table_name" value="{{ $item->table_name }}" required></td>
                                <td><input class="form-control form-control-sm" form="update-table-{{ $item->id }}" name="area" value="{{ $item->area }}" required></td>
                                <td><input class="form-control form-control-sm" form="update-table-{{ $item->id }}" type="number" name="seats" min="1" max="30" value="{{ $item->seats }}" required></td>
                                <td>
                                    <select class="form-select form-select-sm" form="update-table-{{ $item->id }}" name="status">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($item->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="action-cell">
                                    <form id="update-table-{{ $item->id }}" method="POST" action="{{ route('admin.tables.update', $item) }}">@csrf @method('PUT')</form>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $tableDetail($item) }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-primary" form="update-table-{{ $item->id }}" type="submit"><i class="bi bi-pencil-square"></i></button>
                                    <form method="POST" action="{{ route('admin.tables.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'orders')
                        <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Bàn</th><th>Số món</th><th>Tổng tiền</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><strong>{{ $item->order_code }}</strong></td>
                                <td>{{ $item->customer?->full_name ?? 'Khách lẻ' }}</td>
                                <td>{{ $item->table?->table_name ?? '-' }}</td>
                                <td>{{ $item->items->count() }}</td>
                                <td>{{ $money($item->total_amount) }}</td>
                                <td><span class="status-pill">{{ $item->status }}</span></td>
                                <td class="action-cell">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $orderDetail($item) }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" disabled><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'reservations')
                        <thead><tr><th>Mã đặt bàn</th><th>Khách hàng</th><th>Bàn</th><th>Thời gian</th><th>Số khách</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><strong>{{ $item->reservation_code }}</strong></td>
                                <td>{{ $item->customer?->full_name ?? '-' }}</td>
                                <td>{{ $item->table?->table_name ?? '-' }}</td>
                                <td>{{ $item->reservation_time?->format('d/m/Y H:i') }}</td>
                                <td>{{ $item->number_of_guests }}</td>
                                <td><span class="status-pill">{{ $item->status }}</span></td>
                                <td class="action-cell">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $reservationDetail($item) }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" disabled><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'home-parties')
                        <thead><tr><th>Khách hàng</th><th>Lịch tiệc</th><th>Loại tiệc</th><th>Số khách</th><th>Tổng tiền</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><strong>{{ $item->full_name }}</strong><div class="small text-muted">{{ $item->phone }}</div></td>
                                <td>{{ $item->event_date?->format('d/m/Y') }} {{ $item->event_time?->format('H:i') }}</td>
                                <td>{{ $item->party_type }}</td>
                                <td>{{ $item->guest_quantity }}</td>
                                <td>{{ $money($item->total_price) }}</td>
                                <td>
                                    <form id="update-party-{{ $item->id }}" method="POST" action="{{ route('admin.home-parties.update', $item) }}">@csrf @method('PATCH')</form>
                                    <select class="form-select form-select-sm" form="update-party-{{ $item->id }}" name="status">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-select form-select-sm mt-1" form="update-party-{{ $item->id }}" name="assigned_employee_id">
                                        <option value="">Chưa phân công</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" @selected($item->assigned_employee_id === $employee->id)>{{ $employee->employee_code }} - {{ $employee->user?->name ?? $employee->user?->full_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="action-cell">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $homePartyDetail($item) }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-primary" form="update-party-{{ $item->id }}" type="submit"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" type="button" disabled><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có đơn đặt tiệc tại nhà.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'customers')
                        <thead><tr><th>Khách hàng</th><th>Điện thoại</th><th>Email</th><th>Địa chỉ</th><th>Số lần đặt bàn</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><strong>{{ $item->full_name }}</strong></td><td>{{ $item->phone }}</td><td>{{ $item->email }}</td><td>{{ $item->address }}</td><td>{{ $item->reservations_count }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $customerDetail($item) }}"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger" type="button" disabled><i class="bi bi-trash"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'employees')
                        <thead><tr><th>Mã NV</th><th>Họ tên</th><th>Vị trí</th><th>Ca làm</th><th>Lương</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><strong>{{ $item->employee_code }}</strong></td><td>{{ $item->user?->name ?? $item->user?->full_name }}</td><td>{{ $item->position }}</td><td>{{ $item->shift }}</td><td>{{ $money($item->salary) }}</td><td><span class="status-pill">{{ $item->status }}</span></td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $employeeDetail($item) }}"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger" type="button" disabled><i class="bi bi-trash"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'payments')
                        <thead><tr><th>Mã thanh toán</th><th>Khách hàng</th><th>Phương thức</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày thanh toán</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->payment_status }}">
                                <td><strong>{{ $item->payment_code }}</strong></td><td>{{ $item->reservation?->customer?->full_name ?? '-' }}</td><td>{{ $item->payment_method }}</td><td>{{ $money($item->total_amount) }}</td><td><span class="status-pill">{{ $item->payment_status }}</span></td><td>{{ $item->paid_at?->format('d/m/Y H:i') }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $paymentDetail($item) }}"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger" type="button" disabled><i class="bi bi-trash"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'chatbot')
                        <thead><tr><th>Phiên</th><th>Người gửi</th><th>Tin nhắn</th><th>Ý định</th><th>Độ tin cậy</th><th>Thời gian</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->intent }}">
                                <td>{{ $item->session_id }}</td><td>{{ $item->sender }}</td><td>{{ Str::limit($item->message, 80) }}</td><td><span class="status-pill">{{ $item->intent ?? 'chưa xác định' }}</span></td><td>{{ $item->confidence }}</td><td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $chatbotDetail($item) }}"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button><button class="btn btn-sm btn-outline-danger" type="button" disabled><i class="bi bi-trash"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có dữ liệu chatbot.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'menu-galleries')
                        <thead><tr><th>Tiêu đề</th><th>Mô tả</th><th>Tệp</th><th>Ngày tạo</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><strong>{{ $item->title }}</strong></td><td>{{ $item->description }}</td><td><a class="btn btn-sm btn-outline-secondary" href="{{ $item->image_url }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Xem</a></td><td>{{ $item->created_at?->format('d/m/Y') }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $menuGalleryDetail($item) }}"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button><form method="POST" action="{{ route('admin.menu-galleries.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Chưa có menu hình ảnh.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'gallery-images')
                        <thead><tr><th>Ảnh</th><th>Tiêu đề</th><th>Ngày tạo</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><img class="thumb" src="{{ $item->image_url }}" alt="{{ $item->title }}"></td><td><strong>{{ $item->title }}</strong></td><td>{{ $item->created_at?->format('d/m/Y') }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $galleryImageDetail($item) }}"><i class="bi bi-eye"></i></button><button class="btn btn-sm btn-outline-primary" type="button" disabled><i class="bi bi-pencil-square"></i></button><form method="POST" action="{{ route('admin.gallery-images.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Chưa có ảnh nhà hàng.</td></tr>
                        @endforelse
                        </tbody>
                    @endif
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3" data-table-pager></div>
        </div>
    </div>
@endif
@endsection
