<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Services\DSS\LogoGeneratorService;
use App\Services\DSS\TenantContext;
use App\Services\DSS\ThemePresets;
use Illuminate\Http\Request;

class ThemeCustomizerController extends Controller
{
    public function __construct(
        protected LogoGeneratorService $logoGeneratorService
    ) {}

    public function index()
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();
        $logoVariants = $institution->logo_variants ?? [];
        $presets = ThemePresets::all();
        $uiTemplates = ThemePresets::uiTemplates();

        return view('dss.settings.theme', compact('tenant', 'institution', 'theme', 'logoVariants', 'presets', 'uiTemplates'));
    }

    public function switchUiTemplate(Request $request)
    {
        $validated = $request->validate([
            'ui_template' => 'required|string|in:flup_minimal,print_manzil,material_able',
        ]);

        $theme = TenantContext::getTheme();
        $theme->update([
            'ui_template' => $validated['ui_template'],
        ]);

        TenantContext::reset();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'result' => true,
                'ui_template' => $validated['ui_template'],
                'message' => 'UI Layout Template saved to database!',
            ]);
        }

        return back()->with('success', 'UI Layout Template updated and saved!');
    }

    public function update(Request $request)
    {
        $theme = TenantContext::getTheme();

        $validated = $request->validate([
            'primary_color' => 'required|string|max:30',
            'secondary_color' => 'required|string|max:30',
            'accent_color' => 'required|string|max:30',
            'preset_key' => 'nullable|string|max:50',
            'ui_template' => 'nullable|string|in:flup_minimal,print_manzil,material_able',
            'sidebar_theme' => 'required|string|in:light,dark,brand',
            'sidebar_bg' => 'nullable|string|max:30',
            'sidebar_text' => 'nullable|string|max:30',
            'sidebar_active_bg' => 'nullable|string|max:9',
            'sidebar_active_text' => 'nullable|string|max:30',
            'sidebar_border' => 'nullable|string|max:30',
            'navbar_theme' => 'required|string|in:light,dark',
            'topbar_bg' => 'nullable|string|max:30',
            'topbar_text' => 'nullable|string|max:30',
            'topbar_border' => 'nullable|string|max:30',
            'border_radius' => 'required|string|max:20',
            'btn_primary_bg' => 'nullable|string|max:30',
            'btn_primary_text' => 'nullable|string|max:30',
            'btn_secondary_bg' => 'nullable|string|max:30',
            'btn_secondary_text' => 'nullable|string|max:30',
            'btn_radius' => 'nullable|string|max:20',
            'btn_elevation' => 'nullable|string|max:30',
            'card_bg' => 'nullable|string|max:30',
            'card_border' => 'nullable|string|max:30',
            'card_radius' => 'nullable|string|max:20',
            'is_dark_mode' => 'nullable|boolean',
            'font_family' => 'nullable|string|max:100',
            'custom_css' => 'nullable|string',
        ]);

        $theme->update([
            'primary_color' => $validated['primary_color'],
            'secondary_color' => $validated['secondary_color'],
            'accent_color' => $validated['accent_color'],
            'preset_key' => $validated['preset_key'] ?? null,
            'ui_template' => $validated['ui_template'] ?? ($theme->ui_template ?: 'flup_minimal'),
            'sidebar_theme' => $validated['sidebar_theme'],
            'sidebar_bg' => $validated['sidebar_bg'] ?? null,
            'sidebar_text' => $validated['sidebar_text'] ?? null,
            'sidebar_active_bg' => $validated['sidebar_active_bg'] ?? null,
            'sidebar_active_text' => $validated['sidebar_active_text'] ?? null,
            'sidebar_border' => $validated['sidebar_border'] ?? null,
            'navbar_theme' => $validated['navbar_theme'],
            'topbar_bg' => $validated['topbar_bg'] ?? null,
            'topbar_text' => $validated['topbar_text'] ?? null,
            'topbar_border' => $validated['topbar_border'] ?? null,
            'border_radius' => $validated['border_radius'],
            'btn_primary_bg' => $validated['btn_primary_bg'] ?? null,
            'btn_primary_text' => $validated['btn_primary_text'] ?? null,
            'btn_secondary_bg' => $validated['btn_secondary_bg'] ?? null,
            'btn_secondary_text' => $validated['btn_secondary_text'] ?? null,
            'btn_radius' => $validated['btn_radius'] ?? '8px',
            'btn_elevation' => $validated['btn_elevation'] ?? 'subtle',
            'card_bg' => $validated['card_bg'] ?? '#ffffff',
            'card_border' => $validated['card_border'] ?? '#e2e8f0',
            'card_radius' => $validated['card_radius'] ?? '12px',
            'is_dark_mode' => $request->boolean('is_dark_mode'),
            'font_family' => 'Inter',
            'custom_css' => $validated['custom_css'] ?? null,
        ]);

        return response()->json([
            'result' => true,
            'message' => 'Institutional theme and brand styles updated live!',
            'theme' => $theme,
        ]);
    }

    /**
     * Upload Institutional Logo and auto-generate 7 white-label versions.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:10240',
        ]);

        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();

        $variants = $this->logoGeneratorService->generateVariants($request->file('logo'), $tenant->id);

        $mainLogoPath = $variants['navbar']['relative_path'] ?? ($variants['master']['relative_path'] ?? null);
        $sealPath = $variants['print_seal']['relative_path'] ?? null;
        $watermarkPath = $variants['watermark']['relative_path'] ?? null;

        $institution->update([
            'logo_path' => $mainLogoPath,
            'seal_path' => $sealPath,
            'watermark_path' => $watermarkPath,
            'logo_variants' => $variants,
        ]);

        return response()->json([
            'result' => true,
            'message' => 'Institutional logo uploaded successfully! 7 white-label versions generated.',
            'logo_path' => asset($mainLogoPath),
            'variants' => $variants,
        ]);
    }

    /**
     * Reset / Remove Institutional Logo.
     */
    public function removeLogo()
    {
        $institution = TenantContext::getInstitution();

        $institution->update([
            'logo_path' => null,
            'seal_path' => null,
            'watermark_path' => null,
            'logo_variants' => null,
        ]);

        return response()->json([
            'result' => true,
            'message' => 'Institutional logo removed. System default brand marks restored.',
        ]);
    }
}
