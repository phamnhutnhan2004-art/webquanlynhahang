<?php

namespace App\Http\Controllers;

use App\Models\ThemeSetting;
use App\Models\WebsitePageSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminThemeSettingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        ThemeSetting::current()
            ->forceFill(['settings' => $this->validateTheme($request)])
            ->save();

        return back()->with('status', 'Da luu cai dat giao dien chung.');
    }

    public function updatePage(Request $request, string $pageKey): RedirectResponse
    {
        abort_unless(array_key_exists($pageKey, WebsitePageSetting::PAGE_OPTIONS), 404);

        $page = WebsitePageSetting::current($pageKey);
        $settings = $page->resolvedSettings();
        $settings['theme'] = $this->validateTheme($request);

        $page->update(['settings' => $settings]);

        return back()->with('status', 'Da luu cai dat giao dien cho trang.');
    }

    public function reset(): RedirectResponse
    {
        ThemeSetting::current()->resetToDefaults();

        return back()->with('status', 'Da khoi phuc giao dien mac dinh.');
    }

    public function resetPage(string $pageKey): RedirectResponse
    {
        abort_unless(array_key_exists($pageKey, WebsitePageSetting::PAGE_OPTIONS), 404);

        $page = WebsitePageSetting::current($pageKey);
        $settings = $page->resolvedSettings();
        unset($settings['theme']);

        $page->update(['settings' => $settings]);

        return back()->with('status', 'Da khoi phuc giao dien trang theo cai dat chung.');
    }

    public function updateAuthPage(Request $request): RedirectResponse
    {
        $page = WebsitePageSetting::current('auth');
        $settings = $page->settings ?? [];
        $settings['auth_page'] = $this->validateAuthPage($request);

        $page->update(['settings' => $settings]);

        return back()->with('status', 'Đã lưu giao diện đăng nhập / đăng ký.');
    }

    public function resetAuthPage(): RedirectResponse
    {
        $page = WebsitePageSetting::current('auth');
        $settings = $page->settings ?? [];
        unset($settings['auth_page']);

        $page->update(['settings' => $settings]);

        return back()->with('status', 'Đã khôi phục giao diện đăng nhập / đăng ký mặc định.');
    }

    private function validateTheme(Request $request): array
    {
        $fonts = array_keys(ThemeSetting::fontOptions());

        $data = $request->validate([
            'colors' => ['required', 'array'],
            'colors.primary_title' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.secondary_title' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.menu_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.button_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.button_text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.footer_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.button_hover_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.menu_hover' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],

            'fonts' => ['required', 'array'],
            'fonts.heading' => ['required', Rule::in($fonts)],
            'fonts.body' => ['required', Rule::in($fonts)],
            'fonts.menu' => ['required', Rule::in($fonts)],

            'font_sizes' => ['required', 'array'],
            'font_sizes.hero_title' => ['required', 'integer', 'min:32', 'max:112'],
            'font_sizes.section_title' => ['required', 'integer', 'min:20', 'max:72'],
            'font_sizes.body' => ['required', 'integer', 'min:12', 'max:24'],
            'font_sizes.menu' => ['required', 'integer', 'min:10', 'max:22'],
            'font_sizes.button' => ['required', 'integer', 'min:12', 'max:24'],

            'banner' => ['required', 'array'],
            'banner.height' => ['required', 'integer', 'min:320', 'max:900'],
            'banner.overlay_opacity' => ['required', 'integer', 'min:0', 'max:95'],
            'banner.title_position' => ['required', Rule::in(['top', 'center', 'bottom'])],
            'banner.content_align' => ['required', Rule::in(['left', 'center', 'right'])],
            'banner.padding_y' => ['required', 'integer', 'min:24', 'max:160'],

            'button' => ['required', 'array'],
            'button.radius' => ['required', 'integer', 'min:0', 'max:40'],
            'button.shadow' => ['nullable', 'boolean'],
            'button.effect' => ['required', Rule::in(['none', 'lift'])],

            'spacing' => ['required', 'array'],
            'spacing.page_margin' => ['required', 'integer', 'min:0', 'max:80'],
            'spacing.section_padding' => ['required', 'integer', 'min:20', 'max:140'],
            'spacing.section_gap' => ['required', 'integer', 'min:8', 'max:80'],
            'spacing.title_content_gap' => ['required', 'integer', 'min:4', 'max:56'],
        ], [
            'regex' => 'Mau sac phai co dinh dang HEX, vi du #f6df9d.',
        ]);

        $data['button']['shadow'] = $request->boolean('button.shadow');

        return $data;
    }

    private function validateAuthPage(Request $request): array
    {
        $data = $request->validate([
            'content' => ['required', 'array'],
            'content.badge' => ['required', 'string', 'max:80'],
            'content.visual_title' => ['required', 'string', 'max:160'],
            'content.visual_description' => ['required', 'string', 'max:320'],
            'content.eyebrow' => ['required', 'string', 'max:80'],
            'content.heading' => ['required', 'string', 'max:140'],
            'content.description' => ['required', 'string', 'max:220'],
            'content.login_tab' => ['required', 'string', 'max:40'],
            'content.register_tab' => ['required', 'string', 'max:40'],
            'content.login_button' => ['required', 'string', 'max:60'],
            'content.register_button' => ['required', 'string', 'max:60'],
            'content.benefits' => ['required', 'array', 'size:3'],
            'content.benefits.*.icon' => ['required', 'string', 'max:40'],
            'content.benefits.*.title' => ['required', 'string', 'max:80'],
            'content.benefits.*.text' => ['required', 'string', 'max:180'],

            'style' => ['required', 'array'],
            'style.background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.shell_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.panel_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.heading_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.body_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.muted_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.visual_text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.link_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.tab_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.tab_text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.tab_active_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.tab_active_text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.button_background' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.button_text' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.button_hover' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.input_border' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.border_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.visual_image' => ['nullable', 'string', 'max:255'],
            'style.visual_overlay_start' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.visual_overlay_end' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'style.visual_overlay_opacity' => ['required', 'integer', 'min:0', 'max:100'],
            'style.radius' => ['required', 'integer', 'min:0', 'max:24'],
        ], [
            'regex' => 'Màu sắc phải có định dạng HEX, ví dụ #111111.',
        ]);

        return array_replace_recursive(WebsitePageSetting::authPageDefaults(), $data);
    }
}
