-- Cơ sở dữ liệu: Hệ thống Quản lý Nhà hàng đa nền tảng
-- Tương thích XAMPP: MariaDB/MySQL, import trực tiếp bằng phpMyAdmin.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `restaurant_management`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `restaurant_management`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `chatbot_logs`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `chatbot_histories`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `reservation_details`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `foods`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `tables`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address` VARCHAR(255) NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL,
  `status` ENUM('đang hoạt động','tạm khóa') NOT NULL DEFAULT 'đang hoạt động',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) NULL,
  `address` VARCHAR(255) NULL,
  `note` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_phone_unique` (`phone`),
  KEY `customers_user_id_foreign` (`user_id`),
  CONSTRAINT `customers_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `employees` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `employee_code` VARCHAR(30) NOT NULL,
  `position` VARCHAR(80) NOT NULL,
  `shift` VARCHAR(80) NOT NULL,
  `salary` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `hire_date` DATE NOT NULL,
  `status` ENUM('đang làm','tạm nghỉ','đã nghỉ') NOT NULL DEFAULT 'đang làm',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_user_id_unique` (`user_id`),
  UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  CONSTRAINT `employees_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tables` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `table_code` VARCHAR(30) NOT NULL,
  `table_name` VARCHAR(80) NOT NULL,
  `area` VARCHAR(80) NOT NULL,
  `seats` TINYINT UNSIGNED NOT NULL,
  `status` ENUM('trống','đang phục vụ','đã đặt','bảo trì') NOT NULL DEFAULT 'trống',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tables_table_code_unique` (`table_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `status` ENUM('hiển thị','ẩn') NOT NULL DEFAULT 'hiển thị',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `foods` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `food_name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `image` VARCHAR(255) NULL,
  `spicy_level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `preparation_time` TINYINT UNSIGNED NOT NULL DEFAULT 10,
  `status` ENUM('đang bán','tạm hết','ngừng bán') NOT NULL DEFAULT 'đang bán',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `foods_slug_unique` (`slug`),
  KEY `foods_category_id_foreign` (`category_id`),
  CONSTRAINT `foods_category_id_foreign`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `image` VARCHAR(255) NULL,
  `status` ENUM('available','out_of_stock','inactive') NOT NULL DEFAULT 'available',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `table_id` BIGINT UNSIGNED NULL,
  `employee_id` BIGINT UNSIGNED NULL,
  `reservation_code` VARCHAR(40) NOT NULL,
  `reservation_time` DATETIME NOT NULL,
  `number_of_guests` TINYINT UNSIGNED NOT NULL,
  `note` VARCHAR(255) NULL,
  `source` ENUM('website','android','chatbot','điện thoại','trực tiếp') NOT NULL DEFAULT 'website',
  `status` ENUM('chờ xác nhận','đã xác nhận','đã hủy','hoàn thành') NOT NULL DEFAULT 'chờ xác nhận',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservations_reservation_code_unique` (`reservation_code`),
  KEY `reservations_customer_id_foreign` (`customer_id`),
  KEY `reservations_table_id_foreign` (`table_id`),
  KEY `reservations_employee_id_foreign` (`employee_id`),
  CONSTRAINT `reservations_customer_id_foreign`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `reservations_table_id_foreign`
    FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `reservations_employee_id_foreign`
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` BIGINT UNSIGNED NULL,
  `customer_id` BIGINT UNSIGNED NULL,
  `table_id` BIGINT UNSIGNED NULL,
  `employee_id` BIGINT UNSIGNED NULL,
  `order_code` VARCHAR(40) NOT NULL,
  `status` ENUM('pending','serving','completed','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `service_fee` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `vat` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `ordered_at` DATETIME NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_reservation_id_unique` (`reservation_id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_table_id_foreign` (`table_id`),
  KEY `orders_employee_id_foreign` (`employee_id`),
  CONSTRAINT `orders_reservation_id_foreign`
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `orders_customer_id_foreign`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `orders_table_id_foreign`
    FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `orders_employee_id_foreign`
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservation_details` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` BIGINT UNSIGNED NOT NULL,
  `food_id` BIGINT UNSIGNED NOT NULL,
  `quantity` SMALLINT UNSIGNED NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `note` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservation_details_reservation_id_foreign` (`reservation_id`),
  KEY `reservation_details_food_id_foreign` (`food_id`),
  CONSTRAINT `reservation_details_reservation_id_foreign`
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `reservation_details_food_id_foreign`
    FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` SMALLINT UNSIGNED NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `total_price` DECIMAL(12,2) NOT NULL,
  `note` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reservation_id` BIGINT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NULL,
  `payment_code` VARCHAR(40) NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `discount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `service_fee` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `vat` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `payment_method` ENUM('tiền mặt','chuyển khoản','thẻ ngân hàng','ví điện tử') NOT NULL DEFAULT 'tiền mặt',
  `payment_status` ENUM('chưa thanh toán','đã thanh toán','đã hoàn tiền') NOT NULL DEFAULT 'chưa thanh toán',
  `paid_at` DATETIME NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_payment_code_unique` (`payment_code`),
  KEY `payments_reservation_id_foreign` (`reservation_id`),
  KEY `payments_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payments_reservation_id_foreign`
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `payments_employee_id_foreign`
    FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chatbot_histories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` BIGINT UNSIGNED NULL,
  `reservation_id` BIGINT UNSIGNED NULL,
  `session_id` VARCHAR(100) NOT NULL,
  `sender` ENUM('khách hàng','chatbot','nhân viên') NOT NULL,
  `message` TEXT NOT NULL,
  `intent` VARCHAR(100) NULL,
  `confidence` DECIMAL(5,2) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_histories_customer_id_foreign` (`customer_id`),
  KEY `chatbot_histories_reservation_id_foreign` (`reservation_id`),
  KEY `chatbot_histories_session_id_index` (`session_id`),
  CONSTRAINT `chatbot_histories_customer_id_foreign`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `chatbot_histories_reservation_id_foreign`
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chatbot_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` BIGINT UNSIGNED NULL,
  `reservation_id` BIGINT UNSIGNED NULL,
  `order_id` BIGINT UNSIGNED NULL,
  `session_id` VARCHAR(100) NOT NULL,
  `sender` VARCHAR(40) NOT NULL,
  `message` TEXT NOT NULL,
  `intent` VARCHAR(100) NULL,
  `confidence` DECIMAL(5,2) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_logs_customer_id_foreign` (`customer_id`),
  KEY `chatbot_logs_reservation_id_foreign` (`reservation_id`),
  KEY `chatbot_logs_order_id_foreign` (`order_id`),
  KEY `chatbot_logs_session_id_index` (`session_id`),
  CONSTRAINT `chatbot_logs_customer_id_foreign`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `chatbot_logs_reservation_id_foreign`
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `chatbot_logs_order_id_foreign`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Toàn quyền hệ thống', NOW(), NOW()),
(2, 'Nhân viên', 'Tạo đơn hàng, đặt bàn, quản lý khách hàng và xem món ăn', NOW(), NOW()),
(3, 'Khách hàng', 'Xem menu, đặt bàn, đặt món và sử dụng chatbot', NOW(), NOW());

INSERT INTO `users` (`id`, `name`, `role_id`, `full_name`, `email`, `phone`, `address`, `password`, `remember_token`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Quốc Huy', 1, 'Nguyễn Quốc Huy', 'quantri@nhahangworld.vn', '0901000001', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'đang hoạt động', NOW(), NOW()),
(2, 'Phạm Thị Thu Ngân', 2, 'Phạm Thị Thu Ngân', 'thungan@nhahangworld.vn', '0901000002', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'đang hoạt động', NOW(), NOW()),
(3, 'Trần Hoàng Phúc', 2, 'Trần Hoàng Phúc', 'phuc.phucvu@nhahangworld.vn', '0901000003', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'đang hoạt động', NOW(), NOW()),
(4, 'Bùi Văn Bếp', 2, 'Bùi Văn Bếp', 'beptruong@nhahangworld.vn', '0901000004', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'đang hoạt động', NOW(), NOW()),
(5, 'Nguyễn Văn An', 3, 'Nguyễn Văn An', 'nguyenvanan@gmail.com', '0912345678', 'Quận 1, TP. Hồ Chí Minh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'đang hoạt động', NOW(), NOW()),
(6, 'Trần Thị Mai', 3, 'Trần Thị Mai', 'tranthimai@gmail.com', '0987654321', 'Quận Bình Thạnh, TP. Hồ Chí Minh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'đang hoạt động', NOW(), NOW()),
(7, 'Lê Hoàng Nam', 3, 'Lê Hoàng Nam', 'lehoangnam@gmail.com', '0977000111', 'Thành phố Thủ Đức, TP. Hồ Chí Minh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'đang hoạt động', NOW(), NOW());

INSERT INTO `customers` (`id`, `user_id`, `full_name`, `phone`, `email`, `address`, `note`, `created_at`, `updated_at`) VALUES
(1, 5, 'Nguyễn Văn An', '0912345678', 'nguyenvanan@gmail.com', 'Quận 1, TP. Hồ Chí Minh', 'Thích bàn gần cửa sổ', NOW(), NOW()),
(2, 6, 'Trần Thị Mai', '0987654321', 'tranthimai@gmail.com', 'Quận Bình Thạnh, TP. Hồ Chí Minh', 'Ưu tiên món ít cay', NOW(), NOW()),
(3, 7, 'Lê Hoàng Nam', '0977000111', 'lehoangnam@gmail.com', 'Thành phố Thủ Đức, TP. Hồ Chí Minh', 'Thường đặt bàn gia đình', NOW(), NOW());

INSERT INTO `employees` (`id`, `user_id`, `employee_code`, `position`, `shift`, `salary`, `hire_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'NV001', 'Quản lý nhà hàng', 'Ca hành chính', 18000000, '2025-01-05', 'đang làm', NOW(), NOW()),
(2, 2, 'NV002', 'Thu ngân', '10:00 - 22:00', 9500000, '2025-03-12', 'đang làm', NOW(), NOW()),
(3, 3, 'NV003', 'Nhân viên phục vụ', '14:00 - 22:00', 8500000, '2025-05-20', 'đang làm', NOW(), NOW()),
(4, 4, 'NV004', 'Bếp trưởng', '09:00 - 21:00', 16000000, '2024-11-01', 'đang làm', NOW(), NOW());

