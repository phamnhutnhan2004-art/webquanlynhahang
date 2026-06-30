<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xác nhận đặt bàn - Nhà hàng Hoa Sen</title>
</head>
<body style="margin:0;background:#f6efe0;font-family:Arial,sans-serif;color:#221812;">
    <div style="max-width:620px;margin:0 auto;padding:28px 16px;">
        <div style="background:#fffaf0;border:1px solid #ead8b8;border-radius:8px;padding:24px;">
            <div style="font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#0e3b32;">Nhà hàng Hoa Sen</div>
            <h1 style="margin:10px 0 12px;font-size:26px;line-height:1.2;">Đặt bàn thành công</h1>
            <p style="margin:0 0 18px;">Cảm ơn {{ $reservation->customerName() ?: 'quý khách' }} đã gửi yêu cầu đặt bàn. Nhân viên sẽ kiểm tra và xác nhận sớm.</p>

            <table style="width:100%;border-collapse:collapse;font-size:15px;">
                <tr>
                    <td style="padding:8px 0;color:#756a5e;">Mã đặt bàn</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700;">{{ $reservation->reservation_code }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#756a5e;">Thời gian</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700;">{{ $reservation->reservation_time?->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#756a5e;">Số khách</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700;">{{ $reservation->number_of_guests }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#756a5e;">Bàn</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700;">{{ $reservation->table?->table_name ?? 'Nhân viên sắp xếp' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#756a5e;">Trạng thái</td>
                    <td style="padding:8px 0;text-align:right;font-weight:700;">{{ $reservation->status }}</td>
                </tr>
            </table>

            <p style="margin:18px 0 0;color:#756a5e;">Hotline hỗ trợ: <strong>0789661781</strong></p>
        </div>
    </div>
</body>
</html>
