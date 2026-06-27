<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminPaymentMethodController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePaymentMethod($request);

        if ($request->hasFile('qr_image')) {
            $data['qr_image'] = $request->file('qr_image')->store('payment-methods', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['transfer_content_template'] = $data['transfer_content_template'] ?: 'THANHTOAN_[ORDER_CODE]';

        PaymentMethod::create($data);

        return back()->with('status', 'Đã thêm phương thức thanh toán.');
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $data = $this->validatePaymentMethod($request);

        if ($request->hasFile('qr_image')) {
            $this->deleteQrImage($paymentMethod);
            $data['qr_image'] = $request->file('qr_image')->store('payment-methods', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['transfer_content_template'] = $data['transfer_content_template'] ?: 'THANHTOAN_[ORDER_CODE]';

        $paymentMethod->update($data);

        return back()->with('status', 'Đã cập nhật phương thức thanh toán.');
    }

    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        $this->deleteQrImage($paymentMethod);
        $paymentMethod->delete();

        return back()->with('status', 'Đã xóa phương thức thanh toán.');
    }

    private function validatePaymentMethod(Request $request): array
    {
        return $request->validate([
            'method_key' => ['required', Rule::in(['cash', 'bank_transfer', 'qr', 'e_wallet'])],
            'display_name' => ['required', 'string', 'max:120'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'account_holder' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'transfer_content_template' => ['nullable', 'string', 'max:180'],
            'qr_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [
            'display_name.required' => 'Vui lòng nhập tên phương thức thanh toán.',
            'qr_image.image' => 'Ảnh QR phải là tệp hình ảnh.',
        ]);
    }

    private function deleteQrImage(PaymentMethod $paymentMethod): void
    {
        if ($paymentMethod->qr_image && Storage::disk('public')->exists($paymentMethod->qr_image)) {
            Storage::disk('public')->delete($paymentMethod->qr_image);
        }
    }
}
