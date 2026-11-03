<?php

namespace App\Services\RIE;

use App\Models\Dts\DocumentType;
use App\Models\RIE\ActivityDesign;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Services\DSS\TenantContext;

class DocumentCodeService
{
    /**
     * Map of RIE module keys to dts_document_types IDs and defaults.
     */
    public static function moduleConfig(): array
    {
        return [
            'plan' => [
                'id' => 1,
                'name' => 'Extension Plan',
                'default_code' => 'SNSU-PLAN-XXXX',
                'description' => 'Annual & Academic Program Extension Work and Financial Plan (Module 1)',
            ],
            'proposal' => [
                'id' => 2,
                'name' => 'Extension Proposal',
                'default_code' => 'SNSU-PROP-XXXX',
                'description' => 'Comprehensive Extension Project / Program Proposal (Module 2)',
            ],
            'activity_design' => [
                'id' => 3,
                'name' => 'Activity Design',
                'default_code' => 'SNSU-ACT-XXXX',
                'description' => 'Detailed Activity Design & Budget Certification Request (Module 3)',
            ],
        ];
    }

    /**
     * Retrieve all RIE document types from dts_document_types.
     */
    public static function getAllDocumentTypes(?int $tenantId = null): array
    {
        $tenantId = $tenantId ?: (TenantContext::getTenant()?->id ?? 1);
        $configs = self::moduleConfig();

        $records = DocumentType::where('tenant_id', $tenantId)
            ->whereIn('id', [1, 2, 3])
            ->get()
            ->keyBy('id');

        $result = [];
        foreach ($configs as $key => $conf) {
            $doc = $records->get($conf['id']);
            $code = $doc?->code ?: $conf['default_code'];

            $result[$key] = [
                'id' => $conf['id'],
                'key' => $key,
                'name' => $doc?->name ?: $conf['name'],
                'code' => $code,
                'category' => $doc?->category ?: 'RIE & Extension',
                'description' => $doc?->description ?: $conf['description'],
                'preview' => self::renderPreview($code),
            ];
        }

        return $result;
    }

    /**
     * Alias for getAllDocumentTypes.
     */
    public static function getAllFormats(?int $tenantId = null): array
    {
        return self::getAllDocumentTypes($tenantId);
    }

    /**
     * Render live sample preview from code pattern (e.g. SNSU-PLAN-XXXX -> SNSU-PLAN-2026-0001).
     */
    public static function renderPreview(?string $pattern): string
    {
        $pattern = trim((string) $pattern);
        if ($pattern === '') {
            $pattern = 'SNSU-DOC-XXXX';
        }

        $year = date('Y');

        if (str_contains($pattern, 'XXXX')) {
            if (! str_contains($pattern, $year) && ! str_contains($pattern, 'YYYY')) {
                return str_replace('-XXXX', "-{$year}-0001", $pattern);
            }

            return str_replace(['YYYY', 'XXXX'], [$year, '0001'], $pattern);
        }

        return "{$pattern}-{$year}-0001";
    }

    /**
     * Save updated document codes directly in dts_document_types table.
     */
    public static function saveDocumentCodes(array $codes, ?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?: (TenantContext::getTenant()?->id ?? 1);
        $configs = self::moduleConfig();

        foreach ($codes as $key => $codePattern) {
            if (! isset($configs[$key])) {
                continue;
            }

            $id = $configs[$key]['id'];
            $cleanCode = strtoupper(trim($codePattern));
            if ($cleanCode === '') {
                $cleanCode = $configs[$key]['default_code'];
            }

            DocumentType::where('tenant_id', $tenantId)
                ->where('id', $id)
                ->update([
                    'code' => $cleanCode,
                    'updated_at' => now(),
                ]);
        }

        return true;
    }

    /**
     * Generate next document code using the pattern from dts_document_types.
     * Strict structure: {format_prefix}-{current_year}-{series} (e.g. SNSU-PLAN-2026-0001)
     */
    public static function generateCodeForModule(string $module, ?int $tenantId = null): string
    {
        $tenantId = $tenantId ?: (TenantContext::getTenant()?->id ?? 1);
        $configs = self::moduleConfig();
        $conf = $configs[$module] ?? null;

        $docType = $conf ? DocumentType::where('tenant_id', $tenantId)->find($conf['id']) : null;
        $pattern = $docType?->code ?: ($conf['default_code'] ?? 'SNSU-DOC-XXXX');

        $year = date('Y');

        // Extract base search prefix: format + current year
        if (str_contains($pattern, 'XXXX')) {
            $base = str_replace(['-XXXX', 'XXXX'], '', $pattern);
            if (! str_contains($base, $year) && ! str_contains($base, 'YYYY')) {
                $searchPrefix = "{$base}-{$year}-";
            } else {
                $searchPrefix = str_replace('YYYY', $year, $base).'-';
            }
        } else {
            $searchPrefix = "{$pattern}-{$year}-";
        }

        $searchPrefix = preg_replace('/-+/', '-', $searchPrefix);

        // Find the latest document with this exact prefix to determine series sequence
        $latestCode = match ($module) {
            'plan' => ExtensionPlan::where('tenant_id', $tenantId)
                ->where('plan_code', 'like', "{$searchPrefix}%")
                ->orderBy('id', 'desc')
                ->value('plan_code'),
            'proposal' => ExtensionProposal::where('tenant_id', $tenantId)
                ->where('proposal_code', 'like', "{$searchPrefix}%")
                ->orderBy('id', 'desc')
                ->value('proposal_code'),
            'activity_design' => ActivityDesign::where('tenant_id', $tenantId)
                ->where('design_code', 'like', "{$searchPrefix}%")
                ->orderBy('id', 'desc')
                ->value('design_code'),
            default => null,
        };

        $nextNum = 1;
        if ($latestCode && preg_match('/-(\d+)$/', $latestCode, $m)) {
            $nextNum = ((int) $m[1]) + 1;
        }

        // Generate candidate and ensure absolute uniqueness
        $candidate = $searchPrefix.sprintf('%04d', $nextNum);
        while (self::codeExists($module, $candidate, $tenantId)) {
            $nextNum++;
            $candidate = $searchPrefix.sprintf('%04d', $nextNum);
        }

        return $candidate;
    }

    /**
     * Check if a generated code already exists for the given module and tenant.
     */
    public static function codeExists(string $module, string $code, int $tenantId): bool
    {
        return match ($module) {
            'plan' => ExtensionPlan::where('tenant_id', $tenantId)->where('plan_code', $code)->exists(),
            'proposal' => ExtensionProposal::where('tenant_id', $tenantId)->where('proposal_code', $code)->exists(),
            'activity_design' => ActivityDesign::where('tenant_id', $tenantId)->where('design_code', $code)->exists(),
            default => false,
        };
    }

    public static function generatePlanCode(?int $tenantId = null): string
    {
        return self::generateCodeForModule('plan', $tenantId);
    }

    public static function generateProposalCode(?int $tenantId = null): string
    {
        return self::generateCodeForModule('proposal', $tenantId);
    }

    public static function generateActivityDesignCode(?int $tenantId = null): string
    {
        return self::generateCodeForModule('activity_design', $tenantId);
    }
}
