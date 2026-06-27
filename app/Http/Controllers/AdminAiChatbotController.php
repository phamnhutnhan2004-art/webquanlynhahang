<?php

namespace App\Http\Controllers;

use App\Models\AiChatbotSetting;
use App\Services\GeminiChatbotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAiChatbotController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'is_enabled' => ['nullable', 'boolean'],
            'api_key' => ['nullable', 'string', 'max:500'],
            'clear_api_key' => ['nullable', 'boolean'],
            'model' => ['required', 'string', 'max:80', Rule::in([
                'gemini-3.5-flash',
                'gemini-2.5-flash',
                'gemini-2.5-pro',
                'gemini-2.0-flash',
                'gemini-1.5-flash',
            ])],
            'temperature' => ['required', 'numeric', 'min:0', 'max:1'],
            'max_output_tokens' => ['required', 'integer', 'min:200', 'max:4096'],
            'system_prompt' => ['required', 'string', 'min:20', 'max:8000'],
        ]);

        $setting = AiChatbotSetting::current();
        $setting->fill([
            'is_enabled' => (bool) ($data['is_enabled'] ?? false),
            'provider' => 'gemini',
            'model' => $data['model'],
            'temperature' => $data['temperature'],
            'max_output_tokens' => $data['max_output_tokens'],
            'system_prompt' => $data['system_prompt'],
        ]);

        if ($request->boolean('clear_api_key')) {
            $setting->setApiKey(null);
        } elseif (filled($data['api_key'] ?? null)) {
            $setting->setApiKey($data['api_key']);
        }

        $setting->save();

        return back()->with('status', 'Đã cập nhật cấu hình AI Chatbot.');
    }

    public function test(GeminiChatbotService $gemini): RedirectResponse
    {
        $setting = AiChatbotSetting::current();
        $result = $gemini->checkConnection($setting);

        $setting->forceFill([
            'last_checked_at' => now(),
            'last_status' => $result['ok'] ? 'success' : 'failed',
            'last_error' => $result['ok'] ? null : $result['message'],
        ])->save();

        return back()->with($result['ok'] ? 'status' : 'error', $result['message']);
    }
}
