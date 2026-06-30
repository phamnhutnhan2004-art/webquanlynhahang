<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xác thực tài khoản - Nhà hàng Hoa Sen</title>
</head>
<body style="margin:0;background:#fffaf0;color:#221812;font-family:Arial,sans-serif;line-height:1.6;">
    <div style="max-width:620px;margin:0 auto;padding:32px 18px;">
        <div style="border:1px solid rgba(217,164,65,.35);border-radius:8px;background:#ffffff;overflow:hidden;">
            <div style="background:#0e3b32;color:#f6df9d;padding:22px 26px;">
                <div style="font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">Nhà hàng Hoa Sen</div>
                <h1 style="margin:8px 0 0;font-size:24px;line-height:1.25;color:#ffffff;">Xác thực tài khoản</h1>
            </div>

            <div style="padding:26px;">
                <p>Xin chào {{ $customerName }},</p>
                <p>Mã xác thực của bạn là:</p>

                <div style="margin:22px 0;padding:18px;border-radius:8px;background:#fff3cf;text-align:center;font-size:34px;font-weight:800;letter-spacing:8px;color:#0e3b32;">
                    {{ $otpCode }}
                </div>

                <p>Mã có hiệu lực trong 5 phút.</p>
                <p>Nếu bạn không thực hiện đăng ký tài khoản thì vui lòng bỏ qua email này.</p>
                <p style="margin-bottom:0;">Nhà hàng Hoa Sen.</p>
            </div>
        </div>
    </div>
</body>
</html>
