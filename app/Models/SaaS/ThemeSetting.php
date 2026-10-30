<?php

namespace App\Models\SaaS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeSetting extends Model
{
    use HasFactory;

    protected $table = 'sys_theme_settings';

    protected $fillable = [
        'tenant_id',
        'primary_color',
        'secondary_color',
        'accent_color',
        'preset_key',
        'ui_template',
        'sidebar_theme',
        'sidebar_bg',
        'sidebar_text',
        'sidebar_active_bg',
        'sidebar_active_text',
        'sidebar_border',
        'navbar_theme',
        'topbar_bg',
        'topbar_text',
        'topbar_border',
        'border_radius',
        'btn_primary_bg',
        'btn_primary_text',
        'btn_secondary_bg',
        'btn_secondary_text',
        'btn_radius',
        'btn_elevation',
        'card_bg',
        'card_border',
        'card_radius',
        'is_dark_mode',
        'font_family',
        'custom_css',
    ];

    protected $casts = [
        'is_dark_mode' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
