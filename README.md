# Website Quản lý Nhà hàng World

Đề tài: **Xây dựng Hệ thống Quản lý Nhà hàng đa nền tảng tích hợp Chatbot và Ứng dụng Android**.

Giai đoạn hiện tại: **Xây dựng nền tảng Web Laravel, thiết kế cơ sở dữ liệu, Authentication, phân quyền và Dashboard quản trị cơ bản**.

## Giới thiệu

Website Quản lý Nhà hàng World là hệ thống hỗ trợ quản lý hoạt động nhà hàng trên nền tảng web. Hệ thống tập trung vào các nghiệp vụ chính như quản lý tài khoản người dùng, phân quyền theo vai trò, quản lý danh mục, món ăn, bàn ăn, đặt bàn, đơn hàng, thanh toán và chuẩn bị dữ liệu cho chức năng chatbot hỗ trợ khách hàng.

Dự án được xây dựng theo cấu trúc Laravel 11, có thể chạy trên môi trường XAMPP để phục vụ học tập, báo cáo tiến độ và phát triển tiếp sang API/Android.

## Công nghệ sử dụng

- Laravel 11
- PHP 8.2
- MySQL/MariaDB trên XAMPP
- Bootstrap 5
- Composer
- Git/GitHub
- Charset: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`
- Ngôn ngữ giao diện và dữ liệu: tiếng Việt

## Chức năng đã xây dựng

- Trang chủ giới thiệu hệ thống.
- Đăng ký tài khoản khách hàng.
- Đăng nhập.
- Đăng xuất.
- Quên mật khẩu và đặt lại mật khẩu.
- Mã hóa mật khẩu bằng Laravel Hash.
- Phân quyền theo `role_id`.
- Middleware phân quyền:
  - `AdminMiddleware`
  - `StaffMiddleware`
  - `CustomerMiddleware`
- Dashboard Admin.
- Dashboard Nhân viên.
- Dashboard Khách hàng.
- API kiểm tra tổng quan dữ liệu.
- Migration và Seeder dữ liệu mẫu cho hệ thống nhà hàng.

## Phân quyền người dùng

Hệ thống hiện sử dụng 3 vai trò chính:

- `role_id = 1`: Admin
  - Quản lý toàn bộ hệ thống.
  - Xem dashboard quản trị.
  - Quản lý nhân viên, món ăn, danh mục, bàn ăn, đơn hàng và thống kê.

- `role_id = 2`: Nhân viên
  - Tạo đơn hàng.
  - Đặt bàn.
  - Quản lý khách hàng.
  - Xem thông tin món ăn.

- `role_id = 3`: Khách hàng
  - Xem menu.
  - Đặt bàn.
  - Đặt món.
  - Sử dụng chatbot hỗ trợ.

## Các bảng dữ liệu chính

- `roles`: Vai trò người dùng.
- `users`: Tài khoản đăng nhập.
- `customers`: Thông tin khách hàng.
- `employees`: Thông tin nhân viên.
- `tables`: Bàn ăn trong nhà hàng.
- `categories`: Danh mục món ăn.
- `foods`: Món ăn theo thiết kế ban đầu.
- `products`: Món ăn/sản phẩm dùng cho đơn hàng.
- `reservations`: Phiếu đặt bàn.
- `reservation_details`: Chi tiết món ăn đặt trước.
- `orders`: Đơn hàng.
- `order_items`: Chi tiết đơn hàng.
- `payments`: Thanh toán.
- `chatbot_histories`: Lịch sử hội thoại chatbot theo thiết kế ban đầu.
- `chatbot_logs`: Nhật ký chatbot dùng cho hệ thống hiện tại.
- `password_reset_tokens`: Token đặt lại mật khẩu.

## Quan hệ chính

- Một vai trò có nhiều người dùng.
- Một người dùng thuộc một vai trò.
- Một người dùng có thể gắn với thông tin khách hàng hoặc nhân viên.
- Một danh mục có nhiều món ăn/sản phẩm.
- Một khách hàng có nhiều phiếu đặt bàn.
- Một bàn ăn có nhiều phiếu đặt bàn theo thời gian.
- Một phiếu đặt bàn có nhiều món ăn đặt trước.
- Một phiếu đặt bàn có thể phát sinh đơn hàng.
- Một đơn hàng có nhiều chi tiết đơn hàng.
- Một phiếu đặt bàn có thông tin thanh toán.
- Một khách hàng có nhiều lịch sử trò chuyện với chatbot.

## Cách chạy dự án trên XAMPP

1. Mở XAMPP và bật Apache, MySQL.
2. Clone project vào thư mục `htdocs`.
3. Mở terminal tại thư mục project.
4. Cài thư viện PHP:

```bash
composer install
```

5. Tạo file môi trường:

```bash
copy .env.example .env
```

6. Tạo khóa ứng dụng:

```bash
php artisan key:generate
```

7. Tạo database MySQL tên:

```text
restaurant_management
```

8. Chạy migration và seeder:

```bash
php artisan migrate --seed
```

9. Chạy server Laravel:

```bash
php artisan serve
```

10. Truy cập website:

```text
http://127.0.0.1:8000
```

## Chạy bằng virtual host XAMPP

Nếu đã cấu hình virtual host trỏ vào thư mục `public`, có thể truy cập:

```text
http://nhahangworld.test
```

DocumentRoot cần trỏ về:

```text
C:/xampp1/htdocs/Web Quản Lý Nhà Hàng/public
```

## Import database bằng phpMyAdmin

Có thể import trực tiếp file SQL:

1. Mở `http://localhost/phpmyadmin`.
2. Tạo hoặc chọn database `restaurant_management`.
3. Chọn tab **Import**.
4. Chọn file `database-schema.sql`.
5. Bấm **Go**.

