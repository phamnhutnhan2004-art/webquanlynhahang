<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    protected $fillable = [
        'method_key',
        'display_name',
        'bank_name',
        'account_holder',
        'account_number',
        'transfer_content_template',
        'qr_image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function getQrImageUrlAttribute(): ?string
    {
        if (! $this->qr_image) {
            return null;
        }

        return Storage::disk('public')->url($this->qr_image);
    }

    public function transferContentFor(Order $order): string
    {
        $template = $this->transfer_content_template ?: 'THANHTOAN_[ORDER_CODE]';

        return str_replace(
            ['[ORDER_CODE]', '[Mã đơn hàng]', '[MA_DON_HANG]'],
            $order->order_code,
            $template
        );
    }

    public function methodLabel(): string
    {
        return match ($this->method_key) {
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'qr' => 'Quét mã QR',
            'e_wallet' => 'Ví điện tử',
            default => $this->display_name,
        };
    }
}
