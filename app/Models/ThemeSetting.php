<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ThemeSetting extends Model
{
    public const FONT_OPTIONS = [
        'Be Vietnam Pro',
        'Poppins',
        'Inter',
        'Roboto',
        'Open Sans',
        'Montserrat',
    ];

    public const DEFAULTS = [
        'colors' => [
            'primary_title' => '#f6df9d',
            'secondary_title' => '#f6df9d',
            'menu_background' => '#0e3b32',
            'background' => '#fffaf0',
            'button_background' => '#d9a441',
            'button_text' => '#2c1b12',
            'text' => '#221812',
            'footer_background' => '#0e3b32',
            'button_hover_background' => '#f0bd55',
            'menu_hover' => '#f6df9d',
        ],
        'fonts' => [
            'heading' => 'Be Vietnam Pro',
            'body' => 'Be Vietnam Pro',
            'menu' => 'Be Vietnam Pro',
        ],
        'font_sizes' => [
            'hero_title' => 82,
            'section_title' => 42,
            'body' => 16,
            'menu' => 14,
            'button' => 16,
        ],
        'banner' => [
            'height' => 560,
            'overlay_opacity' => 72,
            'title_position' => 'bottom',
            'content_align' => 'left',
            'padding_y' => 80,
        ],
        'button' => [
            'radius' => 8,
            'shadow' => true,
            'effect' => 'lift',
        ],
        'spacing' => [
            'page_margin' => 0,
            'section_padding' => 56,
            'section_gap' => 24,
            'title_content_gap' => 20,
        ],
    ];

    protected $fillable = ['settings'];

    protected $casts = [
        'settings' => 'array',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            ['settings' => self::DEFAULTS]
        );
    }

    public function resolvedSettings(): array
    {
        return array_replace_recursive(self::DEFAULTS, $this->settings ?? []);
    }

    public function resetToDefaults(): void
    {
        $this->forceFill(['settings' => self::DEFAULTS])->save();
    }

    public static function fontOptions(): array
    {
        return array_combine(self::FONT_OPTIONS, self::FONT_OPTIONS);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->resolvedSettings(), $key, $default);
    }

    public function cssFontFamily(string $key): string
    {
        $font = (string) $this->get("fonts.{$key}", self::DEFAULTS['fonts'][$key] ?? 'Be Vietnam Pro');

        if (! in_array($font, self::FONT_OPTIONS, true)) {
            $font = 'Be Vietnam Pro';
        }

        return "'{$font}', \"Segoe UI\", system-ui, sans-serif";
    }

    public function cssColor(string $key): string
    {
        $color = (string) $this->get("colors.{$key}", '#000000');

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : '#000000';
    }

    public function cssInt(string $key, int $min, int $max): int
    {
        $value = (int) $this->get($key, Arr::get(self::DEFAULTS, $key, $min));

        return max($min, min($max, $value));
    }

    public function bannerAlignItems(): string
    {
        return match ($this->get('banner.title_position')) {
            'top' => 'start',
            'center' => 'center',
            default => 'end',
        };
    }

    public function contentTextAlign(): string
    {
        return match ($this->get('banner.content_align')) {
            'center' => 'center',
            'right' => 'right',
            default => 'left',
        };
    }

    public function contentJustify(): string
    {
        return match ($this->get('banner.content_align')) {
            'center' => 'center',
            'right' => 'flex-end',
            default => 'flex-start',
        };
    }

    public function buttonShadow(): string
    {
        return $this->get('button.shadow', true)
            ? '0 14px 34px rgba(44, 27, 18, .18)'
            : 'none';
    }

    public function buttonHoverTransform(): string
    {
        return $this->get('button.effect') === 'lift' ? 'translateY(-2px)' : 'none';
    }
}
