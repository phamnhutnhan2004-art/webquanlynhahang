<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiChatbotSetting;
use App\Models\ChatbotLog;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Services\GeminiChatbotService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(private readonly GeminiChatbotService $gemini)
    {
    }

    public function message(Request $request): JsonResponse
    {
        if ($blocked = $this->rejectInvalidToken($request)) {
            return $blocked;
        }

        $result = $this->handle($request);

        return response()->json($result);
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
        $setting = AiChatbotSetting::current();

        return response()->json([
            'restaurant' => $this->restaurantProfile(),
            'ai' => [
                'enabled' => $setting->is_enabled,
                'provider' => 'gemini',
                'model' => $setting->model,
                'configured' => $setting->hasApiKey(),
            ],
            'quick_replies' => [
                'Giờ mở cửa',
                'Gửi thực đơn',
                'Có món hải sản nào?',
                'Món nào bán chạy?',
                'Còn bàn trống không?',
                'Đặt tiệc tại nhà',
            ],
            'webhook' => url('/api/chatbot/webhook'),
        ]);
    }

    private function handle(Request $request): array
    {
        $payload = $request->all();
        $message = trim($this->extractMessage($payload));
        $sessionId = $this->extractSessionId($payload);
        $customerId = $request->user()?->customer?->id;
        $bookingResult = $this->createReservationFromStructuredPayload($payload);

        if ($bookingResult) {
            $payload['system_result'] = $bookingResult;
        }

        $result = $this->gemini->reply($message ?: 'Khách vừa gửi yêu cầu hỗ trợ nhà hàng.', $sessionId, $payload);
        $usage = $result['usage'] ?? [];

        $this->logMessage(
            $sessionId,
            'khách hàng',
            $message ?: 'Yêu cầu từ chatbot',
            'user_message',
            null,
            $customerId,
            $bookingResult['reservation_id'] ?? null
        );

        $this->logMessage(
            $sessionId,
            'chatbot',
            $result['reply'],
            $result['intent'] ?? 'gemini_ai',
            null,
            $customerId,
            $bookingResult['reservation_id'] ?? null,
            $result['model'] ?? null,
            $usage['prompt_tokens'] ?? null,
            $usage['completion_tokens'] ?? null,
            $usage['total_tokens'] ?? null,
            [
                'provider' => $result['provider'] ?? 'gemini',
                'source' => $result['data']['source'] ?? null,
                'booking_result' => $bookingResult,
            ]
        );

        return $result + [
            'session_id' => $sessionId,
            'booking' => $bookingResult,
        ];
    }

    private function createReservationFromStructuredPayload(array $payload): ?array
    {
        $parameters = $this->extractParameters($payload);

        if ($parameters === []) {
            return null;
        }

        $name = $this->firstFilled($parameters, ['customer_name', 'full_name', 'name', 'ten_khach', 'ten']);
        $phone = $this->normalizePhone($this->firstFilled($parameters, ['phone', 'phone_number', 'sdt', 'so_dien_thoai']));
        $email = $this->firstFilled($parameters, ['email']);
        $guests = (int) ($this->firstFilled($parameters, ['number_of_guests', 'guests', 'people', 'so_luong_nguoi', 'so_khach']) ?: 0);
        $reservationTime = $this->parseReservationTime($this->firstFilled($parameters, ['reservation_time', 'datetime', 'date_time', 'ngay_gio']));

        if (! $name || ! $phone || $guests < 1 || ! $reservationTime) {
            return [
                'created' => false,
                'message' => 'Thiếu tên khách, số điện thoại, số khách hoặc thời gian đặt bàn.',
            ];
        }

        if ($reservationTime->lessThanOrEqualTo(now())) {
            return [
                'created' => false,
                'message' => 'Thời gian đặt bàn cần ở tương lai.',
            ];
        }

        return DB::transaction(function () use ($name, $phone, $email, $guests, $reservationTime, $parameters): array {
            $table = RestaurantTable::query()
                ->whereIn('status', ['trống', 'trá»‘ng'])
                ->where('seats', '>=', $guests)
                ->orderBy('seats')
                ->orderBy('table_code')
                ->lockForUpdate()
                ->first();

            if (! $table) {
                return [
                    'created' => false,
                    'message' => 'Hiện chưa có bàn trống phù hợp với số khách này.',
                    'number_of_guests' => $guests,
                ];
            }

            $customer = Customer::firstOrCreate(
                ['phone' => $phone],
                [
                    'full_name' => $name,
                    'email' => $email,
                    'note' => 'Khách đặt bàn qua AI Chatbot',
                ]
            );

            $reservation = Reservation::create([
                'customer_id' => $customer->id,
                'guest_name' => $customer->full_name,
                'guest_phone' => $customer->phone,
                'guest_email' => $customer->email,
                'customer_type' => $customer->user_id ? 'khách thành viên' : 'khách tiềm năng',
                'table_id' => $table->id,
                'reservation_code' => 'AI'.now()->format('YmdHis').Str::upper(Str::random(4)),
                'reservation_time' => $reservationTime,
                'number_of_guests' => $guests,
                'note' => Arr::get($parameters, 'note') ?: 'Đặt bàn tự động từ AI Chatbot',
                'source' => 'chatbot',
                'status' => 'chờ xác nhận',
            ]);

            $table->update(['status' => 'đã đặt']);

            return [
                'created' => true,
                'message' => 'Đã tạo yêu cầu đặt bàn và đang chờ nhân viên xác nhận.',
                'reservation_id' => $reservation->id,
                'reservation_code' => $reservation->reservation_code,
                'table' => $table->table_code,
                'reservation_time' => $reservationTime->format('d/m/Y H:i'),
                'number_of_guests' => $guests,
            ];
        });
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

    private function restaurantProfile(): array
    {
        return [
            'name' => 'Nhà hàng Hoa Sen',
            'address' => env('RESTAURANT_ADDRESS', '100k Đ. Võ Văn Kiệt, Phường Long Châu, Vĩnh Long'),
            'phone' => env('RESTAURANT_PHONE', '0789661781'),
            'opening_hours' => env('RESTAURANT_OPENING_HOURS', '09:00 - 22:00 hằng ngày'),
            'reservation_hours' => env('RESTAURANT_RESERVATION_HOURS', '10:00 - 21:30 hằng ngày'),
        ];
    }

    private function logMessage(
        string $sessionId,
        string $sender,
        string $message,
        ?string $intent = null,
        ?float $confidence = null,
        ?int $customerId = null,
        ?int $reservationId = null,
        ?string $model = null,
        ?int $promptTokens = null,
        ?int $completionTokens = null,
        ?int $totalTokens = null,
        ?array $metadata = null
    ): void {
        ChatbotLog::create([
            'customer_id' => $customerId,
            'reservation_id' => $reservationId,
            'session_id' => Str::limit($sessionId, 100, ''),
            'sender' => $sender,
            'message' => $message,
            'intent' => $intent,
            'confidence' => $confidence,
            'model' => $model,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $totalTokens,
            'metadata' => $metadata,
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