INSERT INTO `tables` (`id`, `table_code`, `table_name`, `area`, `seats`, `status`, `created_at`, `updated_at`) VALUES
(1, 'B001', 'Bàn 01', 'Tầng 1', 4, 'trống', NOW(), NOW()),
(2, 'B002', 'Bàn 02', 'Tầng 1', 2, 'đang phục vụ', NOW(), NOW()),
(3, 'B003', 'Bàn 03', 'Tầng 1', 6, 'đã đặt', NOW(), NOW()),
(4, 'VIP01', 'Bàn VIP 01', 'Tầng 2', 8, 'đã đặt', NOW(), NOW()),
(5, 'SV01', 'Bàn sân vườn 01', 'Sân vườn', 4, 'trống', NOW(), NOW()),
(6, 'SV02', 'Bàn sân vườn 02', 'Sân vườn', 6, 'trống', NOW(), NOW());

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Món chính', 'Các món ăn chính dùng trong bữa ăn', 'hiển thị', NOW(), NOW()),
(2, 'Món khai vị', 'Các món ăn nhẹ mở đầu bữa ăn', 'hiển thị', NOW(), NOW()),
(3, 'Món tráng miệng', 'Món ngọt và trái cây sau bữa ăn', 'hiển thị', NOW(), NOW()),
(4, 'Đồ uống', 'Nước ép, trà, cà phê và nước giải khát', 'hiển thị', NOW(), NOW()),
(5, 'Món cay', 'Các món có vị cay nổi bật', 'hiển thị', NOW(), NOW());