## Kiểm tra route và dữ liệu

Xem danh sách route:

```bash
php artisan route:list
```

Kiểm tra API tổng quan dữ liệu:

```text
GET http://127.0.0.1:8000/api/kiem-tra-du-lieu
```

Hoặc khi dùng virtual host:

```text
GET http://nhahangworld.test/api/kiem-tra-du-lieu
```

## Tài khoản mẫu

Mật khẩu mẫu sau khi chạy seeder:

```text
password
```

- `quantri@nhahangworld.vn` - Admin
- `thungan@nhahangworld.vn` - Nhân viên
- `phuc.phucvu@nhahangworld.vn` - Nhân viên
- `beptruong@nhahangworld.vn` - Nhân viên
- `nguyenvanan@gmail.com` - Khách hàng
- `tranthimai@gmail.com` - Khách hàng
- `lehoangnam@gmail.com` - Khách hàng

## Cấu trúc source code

- `app/Models`: Model dữ liệu.
- `app/Http/Controllers`: Controller xử lý nghiệp vụ.
- `app/Http/Middleware`: Middleware phân quyền.
- `database/migrations`: Migration tạo bảng.
- `database/seeders`: Seeder dữ liệu mẫu.
- `routes/web.php`: Route giao diện web.
- `routes/api.php`: Route API.
- `resources/views`: Giao diện Blade.
- `public/.htaccess`: Cấu hình rewrite cho Apache/XAMPP.

## Lộ trình phát triển tiếp theo

1. Hoàn thiện CRUD quản lý món ăn.
2. Hoàn thiện CRUD quản lý danh mục.
3. Hoàn thiện CRUD quản lý bàn ăn.
4. Hoàn thiện quản lý đơn hàng và chi tiết đơn hàng.
5. Xây dựng thống kê doanh thu.
6. Xây dựng REST API cho ứng dụng Android.
7. Tích hợp chatbot hỗ trợ khách hàng.
8. Phát triển ứng dụng Android kết nối với backend Laravel.
