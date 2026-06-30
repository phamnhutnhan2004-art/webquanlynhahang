<?php

namespace App\Http\Controllers;

use App\Models\WebsiteMenuItem;
use App\Models\WebsitePageSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminWebsiteBuilderController extends Controller
{
    public function updatePage(Request $request, string $pageKey): RedirectResponse
    {
        abort_unless(array_key_exists($pageKey, WebsitePageSetting::PAGE_OPTIONS), 404);

        $page = WebsitePageSetting::current($pageKey);
        $settings = $page->resolvedSettings();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'banner.subtitle' => ['nullable', 'string', 'max:180'],
            'banner.title' => ['required', 'string', 'max:180'],
            'banner.content' => ['nullable', 'string', 'max:1000'],
            'banner.primary_button_label' => ['nullable', 'string', 'max:80'],
            'banner.primary_button_url' => ['nullable', 'string', 'max:255'],
            'banner.secondary_button_label' => ['nullable', 'string', 'max:80'],
            'banner.secondary_button_url' => ['nullable', 'string', 'max:255'],
            'banner.position' => ['required', Rule::in(['left', 'center', 'right'])],
            'banner.height' => ['required', 'integer', 'min:320', 'max:900'],
            'banner.overlay' => ['required', 'integer', 'min:0', 'max:95'],
            'banner.image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'style.background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.font_family' => ['required', Rule::in(['Be Vietnam Pro', 'Poppins', 'Inter', 'Roboto', 'Open Sans', 'Montserrat'])],
            'style.title_size' => ['required', 'integer', 'min:28', 'max:112'],
            'style.content_size' => ['required', 'integer', 'min:13', 'max:28'],
            'style.radius' => ['required', 'integer', 'min:0', 'max:40'],
            'style.shadow' => ['nullable', 'boolean'],
            'style.spacing' => ['required', 'integer', 'min:16', 'max:120'],
            'style.width' => ['required', 'integer', 'min:860', 'max:1600'],
            'style.animation' => ['required', Rule::in(['none', 'fade-up', 'zoom', 'slide'])],
            'button.background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button.text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button.hover' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button.radius' => ['required', 'integer', 'min:0', 'max:40'],
            'button.shadow' => ['nullable', 'boolean'],
            'button.icon' => ['nullable', 'string', 'max:80'],
            'button.size' => ['required', 'integer', 'min:12', 'max:24'],
            'text.slogan' => ['nullable', 'string', 'max:180'],
            'text.hotline' => ['nullable', 'string', 'max:50'],
            'text.address' => ['nullable', 'string', 'max:255'],
            'text.email' => ['nullable', 'email', 'max:150'],
            'text.footer' => ['nullable', 'string', 'max:1000'],
            'sections' => ['nullable', 'array'],
            'sections.*.label' => ['required_with:sections', 'string', 'max:120'],
            'sections.*.visible' => ['nullable', 'boolean'],
            'sections.*.order' => ['required_with:sections', 'integer', 'min:0', 'max:999'],
        ], $this->messages());

        $settings['banner'] = array_replace($settings['banner'], $data['banner']);
        $settings['style'] = array_replace($settings['style'], $data['style'], [
            'shadow' => $request->boolean('style.shadow'),
        ]);
        $settings['button'] = array_replace($settings['button'], $data['button'], [
            'shadow' => $request->boolean('button.shadow'),
        ]);
        $settings['text'] = array_replace($settings['text'], $data['text']);

        if ($request->hasFile('banner.image_file')) {
            $settings['banner']['image'] = $request->file('banner.image_file')->store('website-builder/banners', 'public');
        }

        if (isset($data['sections'])) {
            $settings['sections'] = collect($data['sections'])
                ->map(fn (array $section) => [
                    'label' => $section['label'],
                    'visible' => (bool) ($section['visible'] ?? false),
                    'order' => (int) $section['order'],
                ])
                ->all();
        }

        $page->update([
            'title' => $data['title'],
            'settings' => $settings,
        ]);

        return back()->with('status', 'Đã lưu cấu hình giao diện trang.');
    }

    public function resetPage(string $pageKey): RedirectResponse
    {
        abort_unless(array_key_exists($pageKey, WebsitePageSetting::PAGE_OPTIONS), 404);

        WebsitePageSetting::current($pageKey)->resetToDefaults();

        return back()->with('status', 'Đã khôi phục mặc định trang.');
    }

    public function storeMenuItem(Request $request): RedirectResponse
    {
        WebsiteMenuItem::create($this->validatedMenu($request));

        return back()->with('status', 'Đã thêm menu website.');
    }

    public function updateMenuItem(Request $request, WebsiteMenuItem $menuItem): RedirectResponse
    {
        $menuItem->update($this->validatedMenu($request));

        return back()->with('status', 'Đã cập nhật menu website.');
    }

    public function destroyMenuItem(WebsiteMenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return back()->with('status', 'Đã xóa menu website.');
    }

    private function validatedMenu(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:120'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_visible' => ['nullable', 'boolean'],
            'target' => ['required', Rule::in(['_self', '_blank'])],
        ], $this->messages());

        $data['is_visible'] = $request->boolean('is_visible');

        return $data;
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'required_with' => ':attribute là bắt buộc.',
            'email' => ':attribute không đúng định dạng.',
            'image' => ':attribute phải là hình ảnh.',
            'mimes' => ':attribute chỉ nhận JPG, PNG hoặc WEBP.',
            'max' => ':attribute không được vượt quá giới hạn.',
            'min' => ':attribute không được nhỏ hơn :min.',
            'integer' => ':attribute phải là số nguyên.',
            'regex' => ':attribute phải là mã màu hợp lệ.',
            'attributes.title' => 'Tên trang',
            'attributes.banner.title' => 'Tiêu đề banner',
            'attributes.banner.image_file' => 'Ảnh banner',
            'attributes.label' => 'Tên menu',
        ];
    }
}
