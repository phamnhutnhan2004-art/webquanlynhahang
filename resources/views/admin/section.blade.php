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
        'payment-methods' => 'Quản lý phương thức thanh toán',
        'chatbot' => 'Quản lý Chatbot',
        'ai-chatbot' => 'Cấu hình AI Chatbot',
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
        'payment-methods' => 'Cấu hình tiền mặt, chuyển khoản, mã QR và ví điện tử cho trang thanh toán.',
        'chatbot' => 'Xem lịch sử hội thoại, ý định xử lý và phiên tương tác.',
        'ai-chatbot' => 'Quản lý Gemini API Key, trạng thái AI và prompt hệ thống của trợ lý nhà hàng.',
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
        'tables' => ['trống' => 'Trống', 'đã đặt' => 'Đã đặt', 'đang sử dụng' => 'Đang sử dụng', 'đang dọn dẹp' => 'Đang dọn dẹp'],
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
    $paymentMethodDetail = fn ($item) => $detail(['Phương thức' => $item->display_name, 'Loại' => $item->methodLabel(), 'Ngân hàng' => $item->bank_name, 'Chủ tài khoản' => $item->account_holder, 'Số tài khoản' => $item->account_number, 'Nội dung mặc định' => $item->transfer_content_template, 'Trạng thái' => $item->is_active ? 'Đang bật' : 'Đang tắt']);
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
                        <tr data-status="{{ $item->is_active ? 'active' : 'inactive' }}">
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
@elseif($section === 'ai-chatbot')
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
@elseif($section === 'settings')
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
                                <span class="status-pill">{{ $statusOptions[$table->status] ?? $table->status }}</span>
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
                                <td><span class="status-pill">{{ $item->status }}</span></td>
                                <td class="action-cell">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-detail="{{ $orderDetail($item) }}"><i class="bi bi-eye"></i></button>
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
                            <tr><td colspan="5" class="text-muted">Chưa có menu hình ảnh.</td></tr>
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
