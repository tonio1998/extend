<?php

namespace App\Models\RIE;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use App\Models\User;
use App\Traits\HasCentralizedDocuments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class ExtensionProposal extends Model
{
    use HasCentralizedDocuments, HasFactory, SoftDeletes;

    protected $table = 'extension_proposals';

    protected $fillable = [
        'tenant_id',
        'campus_id',
        'unit_id',
        'academic_program_id',
        'extension_plan_id',
        'dts_record_id',
        'tracking_number',
        'proposal_code',

        'title',
        'banner_program',
        'banner_program_other',
        'agenda_priorities',
        'beneficiaries',
        'target_beneficiaries_data',
        'implementing_agency',
        'implementing_colleges',
        'lead_agency',
        'collaborating_agencies',
        'collaborating_agencies_data',
        'duration_start',
        'duration_end',
        'duration_text',
        'duration_months',
        'project_location',
        'budget_institution_share',
        'budget_partner_share',
        'fund_source_internal',
        'fund_source_external',
        'budget_total_display',
        'budget_cash_amount',
        'project_leader_user_id',
        'project_leader_employee_id',
        'project_leader_name',
        'project_leader_title',
        'project_leader_gender',
        'asst_project_leader_name',
        'asst_project_leader_title',
        'proponents',

        'introduction',
        'objectives',
        'general_objective',
        'specific_objectives',
        'expected_outputs',
        'relevance_narrative',
        'sdg_alignments',
        'development_gap',
        'pdp_responsiveness',
        'schedule_of_activities',
        'people_involved',
        'org_structure',
        'risk_management',
        'sustainability_measures',
        'budgetary_requirements',

        'prepared_by_user_id',
        'prepared_by_employee_id',
        'prepared_by_name',
        'prepared_by_designation',
        'prepared_at',

        'status',
        'remarks',
        'metadata',
    ];

    protected $casts = [
        'agenda_priorities' => 'array',
        'proponents' => 'array',
        'objectives' => 'array',
        'specific_objectives' => 'array',
        'expected_outputs' => 'array',
        'risk_management' => 'array',
        'implementing_colleges' => 'array',
        'collaborating_agencies_data' => 'array',
        'target_beneficiaries_data' => 'array',
        'sdg_alignments' => 'array',
        'schedule_of_activities' => 'array',
        'people_involved' => 'array',
        'org_structure' => 'array',
        'budgetary_requirements' => 'array',
        'metadata' => 'array',
        'duration_start' => 'date',
        'duration_end' => 'date',
        'duration_months' => 'integer',
        'budget_cash_amount' => 'decimal:2',
        'prepared_at' => 'datetime',
    ];

    public function getRouteKey(): string
    {
        return function_exists('dss_encrypt_id') ? dss_encrypt_id($this->id) : (string) $this->id;
    }

    public function getBudgetSnsuShareAttribute(): ?string
    {
        return $this->budget_institution_share;
    }

    public function getOrgChartUrlAttribute(): ?string
    {
        $path = $this->org_structure['custom_image'] ?? null;
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function getResolvedPeopleInvolvedAttribute(): array
    {
        if (! empty($this->people_involved) && isset($this->people_involved['rows']) && is_array($this->people_involved['rows'])) {
            return $this->people_involved;
        }

        $rows = [];
        if (! empty($this->proponents) && is_array($this->proponents)) {
            foreach ($this->proponents as $idx => $prop) {
                $pName = trim($prop['name'] ?? '');
                $pRole = trim($prop['role'] ?? 'Member');
                if ($pName !== '') {
                    $rows[] = [
                        'id' => 'row_'.($idx + 1),
                        'name' => $pName,
                        'role' => $pRole,
                        'responsibilities' => $this->inferDefaultResponsibilities($pRole),
                    ];
                }
            }
        }

        if (empty($rows)) {
            $rows[] = [
                'id' => 'row_1',
                'name' => $this->project_leader_name,
                'role' => $this->project_leader_title ?: 'Project Leader',
                'responsibilities' => 'Overall project management, institutional coordination, and reporting',
            ];
            if ($this->asst_project_leader_name) {
                $rows[] = [
                    'id' => 'row_2',
                    'name' => $this->asst_project_leader_name,
                    'role' => $this->asst_project_leader_title ?: 'Assistant Project Leader',
                    'responsibilities' => 'Assists project leader, field logistics, and technical execution',
                ];
            }
        }

        return [
            'columns' => [
                ['id' => 'name', 'label' => 'Extensionist / Collaborator', 'width' => 35],
                ['id' => 'role', 'label' => 'Project Role / Title', 'width' => 25],
                ['id' => 'responsibilities', 'label' => 'Specific Responsibilities & Tasks', 'width' => 40],
            ],
            'rows' => $rows,
        ];
    }

    private function inferDefaultResponsibilities(string $role): string
    {
        $r = strtolower($role);
        if (str_contains($r, 'leader') && ! str_contains($r, 'asst') && ! str_contains($r, 'assistant')) {
            return 'Overall project management, institutional coordination, and reporting';
        }
        if (str_contains($r, 'asst') || str_contains($r, 'assistant')) {
            return 'Field facilitation, activity logistics, and technical documentation';
        }
        if (str_contains($r, 'student') || str_contains($r, 'support')) {
            return 'Technical support, attendance tracking, and user assistance';
        }

        return 'Module training, mentoring, and technical facilitation';
    }

    public function getObjectivesHtmlAttribute(): string
    {
        if (empty($this->objectives)) {
            return '';
        }

        $html = '';
        if (is_string($this->objectives)) {
            $html = $this->objectives;
        } elseif (is_array($this->objectives)) {
            if (isset($this->objectives['html'])) {
                $html = (string) $this->objectives['html'];
            } elseif (isset($this->objectives['raw'])) {
                $html = (string) $this->objectives['raw'];
            } elseif (isset($this->objectives['content'])) {
                $html = (string) $this->objectives['content'];
            } else {
                $html = implode('<br><br>', array_map(function ($item) {
                    if (is_array($item)) {
                        $title = $item['title'] ?? '';
                        $desc = $item['description'] ?? '';

                        return trim($title.($desc ? ': '.$desc : ''));
                    }

                    return (string) $item;
                }, $this->objectives));
            }
        }

        return static::decodeRichHtml($html);
    }

    public function getObjectivesTextAttribute(): string
    {
        return $this->objectives_html;
    }

    public static function decodeRichHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        for ($i = 0; $i < 6; $i++) {
            if (! str_contains($html, '&amp;') && ! str_contains($html, '&lt;')) {
                break;
            }
            $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return trim($html);
    }

    public function getIntroductionHtmlAttribute(): string
    {
        return static::decodeRichHtml($this->introduction);
    }

    public function getRelevanceNarrativeHtmlAttribute(): string
    {
        return static::decodeRichHtml($this->relevance_narrative);
    }

    public function getDevelopmentGapHtmlAttribute(): string
    {
        return static::decodeRichHtml($this->development_gap);
    }

    public function getPdpResponsivenessHtmlAttribute(): string
    {
        return static::decodeRichHtml($this->pdp_responsiveness);
    }

    public function getSustainabilityMeasuresHtmlAttribute(): string
    {
        return static::decodeRichHtml($this->sustainability_measures);
    }

    public function getResolvedGeneralObjectiveAttribute(): string
    {
        if (! empty($this->general_objective)) {
            return trim(strip_tags(static::decodeRichHtml($this->general_objective)));
        }

        $html = '';
        if (is_array($this->objectives)) {
            $html = (string) ($this->objectives['html'] ?? ($this->objectives['raw'] ?? ''));
        } elseif (is_string($this->objectives)) {
            $html = $this->objectives;
        }

        if ($html !== '') {
            $htmlDecoded = static::decodeRichHtml($html);

            if (preg_match('#^(.*?)(?:<[ou]l.*?>)#is', $htmlDecoded, $m)) {
                $pre = trim(strip_tags($m[1]));
                if ($pre !== '') {
                    return $pre;
                }
            }
            $clean = trim(strip_tags($htmlDecoded));
            if ($clean !== '') {

                $parts = preg_split('/\n{2,}/', $clean);

                return trim($parts[0]);
            }
        }

        return 'To provide transformative technical capability training and community skills development.';
    }

    public function getResolvedSpecificObjectivesAttribute(): array
    {
        $rawSpecific = $this->specific_objectives;
        if (! empty($rawSpecific) && is_array($rawSpecific)) {
            $cleaned = [];
            foreach ($rawSpecific as $item) {
                $val = is_array($item) ? ($item['description'] ?? $item['title'] ?? '') : (string) $item;
                $val = trim(strip_tags(static::decodeRichHtml($val)));
                if ($val !== '') {
                    $cleaned[] = $val;
                }
            }
            if (! empty($cleaned)) {
                return $cleaned;
            }
        }

        $html = '';
        if (is_array($this->objectives)) {
            $html = (string) ($this->objectives['html'] ?? ($this->objectives['raw'] ?? ''));
        } elseif (is_string($this->objectives)) {
            $html = $this->objectives;
        }

        if ($html !== '') {
            $htmlDecoded = static::decodeRichHtml($html);

            if (preg_match_all('#<li.*?>(.*?)</li>#is', $htmlDecoded, $liMatches)) {
                $items = [];
                foreach ($liMatches[1] as $li) {
                    $c = trim(strip_tags($li));
                    if ($c !== '') {
                        $items[] = $c;
                    }
                }
                if (! empty($items)) {
                    return $items;
                }
            }

            if (is_array($this->objectives) && ! isset($this->objectives['raw']) && ! isset($this->objectives['html'])) {
                $items = [];
                foreach ($this->objectives as $item) {
                    $c = is_array($item) ? ($item['description'] ?? $item['title'] ?? '') : (string) $item;
                    $c = trim(strip_tags($c));
                    if ($c !== '') {
                        $items[] = $c;
                    }
                }
                if (! empty($items)) {
                    return $items;
                }
            }
        }

        return [
            '1. Deliver interactive technical training modules for beneficiaries.',
            '2. Facilitate hands-on workshops and practical demonstrations.',
            '3. Evaluate community learning outcomes and project sustainability.',
        ];
    }

    public function getResolvedScheduleOfActivitiesAttribute(): array
    {
        $data = $this->schedule_of_activities;
        if (! empty($data) && is_array($data) && isset($data['periods']) && isset($data['rows'])) {
            return $data;
        }

        return [
            'type' => 'gantt',
            'periods' => [],
            'rows' => [],
        ];
    }

    public function getResolvedBudgetaryRequirementsAttribute(): array
    {
        if (! empty($this->budgetary_requirements) && is_array($this->budgetary_requirements)) {
            if (isset($this->budgetary_requirements['items']) && is_array($this->budgetary_requirements['items'])) {
                return $this->budgetary_requirements;
            }

            if (isset($this->budgetary_requirements['sections']) && is_array($this->budgetary_requirements['sections'])) {
                $convertedItems = [];
                $grandTotal = 0;
                foreach ($this->budgetary_requirements['sections'] as $secIdx => $sec) {
                    $secHeader = $sec['header'] ?? $sec['title'] ?? ('Section '.($secIdx + 1));
                    $convertedItems[] = [
                        'id' => 'sec_'.$secIdx,
                        'type' => 'header',
                        'title' => $secHeader,
                    ];
                    $secSubtotal = 0;
                    if (isset($sec['items']) && is_array($sec['items'])) {
                        foreach ($sec['items'] as $itemIdx => $it) {
                            $qty = (float) ($it['qty'] ?? 1);
                            $cost = (float) ($it['cost'] ?? 0);
                            $amt = (float) ($it['amount'] ?? ($qty * $cost));
                            $secSubtotal += $amt;
                            $convertedItems[] = [
                                'id' => 'row_'.$secIdx.'_'.$itemIdx,
                                'type' => 'row',
                                'description' => $it['particulars'] ?? $it['description'] ?? '',
                                'qty' => $qty,
                                'unit' => $it['unit'] ?? 'Lot',
                                'cost' => $cost,
                                'amount' => $amt,
                            ];
                        }
                    }
                    $convertedItems[] = [
                        'id' => 'sub_'.$secIdx,
                        'type' => 'subtotal',
                        'title' => 'Subtotal',
                        'amount' => $sec['subtotal'] ?? $secSubtotal,
                    ];
                    $grandTotal += ($sec['subtotal'] ?? $secSubtotal);
                }

                return [
                    'category_title' => $this->budgetary_requirements['category_title'] ?? 'A. Direct Activity Expenses & Requirements',
                    'items' => $convertedItems,
                    'grand_total' => $this->budgetary_requirements['grand_total'] ?? $grandTotal,
                ];
            }
        }

        return [
            'category_title' => 'A. Direct Activity Expenses & Requirements',
            'items' => [],
            'grand_total' => 0,
        ];
    }

    public function getResolvedTargetBeneficiariesAttribute(): array
    {
        $data = $this->target_beneficiaries_data;
        if (! empty($data) && is_array($data)) {
            return $data;
        }

        if (! empty($this->beneficiaries)) {
            return [
                [
                    'name' => $this->beneficiaries,
                    'male' => 0,
                    'female' => 0,
                    'total' => 0,
                ],
            ];
        }

        return [
            [
                'name' => 'Partner Community / Beneficiaries',
                'male' => 0,
                'female' => 0,
                'total' => 0,
            ],
        ];
    }

    public function getResolvedCollaboratingAgenciesAttribute(): array
    {
        $data = $this->collaborating_agencies_data;
        if (! empty($data) && is_array($data)) {
            return $data;
        }

        if (! empty($this->collaborating_agencies)) {
            $raw = trim($this->collaborating_agencies);
            $parts = str_contains($raw, "\n")
                ? explode("\n", $raw)
                : (str_contains($raw, ';') ? explode(';', $raw) : [$raw]);
            $rows = [];
            foreach ($parts as $p) {
                $trimmed = trim($p, " \t\n\r\0\x0B;");
                if ($trimmed !== '') {
                    $rows[] = ['agency' => $trimmed, 'role' => 'Collaborating Partner'];
                }
            }
            if (! empty($rows)) {
                return $rows;
            }
        }

        return [];
    }

    public function getResolvedExpectedOutputsAttribute(): array
    {
        $standardCategories = [
            'Product' => ['target' => 0, 'description' => ''],
            'People' => ['target' => 0, 'description' => ''],
            'Service' => ['target' => 0, 'description' => ''],
            'Partnership' => ['target' => 0, 'description' => ''],
            'Patent/IP' => ['target' => 0, 'description' => ''],
            'Publication' => ['target' => 0, 'description' => ''],
            'Policy' => ['target' => 0, 'description' => ''],
        ];

        $stored = $this->expected_outputs;
        if (is_array($stored)) {
            foreach ($stored as $item) {
                if (isset($item['category']) && isset($standardCategories[$item['category']])) {
                    $standardCategories[$item['category']]['target'] = (int) ($item['target'] ?? 0);
                    $standardCategories[$item['category']]['description'] = (string) ($item['description'] ?? '');
                }
            }
        }

        $results = [];
        foreach ($standardCategories as $cat => $val) {
            $results[] = [
                'category' => $cat,
                'target' => $val['target'],
                'description' => $val['description'],
            ];
        }

        return $results;
    }

    public function getResolvedRiskManagementAttribute(): array
    {
        $data = $this->risk_management;
        if (! empty($data) && is_array($data)) {
            return $data;
        }

        return [];
    }

    public function getResolvedLineItemBudgetAttribute(): array
    {
        $defaultSections = [
            'A' => [
                'code' => 'A',
                'letter' => 'A.',
                'title' => 'Personal Services',
                'items' => [
                    ['particulars' => 'Salary', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A'],
                    ['particulars' => 'Honoraria', 'qty' => 'N/A', 'unit' => 'N/A', 'cost' => 'N/A', 'amount' => 'N/A'],
                ],
            ],
            'B' => [
                'code' => 'B',
                'letter' => 'B.',
                'title' => 'Maintenance & Other Operating Expenses (MOOE)',
                'items' => [],
            ],
            'B_COMM' => [
                'code' => 'B_COMM',
                'letter' => '',
                'parent' => 'B',
                'title' => 'Communication',
                'items' => [],
            ],
            'B_TRANS' => [
                'code' => 'B_TRANS',
                'letter' => '',
                'parent' => 'B',
                'title' => 'Transportation & Travel',
                'items' => [],
            ],
            'B_SUPP' => [
                'code' => 'B_SUPP',
                'letter' => '',
                'parent' => 'B',
                'title' => 'Supplies and Materials',
                'items' => [],
            ],
            'B_TRAIN' => [
                'code' => 'B_TRAIN',
                'letter' => '',
                'parent' => 'B',
                'title' => 'Trainings & Seminars',
                'items' => [],
            ],
            'B_OTHER' => [
                'code' => 'B_OTHER',
                'letter' => '',
                'parent' => 'B',
                'title' => 'Other Services',
                'items' => [],
            ],
            'C' => [
                'code' => 'C',
                'letter' => 'C.',
                'title' => 'Capital Outlay (CO)',
                'items' => [],
            ],
            'D' => [
                'code' => 'D',
                'letter' => 'D.',
                'title' => 'Contingency or Miscellaneous',
                'items' => [],
            ],
        ];

        $stored = $this->budgetary_requirements;
        if (is_array($stored)) {
            $sections = $stored['sections'] ?? $stored;
            if (is_array($sections)) {
                $keyMap = [
                    'C' => isset($sections['B_COMM']) ? 'C' : 'B_COMM',
                    'D' => isset($sections['B_TRANS']) ? 'D' : 'B_TRANS',
                    'E' => 'B_SUPP',
                    'F' => 'B_TRAIN',
                    'G' => 'B_OTHER',
                    'H' => 'C',
                    'I' => 'D',
                ];

                foreach ($sections as $secKey => $sec) {
                    $code = strtoupper(trim((string) ($sec['code'] ?? $secKey)));
                    $targetCode = $code;
                    if (! isset($defaultSections[$code]) && isset($keyMap[$code])) {
                        $targetCode = $keyMap[$code];
                    }
                    if (isset($defaultSections[$targetCode])) {
                        $defaultSections[$targetCode]['items'] = $sec['items'] ?? (is_array($sec) && ! isset($sec['items']) ? $sec : []);
                    }
                }
            }
        }

        return $defaultSections;
    }

    public function getResolvedSdgAlignmentsAttribute(): array
    {
        if ($this->relationLoaded('sdgs') && $this->sdgs->isNotEmpty()) {
            return [
                'selected' => $this->sdgs->pluck('goal_number')->map(fn ($n) => (int) $n)->values()->toArray(),
                'narrative' => $this->relevance_narrative ?? '',
            ];
        }

        $sdgs = $this->sdg_alignments ?? [];
        if (! is_array($sdgs)) {
            return ['selected' => [], 'narrative' => ''];
        }
        if (isset($sdgs['selected'])) {
            return $sdgs;
        }

        return [
            'selected' => array_values(array_filter($sdgs, fn ($v) => is_numeric($v) || is_string($v))),
            'narrative' => $this->relevance_narrative ?? '',
        ];
    }

    public function getResolvedOrgStructureAttribute(): array
    {
        $org = $this->org_structure ?? [];
        if (! is_array($org)) {
            $org = [];
        }
        $mode = $org['mode'] ?? 'dynamic';
        $nodes = $org['nodes'] ?? null;
        if (empty($nodes) || ! is_array($nodes)) {
            $nodes = [];
        }

        return [
            'mode' => $mode,
            'custom_image' => $org['custom_image'] ?? null,
            'nodes' => $nodes,
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function preparedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_user_id');
    }

    public function preparedByEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'prepared_by_employee_id');
    }

    public function academicProgram(): BelongsTo
    {
        return $this->belongsTo(AcademicProgram::class, 'academic_program_id');
    }

    public function extensionPlan(): BelongsTo
    {
        return $this->belongsTo(ExtensionPlan::class, 'extension_plan_id');
    }

    public function dtsRecord(): BelongsTo
    {
        return $this->belongsTo(self::class, 'id')->whereRaw('1 = 0');
    }

    public function projectLeaderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_leader_user_id');
    }

    public function projectLeaderEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_leader_employee_id');
    }

    public function sdgs(): BelongsToMany
    {
        return $this->belongsToMany(
            SustainableDevelopmentGoal::class,
            'extension_proposal_sdgs',
            'extension_proposal_id',
            'sdg_id'
        )->withPivot(['is_primary', 'relevance_notes'])->withTimestamps();
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'submitted' => [
                'label' => 'SUBMITTED (FOR REVIEW)',
                'class' => 'bg-info text-dark',
                'icon' => 'mdi-send-check',
            ],
            'reviewed' => [
                'label' => 'REVIEWED BY EXTENSION HEAD',
                'class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'icon' => 'mdi-check',
            ],
            'noted' => [
                'label' => 'NOTED BY DEAN',
                'class' => 'bg-indigo-subtle text-indigo border border-indigo-subtle',
                'icon' => 'mdi-check-all',
            ],
            'concurred' => [
                'label' => 'CONCURRED BY EXTENSION DIRECTOR',
                'class' => 'bg-info-subtle text-info border border-info-subtle',
                'icon' => 'mdi-file-sign',
            ],
            'recommended' => [
                'label' => 'RECOMMENDED BY VP RIE',
                'class' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'icon' => 'mdi-account-check-outline',
            ],
            'approved' => [
                'label' => 'APPROVED BY UNIVERSITY PRESIDENT',
                'class' => 'bg-success text-white',
                'icon' => 'mdi-check-decagram',
            ],
            'returned' => [
                'label' => 'RETURNED FOR REVISION',
                'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'mdi-alert-circle',
            ],
            default => [
                'label' => 'DRAFT PROPOSAL',
                'class' => 'bg-secondary text-white',
                'icon' => 'mdi-file-document-edit-outline',
            ],
        };
    }

    public function getFormattedTrackingNumberAttribute(): string
    {
        return $this->tracking_number ? formatQR12Digits($this->tracking_number) : '';
    }

    public function getTrackingUrlAttribute(): string
    {
        $code = $this->tracking_number ?: $this->proposal_code;

        if (Route::has('document.track')) {
            return route('document.track', ['tracking_number' => $code]);
        }

        return url('/track/'.$code);
    }

    public function getIsEditableAttribute(): bool
    {
        return in_array(strtolower((string) $this->status), ['draft', 'pending', 'returned'], true);
    }

    public function getIsSubmittedAttribute(): bool
    {
        return ! $this->is_editable;
    }
}
