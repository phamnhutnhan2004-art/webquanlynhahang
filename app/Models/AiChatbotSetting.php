<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AiChatbotSetting extends Model
{
    public const DEFAULT_SYSTEM_PROMPT = <<<'PROMPT'
Bạn là Trợ lý AI của Nhà hàng Hoa Sen.

Luôn trả lời bằng tiếng Việt có dấu, giọng thân thiện, lịch sự và rõ ràng.
Ưu tiên hỗ trợ khách về thực đơn, món ăn, giá món, đặt bàn, đặt tiệc tại nhà, giờ mở cửa, địa chỉ, thanh toán và các dịch vụ của nhà hàng.
Khi có dữ liệu hệ thống được cung cấp, hãy ưu tiên dữ liệu đó hơn kiến thức chung. Nếu dữ liệu hệ thống không có thông tin phù hợp, hãy nói rõ là hiện chưa tìm thấy trong hệ thống rồi dùng Gemini để tư vấn tự nhiên.
Nếu khách hỏi ngoài phạm vi nhà hàng, vẫn có thể trả lời ngắn gọn nhưng nên kéo cuộc trò chuyện quay lại nhu cầu ăn uống, đặt bàn hoặc dịch vụ của Nhà hàng Hoa Sen.
Không bịa món ăn, giá tiền, bàn trống hoặc chính sách nếu dữ liệu hệ thống không cung cấp.
PROMPT;

    protected $fillable = [
        'is_enabled',
        'provider',
        'model',
        'encrypted_api_key',
        'system_prompt',
        'temperature',
        'max_output_tokens',
        'last_checked_at',
        'last_status',
        'last_error',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'temperature' => 'decimal:2',
        'max_output_tokens' => 'integer',
        'last_checked_at' => 'datetime',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            [
                'is_enabled' => true,
                'provider' => 'gemini',
                'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
                'system_prompt' => self::DEFAULT_SYSTEM_PROMPT,
                'temperature' => 0.40,
                'max_output_tokens' => 900,
            ]
        );
    }

    public function setApiKey(?string $apiKey): void
    {
        $this->encrypted_api_key = filled($apiKey) ? Crypt::encryptString($apiKey) : null;
    }

    public function apiKey(): ?string
    {
        if ($this->encrypted_api_key) {
            try {
                return Crypt::decryptString($this->encrypted_api_key);
            } catch (\Throwable) {
                return null;
            }
        }

        return env('GEMINI_API_KEY') ?: null;
    }

    public function hasApiKey(): bool
    {
        return filled($this->apiKey());
    }

    public function maskedApiKey(): string
    {
        $key = $this->apiKey();

        if (! $key) {
            return 'Chưa cấu hình';
        }

        return substr($key, 0, 6).'...'.substr($key, -4);
    }
}