INSERT INTO `foods` (`id`, `category_id`, `food_name`, `slug`, `description`, `price`, `image`, `spicy_level`, `preparation_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 'Gà xào cay', 'ga-xao-cay', 'Thịt gà xào cùng ớt chuông, sả và sa tế.', 89000, 'foods/ga-xao-cay.jpg', 4, 18, 'đang bán', NOW(), NOW()),
(2, 1, 'Cá chép sốt cải xanh', 'ca-chep-sot-cai-xanh', 'Cá chép chiên giòn phủ sốt cải xanh thanh nhẹ.', 135000, 'foods/ca-chep-sot-cai-xanh.jpg', 1, 22, 'đang bán', NOW(), NOW()),
(3, 5, 'Lẩu hải sản siêu cay', 'lau-hai-san-sieu-cay', 'Lẩu hải sản vị cay đậm, phù hợp nhóm 3 đến 4 người.', 329000, 'foods/lau-hai-san-sieu-cay.jpg', 5, 30, 'đang bán', NOW(), NOW()),
(4, 1, 'Baba om chuối đậu', 'baba-om-chuoi-dau', 'Món truyền thống với baba, chuối xanh, đậu phụ và lá lốt.', 249000, 'foods/baba-om-chuoi-dau.jpg', 2, 35, 'đang bán', NOW(), NOW()),
(5, 2, 'Bánh xèo tôm', 'banh-xeo-tom', 'Bánh xèo vàng giòn ăn kèm rau sống và nước mắm chua ngọt.', 65000, 'foods/banh-xeo-tom.jpg', 1, 15, 'đang bán', NOW(), NOW()),
(6, 1, 'Canh chua cá lóc', 'canh-chua-ca-loc', 'Canh chua miền Tây nấu với cá lóc, bạc hà và thơm.', 95000, 'foods/canh-chua-ca-loc.jpg', 1, 20, 'đang bán', NOW(), NOW()),
(7, 3, 'Chè khúc bạch', 'che-khuc-bach', 'Món tráng miệng mát lạnh với hạnh nhân và vải.', 42000, 'foods/che-khuc-bach.jpg', 0, 8, 'đang bán', NOW(), NOW()),
(8, 4, 'Trà sen vàng', 'tra-sen-vang', 'Trà sen thơm nhẹ dùng kèm hạt sen và kem sữa.', 39000, 'foods/tra-sen-vang.jpg', 0, 5, 'đang bán', NOW(), NOW());

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `image`, `status`, `created_at`, `updated_at`)
SELECT `id`, `category_id`, `food_name`, `slug`, `description`, `price`, `image`, 'available', `created_at`, `updated_at`
FROM `foods`;

INSERT INTO `reservations` (`id`, `customer_id`, `table_id`, `employee_id`, `reservation_code`, `reservation_time`, `number_of_guests`, `note`, `source`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 3, 'DB20260610001', '2026-06-10 18:30:00', 4, 'Khách muốn ngồi gần cửa sổ', 'chatbot', 'đã xác nhận', NOW(), NOW()),
(2, 2, 4, 3, 'DB20260610002', '2026-06-10 19:30:00', 6, 'Sinh nhật gia đình, chuẩn bị nến', 'website', 'đã xác nhận', NOW(), NOW()),
(3, 3, 2, 2, 'DB20260610003', '2026-06-10 12:00:00', 2, 'Gọi món tại bàn', 'trực tiếp', 'hoàn thành', NOW(), NOW());

INSERT INTO `orders` (`id`, `reservation_id`, `customer_id`, `table_id`, `employee_id`, `order_code`, `status`, `subtotal`, `discount`, `service_fee`, `vat`, `total_amount`, `ordered_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 3, 'DB20260610001', 'pending', 405000, 20000, 20250, 32420, 437670, '2026-06-10 18:30:00', NOW(), NOW()),
(2, 2, 2, 4, 3, 'DB20260610002', 'pending', 459000, 0, 22950, 38556, 520506, '2026-06-10 19:30:00', NOW(), NOW()),
(3, 3, 3, 2, 2, 'DB20260610003', 'completed', 230000, 0, 11500, 19320, 260820, '2026-06-10 12:00:00', NOW(), NOW());

