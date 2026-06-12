# Hướng dẫn truy cập giao diện

## Cách nhanh nhất bằng Laravel server

1. Mở XAMPP và bật MySQL.
2. Mở terminal tại thư mục dự án:

```powershell
cd "C:\xampp1\htdocs\Web Quản Lý Nhà Hàng"
```

3. Chạy server Laravel:

```powershell
C:\xampp1\php\php.exe artisan serve --host=127.0.0.1 --port=8000
```

4. Mở trình duyệt và truy cập:

```text
http://127.0.0.1:8000
```

## Các trang giao diện chính

- Trang chủ/menu: `http://127.0.0.1:8000`
- Đăng nhập: `http://127.0.0.1:8000/dang-nhap`
- Đăng ký khách hàng: `http://127.0.0.1:8000/dang-ky`
- Quên mật khẩu: `http://127.0.0.1:8000/quen-mat-khau`
- Dashboard Admin: `http://127.0.0.1:8000/admin/dashboard`
- Quản lý món ăn và upload ảnh món: `http://127.0.0.1:8000/admin/products`
- Quản lý Menu Nhà Hàng dạng ảnh/PDF: `http://127.0.0.1:8000/admin/menu-galleries`
- Quản lý thư viện ảnh nhà hàng: `http://127.0.0.1:8000/admin/gallery-images`
- Dashboard Nhân viên: `http://127.0.0.1:8000/nhan-vien/dashboard`
- Dashboard Khách hàng: `http://127.0.0.1:8000/khach-hang/dashboard`
- API kiểm tra dữ liệu: `http://127.0.0.1:8000/api/kiem-tra-du-lieu`

## Nếu chạy bằng Apache của XAMPP

Khi chưa cấu hình virtual host, có thể truy cập qua thư mục `public` của Laravel:

```text
http://localhost/Web%20Quản%20Lý%20Nhà%20Hàng/public
```

Nếu trình duyệt không nhận đường dẫn có dấu, nên dùng cách `artisan serve` ở trên hoặc cấu hình virtual host `nhahangworld.test`.

## Nếu chưa có dữ liệu

Tạo database `restaurant_management`, sau đó chạy:

```powershell
C:\xampp1\php\php.exe artisan migrate --seed
```

## Tài khoản mẫu

Mật khẩu chung của các tài khoản mẫu là:

```text
password
```

- Admin: `quantri@nhahangworld.vn`
- Nhân viên: `thungan@nhahangworld.vn`
- Nhân viên: `phuc.phucvu@nhahangworld.vn`
- Nhân viên: `beptruong@nhahangworld.vn`
- Khách hàng: `nguyenvanan@gmail.com`
- Khách hàng: `tranthimai@gmail.com`
- Khách hàng: `lehoangnam@gmail.com`

## Lưu ý khi dùng XAMPP virtual host

Nếu đã cấu hình virtual host trỏ vào thư mục `public`, có thể mở:

```text
http://nhahangworld.test
```

DocumentRoot cần trỏ đúng vào:

```text
C:/xampp1/htdocs/Web Quản Lý Nhà Hàng/public
```

Không mở trực tiếp file `index.html` ở thư mục gốc. Giao diện chính của dự án chạy qua Laravel tại `public/index.php`.

## Ghi chú upload ảnh

- Ảnh upload từ Admin được lưu bằng Laravel Storage trong `storage/app/public`.
- URL công khai dùng dạng `/storage/...`.
- Dự án đã có route dự phòng `/storage/{path}` để xem ảnh upload trên Windows khi symlink `public/storage` không tạo được.
- Ảnh nền và ảnh món mẫu nằm trong `public/images`.
