@extends('layouts.app')

@section('title', 'Khách hàng')

@section('content')
<div class="container">
    <section class="page-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="eyebrow mb-2">Khu vực khách hàng</div>
                <h1 class="display-6 fw-bold mb-3">Đặt bàn nhanh và xem menu trực quan.</h1>
                <p class="lead mb-0">Chọn thời gian, số khách và bàn còn trống. Nhân viên sẽ xác nhận yêu cầu ngay trên hệ thống.</p>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Gửi yêu cầu đặt bàn</h2>
                        <form method="POST" action="{{ route('customer.reservations.store') }}" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Thời gian</label>
                                <input class="form-control" type="datetime-local" name="reservation_time" min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số khách</label>
                                <input class="form-control" type="number" name="number_of_guests" min="1" max="30" value="2" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Bàn còn trống</label>
                                <select class="form-select" name="table_id">
                                    <option value="">Để nhân viên sắp xếp</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}">{{ $table->table_name }} - {{ $table->area }} - {{ $table->seats }} ghế</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <input class="form-control" name="note" maxlength="255" placeholder="Ví dụ: cần ghế trẻ em, ưu tiên gần cửa sổ">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Gửi yêu cầu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <section class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <div class="section-title">
                        <div>
                            <div class="eyebrow">Lịch đặt bàn</div>
                            <h2 class="h5 mb-0">Yêu cầu của bạn</h2>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Mã</th><th>Thời gian</th><th>Trạng thái</th></tr></thead>
                            <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>
                                        <strong>{{ $reservation->reservation_code }}</strong>
                                        <div class="small text-muted">{{ $reservation->table?->table_name ?? 'Chưa chọn bàn' }}</div>
                                    </td>
                                    <td>{{ optional($reservation->reservation_time)->format('d/m/Y H:i') }}</td>
                                    <td><span class="status-badge">{{ $reservation->status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Bạn chưa có lịch đặt bàn.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="col-lg-7">
            <div class="section-title">
                <div>
                    <div class="eyebrow">Thực đơn</div>
                    <h2 class="h5 mb-0">Menu đang bán</h2>
                </div>
            </div>
            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-md-6">
                        <div class="card food-card h-100">
                            <img class="food-img" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            <div class="card-body d-flex flex-column">
                                <div class="small text-muted">{{ $product->category?->name }}</div>
                                <h3 class="h5 mt-2">{{ $product->name }}</h3>
                                <p class="text-muted flex-grow-1">{{ $product->description }}</p>
                                <strong class="gold-text">{{ number_format((float) $product->price) }} VNĐ</strong>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><div class="muted-box">Chưa có menu.</div></div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="section-pad pb-0">
        <div class="section-title">
            <div>
                <div class="eyebrow">Thanh toán</div>
                <h2 class="h4 mb-0">Đơn hàng của bạn</h2>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Bàn</th>
                            <th>Số món</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($orders ?? [] as $order)
                        <tr>
                            <td><strong>{{ $order->order_code }}</strong></td>
                            <td>{{ $order->table?->table_name ?? 'Khách mang đi' }}</td>
                            <td>{{ $order->items->sum('quantity') }}</td>
                            <td class="fw-bold">{{ number_format((float) $order->total_amount, 0, ',', '.') }} VNĐ</td>
                            <td><span class="status-badge">{{ $order->bill ? 'Đã thanh toán' : 'Chờ thanh toán' }}</span></td>
                            <td class="text-end">
                                @if($order->bill)
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('customer.bills.show', $order->bill) }}">
                                        <i class="bi bi-printer" aria-hidden="true"></i> Xem hóa đơn
                                    </a>
                                @else
                                    <a class="btn btn-primary btn-sm" href="{{ route('customer.orders.checkout', $order) }}">
                                        <i class="bi bi-credit-card" aria-hidden="true"></i> Thanh toán
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">Bạn chưa có đơn hàng cần thanh toán.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="section-pad">
        <div class="section-title">
            <div>
                <div class="eyebrow">Menu Nhà Hàng</div>
                <h2 class="h4 mb-0">Hình ảnh món ăn</h2>
            </div>
        </div>
        <div class="row g-3">
            @forelse($menuGalleries as $menu)
                <div class="col-md-6 col-xl-3">
                    <div class="card media-card h-100">
                        @if(str_ends_with(strtolower($menu->image), '.pdf'))
                            <div class="card-body">
                                <h3 class="h5">{{ $menu->title }}</h3>
                                <p class="text-muted">{{ $menu->description }}</p>
                                <a class="btn btn-outline-primary" href="{{ $menu->image_url }}" target="_blank">Xem PDF</a>
                            </div>
                        @else
                            <img class="media-img" src="{{ $menu->image_url }}" alt="{{ $menu->title }}">
                            <div class="card-body">
                                <h3 class="h5">{{ $menu->title }}</h3>
                                <p class="text-muted mb-0">{{ $menu->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có hình ảnh món ăn.</div></div>
            @endforelse
        </div>
    </section>

    <section class="section-pad pt-0">
        <div class="section-title">
            <div>
                <div class="eyebrow">Hình ảnh</div>
                <h2 class="h4 mb-0">Không gian nhà hàng</h2>
            </div>
        </div>
        <div class="row g-3">
            @forelse($galleryImages as $image)
                <div class="col-md-6 col-xl-3">
                    <div class="card media-card">
                        <img class="gallery-img" src="{{ $image->image_url }}" alt="{{ $image->title }}">
                        <div class="card-body"><strong>{{ $image->title }}</strong></div>
                    </div>
                </div>
            @empty
                <div class="col-12"><div class="muted-box">Chưa có ảnh nhà hàng.</div></div>
            @endforelse
        </div>
    </section>
</div>
@endsection
