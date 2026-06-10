# Hệ thống Quản lý Nhà hàng đa nền tảng

Đề tài: **Xây dựng Hệ thống Quản lý Nhà hàng đa nền tảng tích hợp Chatbot và Ứng dụng Android**.

Giai đoạn hiện tại: **Giai đoạn 2 - Thiết kế Backend và Database trước, chưa tập trung giao diện**.

## Công nghệ

- Laravel 11
- MySQL/MariaDB trên XAMPP
- Charset: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`
- Ngôn ngữ dữ liệu và nội dung: tiếng Việt có dấu

## Cách 1: Import trực tiếp bằng phpMyAdmin

1. Mở XAMPP và bật Apache, MySQL.
2. Vào `http://localhost/phpmyadmin`.
3. Chọn tab **Import**.
4. Chọn file `database-schema.sql`.
5. Bấm **Go**.

File SQL sẽ tự tạo database `restaurant_management`, tạo bảng, khóa chính, khóa ngoại và dữ liệu mẫu.

## Cách 2: Chạy bằng Laravel

Máy hiện tại chưa có `php` và `composer`, vì vậy chưa chạy lệnh được trong môi trường này. Khi cài đủ PHP và Composer, chạy:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Kiểm tra dữ liệu:

```text
GET http://127.0.0.1:8000/api/kiem-tra-du-lieu
```

## Các bảng đã thiết kế

- `roles`: Vai trò người dùng.
- `users`: Tài khoản đăng nhập cho quản trị viên, thu ngân, phục vụ, bếp và khách hàng.
- `customers`: Thông tin khách hàng.
- `employees`: Thông tin nhân viên.
- `tables`: Bàn ăn trong nhà hàng.
- `categories`: Danh mục món ăn.
- `foods`: Món ăn.
- `reservations`: Phiếu đặt bàn.
- `reservation_details`: Chi tiết món ăn đặt trước.
- `payments`: Thanh toán.
- `chatbot_histories`: Lịch sử hội thoại chatbot.

## Quan hệ chính

- Một vai trò có nhiều người dùng.
- Một người dùng có thể là khách hàng hoặc nhân viên.
- Một danh mục có nhiều món ăn.
- Một khách hàng có nhiều phiếu đặt bàn.
- Một bàn ăn có nhiều phiếu đặt bàn theo thời gian.
- Một phiếu đặt bàn có nhiều món ăn đặt trước.
- Một phiếu đặt bàn có một thanh toán.
- Một khách hàng có nhiều lịch sử chatbot.

## Tài khoản mẫu

Mật khẩu mẫu của tất cả tài khoản Laravel seeder: `12345678`.

- `quantri@nhahangworld.vn` - Quản trị viên
- `thungan@nhahangworld.vn` - Thu ngân
- `phuc.phucvu@nhahangworld.vn` - Nhân viên phục vụ
- `beptruong@nhahangworld.vn` - Nhân viên bếp
- `nguyenvanan@gmail.com` - Khách hàng

## Lộ trình tiếp theo

1. Hoàn thiện và kiểm tra Database.
2. Xây dựng Authentication: đăng ký, đăng nhập, đăng xuất, quên mật khẩu.
3. Xây dựng REST API cho món ăn, danh mục, đặt bàn, lịch sử đặt bàn, thanh toán và chatbot.
4. Sau cùng mới hoàn thiện giao diện web và kết nối Android.