INSERT INTO `reservation_details` (`id`, `reservation_id`, `food_id`, `quantity`, `unit_price`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 249000, 'Làm ít cay', NOW(), NOW()),
(2, 1, 8, 4, 39000, 'Ít đá', NOW(), NOW()),
(3, 2, 3, 1, 329000, 'Cay vừa', NOW(), NOW()),
(4, 2, 5, 2, 65000, 'Nước mắm để riêng', NOW(), NOW()),
(5, 3, 2, 1, 135000, NULL, NOW(), NOW()),
(6, 3, 6, 1, 95000, 'Ít chua', NOW(), NOW());

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `note`, `created_at`, `updated_at`)
SELECT `id`, `reservation_id`, `food_id`, `quantity`, `unit_price`, (`quantity` * `unit_price`), `note`, `created_at`, `updated_at`
FROM `reservation_details`;

INSERT INTO `payments` (`id`, `reservation_id`, `employee_id`, `payment_code`, `subtotal`, `discount`, `service_fee`, `vat`, `total_amount`, `payment_method`, `payment_status`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 'TT20260610001', 230000, 0, 11500, 19320, 260820, 'tiền mặt', 'đã thanh toán', '2026-06-10 13:05:00', NOW(), NOW()),
(2, 1, 2, 'TT20260610002', 405000, 20000, 20250, 32420, 437670, 'chuyển khoản', 'chưa thanh toán', NULL, NOW(), NOW()),
(3, 2, 2, 'TT20260610003', 459000, 0, 22950, 38556, 520506, 'thẻ ngân hàng', 'chưa thanh toán', NULL, NOW(), NOW());

