<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestaurantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'chatbot_logs',
            'order_items',
            'orders',
            'chatbot_histories',
            'payments',
            'reservation_details',
            'reservations',
            'products',
            'foods',
            'categories',
            'tables',
            'employees',
            'customers',
            'password_reset_tokens',
            'users',
            'roles',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Quản trị viên', 'description' => 'Toàn quyền quản lý hệ thống', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Thu ngân', 'description' => 'Quản lý hóa đơn và thanh toán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Nhân viên phục vụ', 'description' => 'Tiếp nhận đặt bàn và gọi món', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Nhân viên bếp', 'description' => 'Theo dõi món cần chế biến', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Khách hàng', 'description' => 'Tài khoản đặt bàn trực tuyến', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('users')->insert([
            ['id' => 1, 'role_id' => 1, 'full_name' => 'Nguyễn Quốc Huy', 'email' => 'quantri@nhahangworld.vn', 'phone' => '0901000001', 'password' => Hash::make('12345678'), 'status' => 'đang hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'role_id' => 2, 'full_name' => 'Phạm Thị Thu Ngân', 'email' => 'thungan@nhahangworld.vn', 'phone' => '0901000002', 'password' => Hash::make('12345678'), 'status' => 'đang hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'role_id' => 3, 'full_name' => 'Trần Hoàng Phúc', 'email' => 'phuc.phucvu@nhahangworld.vn', 'phone' => '0901000003', 'password' => Hash::make('12345678'), 'status' => 'đang hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'role_id' => 4, 'full_name' => 'Bùi Văn Bếp', 'email' => 'beptruong@nhahangworld.vn', 'phone' => '0901000004', 'password' => Hash::make('12345678'), 'status' => 'đang hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'role_id' => 5, 'full_name' => 'Nguyễn Văn An', 'email' => 'nguyenvanan@gmail.com', 'phone' => '0912345678', 'password' => Hash::make('12345678'), 'status' => 'đang hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'role_id' => 5, 'full_name' => 'Trần Thị Mai', 'email' => 'tranthimai@gmail.com', 'phone' => '0987654321', 'password' => Hash::make('12345678'), 'status' => 'đang hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'role_id' => 5, 'full_name' => 'Lê Hoàng Nam', 'email' => 'lehoangnam@gmail.com', 'phone' => '0977000111', 'password' => Hash::make('12345678'), 'status' => 'đang hoạt động', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('users')->whereNull('name')->update(['name' => DB::raw('full_name')]);
        DB::table('users')->update(['password' => Hash::make('password')]);
        DB::table('users')->where('id', 1)->update(['role_id' => 1]);
        DB::table('users')->whereIn('id', [2, 3, 4])->update(['role_id' => 2]);
        DB::table('users')->whereIn('id', [5, 6, 7])->update(['role_id' => 3]);

        DB::table('roles')->where('id', 1)->update(['name' => 'Admin', 'description' => 'Toàn quyền hệ thống']);
        DB::table('roles')->where('id', 2)->update(['name' => 'Nhân viên', 'description' => 'Tạo đơn hàng, đặt bàn và quản lý khách hàng']);
        DB::table('roles')->where('id', 3)->update(['name' => 'Khách hàng', 'description' => 'Xem menu, đặt bàn, đặt món và chatbot']);
        DB::table('roles')->whereIn('id', [4, 5])->delete();

        DB::table('customers')->insert([
            ['id' => 1, 'user_id' => 5, 'full_name' => 'Nguyễn Văn An', 'phone' => '0912345678', 'email' => 'nguyenvanan@gmail.com', 'address' => 'Quận 1, TP. Hồ Chí Minh', 'note' => 'Thích bàn gần cửa sổ', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'user_id' => 6, 'full_name' => 'Trần Thị Mai', 'phone' => '0987654321', 'email' => 'tranthimai@gmail.com', 'address' => 'Quận Bình Thạnh, TP. Hồ Chí Minh', 'note' => 'Ưu tiên món ít cay', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'user_id' => 7, 'full_name' => 'Lê Hoàng Nam', 'phone' => '0977000111', 'email' => 'lehoangnam@gmail.com', 'address' => 'Thành phố Thủ Đức, TP. Hồ Chí Minh', 'note' => 'Thường đặt bàn gia đình', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('employees')->insert([
            ['id' => 1, 'user_id' => 1, 'employee_code' => 'NV001', 'position' => 'Quản lý nhà hàng', 'shift' => 'Ca hành chính', 'salary' => 18000000, 'hire_date' => '2025-01-05', 'status' => 'đang làm', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'user_id' => 2, 'employee_code' => 'NV002', 'position' => 'Thu ngân', 'shift' => '10:00 - 22:00', 'salary' => 9500000, 'hire_date' => '2025-03-12', 'status' => 'đang làm', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'user_id' => 3, 'employee_code' => 'NV003', 'position' => 'Nhân viên phục vụ', 'shift' => '14:00 - 22:00', 'salary' => 8500000, 'hire_date' => '2025-05-20', 'status' => 'đang làm', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'user_id' => 4, 'employee_code' => 'NV004', 'position' => 'Bếp trưởng', 'shift' => '09:00 - 21:00', 'salary' => 16000000, 'hire_date' => '2024-11-01', 'status' => 'đang làm', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('tables')->insert([
            ['id' => 1, 'table_code' => 'B001', 'table_name' => 'Bàn 01', 'area' => 'Tầng 1', 'seats' => 4, 'status' => 'trống', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'table_code' => 'B002', 'table_name' => 'Bàn 02', 'area' => 'Tầng 1', 'seats' => 2, 'status' => 'đang phục vụ', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'table_code' => 'B003', 'table_name' => 'Bàn 03', 'area' => 'Tầng 1', 'seats' => 6, 'status' => 'đã đặt', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'table_code' => 'VIP01', 'table_name' => 'Bàn VIP 01', 'area' => 'Tầng 2', 'seats' => 8, 'status' => 'đã đặt', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'table_code' => 'SV01', 'table_name' => 'Bàn sân vườn 01', 'area' => 'Sân vườn', 'seats' => 4, 'status' => 'trống', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'table_code' => 'SV02', 'table_name' => 'Bàn sân vườn 02', 'area' => 'Sân vườn', 'seats' => 6, 'status' => 'trống', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Món chính', 'description' => 'Các món ăn chính dùng trong bữa ăn', 'status' => 'hiển thị', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Món khai vị', 'description' => 'Các món ăn nhẹ mở đầu bữa ăn', 'status' => 'hiển thị', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Món tráng miệng', 'description' => 'Món ngọt và trái cây sau bữa ăn', 'status' => 'hiển thị', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Đồ uống', 'description' => 'Nước ép, trà, cà phê và nước giải khát', 'status' => 'hiển thị', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Món cay', 'description' => 'Các món có vị cay nổi bật', 'status' => 'hiển thị', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('foods')->insert([
            ['id' => 1, 'category_id' => 5, 'food_name' => 'Gà xào cay', 'slug' => 'ga-xao-cay', 'description' => 'Thịt gà xào cùng ớt chuông, sả và sa tế.', 'price' => 89000, 'image' => 'foods/ga-xao-cay.jpg', 'spicy_level' => 4, 'preparation_time' => 18, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'category_id' => 1, 'food_name' => 'Cá chép sốt cải xanh', 'slug' => 'ca-chep-sot-cai-xanh', 'description' => 'Cá chép chiên giòn phủ sốt cải xanh thanh nhẹ.', 'price' => 135000, 'image' => 'foods/ca-chep-sot-cai-xanh.jpg', 'spicy_level' => 1, 'preparation_time' => 22, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'category_id' => 5, 'food_name' => 'Lẩu hải sản siêu cay', 'slug' => 'lau-hai-san-sieu-cay', 'description' => 'Lẩu hải sản vị cay đậm, phù hợp nhóm 3 đến 4 người.', 'price' => 329000, 'image' => 'foods/lau-hai-san-sieu-cay.jpg', 'spicy_level' => 5, 'preparation_time' => 30, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'category_id' => 1, 'food_name' => 'Baba om chuối đậu', 'slug' => 'baba-om-chuoi-dau', 'description' => 'Món truyền thống với baba, chuối xanh, đậu phụ và lá lốt.', 'price' => 249000, 'image' => 'foods/baba-om-chuoi-dau.jpg', 'spicy_level' => 2, 'preparation_time' => 35, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'category_id' => 2, 'food_name' => 'Bánh xèo tôm', 'slug' => 'banh-xeo-tom', 'description' => 'Bánh xèo vàng giòn ăn kèm rau sống và nước mắm chua ngọt.', 'price' => 65000, 'image' => 'foods/banh-xeo-tom.jpg', 'spicy_level' => 1, 'preparation_time' => 15, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'category_id' => 1, 'food_name' => 'Canh chua cá lóc', 'slug' => 'canh-chua-ca-loc', 'description' => 'Canh chua miền Tây nấu với cá lóc, bạc hà và thơm.', 'price' => 95000, 'image' => 'foods/canh-chua-ca-loc.jpg', 'spicy_level' => 1, 'preparation_time' => 20, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'category_id' => 3, 'food_name' => 'Chè khúc bạch', 'slug' => 'che-khuc-bach', 'description' => 'Món tráng miệng mát lạnh với hạnh nhân và vải.', 'price' => 42000, 'image' => 'foods/che-khuc-bach.jpg', 'spicy_level' => 0, 'preparation_time' => 8, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'category_id' => 4, 'food_name' => 'Trà sen vàng', 'slug' => 'tra-sen-vang', 'description' => 'Trà sen thơm nhẹ dùng kèm hạt sen và kem sữa.', 'price' => 39000, 'image' => 'foods/tra-sen-vang.jpg', 'spicy_level' => 0, 'preparation_time' => 5, 'status' => 'đang bán', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('products')->insertUsing(
            ['id', 'category_id', 'name', 'slug', 'description', 'price', 'image', 'status', 'created_at', 'updated_at'],
            DB::table('foods')->select([
                'id',
                'category_id',
                DB::raw('food_name as name'),
                'slug',
                'description',
                'price',
                'image',
                DB::raw("'available' as status"),
                'created_at',
                'updated_at',
            ])
        );

        DB::table('reservations')->insert([
            ['id' => 1, 'customer_id' => 1, 'table_id' => 3, 'employee_id' => 3, 'reservation_code' => 'DB20260610001', 'reservation_time' => '2026-06-10 18:30:00', 'number_of_guests' => 4, 'note' => 'Khách muốn ngồi gần cửa sổ', 'source' => 'chatbot', 'status' => 'đã xác nhận', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'customer_id' => 2, 'table_id' => 4, 'employee_id' => 3, 'reservation_code' => 'DB20260610002', 'reservation_time' => '2026-06-10 19:30:00', 'number_of_guests' => 6, 'note' => 'Sinh nhật gia đình, chuẩn bị nến', 'source' => 'website', 'status' => 'đã xác nhận', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'customer_id' => 3, 'table_id' => 2, 'employee_id' => 2, 'reservation_code' => 'DB20260610003', 'reservation_time' => '2026-06-10 12:00:00', 'number_of_guests' => 2, 'note' => 'Gọi món tại bàn', 'source' => 'trực tiếp', 'status' => 'hoàn thành', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('orders')->insert([
            ['id' => 1, 'reservation_id' => 1, 'customer_id' => 1, 'table_id' => 3, 'employee_id' => 3, 'order_code' => 'DB20260610001', 'status' => 'pending', 'subtotal' => 405000, 'discount' => 20000, 'service_fee' => 20250, 'vat' => 32420, 'total_amount' => 437670, 'ordered_at' => '2026-06-10 18:30:00', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'reservation_id' => 2, 'customer_id' => 2, 'table_id' => 4, 'employee_id' => 3, 'order_code' => 'DB20260610002', 'status' => 'pending', 'subtotal' => 459000, 'discount' => 0, 'service_fee' => 22950, 'vat' => 38556, 'total_amount' => 520506, 'ordered_at' => '2026-06-10 19:30:00', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'reservation_id' => 3, 'customer_id' => 3, 'table_id' => 2, 'employee_id' => 2, 'order_code' => 'DB20260610003', 'status' => 'completed', 'subtotal' => 230000, 'discount' => 0, 'service_fee' => 11500, 'vat' => 19320, 'total_amount' => 260820, 'ordered_at' => '2026-06-10 12:00:00', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('reservation_details')->insert([
            ['id' => 1, 'reservation_id' => 1, 'food_id' => 4, 'quantity' => 1, 'unit_price' => 249000, 'note' => 'Làm ít cay', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'reservation_id' => 1, 'food_id' => 8, 'quantity' => 4, 'unit_price' => 39000, 'note' => 'Ít đá', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'reservation_id' => 2, 'food_id' => 3, 'quantity' => 1, 'unit_price' => 329000, 'note' => 'Cay vừa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'reservation_id' => 2, 'food_id' => 5, 'quantity' => 2, 'unit_price' => 65000, 'note' => 'Nước mắm để riêng', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'reservation_id' => 3, 'food_id' => 2, 'quantity' => 1, 'unit_price' => 135000, 'note' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'reservation_id' => 3, 'food_id' => 6, 'quantity' => 1, 'unit_price' => 95000, 'note' => 'Ít chua', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('order_items')->insertUsing(
            ['id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price', 'note', 'created_at', 'updated_at'],
            DB::table('reservation_details')->select([
                'id',
                DB::raw('reservation_id as order_id'),
                DB::raw('food_id as product_id'),
                'quantity',
                'unit_price',
                DB::raw('(quantity * unit_price) as total_price'),
                'note',
                'created_at',
                'updated_at',
            ])
        );

        DB::table('payments')->insert([
            ['id' => 1, 'reservation_id' => 3, 'employee_id' => 2, 'payment_code' => 'TT20260610001', 'subtotal' => 230000, 'discount' => 0, 'service_fee' => 11500, 'vat' => 19320, 'total_amount' => 260820, 'payment_method' => 'tiền mặt', 'payment_status' => 'đã thanh toán', 'paid_at' => '2026-06-10 13:05:00', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'reservation_id' => 1, 'employee_id' => 2, 'payment_code' => 'TT20260610002', 'subtotal' => 405000, 'discount' => 20000, 'service_fee' => 20250, 'vat' => 32420, 'total_amount' => 437670, 'payment_method' => 'chuyển khoản', 'payment_status' => 'chưa thanh toán', 'paid_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'reservation_id' => 2, 'employee_id' => 2, 'payment_code' => 'TT20260610003', 'subtotal' => 459000, 'discount' => 0, 'service_fee' => 22950, 'vat' => 38556, 'total_amount' => 520506, 'payment_method' => 'thẻ ngân hàng', 'payment_status' => 'chưa thanh toán', 'paid_at' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('chatbot_histories')->insert([
            ['id' => 1, 'customer_id' => 1, 'reservation_id' => 1, 'session_id' => 'CHAT-AN-001', 'sender' => 'khách hàng', 'message' => 'Tôi muốn đặt bàn cho 4 người lúc 18 giờ 30 tối nay.', 'intent' => 'đặt_bàn', 'confidence' => 96.50, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'customer_id' => 1, 'reservation_id' => 1, 'session_id' => 'CHAT-AN-001', 'sender' => 'chatbot', 'message' => 'Dạ nhà hàng còn Bàn 03 cho 4 khách lúc 18:30. Anh/chị xác nhận đặt bàn không ạ?', 'intent' => 'xác_nhận_đặt_bàn', 'confidence' => 94.00, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'customer_id' => 1, 'reservation_id' => 1, 'session_id' => 'CHAT-AN-001', 'sender' => 'khách hàng', 'message' => 'Xác nhận giúp tôi, tên Nguyễn Văn An.', 'intent' => 'xác_nhận_đặt_bàn', 'confidence' => 98.00, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'customer_id' => 2, 'reservation_id' => 2, 'session_id' => 'CHAT-MAI-001', 'sender' => 'khách hàng', 'message' => 'Cho tôi xem thực đơn món cay.', 'intent' => 'xem_thực_đơn', 'confidence' => 91.25, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'customer_id' => 2, 'reservation_id' => null, 'session_id' => 'CHAT-MAI-001', 'sender' => 'chatbot', 'message' => 'Nhà hàng hiện có Gà xào cay và Lẩu hải sản siêu cay trong danh mục Món cay.', 'intent' => 'gợi_ý_món_ăn', 'confidence' => 93.80, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('chatbot_logs')->insertUsing(
            ['id', 'customer_id', 'reservation_id', 'order_id', 'session_id', 'sender', 'message', 'intent', 'confidence', 'created_at', 'updated_at'],
            DB::table('chatbot_histories')->select([
                'id',
                'customer_id',
                'reservation_id',
                DB::raw('reservation_id as order_id'),
                'session_id',
                'sender',
                'message',
                'intent',
                'confidence',
                'created_at',
                'updated_at',
            ])
        );
    }
}
