<?php

namespace App\Services;

use App\Models\AiChatbotSetting;
use App\Models\ChatbotLog;
use App\Models\HomeParty;
use App\Models\MenuGallery;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\RestaurantTable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeminiChatbotService
{
    private const GEMINI_ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    public function reply(string $message, string $sessionId, array $payload = []): array
    {
        $setting = AiChatbotSetting::current();

        if (! $setting->is_enabled) {
            return $this->unavailable('AI Chatbot hiện đang được tắt trong trang quản trị.');
        }

        if (! $setting->hasApiKey()) {
            return $this->unavailable('AI Chatbot chưa được cấu hình Gemini API Key. Vui lòng liên hệ quản trị viên.');
        }

        try {
            $response = $this->sendToGemini($setting, $message, $sessionId, $payload);
            $text = $this->extractText($response);

            if (! filled($text)) {
                return $this->unavailable();
            }

            return [
                'reply' => $text,
                'intent' => 'gemini_ai',
                'provider' => 'gemini',
                'model' => $setting->model,
                'usage' => $this->extractUsage($response),
                'data' => [
                    'source' => 'gemini',
                    'ai_enabled' => true,
                ],
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->unavailable();
        }
    }

    public function checkConnection(?AiChatbotSetting $setting = null): array
    {
        $setting ??= AiChatbotSetting::current();

        if (! $setting->hasApiKey()) {
            return [
                'ok' => false,
                'message' => 'Chưa có Gemini API Key để kiểm tra kết nối.',
            ];
        }

        try {
            $response = $this->sendToGemini(
                $setting,
                'Hãy trả lời ngắn gọn: Kết nối Gemini của Nhà hàng Hoa Sen đã sẵn sàng.',
                'admin-test-'.Str::uuid(),
                [],
                false
            );

            return [
                'ok' => filled($this->extractText($response)),
                'message' => filled($this->extractText($response))
                    ? 'Kết nối Gemini thành công.'
                    : 'Gemini đã phản hồi nhưng chưa có nội dung trả lời.',
            ];
        } catch (RequestException $exception) {
            return [
                'ok' => false,
                'message' => $exception->response?->json('error.message') ?: 'Gemini API từ chối yêu cầu kiểm tra.',
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return [
                'ok' => false,
                'message' => 'Không thể kết nối Gemini API. Vui lòng kiểm tra internet, API key hoặc quota.',
            ];
        }
    }

    private function sendToGemini(
        AiChatbotSetting $setting,
        string $message,
        string $sessionId,
        array $payload,
        bool $includeBusinessContext = true
    ): array {
        $model = trim($setting->model ?: 'gemini-2.5-flash');
        $url = sprintf(self::GEMINI_ENDPOINT, rawurlencode($model));
        $context = $includeBusinessContext ? $this->businessContext($payload) : '';

        $body = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => trim($setting->system_prompt ?: AiChatbotSetting::DEFAULT_SYSTEM_PROMPT)],
                ],
            ],
            'contents' => $this->contents($message, $sessionId, $context),
            'generationConfig' => [
                'temperature' => (float) $setting->temperature,
                'maxOutputTokens' => (int) $setting->max_output_tokens,
            ],
        ];

        return Http::timeout(25)
            ->retry(1, 300)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $setting->apiKey(),
            ])
            ->post($url, $body)
            ->throw()
            ->json();
    }

    private function contents(string $message, string $sessionId, string $context): array
    {
        $history = ChatbotLog::query()
            ->where('session_id', Str::limit($sessionId, 100, ''))
            ->latest()
            ->limit(8)
            ->get()
            ->reverse()
            ->map(fn (ChatbotLog $log) => [
                'role' => $log->sender === 'chatbot' ? 'model' : 'user',
                'parts' => [
                    ['text' => $log->message],
                ],
            ])
            ->values()
            ->all();

        $currentMessage = trim($context."\n\nTin nhắn hiện tại của khách:\n".$message);

        $history[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $currentMessage],
            ],
        ];

        return $history;
    }

    private function businessContext(array $payload = []): string
    {
        $products = Product::with('category:id,name')
            ->where('status', 'available')
            ->orderBy('name')
            ->limit(40)
            ->get()
            ->map(fn (Product $product) => sprintf(
                '- %s | Nhóm: %s | Giá: %sđ | Mô tả: %s',
                $product->name,
                $product->category?->name ?: 'Chưa phân loại',
                number_format((float) $product->price, 0, ',', '.'),
                $product->description ?: 'Chưa có mô tả'
            ))
            ->implode("\n");

        $bestSelling = $this->bestSellingProducts()
            ->map(fn (array $row) => sprintf('- %s: %s phần đã bán', $row['name'], $row['quantity']))
            ->implode("\n");

        $tables = RestaurantTable::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => "- {$row->status}: {$row->total} bàn")
            ->implode("\n");

        $availableTables = RestaurantTable::query()
            ->where('status', 'trống')
            ->orderBy('area')
            ->orderBy('seats')
            ->limit(20)
            ->get(['table_code', 'table_name', 'area', 'seats'])
            ->map(fn (RestaurantTable $table) => "- {$table->table_code} {$table->table_name}, khu {$table->area}, {$table->seats} ghế")
            ->implode("\n");

        $paymentMethods = class_exists(PaymentMethod::class)
            ? PaymentMethod::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (PaymentMethod $method) => '- '.$method->display_name.($method->bank_name ? " ({$method->bank_name})" : ''))
                ->implode("\n")
            : '';

        $menuGalleries = MenuGallery::latest()
            ->limit(5)
            ->get()
            ->map(fn (MenuGallery $menu) => '- '.$menu->title.($menu->description ? ': '.$menu->description : ''))
            ->implode("\n");

        $homePartyStats = class_exists(HomeParty::class)
            ? 'Tổng yêu cầu đặt tiệc tại nhà: '.HomeParty::count().'. Yêu cầu mới/chờ xử lý: '.HomeParty::whereIn('status', ['chờ xác nhận', 'đã xác nhận', 'đang chuẩn bị'])->count().'.'
            : 'Nhà hàng có hỗ trợ đặt tiệc tại nhà.';

        $bookingPayload = $this->bookingPayloadSummary($payload);
        $address = env('RESTAURANT_ADDRESS', '100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long');
        $phone = env('RESTAURANT_PHONE', '0789661781');
        $openingHours = env('RESTAURANT_OPENING_HOURS', '09:00 - 22:00 hằng ngày');
        $reservationHours = env('RESTAURANT_RESERVATION_HOURS', '10:00 - 21:30 hằng ngày');

        return trim(<<<CONTEXT
Dữ liệu hệ thống Nhà hàng Hoa Sen hiện có:

Thông tin nhà hàng:
- Tên: Nhà hàng Hoa Sen
- Địa chỉ: {$address}
- Hotline: {$phone}
- Giờ mở cửa: {$openingHours}
- Giờ nhận đặt bàn: {$reservationHours}

Thực đơn đang bán:
{$products}

Món bán chạy theo dữ liệu đơn hàng:
{$bestSelling}

Trạng thái bàn:
{$tables}

Bàn trống có thể tư vấn:
{$availableTables}

Phương thức thanh toán đang bật:
{$paymentMethods}

Menu hình ảnh/tài liệu:
{$menuGalleries}

Đặt tiệc tại nhà:
{$homePartyStats}

Thông tin form đặt bàn khách vừa gửi nếu có:
{$bookingPayload}

Hướng dẫn trả lời:
- Khi khách hỏi giá, món ăn, hải sản, đồ uống, món bán chạy hoặc bàn trống, hãy dựa trên dữ liệu hệ thống ở trên trước.
- Nếu danh sách dữ liệu trống hoặc không có món/bàn phù hợp, hãy nói rõ là hiện chưa tìm thấy trong hệ thống và gợi ý khách gọi hotline.
- Không tự tạo đơn đặt bàn nếu chưa có đủ tên, số điện thoại, số khách và thời gian.
CONTEXT);
    }

    private function bestSellingProducts(): Collection
    {
        return OrderItem::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(8)
            ->get()
            ->map(fn (OrderItem $item) => [
                'name' => $item->product?->name ?: 'Món đã xóa',
                'quantity' => (int) $item->total_quantity,
            ]);
    }

    private function bookingPayloadSummary(array $payload): string
    {
        $parameters = $payload['parameters'] ?? $payload['queryResult']['parameters'] ?? [];
        $systemResult = $payload['system_result'] ?? null;
        $lines = collect();

        if (is_array($systemResult) && $systemResult !== []) {
            $lines->push('Kết quả xử lý đặt bàn trong hệ thống: '.json_encode($systemResult, JSON_UNESCAPED_UNICODE));
        }

        if (! is_array($parameters) || $parameters === []) {
            return $lines->isNotEmpty() ? $lines->implode("\n") : 'Không có dữ liệu form đặt bàn.';
        }

        collect($parameters)
            ->map(fn ($value, string $key) => '- '.$key.': '.(is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value))
            ->each(fn (string $line) => $lines->push($line));

        return $lines->implode("\n");
    }

    private function extractText(array $response): string
    {
        return collect($response['candidates'][0]['content']['parts'] ?? [])
            ->pluck('text')
            ->filter()
            ->implode("\n");
    }

    private function extractUsage(array $response): array
    {
        $usage = $response['usageMetadata'] ?? [];

        return [
            'prompt_tokens' => $usage['promptTokenCount'] ?? null,
            'completion_tokens' => $usage['candidatesTokenCount'] ?? null,
            'total_tokens' => $usage['totalTokenCount'] ?? null,
        ];
    }

    private function unavailable(string $message = 'Xin lỗi, hệ thống AI hiện đang tạm thời không khả dụng. Vui lòng thử lại sau.'): array
    {
        return [
            'reply' => $message,
            'intent' => 'ai_unavailable',
            'provider' => 'gemini',
            'model' => AiChatbotSetting::current()->model,
            'usage' => [
                'prompt_tokens' => null,
                'completion_tokens' => null,
                'total_tokens' => null,
            ],
            'data' => [
                'source' => 'fallback',
                'ai_enabled' => false,
            ],
        ];
    }
}
