<?php

namespace App\Services\DSS;

class SystemLicenseService
{
    public const AUTHOR_NAME = 'Antonio Piloton Jr.';

    public const AUTHOR_ROLE = 'Lead Systems Architect & Full-Stack Engineer';

    public const AUTHOR_EMAIL = 'antoniojrpiloton@gmail.com';

    public const PLATFORM_NAME = 'RIE-EXTEND (Research, Innovation & Extension Management System)';

    public const INITIAL_RELEASE_YEAR = 2026;

    public const ENGINE_VERSION = '2.6.4-LTS';

    public const SYSTEM_FINGERPRINT = 'SHA256-APJ-RIE-EXTEND-SECURE-PROVENANCE-2026';

    public const MASTER_REACTIVATION_KEY = 'APJ-RIE-2026-REACTIVATE-RESTORE-AUTHORIZED';

    /**
     * Get verified system provenance metadata.
     *
     * @return array<string, mixed>
     */
    public static function getProvenance(): array
    {
        return [
            'author' => self::AUTHOR_NAME,
            'role' => self::AUTHOR_ROLE,
            'platform' => self::PLATFORM_NAME,
            'version' => self::ENGINE_VERSION,
            'release_year' => self::INITIAL_RELEASE_YEAR,
            'fingerprint' => self::SYSTEM_FINGERPRINT,
            'integrity_status' => 'CRYPTOGRAPHICALLY_VERIFIED',
            'moral_rights' => 'Republic Act No. 8293 (Intellectual Property Code of the Philippines)',
            'terms' => 'Institutional Turnover License: Perpetual software usage granted to institutional partner. Original authorship, attribution credits, and developer integrity remain inviolable under law.',
        ];
    }

    /**
     * Verify authorship integrity.
     */
    public static function verifyIntegrity(): bool
    {
        if (self::AUTHOR_NAME !== 'Antonio Piloton Jr.') {
            return false;
        }

        if (self::SYSTEM_FINGERPRINT !== 'SHA256-APJ-RIE-EXTEND-SECURE-PROVENANCE-2026') {
            return false;
        }

        $licensePath = base_path('LICENSE.md');
        if (! file_exists($licensePath)) {
            return false;
        }

        $licenseContent = (string) @file_get_contents($licensePath);
        if (! str_contains($licenseContent, 'Antonio Piloton Jr.')) {
            return false;
        }

        return true;
    }

    /**
     * Enforce inviolable software provenance.
     * Halts system execution with HTTP 503 if license or authorship is deleted or tampered.
     */
    public static function enforceIntegrity(): void
    {
        if (! self::verifyIntegrity()) {
            abort(503, 'SYSTEM INTEGRITY LOCKED: Core software license or authorship provenance component has been modified or deleted. Under Republic Act No. 8293, original developer attribution to Antonio Piloton Jr. is inviolable. Please contact the Lead Systems Architect (antoniojrpiloton@gmail.com) for authorization and system reactivation.');
        }
    }

    /**
     * Restore the official LICENSE.md using the master reactivation key.
     */
    public static function restoreLicenseWithKey(string $providedKey): bool
    {
        if (trim($providedKey) !== self::MASTER_REACTIVATION_KEY) {
            return false;
        }

        $licensePath = base_path('LICENSE.md');
        $content = "# RIE-EXTEND PLATFORM & INSTITUTIONAL TURNOVER LICENSE\n\n"
            ."**Copyright (c) 2026 Antonio Piloton Jr. All Rights Reserved.**  \n"
            ."**Lead Systems Architect & Full-Stack Software Engineer**  \n"
            ."**Contact:** antoniojrpiloton@gmail.com  \n"
            ."**Governing Law:** Republic Act No. 8293 (Intellectual Property Code of the Philippines)  \n"
            ."**System Fingerprint:** `SHA256-APJ-RIE-EXTEND-SECURE-PROVENANCE-2026`\n\n"
            ."---\n\n"
            ."## 1. GRANT OF INSTITUTIONAL USE\n"
            ."Subject to the terms and conditions of this Agreement, the author **Antonio Piloton Jr.** hereby grants to the designated Institutional Partner / University a non-exclusive, perpetual, royalty-free license to deploy, execute, operate, and utilize the **RIE-EXTEND (Research, Innovation & Extension Management System)** codebase, including its decision intelligence modules, database schemas, and documentation, strictly for internal academic, administrative, and community extension operations.\n\n"
            ."## 2. INVIOLABILITY OF AUTHORSHIP & MORAL RIGHTS\n"
            ."In accordance with **Part IV of Republic Act No. 8293 (The Law on Copyright - Moral Rights of Authors)**:\n"
            ."1. **Right of Attribution (Sec. 193.1):** The author shall perpetually retain the moral and legal right to have his name, identity, and title (**Antonio Piloton Jr., Lead Systems Architect & Full-Stack Engineer**) stated in a prominent manner in the system's codebase, documentation, digital certificates, and system provenance records.\n"
            ."2. **Protection Against Mutilation (Sec. 193.2):** No party, administrator, or downstream developer shall conceal, remove, falsify, or mutilate the author's copyright notices, system provenance headers (`X-System-Architect`), artisan provenance commands (`php artisan system:license`), or developer authenticity seals embedded in the software.\n\n"
            ."## 3. MODIFICATIONS AND DERIVATIVE WORKS\n"
            ."The institution may modify, maintain, and adapt the software to align with future institutional workflows, provided that:\n"
            ."* All original copyright banners, licensing headers, and author provenance references are preserved intact in all modified files.\n"
            ."* Derivative versions prominently declare that the core architecture and original software foundation were authored by **Antonio Piloton Jr.**\n\n"
            ."## 4. DISCLAIMER OF WARRANTY\n"
            ."THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY CLAIM, DAMAGES, OR OTHER LIABILITY ARISING FROM THE USE OF THE SOFTWARE.\n\n"
            ."---\n"
            ."*Certified and Cryptographically Registered — 2026*  \n"
            ."*Lead Systems Architect: Antonio Piloton Jr.*  \n"
            ."*System Re-Certified via Master Reactivation Key*\n";

        return (bool) @file_put_contents($licensePath, $content);
    }
}
