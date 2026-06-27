<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HoaSenExpansionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $categoryIds = [];

        foreach ($this->categories() as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                $category + ['status' => 'hiển thị', 'updated_at' => $now, 'created_at' => $now]
            );

            $categoryIds[$category['name']] = DB::table('categories')->where('name', $category['name'])->value('id');
        }

        foreach ($this->products() as $product) {
            $slug = Str::slug($product['name']);

            DB::table('products')->updateOrInsert(
                ['slug' => $slug],
                [
                    'category_id' => $categoryIds[$product['category']],
                    'name' => $product['name'],
                    'slug' => $slug,
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'status' => 'available',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        foreach ($this->tables() as $table) {
            DB::table('tables')->updateOrInsert(
                ['table_code' => $table['table_code']],
                $table + ['updated_at' => $now, 'created_at' => $now]
            );
        }
    }

    private function categories(): array
    {
        return [
            ['name' => 'Hải sản', 'description' => 'Tôm, cua, mực, nghêu và các món hải sản tươi.'],
            ['name' => 'Món đồng quê', 'description' => 'Món Việt dân dã, đậm vị gia đình và miền Tây.'],
            ['name' => 'Món khai vị', 'description' => 'Món nhẹ mở đầu bữa ăn, dễ dùng và đẹp mắt.'],
            ['name' => 'Món chính', 'description' => 'Các món no, dùng trong bữa trưa và bữa tối.'],
            ['name' => 'Món lẩu', 'description' => 'Lẩu nóng phục vụ nhóm khách và tiệc gia đình.'],
            ['name' => 'Đồ uống', 'description' => 'Nước ép, trà và thức uống giải khát.'],
            ['name' => 'Tráng miệng', 'description' => 'Món ngọt nhẹ sau bữa ăn.'],
        ];
    }

    private function products(): array
    {
        return [
            [
                'category' => 'Món đồng quê',
                'name' => 'Gà nướng mật ong',
                'description' => 'Gà ta ướp mật ong, nướng vàng da, ăn kèm muối ớt xanh.',
                'price' => 185000,
                'image' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món lẩu',
                'name' => 'Lẩu hải sản đặc biệt',
                'description' => 'Nồi lẩu chua cay với tôm, mực, nghêu, cá và rau ăn kèm.',
                'price' => 389000,
                'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Hải sản',
                'name' => 'Tôm nướng muối ớt',
                'description' => 'Tôm tươi nướng than, vị cay nhẹ, thơm muối ớt và sả.',
                'price' => 169000,
                'image' => 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Hải sản',
                'name' => 'Mực hấp gừng',
                'description' => 'Mực ống hấp gừng hành, giữ độ giòn ngọt tự nhiên.',
                'price' => 155000,
                'image' => 'https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Hải sản',
                'name' => 'Nghêu hấp sả',
                'description' => 'Nghêu tươi hấp sả ớt, nước dùng thơm nhẹ và thanh vị.',
                'price' => 98000,
                'image' => 'https://images.unsplash.com/photo-1606851091851-e8c8c0fca5ba?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món chính',
                'name' => 'Cơm chiên hải sản',
                'description' => 'Cơm chiên tơi hạt với tôm, mực, trứng và rau củ.',
                'price' => 115000,
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Hải sản',
                'name' => 'Cua rang me',
                'description' => 'Cua chắc thịt rang sốt me chua ngọt, phủ hành phi thơm.',
                'price' => 295000,
                'image' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523b?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Hải sản',
                'name' => 'Sò huyết xào tỏi',
                'description' => 'Sò huyết xào tỏi cháy cạnh, đậm mùi thơm và vị biển.',
                'price' => 145000,
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món đồng quê',
                'name' => 'Cá lóc nướng trui',
                'description' => 'Cá lóc nướng dân dã, cuốn bánh tráng rau sống và mắm me.',
                'price' => 225000,
                'image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món đồng quê',
                'name' => 'Cá kho tộ Hoa Sen',
                'description' => 'Cá kho tiêu trong tộ đất, nước kho sánh và đậm vị cơm nhà.',
                'price' => 128000,
                'image' => 'https://images.unsplash.com/photo-1617093727343-374698b1b08d?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món đồng quê',
                'name' => 'Ếch xào sả ớt',
                'description' => 'Ếch đồng xào sả ớt, thịt chắc, cay thơm vừa miệng.',
                'price' => 139000,
                'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món đồng quê',
                'name' => 'Rau luộc kho quẹt',
                'description' => 'Rau củ luộc xanh giòn chấm kho quẹt tóp mỡ mặn ngọt.',
                'price' => 79000,
                'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món khai vị',
                'name' => 'Gỏi ngó sen tôm thịt',
                'description' => 'Ngó sen giòn trộn tôm thịt, rau răm và nước mắm chua ngọt.',
                'price' => 125000,
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món khai vị',
                'name' => 'Chả giò hải sản',
                'description' => 'Chả giò nhân tôm mực chiên giòn, dùng cùng sốt mayonnaise.',
                'price' => 99000,
                'image' => 'https://images.unsplash.com/photo-1541696432-82c6da8ce7bf?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món khai vị',
                'name' => 'Súp cua nấm tuyết',
                'description' => 'Súp cua nóng với nấm tuyết, trứng và thịt cua xé.',
                'price' => 68000,
                'image' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món chính',
                'name' => 'Bò lúc lắc khoai tây',
                'description' => 'Bò mềm áp chảo cùng ớt chuông, hành tây và khoai chiên.',
                'price' => 178000,
                'image' => 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món lẩu',
                'name' => 'Lẩu cá kèo lá giang',
                'description' => 'Lẩu cá kèo vị chua thanh từ lá giang, ăn cùng rau miền Tây.',
                'price' => 279000,
                'image' => 'https://images.unsplash.com/photo-1582878826629-29b7ad1cdc43?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Món lẩu',
                'name' => 'Lẩu gà lá é',
                'description' => 'Gà ta nấu lá é, nước dùng thơm cay nhẹ, hợp nhóm gia đình.',
                'price' => 259000,
                'image' => 'https://images.unsplash.com/photo-1604908177522-402891a9f4a7?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Đồ uống',
                'name' => 'Nước ép dưa hấu bạc hà',
                'description' => 'Dưa hấu ép tươi cùng bạc hà, mát nhẹ và dễ uống.',
                'price' => 45000,
                'image' => 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Tráng miệng',
                'name' => 'Bánh flan caramel',
                'description' => 'Flan mềm mịn, phủ caramel thơm nhẹ, dùng lạnh sau bữa ăn.',
                'price' => 42000,
                'image' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=900&q=80',
            ],
        ];
    }

    private function tables(): array
    {
        return collect(range(1, 10))->map(function (int $number) {
            $statuses = ['trống', 'đã đặt', 'đang sử dụng', 'đang dọn dẹp'];

            return [
                'table_code' => 'HS'.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'table_name' => 'Bàn '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'area' => $number <= 4 ? 'Tầng 1' : ($number <= 7 ? 'Sân vườn' : 'Phòng VIP'),
                'seats' => [2, 4, 4, 6, 6, 8, 4, 8, 10, 12][$number - 1],
                'status' => $statuses[($number - 1) % count($statuses)],
            ];
        })->all();
    }
}