INSERT INTO `chatbot_histories` (`id`, `customer_id`, `reservation_id`, `session_id`, `sender`, `message`, `intent`, `confidence`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'CHAT-AN-001', 'khách hàng', 'Tôi muốn đặt bàn cho 4 người lúc 18 giờ 30 tối nay.', 'đặt_bàn', 96.50, NOW(), NOW()),
(2, 1, 1, 'CHAT-AN-001', 'chatbot', 'Dạ nhà hàng còn Bàn 03 cho 4 khách lúc 18:30. Anh/chị xác nhận đặt bàn không ạ?', 'xác_nhận_đặt_bàn', 94.00, NOW(), NOW()),
(3, 1, 1, 'CHAT-AN-001', 'khách hàng', 'Xác nhận giúp tôi, tên Nguyễn Văn An.', 'xác_nhận_đặt_bàn', 98.00, NOW(), NOW()),
(4, 2, 2, 'CHAT-MAI-001', 'khách hàng', 'Cho tôi xem thực đơn món cay.', 'xem_thực_đơn', 91.25, NOW(), NOW()),
(5, 2, NULL, 'CHAT-MAI-001', 'chatbot', 'Nhà hàng hiện có Gà xào cay và Lẩu hải sản siêu cay trong danh mục Món cay.', 'gợi_ý_món_ăn', 93.80, NOW(), NOW());
 
INSERT INTO `chatbot_logs` (`id`, `customer_id`, `reservation_id`, `order_id`, `session_id`, `sender`, `message`, `intent`, `confidence`, `created_at`, `updated_at`)
SELECT `id`, `customer_id`, `reservation_id`, `reservation_id`, `session_id`, `sender`, `message`, `intent`, `confidence`, `created_at`, `updated_at`
FROM `chatbot_histories`;

CREATE TABLE IF NOT EXISTS `menu_galleries` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` VARCHAR(255) NULL,
  `image` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gallery_images` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `menu_galleries` (`id`, `title`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Menu Hải sản', 'Các món hải sản và món chính nổi bật của nhà hàng.', 'images/ca-chep-sot-cai-xanh.png', NOW(), NOW()),
(2, 'Menu Món cay', 'Gợi ý món cay dùng cho bữa tối và tiệc nhóm.', 'images/ga-xao-cay.png', NOW(), NOW()),
(3, 'Menu Tiệc cưới', 'Không gian và thực đơn phù hợp tiệc gia đình, tiệc cưới.', 'images/restaurant-interior.png', NOW(), NOW());

INSERT IGNORE INTO `gallery_images` (`id`, `title`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Không gian mặt tiền', 'images/hero-restaurant.png', NOW(), NOW()),
(2, 'Không gian bàn tiệc', 'images/restaurant-interior.png', NOW(), NOW()),
(3, 'Gà xào cay', 'images/ga-xao-cay.png', NOW(), NOW()),
(4, 'Cá chép sốt cải xanh', 'images/ca-chep-sot-cai-xanh.png', NOW(), NOW());
