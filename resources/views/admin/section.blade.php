@extends('layouts.app')

@php
    $titles = [
        'employees' => 'Nhân viên',
        'products' => 'Món ăn',
        'categories' => 'Danh mục',
        'tables' => 'Bàn ăn',
        'orders' => 'Đơn hàng',
        'home-parties' => 'Đặt tiệc tại nhà',
        'menu-galleries' => 'Menu hình ảnh',
        'gallery-images' => 'Thư viện ảnh',
        'stats' => 'Thống kê',
    ];
@endphp

@section('title', 'Quản lý ' . ($titles[$section] ?? $section))

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="eyebrow mb-2">Khu vực Admin</div>
            <h1 class="h3 mb-1">Quản lý {{ $titles[$section] ?? $section }}</h1>
            <p class="text-muted mb-0">Xem dữ liệu và thao tác các phần chính của hệ thống.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}">Về dashboard</a>
    </div>

    @if($section === 'categories')
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Thêm danh mục</h2>
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
                        <button class="btn btn-primary" type="submit">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($section === 'products')
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Thêm món ăn</h2>
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Tên món</label>
                        <input class="form-control" name="name" required maxlength="150">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Danh mục</label>
                        <select class="form-select" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Giá</label>
                        <input class="form-control" type="number" name="price" min="0" step="1000" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="available">Đang bán</option>
                            <option value="out_of_stock">Tạm hết</option>
                            <option value="inactive">Ẩn</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ảnh món</label>
                        <input class="form-control" type="file" name="image" accept="image/png,image/jpeg">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="col-12 d-grid d-md-flex justify-content-md-end">
                        <button class="btn btn-primary" type="submit">Thêm món</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($section === 'tables')
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Thêm bàn ăn</h2>
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
                    <div class="col-12 d-grid d-md-flex justify-content-md-end">
                        <button class="btn btn-primary" type="submit">Thêm bàn</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($section === 'menu-galleries')
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Tải lên Menu Nhà Hàng</h2>
                <form method="POST" action="{{ route('admin.menu-galleries.store') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Tiêu đề menu</label>
                        <input class="form-control" name="title" required maxlength="150" placeholder="Menu Hải sản">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Mô tả</label>
                        <input class="form-control" name="description" maxlength="255">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ảnh/PDF menu</label>
                        <input class="form-control" type="file" name="image" accept="image/png,image/jpeg,application/pdf" required>
                    </div>
                    <div class="col-md-1 d-grid align-items-end">
                        <button class="btn btn-primary" type="submit">Tải</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($section === 'gallery-images')
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Tải lên ảnh nhà hàng</h2>
                <form method="POST" action="{{ route('admin.gallery-images.store') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label">Tiêu đề ảnh</label>
                        <input class="form-control" name="title" required maxlength="150">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Ảnh JPG/PNG</label>
                        <input class="form-control" type="file" name="image" accept="image/png,image/jpeg" required>
                    </div>
                    <div class="col-md-2 d-grid align-items-end">
                        <button class="btn btn-primary" type="submit">Tải ảnh</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($section === 'products')
        <div class="row g-4">
            @forelse($items as $item)
                <div class="col-lg-6 col-xl-4">
                    <article class="card h-100 food-card">
                        <img class="food-img" src="{{ $item->image_url }}" alt="{{ $item->name }}">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.products.update', $item) }}" enctype="multipart/form-data" class="row g-2">
                                @csrf
                                @method('PUT')
                                <div class="col-12">
                                    <label class="form-label">Tên món</label>
                                    <input class="form-control" name="name" value="{{ $item->name }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Danh mục</label>
                                    <select class="form-select" name="category_id">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected($item->category_id === $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Giá</label>
                                    <input class="form-control" type="number" name="price" min="0" step="1000" value="{{ (float) $item->price }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select" name="status">
                                        <option value="available" @selected($item->status === 'available')>Đang bán</option>
                                        <option value="out_of_stock" @selected($item->status === 'out_of_stock')>Tạm hết</option>
                                        <option value="inactive" @selected($item->status === 'inactive')>Ẩn</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Đổi ảnh</label>
                                    <input class="form-control" type="file" name="image" accept="image/png,image/jpeg">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mô tả</label>
                                    <textarea class="form-control" name="description" rows="3">{{ $item->description }}</textarea>
                                </div>
                                <div class="col-12 d-grid">
                                    <button class="btn btn-primary" type="submit">Lưu món ăn</button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.products.destroy', $item) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger w-100" type="submit">Xóa món</button>
                            </form>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có dữ liệu.</div></div>
            @endforelse
        </div>
    @elseif($section === 'menu-galleries')
        <div class="row g-4">
            @forelse($items as $item)
                <div class="col-md-6 col-xl-4">
                    <article class="card media-card h-100">
                        @if(str_ends_with(strtolower($item->image), '.pdf'))
                            <div class="card-body">
                                <h3 class="h5">{{ $item->title }}</h3>
                                <p class="text-muted">{{ $item->description }}</p>
                                <a class="btn btn-outline-primary" href="{{ $item->image_url }}" target="_blank">Xem PDF</a>
                            </div>
                        @else
                            <img class="media-img" src="{{ $item->image_url }}" alt="{{ $item->title }}">
                            <div class="card-body">
                                <h3 class="h5">{{ $item->title }}</h3>
                                <p class="text-muted">{{ $item->description }}</p>
                            </div>
                        @endif
                        <div class="card-footer bg-white border-0">
                            <form method="POST" action="{{ route('admin.menu-galleries.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger w-100" type="submit">Xóa menu</button>
                            </form>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có menu hình ảnh.</div></div>
            @endforelse
        </div>
    @elseif($section === 'gallery-images')
        <div class="row g-4">
            @forelse($items as $item)
                <div class="col-md-6 col-xl-4">
                    <article class="card media-card h-100">
                        <img class="gallery-img" src="{{ $item->image_url }}" alt="{{ $item->title }}">
                        <div class="card-body">
                            <h3 class="h5">{{ $item->title }}</h3>
                            <form method="POST" action="{{ route('admin.gallery-images.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger w-100" type="submit">Xóa ảnh</button>
                            </form>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có ảnh nhà hàng.</div></div>
            @endforelse
        </div>
    @elseif($section === 'stats')
        <div class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">Doanh thu theo ngày</h2>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Ngày</th><th>Doanh thu</th></tr></thead>
                        <tbody>
                        @forelse($revenueByDay as $row)
                            <tr><td>{{ $row->date }}</td><td>{{ number_format((float) $row->revenue) }} VNĐ</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">Chưa có dữ liệu thống kê.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($section === 'home-parties')
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Khách hàng</th>
                                <th>Lịch tổ chức</th>
                                <th>Loại tiệc</th>
                                <th>Thực đơn</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái / Nhân viên</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->full_name }}</strong>
                                    <div class="small text-muted">{{ $item->phone }}</div>
                                    <div class="small text-muted">{{ $item->email ?: 'Chưa có email' }}</div>
                                    <div class="small">{{ $item->address }}</div>
                                </td>
                                <td>
                                    <strong>{{ $item->event_date?->format('d/m/Y') }}</strong>
                                    <div class="small text-muted">{{ $item->event_time?->format('H:i') }}</div>
                                    <div class="small">{{ $item->guest_quantity }} khách</div>
                                </td>
                                <td>
                                    <span class="status-badge">{{ $item->party_type }}</span>
                                    @if($item->note)
                                        <div class="small text-muted mt-2">{{ $item->note }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-grid gap-1">
                                        @forelse($item->details as $detail)
                                            <div class="small">
                                                <strong>{{ $detail->food?->name ?? 'Món đã xóa' }}</strong>
                                                x {{ $detail->quantity }}
                                                <span class="text-muted">({{ number_format((float) $detail->subtotal) }} VNĐ)</span>
                                            </div>
                                        @empty
                                            <span class="text-muted">Chưa có món.</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td><strong class="gold-text">{{ number_format((float) $item->total_price) }} VNĐ</strong></td>
                                <td style="min-width: 260px;">
                                    <form method="POST" action="{{ route('admin.home-parties.update', $item) }}" class="d-grid gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select class="form-select" name="status" aria-label="Trạng thái đơn đặt tiệc">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-select" name="assigned_employee_id" aria-label="Nhân viên phụ trách">
                                            <option value="">Chưa phân công</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" @selected($item->assigned_employee_id === $employee->id)>
                                                    {{ $employee->employee_code }} - {{ $employee->user?->name ?? $employee->user?->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary btn-sm" type="submit">Cập nhật</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">Chưa có đơn đặt tiệc tại nhà.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        @if($section === 'employees')
                            <thead class="table-light"><tr><th>Mã NV</th><th>Họ tên</th><th>Vị trí</th><th>Ca làm</th><th>Lương</th></tr></thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr><td>{{ $item->employee_code }}</td><td>{{ $item->user?->name ?? $item->user?->full_name }}</td><td>{{ $item->position }}</td><td>{{ $item->shift }}</td><td>{{ number_format((float) $item->salary) }} VNĐ</td></tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">Chưa có dữ liệu.</td></tr>
                            @endforelse
                            </tbody>
                        @elseif($section === 'categories')
                            <thead class="table-light"><tr><th>Tên danh mục</th><th>Mô tả</th><th>Số món</th><th>Trạng thái</th></tr></thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr><td><strong>{{ $item->name }}</strong></td><td>{{ $item->description }}</td><td>{{ $item->products_count }}</td><td><span class="status-badge">{{ $item->status }}</span></td></tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">Chưa có dữ liệu.</td></tr>
                            @endforelse
                            </tbody>
                        @elseif($section === 'tables')
                            <thead class="table-light"><tr><th>Mã bàn</th><th>Tên bàn</th><th>Khu vực</th><th>Số ghế</th><th>Trạng thái</th></tr></thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr><td><strong>{{ $item->table_code }}</strong></td><td>{{ $item->table_name }}</td><td>{{ $item->area }}</td><td>{{ $item->seats }}</td><td><span class="status-badge">{{ $item->status }}</span></td></tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">Chưa có dữ liệu.</td></tr>
                            @endforelse
                            </tbody>
                        @elseif($section === 'orders')
                            <thead class="table-light"><tr><th>Mã đơn</th><th>Khách hàng</th><th>Bàn</th><th>Số món</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr><td><strong>{{ $item->order_code }}</strong></td><td>{{ $item->customer?->full_name }}</td><td>{{ $item->table?->table_name }}</td><td>{{ $item->items->count() }}</td><td>{{ number_format((float) $item->total_amount) }} VNĐ</td><td><span class="status-badge">{{ $item->status }}</span></td></tr>
                            @empty
                                <tr><td colspan="6" class="text-muted">Chưa có dữ liệu.</td></tr>
                            @endforelse
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
