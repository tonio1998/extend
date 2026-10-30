<?php

namespace App\Services\PDF;

use App\Models\SaaS\InstitutionSetting;

/**
 * Backward-compatible PolicyTemplate extending the universal AdssuTemplate
 */
class PolicyTemplate extends AdssuTemplate
{
    public function __construct(
        string $orientation = 'P',
        string $unit = 'mm',
        string|array $size = 'A4',
        ?InstitutionSetting $institution = null
    ) {
        parent::__construct($size, $orientation, $unit, $institution);
    }
}
