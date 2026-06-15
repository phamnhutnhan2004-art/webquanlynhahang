<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\Customer;
use App\Models\MenuGallery;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function message(Request $request): JsonResponse
    {
        if ($blocked = $this->rejectInvalidToken($request)) {
            return $blocked;
        }

        return response()->json($this->handle($request));
    }

    public function webhook(Request $request): JsonResponse
    {
        if ($blocked = $this->rejectInvalidToken($request)) {
            return $blocked;
        }

        $result = $this->handle($request);

        return response()->json([
            'fulfillmentText' => $result['reply'],
            'fulfillmentMessages' => [
                ['text' => ['text' => [$result['reply']]]],
            ],
            'payload' => $result,
        ]);
    }

    public function config(): JsonResponse
    {
        return response()->json([
            'restaurant' => $this->restaurantProfile(),
            'quick_replies' => [
                'Giờ mở cửa',
                'Món cay',
                'Đồ uống',
                'Món hải sản',
                'Món bán chạy',
                'Đặt bàn',
            ],
            'webhook' => url('/api/chatbot/webhook'),
        ]);
    }

    private function handle(Request $request): array
    {
        $payload = $request->all();
        $message = trim($this->extractMessage($payload));
        $sessionId = $this->extractSessionId($payload);
        $analysis = $this->analyzeMessage($payload, $message, $sessionId);
        $intent = $analysis['intent'];
        $confidence = $this->extractConfidence($payload);

        $this->logMessage($sessionId, 'khách hàng', $message ?: 'Yêu cầu từ chatbot', $intent, $confidence);

        $result = match ($intent) {
            'greeting' => $this->greeting(),
            'restaurant_info' => $this->restaurantInfo($message),
            'delivery_info' => $this->deliveryInfo(),
            'book_table' => $this->bookingFlow($payload, $message, $sessionId, $intent, $confidence),
            'product_detail' => $this->productDetail($message, $sessionId, $analysis['keywords']),
            'best_selling' => $this->bestSellingProducts(),
            'food_recommendation' => $this->foodRecommendation($message, $sessionId, $analysis['keywords']),
            'menu' => $this->menu($message),
            default => $this->fallback($message),
        };

        $this->logMessage(
            $sessionId,
            'chatbot',
            $result['reply'],
            $intent,
            null,
            $result['customer_id'] ?? null,
            $result['reservation_id'] ?? null
        );

        unset($result['customer_id'], $result['reservation_id']);

        return $result + [
            'intent' => $intent,
            'session_id' => $sessionId,
            'analysis' => [
                'keywords' => $analysis['keywords'],
                'matched_products' => $analysis['matched_products'],
            ],
        ];
    }

    private function analyzeMessage(array $payload, string $message, string $sessionId): array
    {
        $normalized = $this->normalizeText($message);
        $keywords = $this->extractKeywords($normalized);
        $matchedProducts = $this->searchProductsByKeywords($keywords, $message, 5);

        return [
            'normalized' => $normalized,
            'keywords' => $keywords,
            'matched_products' => $this->productData($matchedProducts),
            'intent' => $this->detectIntent($payload, $message, $sessionId, $keywords, $matchedProducts),
        ];
    }

    private function greeting(): array
    {
        return [
            'reply' => 'Chào anh/chị, mình là trợ lý của Nhà hàng Hoa Sen. Mình có thể tư vấn món ăn, lọc món cay/hải sản/đồ uống, xem món bán chạy, hỗ trợ đặt bàn và trả lời thông tin nhà hàng.',
            'data' => [
                'quick_replies' => ['Gợi ý món cay', 'Có món hải sản không?', 'Món bán chạy', 'Đặt bàn cho 4 người tối nay'],
            ],
        ];
    }

    private function deliveryInfo(): array
    {
        return [
            'reply' => 'Hiện hệ thống đang ưu tiên đặt bàn và phục vụ tại nhà hàng. Nếu anh/chị muốn giao hàng, vui lòng gọi hotline '.$this->restaurantProfile()['phone'].' để nhân viên kiểm tra khu vực hỗ trợ.',
            'data' => ['restaurant' => $this->restaurantProfile()],
        ];
    }

    private function restaurantInfo(string $message): array
    {
        $text = $this->normalizeText($message);
        $profile = $this->restaurantProfile();

        if ($this->containsAny($text, ['dia chi', 'o dau', 'duong nao', 'vi tri'])) {
            return [
                'reply' => "Nhà hàng Hoa Sen ở {$profile['address']}.",
                'data' => ['restaurant' => $profile],
            ];
        }

        if ($this->containsAny($text, ['so dien thoai', 'sdt', 'hotline', 'lien he', 'goi'])) {
            return [
                'reply' => "Hotline nhà hàng là {$profile['phone']}. Anh/chị có thể gọi để được hỗ trợ nhanh.",
                'data' => ['restaurant' => $profile],
            ];
        }

        if ($this->containsAny($text, ['dau xe', 'gui xe', 'parking', 'xe hoi', 'oto', 'o to'])) {
            return [
                'reply' => $profile['parking'],
                'data' => ['restaurant' => $profile],
            ];
        }

        return [
            'reply' => "Nhà hàng Hoa Sen mở cửa {$profile['opening_hours']}. Địa chỉ: {$profile['address']}. Hotline: {$profile['phone']}.",
            'data' => ['restaurant' => $profile],
        ];
    }

    private function menu(string $message = ''): array
    {
        $products = $this->availableProducts()
            ->sortBy('name')
            ->take(8)
            ->values();

        $galleries = MenuGallery::latest()
            ->limit(3)
            ->get()
            ->map(fn (MenuGallery $menu) => [
                'title' => $menu->title,
                'description' => $menu->description,
                'url' => $menu->image_url,
            ]);

        if ($products->isEmpty()) {
            return [
                'reply' => 'Hiện chưa có món ăn đang bán trong hệ thống.',
                'data' => ['products' => [], 'menu_galleries' => $galleries],
            ];
        }

        return [
            'reply' => "Một số món đang bán:\n".$this->formatProducts($products)."\nAnh/chị có thể hỏi theo nhóm như món cay, đồ uống, món khai vị, tráng miệng hoặc hải sản.",
            'data' => [
                'products' => $this->productData($products),
                'menu_galleries' => $galleries,
            ],
        ];
    }

    private function foodRecommendation(string $message, string $sessionId, array $keywords = []): array
    {
        $text = $this->normalizeText($message);

        if ($this->containsAny($text, ['tre em', 'cho be', 'em be', 'kids', 'khong cay', 'it cay'])) {
            return $this->childrenFriendlyProducts();
        }

        $category = $this->detectFoodCategory($text);

        if ($category) {
            return $this->productsByFoodCategory($category);
        }

        if ($this->containsAny($text, ['tu van', 'goi y', 'nen an', 'an gi', 'mon nao ngon', 'mon ngon'])) {
            return $this->generalSuggestions($sessionId);
        }

        $matchedProducts = $this->searchProductsByKeywords($keywords, $message, 6);

        if ($matchedProducts->isNotEmpty()) {
            return [
                'reply' => "Mình tìm thấy một số món khá khớp với nhu cầu của anh/chị:\n".$this->formatProducts($matchedProducts)."\nAnh/chị muốn xem chi tiết món nào?",
                'data' => ['products' => $this->productData($matchedProducts)],
            ];
        }

        return $this->generalSuggestions($sessionId);
    }

    private function productDetail(string $message, string $sessionId, array $keywords = []): array
    {
        $text = $this->normalizeText($message);
        $product = $this->productFromOrdinalContext($text, $sessionId)
            ?: $this->productFromLastContext($sessionId)
            ?: $this->searchProductsByKeywords($keywords, $message, 1)->first();

        if (! $product) {
            return [
                'reply' => 'Anh/chị muốn xem chi tiết món nào? Có thể nhắn tên món hoặc chọn “món đầu tiên”, “món thứ hai” sau khi mình gợi ý danh sách.',
                'data' => ['products' => []],
            ];
        }

        $price = number_format((float) $product->price, 0, ',', '.').'đ';
        $category = $product->category?->name ? " thuộc nhóm {$product->category->name}" : '';

        if ($this->containsAny($text, ['gia', 'bao nhieu tien', 'bao nhieu', 'price'])) {
            return [
                'reply' => "{$product->name} hiện có giá {$price}. Anh/chị muốn mình gợi ý thêm món dùng kèm không?",
                'data' => ['product' => $this->productData(collect([$product]))[0]],
            ];
        }

        return [
            'reply' => "{$product->name}{$category}, giá {$price}. ".($product->description ?: 'Món này đang có trong thực đơn của nhà hàng.').' Anh/chị muốn đặt bàn hoặc xem món tương tự không?',
            'data' => ['product' => $this->productData(collect([$product]))[0]],
        ];
    }

    private function productsByFoodCategory(array $category): array
    {
        $products = $this->availableProducts()
            ->filter(fn (Product $product): bool => $this->productMatchesFoodCategory($product, $category['key']))
            ->take(8)
            ->values();

        if ($products->isEmpty()) {
            return [
                'reply' => "Hiện hệ thống chưa có món phù hợp với nhóm {$category['label']}. Anh/chị có thể xem thực đơn chung hoặc hỏi nhóm món khác.",
                'data' => ['products' => [], 'category' => $category['key']],
            ];
        }

        return [
            'reply' => "Các món phù hợp với yêu cầu {$category['label']}:\n".$this->formatProducts($products),
            'data' => [
                'category' => $category['key'],
                'products' => $this->productData($products),
            ],
        ];
    }

    private function childrenFriendlyProducts(): array
    {
        $products = $this->availableProducts()
            ->filter(function (Product $product): bool {
                $haystack = $this->normalizeText(
                    $product->name.' '.$product->description.' '.($product->category?->name ?? '')
                );

                return ! $this->containsAny($haystack, ['cay', 'sa te', 'ot'])
                    && $this->containsAny($haystack, ['trang mieng', 'do uong', 'khai vi', 'banh', 'che', 'tra', 'ca']);
            })
            ->take(6)
            ->values();

        if ($products->isEmpty()) {
            return [
                'reply' => 'Hiện chưa có nhóm món trẻ em riêng. Anh/chị có thể chọn món ít cay và báo ghi chú khi đặt món.',
                'data' => ['products' => []],
            ];
        }

        return [
            'reply' => "Gợi ý món nhẹ, dễ dùng cho trẻ em hoặc khách không ăn cay:\n".$this->formatProducts($products),
            'data' => ['products' => $this->productData($products)],
        ];
    }

    private function productMatchesFoodCategory(Product $product, string $categoryKey): bool
    {
        $category = $this->normalizeText($product->category?->name ?? '');
        $name = $this->normalizeText($product->name);
        $description = $this->normalizeText((string) $product->description);
        $text = $name.' '.$description;

        return match ($categoryKey) {
            'spicy' => str_contains($category, 'mon cay') || $this->containsAny($text, ['cay', 'sa te', 'ot']),
            'drink' => str_contains($category, 'do uong') || $this->containsAny($text, ['nuoc ep', 'ca phe', 'tra sen', 'giai khat']),
            'seafood' => str_contains($text, 'hai san') || $this->containsAny($text, ['tom', 'muc', 'ca chep', 'ca loc']),
            'starter' => str_contains($category, 'khai vi') || str_contains($name, 'banh xeo'),
            'dessert' => str_contains($category, 'trang mieng') || str_contains($name, 'che khuc'),
            default => false,
        };
    }

    private function bestSellingProducts(): array
    {
        $soldRows = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('sold_quantity')
            ->limit(5)
            ->get();

        if ($soldRows->isEmpty()) {
            return [
                'reply' => "Hệ thống chưa có đủ dữ liệu bán chạy. Mình gợi ý một số món đang bán:\n".$this->formatProducts($this->availableProducts()->take(5)),
                'data' => ['products' => $this->productData($this->availableProducts()->take(5))],
            ];
        }

        $products = Product::with('category:id,name')
            ->where('status', 'available')
            ->whereIn('id', $soldRows->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $ranked = $soldRows
            ->map(fn ($row) => $products->get($row->product_id))
            ->filter()
            ->values();

        if ($ranked->isEmpty()) {
            return $this->generalSuggestions('best-selling');
        }

        return [
            'reply' => "Các món đang bán chạy theo dữ liệu order:\n".$this->formatProducts($ranked),
            'data' => ['products' => $this->productData($ranked)],
        ];
    }

    private function generalSuggestions(string $sessionId): array
    {
        $products = $this->availableProducts()
            ->sortByDesc(fn (Product $product) => (float) $product->price)
            ->take(5)
            ->values();

        if ($products->isEmpty()) {
            return [
                'reply' => 'Hiện chưa có dữ liệu món ăn để tư vấn. Anh/chị vui lòng thử lại sau.',
                'data' => ['products' => []],
            ];
        }

        return [
            'reply' => "Mình gợi ý một số món nổi bật trong thực đơn:\n".$this->formatProducts($products)."\nNếu anh/chị thích vị cay, hải sản, khai vị hoặc đồ uống, hãy nhắn đúng nhu cầu để mình lọc kỹ hơn.",
            'data' => ['products' => $this->productData($products)],
        ];
    }

    private function bookingFlow(array $payload, string $message, string $sessionId, string $intent, ?float $confidence): array
    {
        $booking = $this->normalizeBooking($payload, $message);
        $missing = $this->missingBookingFields($booking);

        if ($missing === []) {
            return $this->bookTable($booking, $message, $sessionId, $intent, $confidence);
        }

        $guestCount = max((int) $booking['number_of_guests'], 1);
        $availableTables = $this->availableTablesForGuests($guestCount);
        $timeText = $booking['reservation_time']
            ? ' lúc '.$booking['reservation_time']->format('d/m/Y H:i')
            : '';

        $availabilityText = $availableTables->isNotEmpty()
            ? "Hiện có {$availableTables->count()} bàn trống phù hợp{$timeText}."
            : "Hiện chưa có bàn trống phù hợp với {$guestCount} khách{$timeText}.";

        return [
            'reply' => $availabilityText.' Để đặt bàn tự động, anh/chị vui lòng gửi thêm: '.implode(', ', $missing).'.',
            'data' => [
                'available' => $availableTables->isNotEmpty(),
                'available_tables' => $availableTables->map->only(['id', 'table_code', 'table_name', 'area', 'seats'])->values(),
                'missing_fields' => $missing,
                'suggested_fields' => ['customer_name', 'phone', 'number_of_guests', 'reservation_time'],
            ],
        ];
    }

    private function bookTable(array $booking, string $message, string $sessionId, string $intent, ?float $confidence): array
    {
        if ($booking['reservation_time']->lessThanOrEqualTo(now())) {
            return [
                'reply' => 'Thời gian đặt bàn cần ở tương lai. Anh/chị vui lòng chọn lại ngày giờ nhé.',
                'data' => ['missing_fields' => ['thời gian hợp lệ']],
            ];
        }

        return DB::transaction(function () use ($booking, $message, $sessionId, $intent, $confidence): array {
            $table = RestaurantTable::query()
                ->where('status', 'trống')
                ->where('seats', '>=', $booking['number_of_guests'])
                ->orderBy('seats')
                ->orderBy('table_code')
                ->lockForUpdate()
                ->first();

            if (! $table) {
                return [
                    'reply' => 'Hiện chưa còn bàn trống phù hợp với số lượng khách này. Anh/chị có thể đổi giờ hoặc gọi hotline để được hỗ trợ.',
                    'data' => [
                        'available' => false,
                        'number_of_guests' => $booking['number_of_guests'],
                    ],
                ];
            }

            $customerData = [
                'full_name' => $booking['customer_name'],
                'note' => 'Khách đặt bàn qua chatbot',
            ];

            if (filled($booking['email'])) {
                $customerData['email'] = $booking['email'];
            }

            $customer = Customer::updateOrCreate(['phone' => $booking['phone']], $customerData);

            $reservation = Reservation::create([
                'customer_id' => $customer->id,
                'table_id' => $table->id,
                'reservation_code' => 'CB'.now()->format('YmdHis').Str::upper(Str::random(4)),
                'reservation_time' => $booking['reservation_time'],
                'number_of_guests' => $booking['number_of_guests'],
                'note' => $booking['note'] ?: 'Đặt bàn tự động từ chatbot',
                'source' => 'chatbot',
                'status' => 'đã xác nhận',
            ]);

            $table->update(['status' => 'đã đặt']);

            $this->logMessage($sessionId, 'khách hàng', $message ?: 'Đặt bàn qua chatbot', $intent, $confidence, $customer->id, $reservation->id);

            $time = $booking['reservation_time']->format('d/m/Y H:i');

            return [
                'reply' => "Đặt bàn thành công. Mã đặt bàn {$reservation->reservation_code}, {$table->table_name}, {$booking['number_of_guests']} khách lúc {$time}.",
                'customer_id' => $customer->id,
                'reservation_id' => $reservation->id,
                'data' => [
                    'available' => true,
                    'reservation_code' => $reservation->reservation_code,
                    'table' => [
                        'id' => $table->id,
                        'name' => $table->table_name,
                        'area' => $table->area,
                        'seats' => $table->seats,
                    ],
                    'reservation_time' => $booking['reservation_time']->toDateTimeString(),
                    'number_of_guests' => $booking['number_of_guests'],
                ],
            ];
        });
    }

    private function fallback(string $message): array
    {
        $keywords = $this->extractKeywords($this->normalizeText($message));
        $matchedProducts = $this->searchProductsByKeywords($keywords, $message, 4);

        if ($matchedProducts->isNotEmpty()) {
            return [
                'reply' => "Mình chưa chắc hoàn toàn ý anh/chị, nhưng tìm thấy vài món có vẻ liên quan:\n".$this->formatProducts($matchedProducts)."\nAnh/chị muốn xem giá hay mô tả món nào?",
                'data' => ['products' => $this->productData($matchedProducts)],
            ];
        }

        $suggestions = [
            'Gợi ý món cay',
            'Có món hải sản nào ngon không?',
            'Có nước uống gì vậy?',
            'Món nào bán chạy nhất?',
            'Làm sao để đặt bàn?',
            'Nhà hàng mở cửa lúc mấy giờ?',
        ];

        return [
            'reply' => 'Mình chưa hiểu trọn ý câu này, nhưng mình có thể tư vấn món ăn, tìm món theo khẩu vị, kiểm tra bàn trống hoặc trả lời thông tin nhà hàng. Anh/chị thử hỏi theo kiểu: “Tôi thích ăn cay”, “Có nước uống gì vậy?” hoặc “Đặt bàn cho 4 người tối nay”.',
            'data' => ['quick_replies' => $suggestions],
        ];
    }

    private function detectIntent(array $payload, string $message, string $sessionId, array $keywords = [], ?Collection $matchedProducts = null): string
    {
        $externalIntent = Arr::get($payload, 'intent')
            ?: Arr::get($payload, 'queryResult.intent.displayName')
            ?: Arr::get($payload, 'nlu.intent.name')
            ?: Arr::get($payload, 'tracker.latest_message.intent.name');

        $text = $this->normalizeText((string) ($externalIntent ?: $message));

        $hasSpecificQuestion = $this->containsAny($text, [
            'dia chi', 'so dien thoai', 'sdt', 'hotline', 'lien he', 'dau xe', 'gui xe',
            'parking', 'gio mo cua', 'mo cua', 'dong cua', 'dat ban', 'giu ban', 'con ban',
            'ban trong', 'giao hang', 'ship', 'delivery', 'mang ve', 'mon', 'do uong',
            'hai san', 'khai vi', 'trang mieng', 'cay', 'ban chay',
        ]);

        if ($this->isGreeting($text) && ! $hasSpecificQuestion) {
            return 'greeting';
        }

        if ($this->containsAny($text, ['giao hang', 'ship', 'delivery', 'mang ve', 'dat ve nha'])) {
            return 'delivery_info';
        }

        if ($this->isProductDetailQuestion($text, $sessionId) || ($matchedProducts?->isNotEmpty() && $this->containsAny($text, ['gia', 'chi tiet', 'mo ta', 'thong tin']))) {
            return 'product_detail';
        }

        if ($this->containsAny($text, ['dia chi', 'so dien thoai', 'sdt', 'hotline', 'lien he', 'dau xe', 'gui xe', 'parking', 'gio mo cua', 'mo cua', 'dong cua', 'open', 'hours'])) {
            return 'restaurant_info';
        }

        if ($this->containsAny($text, ['dat ban', 'giu ban', 'con ban', 'ban trong', 'reservation', 'booking', 'book table'])) {
            return 'book_table';
        }

        if ($this->containsAny($text, ['ban chay', 'bestseller', 'pho bien nhat', 'duoc goi nhieu', 'mon nao ban chay'])) {
            return 'best_selling';
        }

        if ($this->containsAny($text, ['thuc don', 'menu']) && ! $this->detectFoodCategory($text)) {
            return 'menu';
        }

        if ($this->containsAny($text, ['mon', 'do uong', 'hai san', 'khai vi', 'trang mieng', 'cay', 'tre em', 'cho be', 'goi y', 'tu van', 'an gi', 'khong cay', 'it cay'])) {
            return 'food_recommendation';
        }

        if ($matchedProducts?->isNotEmpty()) {
            return 'food_recommendation';
        }

        $lastIntent = $this->lastIntent($sessionId);
        if ($lastIntent && $this->containsAny($text, ['them', 'nua', 'khac', 'goi y tiep'])) {
            return str_contains($lastIntent, 'food') || $lastIntent === 'menu'
                ? 'food_recommendation'
                : $lastIntent;
        }

        return 'fallback';
    }

    private function isGreeting(string $text): bool
    {
        return $this->containsAny($text, [
            'xin chao',
            'chao ban',
            'hello',
            'hi',
            'chao nha hang',
            'cho minh hoi',
            'cho toi hoi',
            'alo',
        ]) || in_array($text, ['chao', 'hello', 'hi'], true);
    }

    private function isProductDetailQuestion(string $text, string $sessionId): bool
    {
        $hasContext = $this->productsFromLastBotReply($sessionId)->isNotEmpty();

        if (! $hasContext) {
            return false;
        }

        return $this->containsAny($text, [
            'mon dau tien',
            'mon thu nhat',
            'mon so 1',
            'mon thu 2',
            'mon thu hai',
            'mon so 2',
            'mon thu 3',
            'mon so 3',
            'mon do',
            'cai do',
            'gia bao nhieu',
            'bao nhieu tien',
            'gia mon nay',
            'chi tiet',
            'xem them',
        ]);
    }

    private function extractKeywords(string $text): array
    {
        preg_match_all('/[a-z0-9]+/u', $text, $matches);

        $stopWords = [
            'toi', 'minh', 'ban', 'anh', 'chi', 'em', 'nha', 'hang', 'co', 'khong',
            'nao', 'gi', 'vay', 'cho', 'hoi', 'muon', 'thich', 'an', 'uống', 'uong',
            'nuoc', 'hay', 'la', 'cua', 'o', 'duoc', 'khach', 'vai', 'mot', 'may', 'luc',
            'mon', 'xem', 'dau', 'tien', 'thu', 'so', 'gia', 'bao', 'nhieu', 'nay', 'do',
            'mo', 'gio', 'nhat', 'chay',
        ];

        $keywords = collect($matches[0] ?? [])
            ->map(fn (string $word) => trim($word))
            ->filter(fn (string $word) => strlen($word) >= 2 && ! in_array($word, $stopWords, true))
            ->values();

        $phrases = [
            'hai san',
            'do uong',
            'nuoc uong',
            'thuc uong',
            'mon cay',
            'khai vi',
            'trang mieng',
            'ban chay',
            'tre em',
            'giao hang',
            'dat ban',
        ];

        foreach ($phrases as $phrase) {
            if (str_contains($text, $phrase)) {
                $keywords->push($phrase);
            }
        }

        return $keywords->unique()->values()->all();
    }

    private function searchProductsByKeywords(array $keywords, string $message = '', int $limit = 5): Collection
    {
        if ($keywords === []) {
            return collect();
        }

        $messageText = $this->normalizeText($message);

        return $this->availableProducts()
            ->map(function (Product $product) use ($keywords, $messageText): array {
                $name = $this->normalizeText($product->name);
                $category = $this->normalizeText($product->category?->name ?? '');
                $description = $this->normalizeText((string) $product->description);
                $haystack = $name.' '.$category.' '.$description;
                $score = 0;

                if ($messageText && str_contains($messageText, $name)) {
                    $score += 12;
                }

                foreach ($keywords as $keyword) {
                    $keyword = $this->normalizeText($keyword);

                    if (strlen($keyword) < 2) {
                        continue;
                    }

                    if (str_contains($name, $keyword)) {
                        $score += 6;
                    } elseif (str_contains($category, $keyword)) {
                        $score += 5;
                    } elseif (str_contains($description, $keyword)) {
                        $score += 2;
                    } elseif ($this->hasNearWord($keyword, $haystack)) {
                        $score += 1;
                    }
                }

                return ['product' => $product, 'score' => $score];
            })
            ->filter(fn (array $row) => $row['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->map(fn (array $row) => $row['product'])
            ->values();
    }

    private function hasNearWord(string $keyword, string $haystack): bool
    {
        if (strlen($keyword) < 4) {
            return false;
        }

        preg_match_all('/[a-z0-9]+/u', $haystack, $matches);

        foreach ($matches[0] ?? [] as $word) {
            if (strlen($word) >= 4 && levenshtein($keyword, $word) <= 1) {
                return true;
            }
        }

        return false;
    }

    private function detectFoodCategory(string $text): ?array
    {
        $categories = [
            [
                'key' => 'spicy',
                'label' => 'món cay',
                'terms' => ['mon cay', 'cay', 'sa te', 'ot'],
            ],
            [
                'key' => 'drink',
                'label' => 'đồ uống',
                'terms' => ['do uong', 'nuoc uong', 'thuc uong', 'nuoc ep', 'tra', 'ca phe', 'giai khat', 'drink'],
            ],
            [
                'key' => 'seafood',
                'label' => 'món hải sản',
                'terms' => ['hai san', 'tom', 'muc', 'ca', 'ca chep', 'ca loc', 'lau hai san'],
            ],
            [
                'key' => 'starter',
                'label' => 'món khai vị',
                'terms' => ['khai vi', 'an nhe', 'mo dau', 'banh xeo'],
            ],
            [
                'key' => 'dessert',
                'label' => 'món tráng miệng',
                'terms' => ['trang mieng', 'mon ngot', 'trai cay', 'che', 'banh ngot'],
            ],
        ];

        foreach ($categories as $category) {
            if ($this->containsAny($text, $category['terms'])) {
                return $category;
            }
        }

        return null;
    }

    private function normalizeBooking(array $payload, string $message): array
    {
        $parameters = $this->extractParameters($payload);
        $date = $this->firstFilled($parameters, ['date', 'ngay', 'reservation_date']);
        $time = $this->firstFilled($parameters, ['time', 'gio', 'reservation_hour']);
        $explicitTime = $this->firstFilled($parameters, ['reservation_time', 'datetime', 'date_time', 'date-time', 'ngay_gio']);

        return [
            'customer_name' => $this->firstFilled($parameters, ['customer_name', 'full_name', 'name', 'ten_khach', 'ten']) ?: $this->extractName($message),
            'phone' => $this->normalizePhone($this->firstFilled($parameters, ['phone', 'phone_number', 'sdt', 'so_dien_thoai']) ?: $this->extractPhone($message)),
            'email' => $this->firstFilled($parameters, ['email']),
            'number_of_guests' => (int) ($this->firstFilled($parameters, ['number_of_guests', 'guests', 'people', 'so_luong_nguoi', 'so_khach']) ?: $this->extractGuests($message)),
            'reservation_time' => $this->parseReservationTime($explicitTime ?: trim($date.' '.$time)) ?: $this->parseNaturalReservationTime($message),
            'note' => $this->firstFilled($parameters, ['note', 'ghi_chu']),
        ];
    }

    private function missingBookingFields(array $booking): array
    {
        $missing = [];

        if (! $booking['customer_name']) {
            $missing[] = 'tên khách';
        }

        if (! $booking['phone']) {
            $missing[] = 'số điện thoại';
        }

        if ($booking['number_of_guests'] < 1) {
            $missing[] = 'số lượng khách';
        }

        if (! $booking['reservation_time']) {
            $missing[] = 'thời gian đặt bàn';
        }

        return $missing;
    }

    private function availableTablesForGuests(int $guests): Collection
    {
        return RestaurantTable::query()
            ->where('status', 'trống')
            ->where('seats', '>=', $guests)
            ->orderBy('seats')
            ->orderBy('table_code')
            ->get();
    }

    private function availableProducts(): Collection
    {
        return Product::with('category:id,name')
            ->where('status', 'available')
            ->orderBy('name')
            ->get();
    }

    private function formatProducts(Collection $products): string
    {
        return $products
            ->map(function (Product $product): string {
                $category = $product->category?->name ? " ({$product->category->name})" : '';
                $description = $product->description ? ' - '.$product->description : '';

                return sprintf('- %s%s: %sđ%s', $product->name, $category, number_format((float) $product->price, 0, ',', '.'), $description);
            })
            ->implode("\n");
    }

    private function productData(Collection $products): array
    {
        return $products
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category?->name,
                'price' => (float) $product->price,
                'description' => $product->description,
                'image_url' => $product->image_url,
            ])
            ->values()
            ->all();
    }

    private function productFromOrdinalContext(string $text, string $sessionId): ?Product
    {
        $products = $this->productsFromLastBotReply($sessionId);

        if ($products->isEmpty()) {
            return null;
        }

        $index = match (true) {
            $this->containsAny($text, ['dau tien', 'thu nhat', 'so 1', 'mon 1']) => 0,
            $this->containsAny($text, ['thu hai', 'so 2', 'mon 2']) => 1,
            $this->containsAny($text, ['thu ba', 'so 3', 'mon 3']) => 2,
            $this->containsAny($text, ['thu tu', 'so 4', 'mon 4']) => 3,
            default => null,
        };

        return $index !== null ? $products->get($index) : null;
    }

    private function productFromLastContext(string $sessionId): ?Product
    {
        return $this->productsFromLastBotReply($sessionId)->first();
    }

    private function productsFromLastBotReply(string $sessionId): Collection
    {
        $message = ChatbotLog::where('session_id', Str::limit($sessionId, 100, ''))
            ->where('sender', 'chatbot')
            ->whereIn('intent', ['food_recommendation', 'menu', 'best_selling', 'product_detail'])
            ->latest()
            ->value('message');

        if (! $message) {
            return collect();
        }

        $normalizedMessage = $this->normalizeText($message);

        return $this->availableProducts()
            ->map(function (Product $product) use ($normalizedMessage): array {
                $position = strpos($normalizedMessage, $this->normalizeText($product->name));

                return [
                    'product' => $product,
                    'position' => $position === false ? PHP_INT_MAX : $position,
                ];
            })
            ->filter(fn (array $row) => $row['position'] !== PHP_INT_MAX)
            ->sortBy('position')
            ->map(fn (array $row) => $row['product'])
            ->values();
    }

    private function restaurantProfile(): array
    {
        return [
            'name' => 'Nhà hàng Hoa Sen',
            'address' => env('RESTAURANT_ADDRESS', '100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long'),
            'phone' => env('RESTAURANT_PHONE', '0789661781'),
            'opening_hours' => env('RESTAURANT_OPENING_HOURS', '09:00 - 22:00 hằng ngày'),
            'reservation_hours' => env('RESTAURANT_RESERVATION_HOURS', '10:00 - 21:30 hằng ngày'),
            'parking' => env('RESTAURANT_PARKING', 'Nhà hàng có hỗ trợ khu vực gửi xe cho khách. Nếu đi ô tô, anh/chị nên gọi hotline trước để được hướng dẫn vị trí đậu xe thuận tiện.'),
        ];
    }

    private function extractParameters(array $payload): array
    {
        return array_replace(
            (array) Arr::get($payload, 'queryResult.parameters', []),
            (array) Arr::get($payload, 'parameters', []),
            (array) Arr::get($payload, 'slots', [])
        );
    }

    private function firstFilled(array $source, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = Arr::get($source, $key);

            if (is_array($value)) {
                $value = collect($value)->flatten()->first(fn ($item) => filled($item));
            }

            if (filled($value)) {
                return $value;
            }
        }

        return null;
    }

    private function parseReservationTime(mixed $value): ?Carbon
    {
        if (! filled($value)) {
            return null;
        }

        try {
            return Carbon::parse((string) $value, config('app.timezone'))->timezone(config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseNaturalReservationTime(string $message): ?Carbon
    {
        $text = $this->normalizeText($message);
        $hasDateHint = $this->containsAny($text, ['hom nay', 'toi nay', 'chieu nay', 'ngay mai', 'mai']);
        $hour = null;
        $minute = 0;

        if (preg_match('/\b([01]?\d|2[0-3])\s*(?:h|gio)\s*(\d{1,2})?\b/u', $text, $matches)) {
            $hour = (int) $matches[1];
            $minute = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : 0;
        } elseif ($this->containsAny($text, ['toi nay'])) {
            $hour = 19;
        }

        if ($hour === null && ! $hasDateHint) {
            return null;
        }

        $date = $this->containsAny($text, ['ngay mai', 'mai'])
            ? now()->addDay()
            : now();

        return $date->copy()->setTime($hour ?? 19, min($minute, 59));
    }

    private function extractMessage(array $payload): string
    {
        return (string) (
            Arr::get($payload, 'message')
            ?: Arr::get($payload, 'text')
            ?: Arr::get($payload, 'query')
            ?: Arr::get($payload, 'queryResult.queryText')
            ?: Arr::get($payload, 'message.text')
            ?: ''
        );
    }

    private function extractSessionId(array $payload): string
    {
        return (string) (
            Arr::get($payload, 'session_id')
            ?: Arr::get($payload, 'session')
            ?: Arr::get($payload, 'sender')
            ?: Str::uuid()
        );
    }

    private function extractConfidence(array $payload): ?float
    {
        $confidence = Arr::get($payload, 'confidence')
            ?: Arr::get($payload, 'queryResult.intentDetectionConfidence')
            ?: Arr::get($payload, 'nlu.intent.confidence')
            ?: Arr::get($payload, 'tracker.latest_message.intent.confidence');

        return is_numeric($confidence) ? (float) $confidence : null;
    }

    private function extractPhone(string $message): ?string
    {
        return preg_match('/(\+?84|0)[0-9 .-]{8,13}/', $message, $matches)
            ? $matches[0]
            : null;
    }

    private function extractGuests(string $message): ?int
    {
        return preg_match('/(\d{1,2})\s*(khách|khach|người|nguoi)/iu', $message, $matches)
            ? (int) $matches[1]
            : null;
    }

    private function extractName(string $message): ?string
    {
        return preg_match('/(?:tên|ten)\s+(?:là|la)?\s*([^\d,.;]+)/iu', $message, $matches)
            ? trim($matches[1])
            : null;
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '84')) {
            $digits = '0'.substr($digits, 2);
        }

        return strlen($digits) >= 10 ? $digits : null;
    }

    private function normalizeText(string $value): string
    {
        $value = Str::lower($value);
        $text = strtr($value, [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ]);
        $text = preg_replace('/\p{M}+/u', '', $text);

        return preg_replace('/\s+/', ' ', trim($text));
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            $needle = trim($needle);

            if ($needle === '') {
                continue;
            }

            $matched = strlen($needle) <= 3
                ? (bool) preg_match('/(^|[^a-z0-9])'.preg_quote($needle, '/').'($|[^a-z0-9])/u', $text)
                : str_contains($text, $needle);

            if ($matched) {
                return true;
            }
        }

        return false;
    }

    private function lastIntent(string $sessionId): ?string
    {
        return ChatbotLog::where('session_id', Str::limit($sessionId, 100, ''))
            ->whereNotNull('intent')
            ->latest()
            ->value('intent');
    }

    private function logMessage(
        string $sessionId,
        string $sender,
        string $message,
        ?string $intent = null,
        ?float $confidence = null,
        ?int $customerId = null,
        ?int $reservationId = null
    ): void {
        ChatbotLog::create([
            'customer_id' => $customerId,
            'reservation_id' => $reservationId,
            'session_id' => Str::limit($sessionId, 100, ''),
            'sender' => $sender,
            'message' => $message,
            'intent' => $intent,
            'confidence' => $confidence,
        ]);
    }

    private function rejectInvalidToken(Request $request): ?JsonResponse
    {
        $token = env('CHATBOT_WEBHOOK_TOKEN');

        if (! $token) {
            return null;
        }

        $incoming = $request->bearerToken() ?: $request->header('X-Chatbot-Token');

        return hash_equals($token, (string) $incoming)
            ? null
            : response()->json(['message' => 'Chatbot webhook token không hợp lệ.'], 401);
    }
}
