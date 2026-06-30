@extends('layouts.admin')

@php
    $titles = [
        'employees' => 'Quản lý nhân viên',
        'products' => 'Quản lý món ăn',
        'categories' => 'Quản lý danh mục',
        'tables' => 'Quản lý bàn ăn',
        'orders' => 'Quản lý đơn hàng',
        'accounts' => 'Quản lý tài khoản',
        'reservations' => 'Quản lý đặt bàn',
        'home-parties' => 'Quản lý đặt tiệc tại nhà',
        'customers' => 'Quản lý khách hàng',
        'payments' => 'Quản lý thanh toán',
        'payment-methods' => 'Quản lý phương thức thanh toán',
        'chatbot' => 'Quản lý Chatbot',
        'ai-chatbot' => 'Cấu hình AI Chatbot',
        'theme-settings' => 'Cài đặt giao diện',
        'auth-interface' => 'Giao diện đăng nhập',
        'menu-galleries' => 'Quản lý hình ảnh món ăn',
        'gallery-images' => 'Quản lý thư viện ảnh',
        'news' => 'Quản lý tin tức',
        'stats' => 'Thống kê và báo cáo',
    ];

    $descriptions = [
        'products' => 'Quản lý danh sách món ăn, giá bán, danh mục và trạng thái hiển thị.',
        'categories' => 'Sắp xếp nhóm món ăn để khách hàng và nhân viên dễ tra cứu.',
        'tables' => 'Theo dõi mã bàn, khu vực, số ghế và trạng thái phục vụ.',
        'orders' => 'Quan sát đơn hàng, khách hàng, bàn ăn và giá trị thanh toán.',
        'accounts' => 'Quản lý tài khoản Admin, nhân viên và khách hàng; chỉnh vai trò, trạng thái, mật khẩu và thông tin liên hệ.',
        'reservations' => 'Theo dõi lịch đặt bàn từ website và chatbot.',
        'home-parties' => 'Quản lý lịch tiệc tại nhà, trạng thái xử lý và nhân viên phụ trách.',
        'customers' => 'Tổng hợp thông tin khách hàng và lịch sử đặt bàn.',
        'employees' => 'Theo dõi hồ sơ nhân viên, vị trí, ca làm và lương.',
        'payments' => 'Kiểm tra giao dịch, phương thức thanh toán và trạng thái thu tiền.',
        'payment-methods' => 'Cấu hình tiền mặt, chuyển khoản, mã QR và ví điện tử cho trang thanh toán.',
        'chatbot' => 'Xem lịch sử hội thoại, ý định xử lý và phiên tương tác.',
        'ai-chatbot' => 'Quản lý Gemini API Key, trạng thái AI và prompt hệ thống của trợ lý nhà hàng.',
        'theme-settings' => 'Tùy chỉnh màu sắc, font chữ, kích thước, banner, nút bấm và khoảng cách cho giao diện website.',
        'auth-interface' => 'Chỉnh nội dung, màu sắc, ảnh nền và các tab của trang đăng nhập / đăng ký.',
        'menu-galleries' => 'Quản lý hình ảnh hoặc PDF menu nhà hàng.',
        'gallery-images' => 'Quản lý thư viện ảnh hiển thị trên website.',
        'news' => 'Khu vực chuẩn bị cho bài viết, tin tức và thông báo nhà hàng.',
        'stats' => 'Bảng báo cáo doanh thu theo ngày.',
    ];

    $money = fn ($value) => number_format((float) $value, 0, ',', '.').' VNĐ';
    $detail = fn (array $data) => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    $statusOptions = match ($section) {
        'products' => ['available' => 'Đang bán', 'out_of_stock' => 'Tạm hết', 'inactive' => 'Ẩn'],
        'categories' => ['hiển thị' => 'Hiển thị', 'ẩn' => 'Ẩn'],
        'tables' => ['trống' => 'Trống', 'đã đặt' => 'Đã đặt', 'đang sử dụng' => 'Đang sử dụng', 'đang dọn dẹp' => 'Đang dọn dẹp'],
        'orders' => ['pending' => 'Chờ xử lý', 'serving' => 'Đang phục vụ', 'completed' => 'Hoàn thành', 'paid' => 'Đã thanh toán', 'cancelled' => 'Đã hủy'],
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
    $orderDetail = fn ($item) => $detail(['Mã đơn' => $item->order_code, 'Khách hàng' => $item->customer?->full_name, 'Bàn' => $item->table?->table_name, 'Tổng tiền' => $money($item->total_amount), 'Trạng thái' => $statusOptions[$item->status] ?? $item->status]);
    $reservationDetail = fn ($item) => $detail(['Mã đặt bàn' => $item->reservation_code, 'Khách hàng' => $item->customerName(), 'Loại khách' => $item->customer_type, 'Điện thoại' => $item->customerPhone(), 'Email' => $item->customerEmail(), 'Bàn' => $item->table?->table_name, 'Thời gian' => $item->reservation_time?->format('d/m/Y H:i'), 'Ghi chú' => $item->note, 'Trạng thái' => $item->status]);
    $homePartyDetail = fn ($item) => $detail(['Khách hàng' => $item->full_name, 'Điện thoại' => $item->phone, 'Địa chỉ' => $item->address, 'Số khách' => $item->guest_quantity, 'Tổng tiền' => $money($item->total_price), 'Ghi chú' => $item->note]);
    $accountDetail = fn ($item) => $detail([
        'Mã tài khoản' => '#'.$item->id,
        'Họ và tên' => $item->name ?? $item->full_name,
        'Email' => $item->email,
        'Số điện thoại' => $item->phone,
        'Địa chỉ' => $item->address,
        'Vai trò' => $item->role?->name,
        'Trạng thái' => $item->status,
        'Email xác thực' => $item->email_verified_at?->format('d/m/Y H:i') ?? 'Chưa xác thực',
        'Lịch sử đặt bàn' => $item->customer?->reservations?->count() ?? 0,
        'Tổng số đơn' => $item->customer?->orders?->count() ?? 0,
        'Tổng số tiền đã chi' => $money($item->customer?->bills?->sum('total_amount') ?? 0),
        'Chức vụ' => $item->employee?->position,
        'Ca làm việc' => $item->employee?->shift,
        'Số đơn đã phục vụ' => $item->employee?->orders?->count() ?? 0,
        'Ngày vào làm' => $item->employee?->hire_date?->format('d/m/Y'),
        'Ngày tạo' => $item->created_at?->format('d/m/Y H:i'),
    ]);
    $customerDetail = fn ($item) => $detail(['Khách hàng' => $item->full_name, 'Điện thoại' => $item->phone, 'Email' => $item->email, 'Địa chỉ' => $item->address, 'Ghi chú' => $item->note]);
    $employeeDetail = fn ($item) => $detail(['Mã NV' => $item->employee_code, 'Họ tên' => $item->user?->name ?? $item->user?->full_name, 'Vị trí' => $item->position, 'Ca làm' => $item->shift, 'Lương' => $money($item->salary), 'Trạng thái' => $item->status]);
    $paymentDetail = fn ($item) => $detail(['Mã thanh toán' => $item->payment_code, 'Khách hàng' => $item->reservation?->customer?->full_name, 'Phương thức' => $item->payment_method, 'Tổng tiền' => $money($item->total_amount), 'Trạng thái' => $item->payment_status]);
    $paymentMethodDetail = fn ($item) => $item->method_key === 'cash'
        ? $detail(['Phương thức' => 'Tiền mặt', 'Trạng thái' => $item->is_active ? 'Đang bật' : 'Đang tắt'])
        : $detail(['Phương thức' => $item->display_name, 'Loại' => $item->methodLabel(), 'Ngân hàng' => $item->bank_name, 'Chủ tài khoản' => $item->account_holder, 'Số tài khoản' => $item->account_number, 'Nội dung mặc định' => $item->transfer_content_template, 'Trạng thái' => $item->is_active ? 'Đang bật' : 'Đang tắt']);
    $chatbotDetail = fn ($item) => $detail(['Phiên' => $item->session_id, 'Người gửi' => $item->sender, 'Tin nhắn' => $item->message, 'Ý định' => $item->intent, 'Model' => $item->model, 'Token sử dụng' => $item->total_tokens, 'Độ tin cậy' => $item->confidence]);
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
                        <option value="đã đặt">Đã đặt</option>
                        <option value="đang sử dụng">Đang sử dụng</option>
                        <option value="đang dọn dẹp">Đang dọn dẹp</option>
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

@if($section === 'payment-methods')
    @php
        $methodLabels = [
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'qr' => 'Quét mã QR',
            'e_wallet' => 'Ví điện tử',
        ];
    @endphp

    <div class="admin-card mb-3">
        <div class="admin-card-header"><h2 class="h5 mb-0">Thêm phương thức thanh toán</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.payment-methods.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Loại thanh toán</label>
                    <select class="form-select" name="method_key" required>
                        @foreach($methodLabels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tên hiển thị</label>
                    <input class="form-control" name="display_name" required maxlength="120">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ngân hàng</label>
                    <input class="form-control" name="bank_name" maxlength="120" placeholder="VD: VCB">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Chủ tài khoản</label>
                    <input class="form-control" name="account_holder" maxlength="150" placeholder="PHAM NHUT NHAN">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Số tài khoản</label>
                    <input class="form-control" name="account_number" maxlength="80" placeholder="9789661781">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nội dung chuyển khoản mặc định</label>
                    <input class="form-control" name="transfer_content_template" maxlength="180" value="THANHTOAN_[ORDER_CODE]">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ảnh mã QR</label>
                    <input class="form-control" type="file" name="qr_image" accept="image/png,image/jpeg,image/webp">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Thứ tự</label>
                    <input class="form-control" type="number" name="sort_order" min="0" max="999" value="10">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Bật</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg me-1"></i>Thêm phương thức</button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card" data-admin-table>
        <div class="admin-card-header"><h2 class="h5 mb-0">Danh sách phương thức thanh toán</h2></div>
        <div class="admin-card-body">
            <div class="admin-table-toolbar">
                <input class="form-control" data-table-search placeholder="Tìm ngân hàng, tài khoản, phương thức">
                <select class="form-select" data-table-filter>
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Đang bật</option>
                    <option value="inactive">Đang tắt</option>
                </select>
                <select class="form-select" data-table-size><option>10</option><option>25</option><option>50</option></select>
            </div>
            <div class="table-responsive">
                <table class="table table-hover admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Phương thức</th>
                            <th>Ngân hàng</th>
                            <th>Tài khoản</th>
                            <th>Nội dung CK</th>
                            <th>QR</th>
                            <th>Trạng thái</th>
                            <th data-no-sort>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        @php
                            $isCash = $item->method_key === 'cash';
                        @endphp
                        <tr data-status="{{ $item->is_active ? 'active' : 'inactive' }}">
                            @if($isCash)
                                <td colspan="5">
                                    <strong>Tiền mặt</strong>
                                    <input type="hidden" form="update-payment-method-{{ $item->id }}" name="method_key" value="cash">
                                    <input type="hidden" form="update-payment-method-{{ $item->id }}" name="display_name" value="Tiền mặt">
                                    <input type="hidden" form="update-payment-method-{{ $item->id }}" name="bank_name" value="">
                                    <input type="hidden" form="update-payment-method-{{ $item->id }}" name="account_holder" value="">
                                    <input type="hidden" form="update-payment-method-{{ $item->id }}" name="account_number" value="">
                                    <input type="hidden" form="update-payment-method-{{ $item->id }}" name="transfer_content_template" value="{{ $item->transfer_content_template }}">
                                    <input type="hidden" form="update-payment-method-{{ $item->id }}" name="sort_order" value="{{ $item->sort_order }}">
                                </td>
                            @else
                                <td>
                                    <select class="form-select form-select-sm mb-1" form="update-payment-method-{{ $item->id }}" name="method_key">
                                        @foreach($methodLabels as $value => $label)
                                            <option value="{{ $value }}" @selected($item->method_key === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input class="form-control form-control-sm" form="update-payment-method-{{ $item->id }}" name="display_name" value="{{ $item->display_name }}" required>
                                </td>
                                <td><input class="form-control form-control-sm" form="update-payment-method-{{ $item->id }}" name="bank_name" value="{{ $item->bank_name }}"></td>
                                <td>
                                    <input class="form-control form-control-sm mb-1" form="update-payment-method-{{ $item->id }}" name="account_holder" value="{{ $item->account_holder }}" placeholder="Chủ tài khoản">
                                    <input class="form-control form-control-sm" form="update-payment-method-{{ $item->id }}" name="account_number" value="{{ $item->account_number }}" placeholder="Số tài khoản">
                                </td>
                                <td>
                                    <input class="form-control form-control-sm mb-1" form="update-payment-method-{{ $item->id }}" name="transfer_content_template" value="{{ $item->transfer_content_template }}">
                                    <input class="form-control form-control-sm" form="update-payment-method-{{ $item->id }}" type="number" name="sort_order" min="0" max="999" value="{{ $item->sort_order }}">
                                </td>
                                <td>
                                    @if($item->qr_image_url)
                                        <img class="thumb mb-1" src="{{ $item->qr_image_url }}" alt="QR {{ $item->display_name }}">
                                    @else
                                        <span class="small text-muted d-block mb-1">Chưa có QR</span>
                                    @endif
                                    <input class="form-control form-control-sm" form="update-payment-method-{{ $item->id }}" type="file" name="qr_image" accept="image/png,image/jpeg,image/webp">
                                </td>
                            @endif
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" form="update-payment-method-{{ $item->id }}" type="checkbox" name="is_active" value="1" @checked($item->is_active)>
                                    <label class="form-check-label">{{ $item->is_active ? 'Đang bật' : 'Đang tắt' }}</label>
                                </div>
                                <div class="small text-muted">{{ $item->bills_count }} hóa đơn</div>
                            </td>
                            <td class="action-cell">
                                <form id="update-payment-method-{{ $item->id }}" method="POST" action="{{ route('admin.payment-methods.update', $item) }}" enctype="multipart/form-data">@csrf @method('PUT')</form>
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $paymentMethodDetail($item) }}"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-primary" form="update-payment-method-{{ $item->id }}" type="submit"><i class="bi bi-pencil-square"></i></button>
                                <form method="POST" action="{{ route('admin.payment-methods.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">Chưa có phương thức thanh toán.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3" data-table-pager></div>
        </div>
    </div>
@endif

@if($section === 'ai-chatbot')
    @php
        $aiSetting ??= \App\Models\AiChatbotSetting::current();
        $modelOptions = [
            'gemini-3.5-flash' => 'Gemini 3.5 Flash',
            'gemini-2.5-flash' => 'Gemini 2.5 Flash',
            'gemini-2.5-pro' => 'Gemini 2.5 Pro',
            'gemini-2.0-flash' => 'Gemini 2.0 Flash',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash',
        ];
    @endphp

    <div class="row g-3">
        <section class="col-xl-8">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <h2 class="h5 mb-0">Cấu hình Gemini API</h2>
                    <span class="status-pill">{{ $aiSetting->is_enabled ? 'AI đang bật' : 'AI đang tắt' }}</span>
                </div>
                <div class="admin-card-body">
                    <form method="POST" action="{{ route('admin.ai-chatbot.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_enabled" value="1" id="aiEnabled" @checked($aiSetting->is_enabled)>
                                <label class="form-check-label fw-bold" for="aiEnabled">Bật AI Chatbot Gemini</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gemini API Key hiện tại</label>
                            <input class="form-control" value="{{ $aiSetting->maskedApiKey() }}" disabled>
                            <div class="form-text">API Key được mã hóa trong hệ thống và không hiển thị đầy đủ.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nhập API Key mới</label>
                            <input class="form-control" type="password" name="api_key" autocomplete="new-password" placeholder="Dán key mới nếu muốn thay đổi">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="clear_api_key" value="1" id="clearApiKey">
                                <label class="form-check-label" for="clearApiKey">Xóa API Key đang lưu</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <select class="form-select" name="model" required>
                                @foreach($modelOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($aiSetting->model === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Độ sáng tạo</label>
                            <input class="form-control" type="number" name="temperature" min="0" max="1" step="0.05" value="{{ old('temperature', $aiSetting->temperature) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Token trả lời tối đa</label>
                            <input class="form-control" type="number" name="max_output_tokens" min="200" max="4096" step="50" value="{{ old('max_output_tokens', $aiSetting->max_output_tokens) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">System Prompt</label>
                            <textarea class="form-control" name="system_prompt" rows="12" required>{{ old('system_prompt', $aiSetting->system_prompt) }}</textarea>
                            <div class="form-text">Prompt này định hướng Gemini luôn đóng vai Trợ lý AI của Nhà hàng Hoa Sen và ưu tiên dữ liệu MySQL của hệ thống.</div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Lưu cấu hình</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="col-xl-4">
            <div class="admin-card mb-3">
                <div class="admin-card-header"><h2 class="h5 mb-0">Trạng thái kết nối</h2></div>
                <div class="admin-card-body">
                    <div class="d-grid gap-2">
                        <div class="soft-note">
                            <div class="fw-bold mb-1">{{ $aiSetting->last_status === 'success' ? 'Kết nối gần nhất thành công' : 'Chưa có kết nối thành công gần đây' }}</div>
                            <div>Lần kiểm tra: {{ $aiSetting->last_checked_at?->format('d/m/Y H:i') ?? 'Chưa kiểm tra' }}</div>
                            @if($aiSetting->last_error)
                                <div class="text-danger mt-2">{{ $aiSetting->last_error }}</div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.ai-chatbot.test') }}">
                            @csrf
                            <button class="btn btn-outline-primary w-100" type="submit"><i class="bi bi-wifi me-1"></i>Kiểm tra Gemini API</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Ngữ cảnh hệ thống</h2></div>
                <div class="admin-card-body">
                    <div class="soft-note">
                        Chatbot sẽ gửi dữ liệu thực đơn, món bán chạy, bàn trống, thanh toán, đặt bàn và đặt tiệc tại nhà từ MySQL vào Gemini để trả lời chính xác hơn.
                    </div>
                </div>
            </div>
        </section>
    </div>
@endif

@if($section === 'auth-interface')
    @php
        $authPageSetting ??= \App\Models\WebsitePageSetting::current('auth');
        $authDefaults = \App\Models\WebsitePageSetting::authPageDefaults();
        $savedAuthConfig = $authPageSetting->getSetting('auth_page', []);
        $authConfig = array_replace_recursive($authDefaults, is_array($savedAuthConfig) ? $savedAuthConfig : []);
        $authContent = $authConfig['content'];
        $authStyle = $authConfig['style'];
        $authColorFields = [
            'background_color' => 'Màu nền trang',
            'shell_background' => 'Màu khung chính',
            'panel_background' => 'Màu vùng form',
            'heading_color' => 'Màu tiêu đề form',
            'body_color' => 'Màu chữ chính',
            'muted_color' => 'Màu mô tả',
            'visual_text_color' => 'Màu chữ khối ảnh',
            'accent_color' => 'Màu nhấn',
            'link_color' => 'Màu liên kết',
            'tab_background' => 'Màu nền tab',
            'tab_text' => 'Màu chữ tab',
            'tab_active_background' => 'Màu tab đang chọn',
            'tab_active_text' => 'Màu chữ tab đang chọn',
            'button_background' => 'Màu nút',
            'button_text' => 'Màu chữ nút',
            'button_hover' => 'Màu nút khi hover',
            'input_border' => 'Màu viền ô nhập',
            'border_color' => 'Màu viền khung',
            'visual_overlay_start' => 'Màu phủ ảnh 1',
            'visual_overlay_end' => 'Màu phủ ảnh 2',
        ];
    @endphp

    <style>
        .auth-editor-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(320px, .8fr);
            gap: 1rem;
            align-items: start;
        }

        .auth-editor-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .auth-editor-color {
            display: grid;
            grid-template-columns: 48px 1fr;
            gap: .65rem;
            align-items: center;
        }

        .auth-editor-color input[type="color"] {
            width: 48px;
            height: 42px;
            padding: .2rem;
        }

        .auth-editor-preview {
            position: sticky;
            top: 92px;
            overflow: hidden;
            border: 1px solid var(--admin-line);
            border-radius: 8px;
            background: #fffaf0;
            box-shadow: var(--admin-shadow);
        }

        .auth-editor-preview-inner {
            display: grid;
            grid-template-columns: .9fr 1.1fr;
            min-height: 430px;
        }

        .auth-editor-preview-visual {
            padding: 1.35rem;
            background:
                linear-gradient(135deg, rgba(14, 59, 50, .9), rgba(44, 27, 18, .82)),
                url("{{ asset($authStyle['visual_image']) }}") center / cover;
            color: #fff;
        }

        .auth-editor-preview-badge {
            display: inline-flex;
            padding: .35rem .55rem;
            border: 1px solid currentColor;
            border-radius: 999px;
            color: #f6df9d;
            font-size: .72rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .auth-editor-preview-visual h3 {
            margin: 1.2rem 0 .75rem;
            font-size: 1.65rem;
            font-weight: 950;
            line-height: 1.05;
        }

        .auth-editor-preview-form {
            padding: 1.35rem;
            background: #fff;
        }

        .auth-editor-preview-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .35rem;
            margin-top: 1rem;
            padding: .25rem;
            border-radius: 8px;
            background: #f6efe0;
        }

        .auth-editor-preview-tab {
            padding: .55rem;
            border-radius: 8px;
            color: #2c1b12;
            font-weight: 900;
            text-align: center;
        }

        .auth-editor-preview-tab.is-active {
            background: #0e3b32;
            color: #fff;
        }

        .auth-editor-preview-input {
            height: 42px;
            margin-top: .85rem;
            border: 1px solid #d9c6a8;
            border-radius: 8px;
            background: #fff;
        }

        .auth-editor-preview-button {
            display: grid;
            place-items: center;
            min-height: 42px;
            margin-top: .85rem;
            border-radius: 8px;
            background: #d9a441;
            color: #2c1b12;
            font-weight: 900;
        }

        @media (max-width: 1199.98px) {
            .auth-editor-grid,
            .auth-editor-preview-inner {
                grid-template-columns: 1fr;
            }

            .auth-editor-preview {
                position: static;
            }
        }
    </style>

    <div class="auth-editor-grid" data-auth-editor>
        <form method="POST" action="{{ route('admin.auth-interface.update') }}" class="d-grid gap-3">
            @csrf
            @method('PUT')

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Nội dung hiển thị</h2></div>
                <div class="admin-card-body">
                    <div class="auth-editor-fields">
                        <div>
                            <label class="form-label fw-bold">Nhãn thương hiệu</label>
                            <input class="form-control" name="content[badge]" maxlength="80" value="{{ old('content.badge', $authContent['badge']) }}" data-auth-preview="badge" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Dòng nhãn trên form</label>
                            <input class="form-control" name="content[eyebrow]" maxlength="80" value="{{ old('content.eyebrow', $authContent['eyebrow']) }}" data-auth-preview="eyebrow" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Tiêu đề form</label>
                            <input class="form-control" name="content[heading]" maxlength="140" value="{{ old('content.heading', $authContent['heading']) }}" data-auth-preview="heading" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Mô tả form</label>
                            <input class="form-control" name="content[description]" maxlength="220" value="{{ old('content.description', $authContent['description']) }}" data-auth-preview="description" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Tab đăng nhập</label>
                            <input class="form-control" name="content[login_tab]" maxlength="40" value="{{ old('content.login_tab', $authContent['login_tab']) }}" data-auth-preview="login_tab" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Tab đăng ký</label>
                            <input class="form-control" name="content[register_tab]" maxlength="40" value="{{ old('content.register_tab', $authContent['register_tab']) }}" data-auth-preview="register_tab" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Nút đăng nhập</label>
                            <input class="form-control" name="content[login_button]" maxlength="60" value="{{ old('content.login_button', $authContent['login_button']) }}" data-auth-preview="login_button" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Nút đăng ký</label>
                            <input class="form-control" name="content[register_button]" maxlength="60" value="{{ old('content.register_button', $authContent['register_button']) }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Tiêu đề khối ảnh</label>
                        <input class="form-control" name="content[visual_title]" maxlength="160" value="{{ old('content.visual_title', $authContent['visual_title']) }}" data-auth-preview="visual_title" required>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-bold">Mô tả khối ảnh</label>
                        <textarea class="form-control" name="content[visual_description]" rows="2" maxlength="320" data-auth-preview="visual_description" required>{{ old('content.visual_description', $authContent['visual_description']) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Ba lợi ích bên trái</h2></div>
                <div class="admin-card-body">
                    <div class="row g-3">
                        @foreach($authContent['benefits'] as $index => $benefit)
                            <div class="col-lg-4">
                                <div class="border rounded-2 p-3 h-100">
                                    <label class="form-label fw-bold">Icon Bootstrap</label>
                                    <input class="form-control mb-2" name="content[benefits][{{ $index }}][icon]" maxlength="40" value="{{ old("content.benefits.$index.icon", $benefit['icon']) }}" required>
                                    <label class="form-label fw-bold">Tiêu đề</label>
                                    <input class="form-control mb-2" name="content[benefits][{{ $index }}][title]" maxlength="80" value="{{ old("content.benefits.$index.title", $benefit['title']) }}" required>
                                    <label class="form-label fw-bold">Nội dung</label>
                                    <textarea class="form-control" name="content[benefits][{{ $index }}][text]" rows="3" maxlength="180" required>{{ old("content.benefits.$index.text", $benefit['text']) }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Màu sắc và ảnh nền</h2></div>
                <div class="admin-card-body">
                    <div class="auth-editor-fields">
                        @foreach($authColorFields as $key => $label)
                            <div>
                                <label class="form-label fw-bold">{{ $label }}</label>
                                <div class="auth-editor-color">
                                    <input class="form-control form-control-color" type="color" name="style[{{ $key }}]" value="{{ old("style.$key", $authStyle[$key]) }}" data-auth-style="{{ $key }}">
                                    <input class="form-control" value="{{ old("style.$key", $authStyle[$key]) }}" data-auth-color-text="{{ $key }}">
                                </div>
                            </div>
                        @endforeach
                        <div>
                            <label class="form-label fw-bold">Ảnh nền khối trái</label>
                            <input class="form-control" name="style[visual_image]" maxlength="255" value="{{ old('style.visual_image', $authStyle['visual_image']) }}" data-auth-style="visual_image" placeholder="images/restaurant-interior.png">
                        </div>
                        <div>
                            <label class="form-label fw-bold">Độ phủ ảnh</label>
                            <input class="form-range" type="range" min="0" max="100" name="style[visual_overlay_opacity]" value="{{ old('style.visual_overlay_opacity', $authStyle['visual_overlay_opacity']) }}" data-auth-style="visual_overlay_opacity">
                        </div>
                        <div>
                            <label class="form-label fw-bold">Bo góc</label>
                            <input class="form-control" type="number" min="0" max="24" name="style[radius]" value="{{ old('style.radius', $authStyle['radius']) }}" data-auth-style="radius" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2">
                <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Lưu giao diện</button>
                <button class="btn btn-outline-secondary" form="authInterfaceResetForm" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i>Khôi phục mặc định</button>
                <a class="btn btn-outline-secondary" href="{{ route('login') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Xem trang</a>
            </div>
        </form>

        <form id="authInterfaceResetForm" method="POST" action="{{ route('admin.auth-interface.reset') }}">
            @csrf
        </form>

        <aside class="auth-editor-preview" data-auth-preview-card>
            <div class="auth-editor-preview-inner">
                <div class="auth-editor-preview-visual" data-auth-preview-visual>
                    <span class="auth-editor-preview-badge" data-auth-output="badge">{{ $authContent['badge'] }}</span>
                    <h3 data-auth-output="visual_title">{{ $authContent['visual_title'] }}</h3>
                    <p data-auth-output="visual_description">{{ $authContent['visual_description'] }}</p>
                </div>
                <div class="auth-editor-preview-form" data-auth-preview-form>
                    <div class="fw-bold text-uppercase small" data-auth-output="eyebrow">{{ $authContent['eyebrow'] }}</div>
                    <h3 class="fw-bold mt-2 mb-2" data-auth-output="heading">{{ $authContent['heading'] }}</h3>
                    <p class="mb-0" data-auth-output="description">{{ $authContent['description'] }}</p>
                    <div class="auth-editor-preview-tabs" data-auth-preview-tabs>
                        <div class="auth-editor-preview-tab is-active" data-auth-output="login_tab">{{ $authContent['login_tab'] }}</div>
                        <div class="auth-editor-preview-tab" data-auth-output="register_tab">{{ $authContent['register_tab'] }}</div>
                    </div>
                    <div class="auth-editor-preview-input" data-auth-preview-input></div>
                    <div class="auth-editor-preview-input" data-auth-preview-input></div>
                    <div class="auth-editor-preview-button" data-auth-preview-button data-auth-output="login_button">{{ $authContent['login_button'] }}</div>
                </div>
            </div>
        </aside>
    </div>

    @push('scripts')
        <script>
            (() => {
                const root = document.querySelector('[data-auth-editor]');
                if (!root) return;

                const styleInput = (key) => root.querySelector(`[data-auth-style="${key}"]`);
                const styleValue = (key) => styleInput(key)?.value || '';
                const preview = root.querySelector('[data-auth-preview-card]');
                const visual = root.querySelector('[data-auth-preview-visual]');
                const form = root.querySelector('[data-auth-preview-form]');
                const tabs = root.querySelector('[data-auth-preview-tabs]');
                const activeTab = root.querySelector('.auth-editor-preview-tab.is-active');
                const inactiveTab = root.querySelector('.auth-editor-preview-tab:not(.is-active)');
                const button = root.querySelector('[data-auth-preview-button]');
                const inputs = root.querySelectorAll('[data-auth-preview-input]');

                root.querySelectorAll('[data-auth-preview]').forEach((input) => {
                    input.addEventListener('input', () => {
                        const output = root.querySelector(`[data-auth-output="${input.dataset.authPreview}"]`);
                        if (output) output.textContent = input.value;
                    });
                });

                root.querySelectorAll('[data-auth-color-text]').forEach((paired) => {
                    const input = styleInput(paired.dataset.authColorText);
                    if (!input) return;
                    paired.addEventListener('input', () => {
                        input.value = paired.value;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                    input.addEventListener('input', () => {
                        paired.value = input.value;
                    });
                });

                const render = () => {
                    const radius = `${styleValue('radius') || 8}px`;
                    const opacity = Number(styleValue('visual_overlay_opacity') || 88) / 100;
                    const image = styleValue('visual_image') || 'images/restaurant-interior.png';
                    const imageUrl = image.startsWith('http') || image.startsWith('/') ? image : `{{ url('/') }}/${image.replace(/^\/+/, '')}`;

                    preview.style.background = styleValue('background_color');
                    preview.style.borderColor = styleValue('border_color');
                    preview.style.borderRadius = radius;
                    visual.style.color = styleValue('visual_text_color');
                    visual.style.backgroundImage = `linear-gradient(135deg, color-mix(in srgb, ${styleValue('visual_overlay_start')} ${opacity * 100}%, transparent), color-mix(in srgb, ${styleValue('visual_overlay_end')} ${opacity * 100}%, transparent)), url("${imageUrl}")`;
                    form.style.background = styleValue('panel_background');
                    form.style.color = styleValue('body_color');
                    root.querySelector('[data-auth-output="badge"]').style.color = styleValue('accent_color');
                    root.querySelector('[data-auth-output="eyebrow"]').style.color = styleValue('heading_color');
                    root.querySelector('[data-auth-output="heading"]').style.color = styleValue('heading_color');
                    root.querySelector('[data-auth-output="description"]').style.color = styleValue('muted_color');
                    tabs.style.background = styleValue('tab_background');
                    inactiveTab.style.color = styleValue('tab_text');
                    activeTab.style.background = styleValue('tab_active_background');
                    activeTab.style.color = styleValue('tab_active_text');
                    inputs.forEach((input) => {
                        input.style.borderColor = styleValue('input_border');
                        input.style.borderRadius = radius;
                    });
                    button.style.background = styleValue('button_background');
                    button.style.color = styleValue('button_text');
                    button.style.borderRadius = radius;
                };

                root.querySelectorAll('[data-auth-style]').forEach((input) => input.addEventListener('input', render));
                render();
            })();
        </script>
    @endpush
@endif

@if($section === 'theme-settings')
    @php
        $themeSetting ??= \App\Models\ThemeSetting::current();
        $theme = $themeSetting->resolvedSettings();
        $fontOptions = \App\Models\ThemeSetting::fontOptions();
        $colorLabels = [
            'primary_title' => 'Màu tiêu đề chính',
            'secondary_title' => 'Màu tiêu đề phụ',
            'menu_background' => 'Màu menu',
            'background' => 'Màu nền',
            'button_background' => 'Màu nút bấm',
            'button_text' => 'Màu chữ nút',
            'text' => 'Màu chữ',
            'footer_background' => 'Màu Footer',
            'button_hover_background' => 'Màu Hover của nút',
            'menu_hover' => 'Màu Hover của Menu',
        ];
        $fontLabels = [
            'heading' => 'Font chữ tiêu đề',
            'body' => 'Font chữ nội dung',
            'menu' => 'Font chữ menu',
        ];
        $sizeLabels = [
            'hero_title' => ['label' => 'Tiêu đề Hero', 'min' => 32, 'max' => 112],
            'section_title' => ['label' => 'Tiêu đề Section', 'min' => 20, 'max' => 72],
            'body' => ['label' => 'Nội dung', 'min' => 12, 'max' => 24],
            'menu' => ['label' => 'Menu', 'min' => 10, 'max' => 22],
            'button' => ['label' => 'Nút bấm', 'min' => 12, 'max' => 24],
        ];
        $spacingLabels = [
            'page_margin' => ['label' => 'Margin trang', 'min' => 0, 'max' => 80],
            'section_padding' => ['label' => 'Padding Section', 'min' => 20, 'max' => 140],
            'section_gap' => ['label' => 'Khoảng cách giữa Section', 'min' => 8, 'max' => 80],
            'title_content_gap' => ['label' => 'Khoảng cách tiêu đề - nội dung', 'min' => 4, 'max' => 56],
        ];
        $pageThemeOptions ??= \App\Models\WebsitePageSetting::PAGE_OPTIONS;
        $selectedPageTheme = request('page_theme', 'home');
        $selectedPageTheme = array_key_exists($selectedPageTheme, $pageThemeOptions) ? $selectedPageTheme : 'home';
        $selectedPageSetting = \App\Models\WebsitePageSetting::current($selectedPageTheme);
        $selectedPageThemeOverrides = $selectedPageSetting->getSetting('theme', []);
        $selectedPageThemeValues = array_replace_recursive($theme, is_array($selectedPageThemeOverrides) ? $selectedPageThemeOverrides : []);
        $selectedPageHasTheme = is_array($selectedPageThemeOverrides) && $selectedPageThemeOverrides !== [];
    @endphp

    <style>
        .theme-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(320px, .75fr);
            gap: 1rem;
            align-items: start;
        }

        .theme-field-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
        }

        .theme-color-control {
            display: grid;
            grid-template-columns: 48px 1fr;
            gap: .65rem;
            align-items: center;
        }

        .theme-color-control input[type="color"] {
            width: 48px;
            height: 42px;
            padding: .2rem;
        }

        .theme-range-row {
            display: grid;
            grid-template-columns: 1fr 82px;
            gap: .65rem;
            align-items: center;
        }

        .theme-preview {
            position: sticky;
            top: 92px;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid var(--admin-line);
            background: #fffaf0;
            box-shadow: var(--admin-shadow);
        }

        .theme-preview-nav {
            display: flex;
            gap: .75rem;
            padding: .85rem 1rem;
            background: #0e3b32;
            color: #fff;
            font-weight: 800;
        }

        .theme-preview-nav span:last-child {
            color: #f6df9d;
        }

        .theme-preview-hero {
            min-height: 250px;
            display: grid;
            align-items: end;
            padding: 2rem;
            background:
                linear-gradient(90deg, rgba(17, 10, 6, .75), rgba(14, 59, 50, .42)),
                url("{{ asset('images/hero-restaurant.png') }}") center / cover;
            color: #f6df9d;
        }

        .theme-preview-kicker {
            margin-bottom: .65rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .theme-preview-title {
            margin: 0;
            font-size: 3rem;
            font-weight: 950;
            line-height: .98;
        }

        .theme-preview-body {
            padding: 1.25rem;
        }

        .theme-preview-section-title {
            margin: 0 0 .75rem;
            font-weight: 950;
        }

        .theme-preview-button {
            display: inline-flex;
            margin-top: .75rem;
            padding: .72rem 1rem;
            border-radius: 8px;
            background: #d9a441;
            color: #2c1b12;
            font-weight: 900;
        }

        .theme-preview-footer {
            padding: 1rem 1.25rem;
            background: #0e3b32;
            color: #fff;
        }

        @media (max-width: 1199.98px) {
            .theme-grid {
                grid-template-columns: 1fr;
            }

            .theme-preview {
                position: static;
            }
        }
    </style>

    <div class="theme-grid" data-theme-form>
        <form method="POST" action="{{ route('admin.theme-settings.update') }}" class="d-grid gap-3">
            @csrf
            @method('PUT')

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Màu sắc</h2></div>
                <div class="admin-card-body">
                    <div class="theme-field-grid">
                        @foreach($colorLabels as $key => $label)
                            <div>
                                <label class="form-label fw-bold" for="color_{{ $key }}">{{ $label }}</label>
                                <div class="theme-color-control">
                                    <input class="form-control form-control-color" id="color_{{ $key }}" type="color" name="colors[{{ $key }}]" value="{{ old("colors.$key", $theme['colors'][$key]) }}" data-theme-input="colors.{{ $key }}">
                                    <input class="form-control" value="{{ old("colors.$key", $theme['colors'][$key]) }}" data-theme-color-text="colors.{{ $key }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Font chữ</h2></div>
                <div class="admin-card-body">
                    <div class="row g-3">
                        @foreach($fontLabels as $key => $label)
                            <div class="col-md-4">
                                <label class="form-label fw-bold" for="font_{{ $key }}">{{ $label }}</label>
                                <select class="form-select" id="font_{{ $key }}" name="fonts[{{ $key }}]" data-theme-input="fonts.{{ $key }}">
                                    @foreach($fontOptions as $value => $optionLabel)
                                        <option value="{{ $value }}" @selected(old("fonts.$key", $theme['fonts'][$key]) === $value)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Kích thước chữ</h2></div>
                <div class="admin-card-body">
                    <div class="theme-field-grid">
                        @foreach($sizeLabels as $key => $meta)
                            <div>
                                <label class="form-label fw-bold" for="size_{{ $key }}">{{ $meta['label'] }}</label>
                                <div class="theme-range-row">
                                    <input class="form-range" id="size_{{ $key }}" type="range" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" name="font_sizes[{{ $key }}]" value="{{ old("font_sizes.$key", $theme['font_sizes'][$key]) }}" data-theme-input="font_sizes.{{ $key }}">
                                    <input class="form-control" type="number" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" value="{{ old("font_sizes.$key", $theme['font_sizes'][$key]) }}" data-theme-number="font_sizes.{{ $key }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Banner</h2></div>
                <div class="admin-card-body">
                    <div class="theme-field-grid">
                        <div>
                            <label class="form-label fw-bold" for="banner_height">Chiều cao Banner</label>
                            <div class="theme-range-row">
                                <input class="form-range" id="banner_height" type="range" min="320" max="900" name="banner[height]" value="{{ old('banner.height', $theme['banner']['height']) }}" data-theme-input="banner.height">
                                <input class="form-control" type="number" min="320" max="900" value="{{ old('banner.height', $theme['banner']['height']) }}" data-theme-number="banner.height">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold" for="banner_overlay">Độ tối Overlay</label>
                            <div class="theme-range-row">
                                <input class="form-range" id="banner_overlay" type="range" min="0" max="95" name="banner[overlay_opacity]" value="{{ old('banner.overlay_opacity', $theme['banner']['overlay_opacity']) }}" data-theme-input="banner.overlay_opacity">
                                <input class="form-control" type="number" min="0" max="95" value="{{ old('banner.overlay_opacity', $theme['banner']['overlay_opacity']) }}" data-theme-number="banner.overlay_opacity">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold" for="banner_padding">Khoảng cách trên dưới</label>
                            <div class="theme-range-row">
                                <input class="form-range" id="banner_padding" type="range" min="24" max="160" name="banner[padding_y]" value="{{ old('banner.padding_y', $theme['banner']['padding_y']) }}" data-theme-input="banner.padding_y">
                                <input class="form-control" type="number" min="24" max="160" value="{{ old('banner.padding_y', $theme['banner']['padding_y']) }}" data-theme-number="banner.padding_y">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold" for="banner_position">Vị trí tiêu đề</label>
                            <select class="form-select" id="banner_position" name="banner[title_position]" data-theme-input="banner.title_position">
                                <option value="top" @selected(old('banner.title_position', $theme['banner']['title_position']) === 'top')>Trên</option>
                                <option value="center" @selected(old('banner.title_position', $theme['banner']['title_position']) === 'center')>Giữa</option>
                                <option value="bottom" @selected(old('banner.title_position', $theme['banner']['title_position']) === 'bottom')>Dưới</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label fw-bold" for="banner_align">Căn nội dung</label>
                            <select class="form-select" id="banner_align" name="banner[content_align]" data-theme-input="banner.content_align">
                                <option value="left" @selected(old('banner.content_align', $theme['banner']['content_align']) === 'left')>Trái</option>
                                <option value="center" @selected(old('banner.content_align', $theme['banner']['content_align']) === 'center')>Giữa</option>
                                <option value="right" @selected(old('banner.content_align', $theme['banner']['content_align']) === 'right')>Phải</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><h2 class="h5 mb-0">Button và khoảng cách</h2></div>
                <div class="admin-card-body">
                    <div class="theme-field-grid">
                        <div>
                            <label class="form-label fw-bold" for="button_radius">Bo góc nút</label>
                            <div class="theme-range-row">
                                <input class="form-range" id="button_radius" type="range" min="0" max="40" name="button[radius]" value="{{ old('button.radius', $theme['button']['radius']) }}" data-theme-input="button.radius">
                                <input class="form-control" type="number" min="0" max="40" value="{{ old('button.radius', $theme['button']['radius']) }}" data-theme-number="button.radius">
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold" for="button_effect">Hiệu ứng Hover</label>
                            <select class="form-select" id="button_effect" name="button[effect]" data-theme-input="button.effect">
                                <option value="lift" @selected(old('button.effect', $theme['button']['effect']) === 'lift')>Nâng nhẹ</option>
                                <option value="none" @selected(old('button.effect', $theme['button']['effect']) === 'none')>Không hiệu ứng</option>
                            </select>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="button[shadow]" value="1" id="button_shadow" data-theme-input="button.shadow" @checked(old('button.shadow', $theme['button']['shadow']))>
                                <label class="form-check-label" for="button_shadow">Bật đổ bóng</label>
                            </div>
                        </div>
                        @foreach($spacingLabels as $key => $meta)
                            <div>
                                <label class="form-label fw-bold" for="spacing_{{ $key }}">{{ $meta['label'] }}</label>
                                <div class="theme-range-row">
                                    <input class="form-range" id="spacing_{{ $key }}" type="range" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" name="spacing[{{ $key }}]" value="{{ old("spacing.$key", $theme['spacing'][$key]) }}" data-theme-input="spacing.{{ $key }}">
                                    <input class="form-control" type="number" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" value="{{ old("spacing.$key", $theme['spacing'][$key]) }}" data-theme-number="spacing.{{ $key }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2">
                <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Lưu cài đặt</button>
                <button class="btn btn-outline-secondary" form="themeResetForm" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i>Khôi phục mặc định</button>
            </div>
        </form>

        <form id="themeResetForm" method="POST" action="{{ route('admin.theme-settings.reset') }}">
            @csrf
        </form>

        <aside class="theme-preview" data-theme-preview>
            <div class="theme-preview-nav">
                <span>Nhà hàng Hoa Sen</span>
                <span>Thực đơn</span>
                <span>Liên hệ</span>
            </div>
            <div class="theme-preview-hero">
                <div>
                    <div class="theme-preview-kicker">Ẩm thực Việt cao cấp</div>
                    <h3 class="theme-preview-title">Nhà hàng Hoa Sen</h3>
                </div>
            </div>
            <div class="theme-preview-body">
                <h4 class="theme-preview-section-title">Không gian ấm cúng, phục vụ chỉn chu.</h4>
                <p class="mb-0">Đây là phần xem trước màu sắc, font chữ, kích thước, khoảng cách và nút bấm trước khi lưu.</p>
                <span class="theme-preview-button">Đặt bàn nhanh</span>
            </div>
            <div class="theme-preview-footer">Footer Nhà hàng Hoa Sen</div>
        </aside>
    </div>

    <div class="admin-card mt-4" id="page-theme-settings">
        <div class="admin-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h2 class="h5 mb-1">Cài đặt giao diện từng trang</h2>
                <p class="text-muted mb-0">Chọn trang cần chỉnh. Trang chưa lưu riêng sẽ tự dùng cài đặt chung.</p>
            </div>
            <span class="badge {{ $selectedPageHasTheme ? 'bg-success' : 'bg-secondary' }}">
                {{ $selectedPageHasTheme ? 'Đang dùng cài đặt riêng' : 'Đang dùng cài đặt chung' }}
            </span>
        </div>
        <div class="admin-card-body d-grid gap-3">
            <div class="d-flex flex-wrap gap-2">
                @foreach($pageThemeOptions as $pageKey => $pageLabel)
                    <a class="btn btn-sm {{ $selectedPageTheme === $pageKey ? 'btn-primary' : 'btn-outline-secondary' }}" href="{{ route('admin.section', 'theme-settings') }}?page_theme={{ $pageKey }}#page-theme-settings">
                        {{ $pageLabel }}
                    </a>
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.theme-settings.pages.update', $selectedPageTheme) }}" class="d-grid gap-3">
                @csrf
                @method('PUT')

                <div class="admin-card">
                    <div class="admin-card-header"><h3 class="h6 mb-0">Màu sắc</h3></div>
                    <div class="admin-card-body">
                        <div class="theme-field-grid">
                            @foreach($colorLabels as $key => $label)
                                <div>
                                    <label class="form-label fw-bold" for="page_color_{{ $key }}">{{ $label }}</label>
                                    <div class="theme-color-control">
                                        <input class="form-control form-control-color" id="page_color_{{ $key }}" type="color" name="colors[{{ $key }}]" value="{{ old("colors.$key", data_get($selectedPageThemeValues, "colors.$key")) }}" data-page-input="colors.{{ $key }}">
                                        <input class="form-control" value="{{ old("colors.$key", data_get($selectedPageThemeValues, "colors.$key")) }}" data-page-color-text="colors.{{ $key }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header"><h3 class="h6 mb-0">Font chữ</h3></div>
                    <div class="admin-card-body">
                        <div class="row g-3">
                            @foreach($fontLabels as $key => $label)
                                <div class="col-md-4">
                                    <label class="form-label fw-bold" for="page_font_{{ $key }}">{{ $label }}</label>
                                    <select class="form-select" id="page_font_{{ $key }}" name="fonts[{{ $key }}]">
                                        @foreach($fontOptions as $value => $optionLabel)
                                            <option value="{{ $value }}" @selected(old("fonts.$key", data_get($selectedPageThemeValues, "fonts.$key")) === $value)>{{ $optionLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header"><h3 class="h6 mb-0">Kích thước chữ</h3></div>
                    <div class="admin-card-body">
                        <div class="theme-field-grid">
                            @foreach($sizeLabels as $key => $meta)
                                <div>
                                    <label class="form-label fw-bold" for="page_size_{{ $key }}">{{ $meta['label'] }}</label>
                                    <div class="theme-range-row">
                                        <input class="form-range" id="page_size_{{ $key }}" type="range" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" name="font_sizes[{{ $key }}]" value="{{ old("font_sizes.$key", data_get($selectedPageThemeValues, "font_sizes.$key")) }}" data-page-input="font_sizes.{{ $key }}">
                                        <input class="form-control" type="number" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" value="{{ old("font_sizes.$key", data_get($selectedPageThemeValues, "font_sizes.$key")) }}" data-page-number="font_sizes.{{ $key }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header"><h3 class="h6 mb-0">Banner</h3></div>
                    <div class="admin-card-body">
                        <div class="theme-field-grid">
                            <div>
                                <label class="form-label fw-bold" for="page_banner_height">Chiều cao Banner</label>
                                <div class="theme-range-row">
                                    <input class="form-range" id="page_banner_height" type="range" min="320" max="900" name="banner[height]" value="{{ old('banner.height', data_get($selectedPageThemeValues, 'banner.height')) }}" data-page-input="banner.height">
                                    <input class="form-control" type="number" min="320" max="900" value="{{ old('banner.height', data_get($selectedPageThemeValues, 'banner.height')) }}" data-page-number="banner.height">
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold" for="page_banner_overlay">Độ tối Overlay</label>
                                <div class="theme-range-row">
                                    <input class="form-range" id="page_banner_overlay" type="range" min="0" max="95" name="banner[overlay_opacity]" value="{{ old('banner.overlay_opacity', data_get($selectedPageThemeValues, 'banner.overlay_opacity')) }}" data-page-input="banner.overlay_opacity">
                                    <input class="form-control" type="number" min="0" max="95" value="{{ old('banner.overlay_opacity', data_get($selectedPageThemeValues, 'banner.overlay_opacity')) }}" data-page-number="banner.overlay_opacity">
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold" for="page_banner_padding">Khoảng cách trên dưới</label>
                                <div class="theme-range-row">
                                    <input class="form-range" id="page_banner_padding" type="range" min="24" max="160" name="banner[padding_y]" value="{{ old('banner.padding_y', data_get($selectedPageThemeValues, 'banner.padding_y')) }}" data-page-input="banner.padding_y">
                                    <input class="form-control" type="number" min="24" max="160" value="{{ old('banner.padding_y', data_get($selectedPageThemeValues, 'banner.padding_y')) }}" data-page-number="banner.padding_y">
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold" for="page_banner_position">Vị trí tiêu đề</label>
                                <select class="form-select" id="page_banner_position" name="banner[title_position]">
                                    <option value="top" @selected(old('banner.title_position', data_get($selectedPageThemeValues, 'banner.title_position')) === 'top')>Trên</option>
                                    <option value="center" @selected(old('banner.title_position', data_get($selectedPageThemeValues, 'banner.title_position')) === 'center')>Giữa</option>
                                    <option value="bottom" @selected(old('banner.title_position', data_get($selectedPageThemeValues, 'banner.title_position')) === 'bottom')>Dưới</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label fw-bold" for="page_banner_align">Căn nội dung</label>
                                <select class="form-select" id="page_banner_align" name="banner[content_align]">
                                    <option value="left" @selected(old('banner.content_align', data_get($selectedPageThemeValues, 'banner.content_align')) === 'left')>Trái</option>
                                    <option value="center" @selected(old('banner.content_align', data_get($selectedPageThemeValues, 'banner.content_align')) === 'center')>Giữa</option>
                                    <option value="right" @selected(old('banner.content_align', data_get($selectedPageThemeValues, 'banner.content_align')) === 'right')>Phải</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header"><h3 class="h6 mb-0">Button và khoảng cách</h3></div>
                    <div class="admin-card-body">
                        <div class="theme-field-grid">
                            <div>
                                <label class="form-label fw-bold" for="page_button_radius">Bo góc nút</label>
                                <div class="theme-range-row">
                                    <input class="form-range" id="page_button_radius" type="range" min="0" max="40" name="button[radius]" value="{{ old('button.radius', data_get($selectedPageThemeValues, 'button.radius')) }}" data-page-input="button.radius">
                                    <input class="form-control" type="number" min="0" max="40" value="{{ old('button.radius', data_get($selectedPageThemeValues, 'button.radius')) }}" data-page-number="button.radius">
                                </div>
                            </div>
                            <div>
                                <label class="form-label fw-bold" for="page_button_effect">Hiệu ứng Hover</label>
                                <select class="form-select" id="page_button_effect" name="button[effect]">
                                    <option value="lift" @selected(old('button.effect', data_get($selectedPageThemeValues, 'button.effect')) === 'lift')>Nâng nhẹ</option>
                                    <option value="none" @selected(old('button.effect', data_get($selectedPageThemeValues, 'button.effect')) === 'none')>Không hiệu ứng</option>
                                </select>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="button[shadow]" value="1" id="page_button_shadow" @checked(old('button.shadow', data_get($selectedPageThemeValues, 'button.shadow')))>
                                    <label class="form-check-label" for="page_button_shadow">Bật đổ bóng</label>
                                </div>
                            </div>
                            @foreach($spacingLabels as $key => $meta)
                                <div>
                                    <label class="form-label fw-bold" for="page_spacing_{{ $key }}">{{ $meta['label'] }}</label>
                                    <div class="theme-range-row">
                                        <input class="form-range" id="page_spacing_{{ $key }}" type="range" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" name="spacing[{{ $key }}]" value="{{ old("spacing.$key", data_get($selectedPageThemeValues, "spacing.$key")) }}" data-page-input="spacing.{{ $key }}">
                                        <input class="form-control" type="number" min="{{ $meta['min'] }}" max="{{ $meta['max'] }}" value="{{ old("spacing.$key", data_get($selectedPageThemeValues, "spacing.$key")) }}" data-page-number="spacing.{{ $key }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i>Lưu giao diện trang</button>
                    <button class="btn btn-outline-secondary" form="pageThemeResetForm" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i>Dùng lại cài đặt chung</button>
                </div>
            </form>

            <form id="pageThemeResetForm" method="POST" action="{{ route('admin.theme-settings.pages.reset', $selectedPageTheme) }}">
                @csrf
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const root = document.querySelector('[data-theme-form]');
                const preview = document.querySelector('[data-theme-preview]');
                if (!root || !preview) return;

                const get = (path) => root.querySelector(`[data-theme-input="${path}"]`);
                const value = (path) => {
                    const input = get(path);
                    if (!input) return '';
                    if (input.type === 'checkbox') return input.checked;
                    return input.value;
                };
                const font = (name) => `'${name}', "Segoe UI", system-ui, sans-serif`;
                const px = (path) => `${value(path)}px`;
                const overlay = () => Number(value('banner.overlay_opacity') || 0) / 100;
                const alignItems = () => ({ top: 'start', center: 'center', bottom: 'end' }[value('banner.title_position')] || 'end');
                const justify = () => ({ left: 'flex-start', center: 'center', right: 'flex-end' }[value('banner.content_align')] || 'flex-start');

                const syncPairs = () => {
                    root.querySelectorAll('[data-theme-color-text], [data-theme-number]').forEach((paired) => {
                        const path = paired.dataset.themeColorText || paired.dataset.themeNumber;
                        const input = get(path);
                        if (!input) return;

                        paired.value = input.value;
                        paired.oninput = () => {
                            input.value = paired.value;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        };
                    });
                };

                const render = () => {
                    const nav = preview.querySelector('.theme-preview-nav');
                    const hero = preview.querySelector('.theme-preview-hero');
                    const kicker = preview.querySelector('.theme-preview-kicker');
                    const title = preview.querySelector('.theme-preview-title');
                    const body = preview.querySelector('.theme-preview-body');
                    const sectionTitle = preview.querySelector('.theme-preview-section-title');
                    const button = preview.querySelector('.theme-preview-button');
                    const footer = preview.querySelector('.theme-preview-footer');

                    preview.style.background = value('colors.background');
                    preview.style.color = value('colors.text');
                    preview.style.fontFamily = font(value('fonts.body'));
                    nav.style.background = value('colors.menu_background');
                    nav.style.fontFamily = font(value('fonts.menu'));
                    nav.style.fontSize = px('font_sizes.menu');
                    nav.querySelectorAll('span').forEach((item, index) => item.style.color = index === 1 ? value('colors.menu_hover') : '#fff');
                    hero.style.minHeight = `${Math.max(180, Number(value('banner.height')) * .45)}px`;
                    hero.style.alignItems = alignItems();
                    hero.style.justifyContent = justify();
                    hero.style.textAlign = value('banner.content_align');
                    hero.style.paddingBlock = `${Math.max(18, Number(value('banner.padding_y')) * .45)}px`;
                    hero.style.background = `linear-gradient(90deg, rgba(17, 10, 6, ${overlay()}), rgba(14, 59, 50, ${Math.max(.1, overlay() - .16)})), url("{{ asset('images/hero-restaurant.png') }}") center / cover`;
                    kicker.style.color = value('colors.secondary_title');
                    kicker.style.fontFamily = font(value('fonts.heading'));
                    title.style.color = value('colors.primary_title');
                    title.style.fontFamily = font(value('fonts.heading'));
                    title.style.fontSize = `${Math.max(30, Number(value('font_sizes.hero_title')) * .58)}px`;
                    body.style.fontSize = px('font_sizes.body');
                    body.style.padding = px('spacing.section_padding');
                    sectionTitle.style.color = value('colors.primary_title');
                    sectionTitle.style.fontFamily = font(value('fonts.heading'));
                    sectionTitle.style.fontSize = px('font_sizes.section_title');
                    sectionTitle.style.marginBottom = px('spacing.title_content_gap');
                    button.style.background = value('colors.button_background');
                    button.style.color = value('colors.button_text');
                    button.style.fontSize = px('font_sizes.button');
                    button.style.borderRadius = px('button.radius');
                    button.style.boxShadow = value('button.shadow') ? '0 14px 34px rgba(44, 27, 18, .18)' : 'none';
                    footer.style.background = value('colors.footer_background');
                };

                root.addEventListener('input', () => {
                    syncPairs();
                    render();
                });
                root.addEventListener('change', render);
                syncPairs();
                render();
            })();

            (() => {
                const pageRoot = document.querySelector('#page-theme-settings');
                if (!pageRoot) return;

                pageRoot.querySelectorAll('[data-page-color-text], [data-page-number]').forEach((paired) => {
                    const path = paired.dataset.pageColorText || paired.dataset.pageNumber;
                    const input = pageRoot.querySelector(`[data-page-input="${path}"]`);
                    if (!input) return;

                    input.addEventListener('input', () => {
                        paired.value = input.value;
                    });
                    paired.addEventListener('input', () => {
                        input.value = paired.value;
                    });
                });
            })();
        </script>
    @endpush
@endif

@if($section === 'accounts')
    @php
        $accountStatuses = ['đang hoạt động' => 'Đang hoạt động', 'tạm khóa' => 'Tạm khóa'];
    @endphp

    <div class="admin-card mb-3">
        <div class="admin-card-header"><h2 class="h5 mb-0">Thêm tài khoản</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.accounts.store') }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Họ và tên</label>
                    <input class="form-control" name="name" value="{{ old('name') }}" required maxlength="120">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" required maxlength="150">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Số điện thoại</label>
                    <input class="form-control" name="phone" value="{{ old('phone') }}" required maxlength="20">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Vai trò</label>
                    <select class="form-select" name="role_id" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected((int) old('role_id', 3) === (int) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        @foreach($accountStatuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Địa chỉ</label>
                    <input class="form-control" name="address" value="{{ old('address') }}" maxlength="255">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mật khẩu</label>
                    <input class="form-control" type="password" name="password" required autocomplete="new-password">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mã nhân viên</label>
                    <input class="form-control" name="employee_code" maxlength="30" placeholder="Tự sinh nếu trống">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Chức vụ</label>
                    <input class="form-control" name="position" maxlength="80" placeholder="Nhân viên">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ca làm việc</label>
                    <input class="form-control" name="shift" maxlength="80" placeholder="Ca linh hoạt">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lương</label>
                    <input class="form-control" type="number" name="salary" min="0" step="1000">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ngày vào làm</label>
                    <input class="form-control" type="date" name="hire_date">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-person-plus me-1"></i>Thêm tài khoản</button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card" data-admin-table>
        <div class="admin-card-header">
            <h2 class="h5 mb-0">Danh sách tài khoản</h2>
        </div>
        <div class="admin-card-body">
            <div class="admin-table-toolbar">
                <input class="form-control" data-table-search placeholder="Tìm theo tên, email, số điện thoại">
                <select class="form-select" data-table-filter data-filter-key="role">
                    <option value="">Tất cả vai trò</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <select class="form-select" data-table-filter>
                    <option value="">Tất cả trạng thái</option>
                    @foreach($accountStatuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select class="form-select" data-table-size><option>10</option><option>25</option><option>50</option></select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Mã tài khoản</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th data-no-sort>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        @php
                            $isOnlyActiveAdmin = (int) $item->role_id === 1
                                && $items->where('role_id', 1)->where('status', 'đang hoạt động')->count() <= 1;
                        @endphp
                        <tr data-role="{{ $item->role_id }}" data-status="{{ $item->status }}">
                            <td><strong>#{{ $item->id }}</strong></td>
                            <td><input class="form-control form-control-sm" form="update-account-{{ $item->id }}" name="name" value="{{ $item->name ?? $item->full_name }}" required></td>
                            <td><input class="form-control form-control-sm" form="update-account-{{ $item->id }}" type="email" name="email" value="{{ $item->email }}" required></td>
                            <td><input class="form-control form-control-sm" form="update-account-{{ $item->id }}" name="phone" value="{{ $item->phone }}" required></td>
                            <td><input class="form-control form-control-sm" form="update-account-{{ $item->id }}" name="address" value="{{ $item->address }}"></td>
                            <td>
                                <select class="form-select form-select-sm" form="update-account-{{ $item->id }}" name="role_id" @disabled($isOnlyActiveAdmin)>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected((int) $item->role_id === (int) $role->id)>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @if($isOnlyActiveAdmin)
                                    <input type="hidden" form="update-account-{{ $item->id }}" name="role_id" value="{{ $item->role_id }}">
                                    <div class="small text-danger mt-1">Admin duy nhất</div>
                                @endif
                            </td>
                            <td>
                                <select class="form-select form-select-sm" form="update-account-{{ $item->id }}" name="status" @disabled($isOnlyActiveAdmin)>
                                    @foreach($accountStatuses as $value => $label)
                                        <option value="{{ $value }}" @selected($item->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @if($isOnlyActiveAdmin)
                                    <input type="hidden" form="update-account-{{ $item->id }}" name="status" value="{{ $item->status }}">
                                @endif
                            </td>
                            <td>{{ $item->created_at?->format('d/m/Y') }}</td>
                            <td class="action-cell">
                                <form id="update-account-{{ $item->id }}" method="POST" action="{{ route('admin.accounts.update', $item) }}">
                                    @csrf
                                    @method('PUT')
                                </form>
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $accountDetail($item) }}" title="Xem chi tiết"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-primary" form="update-account-{{ $item->id }}" type="submit" title="Lưu chỉnh sửa"><i class="bi bi-save"></i></button>
                                <form method="POST" action="{{ route('admin.accounts.status', $item) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $item->status === 'tạm khóa' ? 'đang hoạt động' : 'tạm khóa' }}">
                                    <button class="btn btn-sm {{ $item->status === 'tạm khóa' ? 'btn-outline-success' : 'btn-outline-warning' }}" type="submit" @disabled($isOnlyActiveAdmin) title="{{ $item->status === 'tạm khóa' ? 'Mở khóa' : 'Khóa' }}">
                                        <i class="bi {{ $item->status === 'tạm khóa' ? 'bi-unlock' : 'bi-lock' }}"></i>
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#passwordModal{{ $item->id }}" title="Đổi mật khẩu"><i class="bi bi-key"></i></button>
                                <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#resetModal{{ $item->id }}" title="Đặt lại mật khẩu"><i class="bi bi-arrow-clockwise"></i></button>
                                <form method="POST" action="{{ route('admin.accounts.destroy', $item) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" @disabled($isOnlyActiveAdmin || auth()->id() === $item->id) onclick="return confirm('Bạn chắc chắn muốn xóa tài khoản này?')" title="Xóa"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-muted">Chưa có tài khoản.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3" data-table-pager></div>
        </div>
    </div>

    @foreach($items as $item)
        <div class="modal fade" id="passwordModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content border-0 shadow" method="POST" action="{{ route('admin.accounts.password', $item) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h2 class="modal-title h5">Đổi mật khẩu - {{ $item->name }}</h2>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Mật khẩu mới</label>
                        <input class="form-control mb-3" type="password" name="password" required minlength="8" autocomplete="new-password">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input class="form-control" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-primary" type="submit">Lưu mật khẩu</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="resetModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content border-0 shadow" method="POST" action="{{ route('admin.accounts.reset-password', $item) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="modal-title h5">Đặt lại mật khẩu - {{ $item->name }}</h2>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="soft-note mb-3">Để trống nếu muốn hệ thống sinh mật khẩu ngẫu nhiên.</div>
                        <label class="form-label">Mật khẩu mới</label>
                        <input class="form-control mb-3" type="password" name="password" minlength="8" autocomplete="new-password">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input class="form-control mb-3" type="password" name="password_confirmation" minlength="8" autocomplete="new-password">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_email" value="1" id="sendReset{{ $item->id }}">
                            <label class="form-check-label" for="sendReset{{ $item->id }}">Gửi mật khẩu mới qua Email đến {{ $item->email }}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Hủy</button>
                        <button class="btn btn-primary" type="submit">Đặt lại mật khẩu</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

@endif

@if($section === 'news')
    <div class="admin-card">
        <div class="admin-card-body">
            <div class="soft-note">Chưa có model tin tức trong hệ thống hiện tại. Giao diện đã có menu và khu vực quản lý để sẵn sàng bổ sung bài viết, chuyên mục và trạng thái xuất bản.</div>
        </div>
    </div>
@endif

@if($section === 'stats')
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
@endif

@if(! in_array($section, ['payment-methods', 'ai-chatbot', 'theme-settings', 'auth-interface', 'accounts', 'news', 'stats'], true))
    @if($section === 'tables')
        <div class="row g-3 mb-3">
            @forelse($items as $table)
                @php
                    $activeOrder = $table->activeOrders->first();
                    $currentItems = $activeOrder?->items ?? collect();
                @endphp
                <div class="col-md-6 col-xl-4">
                    <article class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <div class="admin-kicker">{{ $table->area }}</div>
                                    <h3 class="h5 fw-bold mb-1">{{ $table->table_name }}</h3>
                                    <div class="text-muted">{{ $table->seats }} ghế · {{ $table->table_code }}</div>
                                </div>
                                <form method="POST" action="{{ route('admin.tables.status', $table) }}" class="table-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <label class="visually-hidden" for="table-status-{{ $table->id }}">Trạng thái bàn</label>
                                    <select class="form-select form-select-sm" id="table-status-{{ $table->id }}" name="status">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($table->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary" type="submit" title="Cập nhật trạng thái">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="soft-note">
                                @if($currentItems->isNotEmpty())
                                    <strong class="d-block mb-2">Món đang có</strong>
                                    <div class="d-grid gap-1">
                                        @foreach($currentItems->take(4) as $orderItem)
                                            <span>{{ $orderItem->quantity }} x {{ $orderItem->product?->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    Bàn hiện chưa có món.
                                @endif
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="soft-note">Chưa có bàn ăn.</div></div>
            @endforelse
        </div>
    @endif

    <div class="admin-card" data-admin-table>
        <div class="admin-card-header">
            <h2 class="h5 mb-0">Danh sách dữ liệu</h2>
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
                                <td><span class="status-pill">{{ $statusOptions[$item->status] ?? $item->status }}</span></td>
                                <td class="action-cell">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $orderDetail($item) }}"><i class="bi bi-eye"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'reservations')
                        <thead><tr><th>Mã đặt bàn</th><th>Khách hàng</th><th>Loại khách</th><th>Bàn</th><th>Thời gian</th><th>Số khách</th><th>Trạng thái</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->status }}">
                                <td><strong>{{ $item->reservation_code }}</strong></td>
                                <td>
                                    {{ $item->customerName() ?: '-' }}
                                    <div class="small text-muted">{{ $item->customerPhone() ?: '-' }}</div>
                                </td>
                                <td><span class="status-pill">{{ $item->customer_type ?? ($item->customer_id ? 'khách thành viên' : 'khách tiềm năng') }}</span></td>
                                <td>{{ $item->table?->table_name ?? '-' }}</td>
                                <td>{{ $item->reservation_time?->format('d/m/Y H:i') }}</td>
                                <td>{{ $item->number_of_guests }}</td>
                                <td>
                                    <form id="update-reservation-{{ $item->id }}" method="POST" action="{{ route('admin.reservations.update-status', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <label class="visually-hidden" for="reservation-status-{{ $item->id }}">Trạng thái đặt bàn</label>
                                        <select class="form-select form-select-sm" id="reservation-status-{{ $item->id }}" name="status">
                                            @foreach(['chờ xác nhận' => 'Chờ xác nhận', 'đã xác nhận' => 'Đã xác nhận', 'đã hủy' => 'Đã hủy', 'hoàn thành' => 'Hoàn thành'] as $value => $label)
                                                <option value="{{ $value }}" @selected($item->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="action-cell">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $reservationDetail($item) }}"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-sm btn-primary" form="update-reservation-{{ $item->id }}" type="submit" title="Cập nhật trạng thái"><i class="bi bi-check2"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-muted">Chưa có dữ liệu.</td></tr>
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
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $customerDetail($item) }}"><i class="bi bi-eye"></i></button></td>
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
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $employeeDetail($item) }}"><i class="bi bi-eye"></i></button></td>
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
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $paymentDetail($item) }}"><i class="bi bi-eye"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">Chưa có dữ liệu.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'chatbot')
                        <thead><tr><th>Phiên</th><th>Người gửi</th><th>Tin nhắn</th><th>Ý định</th><th>Model</th><th>Token</th><th>Thời gian</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr data-status="{{ $item->intent }}">
                                <td>{{ $item->session_id }}</td><td>{{ $item->sender }}</td><td>{{ Str::limit($item->message, 80) }}</td><td><span class="status-pill">{{ $item->intent ?? 'chưa xác định' }}</span></td><td>{{ $item->model ?? '-' }}</td><td>{{ $item->total_tokens ?? '-' }}</td><td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $chatbotDetail($item) }}"><i class="bi bi-eye"></i></button></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-muted">Chưa có dữ liệu chatbot.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'menu-galleries')
                        <thead><tr><th>Tiêu đề</th><th>Mô tả</th><th>Tệp</th><th>Ngày tạo</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><strong>{{ $item->title }}</strong></td><td>{{ $item->description }}</td><td><a class="btn btn-sm btn-outline-secondary" href="{{ $item->image_url }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Xem</a></td><td>{{ $item->created_at?->format('d/m/Y') }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $menuGalleryDetail($item) }}"><i class="bi bi-eye"></i></button><form method="POST" action="{{ route('admin.menu-galleries.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Chưa có hình ảnh món ăn.</td></tr>
                        @endforelse
                        </tbody>
                    @elseif($section === 'gallery-images')
                        <thead><tr><th>Ảnh</th><th>Tiêu đề</th><th>Ngày tạo</th><th data-no-sort>Thao tác</th></tr></thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><img class="thumb" src="{{ $item->image_url }}" alt="{{ $item->title }}"></td><td><strong>{{ $item->title }}</strong></td><td>{{ $item->created_at?->format('d/m/Y') }}</td>
                                <td class="action-cell"><button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $galleryImageDetail($item) }}"><i class="bi bi-eye"></i></button><form method="POST" action="{{ route('admin.gallery-images.destroy', $item) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button></form></td>
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
