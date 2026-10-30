<?php

namespace App\Services\DSS;

use App\Models\SaaS\InstitutionSetting;
use App\Models\SaaS\Tenant;
use App\Models\SaaS\ThemeSetting;

class TenantContext
{
    protected static ?Tenant $currentTenant = null;

    protected static ?InstitutionSetting $currentInstitution = null;

    protected static ?ThemeSetting $currentTheme = null;

    public static function reset(): void
    {
        self::$currentTenant = null;
        self::$currentInstitution = null;
        self::$currentTheme = null;
    }

    public static function isImpersonating(): bool
    {
        return false;
    }

    public static function getTenant(): Tenant
    {
        if (self::$currentTenant) {
            return self::$currentTenant;
        }

        self::$currentTenant = Tenant::with(['institutionSetting', 'themeSetting', 'campuses'])->find(1);

        if (! self::$currentTenant) {
            self::$currentTenant = Tenant::with(['institutionSetting', 'themeSetting', 'campuses'])->first();
        }

        if (! self::$currentTenant) {
            self::$currentTenant = new Tenant([
                'id' => 1,
                'uuid' => '00000000-0000-0000-0000-000000000001',
                'name' => 'Surigao del Norte State University',
                'slug' => 'snsu',
                'domain' => 'snsu.edu.ph',
                'plan' => 'Institutional Enterprise',
                'status' => 'active',
            ]);
        }

        return self::$currentTenant;
    }

    public static function getTenantId(): int
    {
        return self::getTenant()->id;
    }

    public static function getInstitution(): InstitutionSetting
    {
        if (self::$currentInstitution) {
            return self::$currentInstitution;
        }

        $tenant = self::getTenant();
        if ($tenant->institutionSetting) {
            self::$currentInstitution = $tenant->institutionSetting;

            return self::$currentInstitution;
        }

        self::$currentInstitution = InstitutionSetting::where('tenant_id', $tenant->id)->first();
        if (! self::$currentInstitution) {
            self::$currentInstitution = new InstitutionSetting([
                'tenant_id' => $tenant->id ?? 1,
                'school_name' => 'Surigao del Norte State University',
                'school_acronym' => 'SNSU',
                'school_type' => 'State University and College (SUC)',
                'address' => 'Surigao City, Surigao del Norte, Philippines 8400',
                'contact_email' => 'president@snsu.edu.ph',
                'contact_phone' => '(086) 826-1738',
                'website_url' => 'https://snsu.edu.ph',
                'current_fy' => '2026',
                'charter_basis' => 'Republic Act No. 9292',
            ]);
        }

        return self::$currentInstitution;
    }

    public static function getTheme(): ThemeSetting
    {
        if (self::$currentTheme) {
            return self::$currentTheme;
        }

        $tenant = self::getTenant();
        if ($tenant->themeSetting) {
            self::$currentTheme = $tenant->themeSetting;

            return self::$currentTheme;
        }

        self::$currentTheme = ThemeSetting::where('tenant_id', $tenant->id)->first();
        if (! self::$currentTheme) {
            self::$currentTheme = new ThemeSetting([
                'tenant_id' => $tenant->id ?? 1,
                'primary_color' => '#047857',
                'secondary_color' => '#0284c7',
                'accent_color' => '#f59e0b',
                'ui_template' => 'flup_minimal',
                'sidebar_theme' => 'brand',
                'navbar_theme' => 'light',
                'border_radius' => '10px',
                'is_dark_mode' => false,
                'font_family' => "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
            ]);
        }

        return self::$currentTheme;
    }
}
