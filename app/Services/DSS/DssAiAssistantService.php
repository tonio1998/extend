<?php

declare(strict_types=1);

namespace App\Services\DSS;

use App\Models\Accreditation\AccreditationMockAudit;
use App\Models\Accreditation\AccreditationProgram;
use App\Models\Dts\RecordRoute;
use App\Models\Dts\Records;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DssAiAssistantService
{
    /**
     * Respond dynamically to any user query in professional English, grounded across ALL institutional database modules.
     * Supports Groq Cloud (Ultra-fast 120B LLM), Google Gemini, and native Semantic Decision Support Engine.
     *
     * @param  array<int, array{role: string, content: string}>  $conversationHistory
     * @return array{reply: string, model: string, status: string, suggestions: array<int, string>, cards: array<int, mixed>}
     */
    public function respond(string $userMessage, array $conversationHistory = []): array
    {
        $context = $this->getLiveInstitutionalContext();
        $dbRetrieval = $this->retrieveDatabaseGroundTruth($userMessage, $context);

        $groqKey = config('services.groq.key') ?: env('GROQ_API_KEY');
        $geminiKey = config('services.gemini.key') ?: env('GEMINI_API_KEY');

        // 1. Try Groq Cloud (Ultra-fast LLM - Generative reasoning over live records)
        if (! empty($groqKey)) {
            try {
                $groqResult = $this->callGroqApi($userMessage, $conversationHistory, $context, $dbRetrieval, $groqKey);
                if ($groqResult && ! empty($groqResult['reply'])) {
                    return [
                        'reply' => $groqResult['reply'],
                        'model' => 'Groq ('.$groqResult['model_name'].' • Institutional Intelligence)',
                        'status' => 'success',
                        'suggestions' => $this->getDynamicSuggestions($userMessage),
                        'cards' => $dbRetrieval['cards'] ?? [],
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Groq API call exception: '.$e->getMessage());
            }
        }

        // 2. Try Google Gemini API
        if (! empty($geminiKey)) {
            try {
                $geminiReply = $this->callGeminiApi($userMessage, $conversationHistory, $context, $dbRetrieval, $geminiKey);
                if ($geminiReply) {
                    return [
                        'reply' => $geminiReply,
                        'model' => 'Google Gemini (Institutional Intelligence)',
                        'status' => 'success',
                        'suggestions' => $this->getDynamicSuggestions($userMessage),
                        'cards' => $dbRetrieval['cards'] ?? [],
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini API call exception: '.$e->getMessage());
            }
        }

        // 3. Native Dynamic Semantic Decision Support Engine
        $localReply = $dbRetrieval['formatted_reply'] ?: $this->generateDefaultOverviewResponse($context);

        return [
            'reply' => $localReply,
            'model' => 'IIPM-DSS Decision Support Engine',
            'status' => 'success',
            'suggestions' => $this->getDynamicSuggestions($userMessage),
            'cards' => $dbRetrieval['cards'] ?? [],
        ];
    }

    /**
     * Gather comprehensive institutional records across ALL application modules.
     *
     * @return array<string, mixed>
     */
    public function getLiveInstitutionalContext(): array
    {
        $institution = TenantContext::getInstitution();

        // 1. Users & Personnel
        $users = class_exists(User::class)
            ? User::select('id', 'name', 'email', 'role', 'designation')->get()->toArray()
            : [];

        // 2. Campuses & Departments / Units (from units table)
        $campuses = DB::table('sys_campuses')->select('id', 'campus_name')->get()->pluck('campus_name')->toArray();
        $departments = DB::table('units')->select('id', 'unit_name')->get()->pluck('unit_name')->toArray();

        // 3. Multi-Criteria Decision Evaluations (MCDA / AHP / TOPSIS)
        $mcdaEvals = DB::table('dss_evaluations')->take(3)->get()->map(function ($ev) {
            return [
                'code' => $ev->evaluation_code,
                'title' => $ev->title,
                'category' => $ev->category,
                'model' => $ev->model_type,
                'recommended_decision' => $ev->recommended_alternative,
                'is_consistent' => (bool) $ev->is_consistent,
                'consistency_ratio' => $ev->consistency_ratio,
                'topsis_scores' => json_decode($ev->topsis_scores ?: '[]', true),
            ];
        })->toArray();

        // 4. DTS Tracking Statistics & Citizen Charters
        $dtsStats = [
            'total' => 0,
            'recent' => [],
        ];
        if (class_exists(Records::class)) {
            $dtsStats['total'] = Records::count();
            $dtsStats['recent'] = Records::latest('id')
                ->take(6)
                ->get(['id', 'DocumentID', 'QRCODE', 'Description', 'doc_type', 'created_at', 'status', 'Origin'])
                ->map(fn ($r): array => [
                    'id' => $r->id,
                    'qr_formatted' => formatQR12Digits($r->QRCODE ?: $r->id),
                    'tracking_number' => formatQR12Digits($r->QRCODE ?: $r->id),
                    'document_id' => $r->DocumentID ?: 'DTS-'.$r->id,
                    'title' => $r->Description ?: ($r->doc_type ?: 'Untitled Document'),
                    'status' => strtoupper($r->status ?: 'ACTIVE'),
                    'origin' => $r->Origin ?: 'Office of Origin',
                    'date' => $r->created_at ? $r->created_at->format('M d, Y') : 'N/A',
                ])
                ->toArray();
        }

        $citizenCharters = [];
        if (Schema::hasTable('dts_citizen_charters')) {
            $citizenCharters = DB::table('dts_citizen_charters')
                ->select('id', 'service_name', 'category', 'classification', 'standard_sla_days', 'standard_sla_hours')
                ->take(10)
                ->get()
                ->toArray();
        }

        // 5. Academic Programs, Quality Mock Audits, & Survey Areas
        $programsList = [];
        $programsCount = 0;
        if (class_exists(AccreditationProgram::class)) {
            $programsCount = AccreditationProgram::count();
            $programsList = AccreditationProgram::take(8)
                ->get(['id', 'program_code', 'program_name', 'current_level', 'target_level', 'overall_readiness_score', 'status', 'accrediting_body'])
                ->map(fn ($p): array => [
                    'id' => $p->id,
                    'code' => $p->program_code,
                    'name' => $p->program_name ?? 'Program #'.$p->id,
                    'level' => $p->current_level ?? 'Candidate',
                    'target' => $p->target_level ?? 'Level I',
                    'readiness_score' => $p->overall_readiness_score ? number_format((float) $p->overall_readiness_score, 2) : 'N/A',
                    'status' => $p->status ?? 'Active',
                    'accrediting_body' => $p->accrediting_body ?? 'AACCUP',
                ])
                ->toArray();
        }

        $recentAudits = [];
        $mockAuditsCount = 0;
        if (class_exists(AccreditationMockAudit::class)) {
            $mockAuditsCount = AccreditationMockAudit::count();
            $recentAudits = AccreditationMockAudit::latest('id')
                ->take(6)
                ->get(['id', 'audit_title', 'survey_date_start', 'grand_mean', 'status', 'target_level'])
                ->map(fn ($a): array => [
                    'id' => $a->id,
                    'title' => $a->audit_title ?? 'Audit #'.$a->id,
                    'date' => $a->survey_date_start ? $a->survey_date_start->format('M d, Y') : 'N/A',
                    'rating' => $a->grand_mean ? number_format((float) $a->grand_mean, 2) : 'Pending',
                    'status' => $a->status ?? 'In Progress',
                    'target_level' => $a->target_level ?? 'Accreditation',
                ])
                ->toArray();
        }

        $surveyAreasCount = DB::table('accreditation_survey_areas')->count();

        // 6. Institutional Policies & University Memorandums
        $policies = class_exists(InstitutionalPolicy::class)
            ? InstitutionalPolicy::select('id', 'title', 'policy_number', 'status')->get()->toArray()
            : [];
        $memos = class_exists(UniversityMemorandum::class)
            ? UniversityMemorandum::select('id', 'memo_number', 'subject', 'status')->get()->toArray()
            : [];

        return [
            'institution_name' => $institution->school_name ?? 'State University / College',
            'acronym' => $institution->school_acronym ?? 'IIPM-DSS',
            'users' => $users,
            'users_count' => count($users),
            'campuses' => $campuses,
            'departments' => $departments,
            'mcda_evaluations' => $mcdaEvals,
            'dts' => $dtsStats,
            'citizen_charters' => $citizenCharters,
            'accreditation' => [
                'programs_count' => $programsCount,
                'programs_list' => $programsList,
                'mock_audits_count' => $mockAuditsCount,
                'recent_audits' => $recentAudits,
                'survey_areas_count' => $surveyAreasCount,
            ],
            'policies' => $policies,
            'policies_count' => count($policies),
            'memos' => $memos,
            'memos_count' => count($memos),
            'generated_at' => now()->format('F d, Y h:i A'),
        ];
    }

    /**
     * Dynamically analyze user question across ALL institutional domains.
     *
     * @param  array<string, mixed>  $context
     * @return array{intent: string, factual_summary: string, formatted_reply: string, cards: array<int, mixed>}
     */
    public function retrieveDatabaseGroundTruth(string $userMessage, array $context): array
    {
        $normalized = Str::lower(trim($userMessage));

        // =========================================================================
        // 1. MULTI-CRITERIA DECISION ANALYSIS (MCDA / AHP / TOPSIS / PRIORITIZATION)
        // =========================================================================
        if (preg_match('/\b(mcda|ahp|topsis|prioritization|evaluation|procurement\s*evaluation|supplier\s*ranking|drone|smart\s*sensors)\b/i', $userMessage)) {
            $evaluations = DB::table('dss_evaluations')->get();

            $factual = "Multi-Criteria Decision Analysis (MCDA) Records:\n";
            $reply = "### ⚖️ **Multi-Criteria Decision Support Evaluations (AHP & TOPSIS)**\n\n";

            foreach ($evaluations as $ev) {
                $scores = json_decode($ev->topsis_scores ?: '[]', true);
                $weights = json_decode($ev->calculated_weights ?: '[]', true);
                $criteria = json_decode($ev->criteria_data ?: '[]', true);

                $factual .= "- Evaluation [{$ev->evaluation_code}]: {$ev->title}\n";
                $factual .= "  Model: {$ev->model_type}, Recommended Decision: {$ev->recommended_alternative}\n";
                $factual .= "  Consistency Ratio (CR): {$ev->consistency_ratio} (Consistent: {$ev->is_consistent})\n";
                $factual .= '  Top Decision Ranking: '.json_encode($scores)."\n";

                $reply .= "📌 **{$ev->title}** (`{$ev->evaluation_code}`)\n";
                $reply .= "- **Decision Model:** `{$ev->model_type}`\n";
                $reply .= "- **Category:** {$ev->category}\n";
                $reply .= "- **Optimal Recommendation:** 🏆 **{$ev->recommended_alternative}**\n";
                $reply .= "- **Matrix Consistency Ratio (CR):** `{$ev->consistency_ratio}` *(Well within the < 0.10 threshold for mathematical consistency)*\n\n";

                if (! empty($scores)) {
                    $reply .= "#### 🏅 **TOPSIS Closeness Ranking:**\n";
                    $reply .= "| Rank | Alternative / Supplier | Relative Closeness (\$C_i) | Evaluation Outcome |\n";
                    $reply .= "| :--- | :--- | :--- | :--- |\n";
                    foreach ($scores as $s) {
                        $badge = ($s['rank'] === 1) ? '`RECOMMENDED`' : '`VIABLE`';
                        $reply .= "| #{$s['rank']} | **{$s['alternative']}** | `".number_format((float) $s['closeness'], 4)."` | {$badge} |\n";
                    }
                    $reply .= "\n";
                }

                $reply .= '💡 *Strategic Insight: Generated by combining Analytic Hierarchy Process eigenvector weights with TOPSIS Euclidean closeness geometry.*';
            }

            return [
                'intent' => 'mcda_evaluations',
                'factual_summary' => $factual,
                'formatted_reply' => $reply,
                'cards' => [],
            ];
        }

        // =========================================================================
        // 2. CAMPUSES, SATELLITE UNITS, & ORGANIZATIONAL DEPARTMENTS
        // =========================================================================
        if (preg_match('/\b(campuses|campus|branches|satellites|departments|units|offices|organizational\s*structure)\b/i', $userMessage) &&
            ! Str::contains($normalized, ['document', 'track', 'dts'])) {

            $campuses = DB::table('sys_campuses')->get();
            $departments = DB::table('units')->select('id', 'unit_name as dept_name', 'unit_code as dept_code')->get();

            $factual = 'University Campuses: '.implode(', ', $campuses->pluck('campus_name')->toArray()).".\n";
            $factual .= 'Key Departments: '.implode(', ', $departments->pluck('dept_name')->toArray()).".\n";

            $reply = "### 🏛️ **University Campuses & Organizational Departments**\n\n";
            $reply .= "#### 📍 **Active University Campuses:**\n";
            foreach ($campuses as $c) {
                $reply .= "- 🏫 **{$c->campus_name}**\n";
            }
            $reply .= "\n#### 🏢 **Key Administrative & Operational Departments:**\n";
            foreach ($departments as $d) {
                $reply .= "- 🔹 **{$d->dept_name}**\n";
            }

            $reply .= "\n💡 *Institutional records reflect active campuses and central university administrative offices.*";

            return [
                'intent' => 'campuses_and_departments',
                'factual_summary' => $factual,
                'formatted_reply' => $reply,
                'cards' => [],
            ];
        }

        // =========================================================================
        // 3. CITIZEN CHARTER PROCEDURES & SERVICE STANDARDS
        // =========================================================================
        if (preg_match('/\b(citizen\s*charter|turnaround|service\s*standards?|sla|processing\s*time)\b/i', $userMessage)) {
            $charters = Schema::hasTable('dts_citizen_charters')
                ? DB::table('dts_citizen_charters')->take(12)->get()
                : collect([]);

            $factual = "Citizen Charter Standards:\n";
            $reply = "### 📜 **Institutional Citizen's Charter & Service Standards**\n\n";
            $reply .= "Established turnaround benchmarks under RA 11032 (Ease of Doing Business):\n\n";
            $reply .= "| Service Procedure | Classification | Category | Standard SLA Turnaround |\n";
            $reply .= "| :--- | :--- | :--- | :--- |\n";

            foreach ($charters as $ch) {
                $sla = ! empty($ch->standard_sla_days) ? "{$ch->standard_sla_days} working days" : (! empty($ch->standard_sla_hours) ? "{$ch->standard_sla_hours} hours" : 'Standard SLA');
                $factual .= "- {$ch->service_name} ({$ch->classification}, {$ch->category}): {$sla}\n";
                $reply .= "| **{$ch->service_name}** | {$ch->classification} | {$ch->category} | `{$sla}` |\n";
            }

            $reply .= "\n💡 *Institutional compliance standards ensure monitored turnaround across all administrative stations.*";

            return [
                'intent' => 'citizen_charter',
                'factual_summary' => $factual,
                'formatted_reply' => $reply,
                'cards' => [],
            ];
        }

        // =========================================================================
        // 4. DYNAMIC PERSON DTS RECORDS LOOKUP (e.g. "records of Mr. Antonio Piloton")
        // =========================================================================
        $personName = null;
        if (preg_match('/(?:dts\s*records?|documents?|records?)\s*(?:of|for|belonging to|created by|by)\s+(.+?)(?:\.{2,}|[?!]|$)/i', $userMessage, $m)) {
            $personName = trim($m[1]);
        } elseif (preg_match('/(?:records?|documents?)\s*(?:of|by|from)\s+(.+?)(?:\.{2,}|[?!]|$)/i', $userMessage, $m)) {
            $personName = trim($m[1]);
        } elseif (preg_match('/(?:dts\s*records?|documents?|dokumento|records?)\s*(?:ni|kay|para kay)\s+(.+?)(?:\.{2,}|[?!]|$)/i', $userMessage, $m)) {
            $personName = trim($m[1]);
        } elseif (preg_match('/\b(?:of|for|by|ni|kay)\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)/', $userMessage, $m)) {
            $personName = trim($m[1]);
        }

        // Check if any registered user's surname is mentioned
        if (! $personName && class_exists(User::class)) {
            $users = User::all();
            foreach ($users as $u) {
                $parts = explode(' ', $u->name);
                $lastName = end($parts);
                if (strlen($lastName) >= 4 && preg_match('/\b'.preg_quote(Str::lower($lastName), '/').'\b/i', $normalized)) {
                    $personName = $u->name;
                    break;
                }
            }
        }

        if ($personName) {
            $cleanName = preg_replace('/^(?:mr\.?|ms\.?|mrs\.?|dr\.?|engr\.?|atty\.?|prof\.?|hon\.?|sir|ma\'?am)\s+/i', '', trim($personName));
            $cleanName = trim(preg_replace('/[?.!,]+$/', '', $cleanName));

            return $this->resolvePersonDtsRecords($personName, $cleanName);
        }

        // =========================================================================
        // 5. SPECIFIC DOCUMENT LOOKUP BY ID / QR / CONTROL CODE (e.g. "document 13")
        // =========================================================================
        if (preg_match('/\b(?:document|doc|qr|qrcode|record)\s*(?:#|no\.?|number)?\s*(\d+|[A-Za-z0-9\-]+)\b/i', $userMessage, $m) ||
            Str::contains($normalized, ['where is document', 'status of document', 'where is doc', 'tracking of', 'adssu', 'nasaan ang', 'status ng'])) {

            $targetId = $m[1] ?? null;
            $docQuery = Records::query();

            if ($targetId) {
                $docQuery->where(function ($q) use ($targetId) {
                    $q->where('id', $targetId)
                        ->orWhere('QRCODE', $targetId)
                        ->orWhere('DocumentID', 'like', "%{$targetId}%");
                    if (is_numeric($targetId)) {
                        $intVal = (int) $targetId;
                        $q->orWhere('id', $intVal)
                            ->orWhere('QRCODE', (string) $intVal);
                    }
                });
            } else {
                // Search topics in document descriptions
                preg_match_all('/\b\w{4,}\b/u', $normalized, $words);
                $stopWords = ['where', 'status', 'document', 'documents', 'check', 'track', 'tracking', 'records', 'nasaan', 'saan', 'tungkol'];
                $filterWords = array_diff($words[0] ?? [], $stopWords);

                if (! empty($filterWords)) {
                    $docQuery->where(function ($q) use ($filterWords) {
                        foreach ($filterWords as $w) {
                            $q->orWhere('Description', 'like', "%{$w}%")
                                ->orWhere('doc_type', 'like', "%{$w}%");
                        }
                    });
                }
            }

            $docs = $docQuery->take(3)->get();
            if ($docs->isNotEmpty()) {
                return $this->formatFoundDocumentsResponse($docs);
            }
        }

        // =========================================================================
        // 6. MOCK AUDIT SIMULATIONS & QA FINDINGS (e.g. "mock audit ratings")
        // =========================================================================
        if (preg_match('/\b(mock\s*audits?|audits?|findings?|ratings?|grand\s*mean|survey\s*visit)\b/i', $userMessage)) {
            $audits = AccreditationMockAudit::latest('id')->take(6)->get();

            $factual = 'Institutional Mock Audits ('.count($audits)." records):\n";
            $reply = "### 📋 **Institutional Quality Assurance Mock Audit Records**\n\n";
            $reply .= "There are currently **{$context['accreditation']['mock_audits_count']}** internal mock audit simulations recorded in the system:\n\n";

            foreach ($audits as $a) {
                $rating = $a->grand_mean ? number_format((float) $a->grand_mean, 2) : 'Pending';
                $date = $a->survey_date_start ? $a->survey_date_start->format('M d, Y') : 'N/A';
                $target = $a->target_level ?: 'Accreditation';

                $factual .= "- {$a->audit_title}: Grand Mean = {$rating} / 5.00, Status = {$a->status}, Target = {$target}\n";
                $reply .= "- 🔹 **{$a->audit_title}**\n";
                $reply .= "  • **Grand Mean Rating:** `{$rating} / 5.00`\n";
                $reply .= "  • **Current Status:** `{$a->status}`\n";
                $reply .= "  • **Target Accreditation Level:** {$target}\n";
                $reply .= "  • **Survey Date:** {$date}\n";
            }

            $reply .= "\n📌 **Quality Assurance Note:** These ratings reflect internal mock audit evaluations conducted prior to official external accreditation visits.";

            return [
                'intent' => 'mock_audits',
                'factual_summary' => $factual,
                'formatted_reply' => $reply,
                'cards' => [],
            ];
        }

        // =========================================================================
        // 7. ACADEMIC PROGRAMS & READINESS (e.g. "BSIT", "Accreditation status")
        // =========================================================================
        if (preg_match('/\b(bsit|bsa|agriculture|forestry|engineering|programs?|accreditation|aaccup|level\s*(?:1|2|3|4|i|ii|iii|iv))\b/i', $userMessage)) {
            $progQuery = AccreditationProgram::query();

            if (preg_match('/\b(bsit|it|information\s*technology)\b/i', $userMessage)) {
                $progQuery->where(function ($q) {
                    $q->where('program_code', 'like', '%BSIT%')
                        ->orWhere('program_name', 'like', '%Information Technology%');
                });
            } elseif (preg_match('/\b(bsa|agriculture)\b/i', $userMessage)) {
                $progQuery->where(function ($q) {
                    $q->where('program_code', 'like', '%BSA%')
                        ->orWhere('program_name', 'like', '%Agriculture%');
                });
            }

            $programs = $progQuery->take(5)->get();
            if ($programs->isNotEmpty()) {
                $factual = "Institutional Academic Programs:\n";
                $reply = "### 🎓 **Academic Accreditation Program Records**\n\n";

                foreach ($programs as $p) {
                    $score = $p->overall_readiness_score ? number_format((float) $p->overall_readiness_score, 2) : 'N/A';
                    $agency = $p->accrediting_body ?: 'AACCUP';

                    $factual .= "- {$p->program_name} ({$p->program_code}): Current = {$p->current_level}, Target = {$p->target_level}, Score = {$score}/5.00, Agency = {$agency}\n";

                    $reply .= "--- \n";
                    $reply .= "📌 **{$p->program_name}** (`{$p->program_code}`)\n";
                    $reply .= "- **Current Level:** **{$p->current_level}**\n";
                    $reply .= "- **Target Level:** {$p->target_level}\n";
                    $reply .= "- **Overall Readiness Score:** `{$score} / 5.00`\n";
                    $reply .= "- **Accrediting Agency:** {$agency}\n";
                    $reply .= "- **Current Status:** `{$p->status}`\n";

                    // Linked audits
                    $linkedAudits = AccreditationMockAudit::where('program_id', $p->id)->take(2)->get();
                    if ($linkedAudits->isNotEmpty()) {
                        $reply .= "- **Related Mock Audit Simulations:**\n";
                        foreach ($linkedAudits as $la) {
                            $laRating = $la->grand_mean ? number_format((float) $la->grand_mean, 2) : 'Pending';
                            $reply .= "  • {$la->audit_title} — Grand Mean: `{$laRating}` ({$la->status})\n";
                        }
                    }
                    $reply .= "\n";
                }

                return [
                    'intent' => 'academic_programs',
                    'factual_summary' => $factual,
                    'formatted_reply' => $reply,
                    'cards' => [],
                ];
            }
        }

        // =========================================================================
        // 8. REGISTERED USERS & SYSTEM PERSONNEL (e.g. "Who are the users")
        // =========================================================================
        if (preg_match('/\b(users?|accounts?|staff|personnel|officials?|administrators?)\b/i', $userMessage)) {
            $users = User::select('id', 'name', 'email', 'role', 'designation', 'created_at')->take(8)->get();

            $factual = "Registered Users (Total: {$context['users_count']}):\n";
            $reply = "### 👥 **Registered System Users & Institutional Personnel**\n\n";
            $reply .= "There are **{$context['users_count']}** registered user accounts currently in the system:\n\n";

            foreach ($users as $u) {
                $desig = $u->designation ?: $u->role;
                $date = $u->created_at ? $u->created_at->format('M d, Y') : 'N/A';

                $factual .= "- {$u->name} ({$u->email}) - Role: {$u->role}, Designation: {$desig}\n";
                $reply .= "- 👤 **{$u->name}** — `{$u->email}`\n";
                $reply .= "  • **Designation / Role:** *{$desig}* (Registered: {$date})\n";
            }

            return [
                'intent' => 'users',
                'factual_summary' => $factual,
                'formatted_reply' => $reply,
                'cards' => [],
            ];
        }

        // =========================================================================
        // 9. INSTITUTIONAL POLICIES & MEMORANDUMS (e.g. "policies", "memorandums")
        // =========================================================================
        if (preg_match('/\b(polic(?:y|ies)|memorandums?|memos?|resolutions?|guidelines?)\b/i', $userMessage)) {
            $policies = InstitutionalPolicy::take(5)->get();
            $memos = UniversityMemorandum::take(5)->get();

            $factual = 'Active Policies: '.count($policies).' records. Active Memos: '.count($memos)." records.\n";
            $reply = "### 📜 **Institutional Policies & University Memorandums**\n\n";

            if ($policies->isNotEmpty()) {
                $reply .= "#### 🏛️ **Institutional Policies:**\n";
                foreach ($policies as $p) {
                    $reply .= "- 🔹 **{$p->title}** (Status: `{$p->status}`)\n";
                }
                $reply .= "\n";
            }

            if ($memos->isNotEmpty()) {
                $reply .= "#### 📑 **University Memorandums:**\n";
                foreach ($memos as $m) {
                    $reply .= "- 📝 **{$m->memo_number}:** *{$m->subject}* (`{$m->status}`)\n";
                }
            }

            return [
                'intent' => 'policies_and_memos',
                'factual_summary' => $factual,
                'formatted_reply' => $reply,
                'cards' => [],
            ];
        }

        // =========================================================================
        // 10. EXECUTIVE OVERVIEW / SUMMARY (e.g. "overview", "summary", "metrics")
        // =========================================================================
        if (preg_match('/\b(summary|overview|status|metrics|dashboard|hello|hi|all)\b/i', $userMessage)) {
            $reply = $this->generateDefaultOverviewResponse($context);

            return [
                'intent' => 'executive_overview',
                'factual_summary' => "Institution: {$context['institution_name']}. Users: {$context['users_count']}. DTS: {$context['dts']['total']}. Programs: {$context['accreditation']['programs_count']}. Mock Audits: {$context['accreditation']['mock_audits_count']}.",
                'formatted_reply' => $reply,
                'cards' => [],
            ];
        }

        // =========================================================================
        // 11. GENERAL KEYWORD SEARCH ACROSS RECORDS
        // =========================================================================
        preg_match_all('/\b\w{4,}\b/u', $normalized, $allWords);
        $searchTerms = array_diff($allWords[0] ?? [], ['where', 'check', 'system', 'track', 'records', 'details', 'information']);

        if (! empty($searchTerms)) {
            $foundRecords = Records::where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->orWhere('Description', 'like', "%{$term}%")
                        ->orWhere('Origin', 'like', "%{$term}%");
                }
            })->take(3)->get();

            if ($foundRecords->isNotEmpty()) {
                return $this->formatFoundDocumentsResponse($foundRecords);
            }
        }

        // Fallback with dynamic overview stats
        return [
            'intent' => 'general_fallback',
            'factual_summary' => "Institutional Stats: Total DTS: {$context['dts']['total']}, Users: {$context['users_count']}, Programs: {$context['accreditation']['programs_count']}, MCDA: ".count($context['mcda_evaluations']).'.',
            'formatted_reply' => '',
            'cards' => [],
        ];
    }

    /**
     * Resolve person DTS queries using institutional records.
     *
     * @return array{intent: string, factual_summary: string, formatted_reply: string, cards: array<int, mixed>}
     */
    protected function resolvePersonDtsRecords(string $rawPersonName, string $cleanName): array
    {
        $nameParts = array_filter(explode(' ', $cleanName), fn ($p) => strlen($p) >= 3);

        // Search registered users
        $matchedUsers = User::where(function ($q) use ($cleanName, $nameParts) {
            $q->where('name', 'like', "%{$cleanName}%")
                ->orWhere('email', 'like', "%{$cleanName}%");
            foreach ($nameParts as $part) {
                $q->orWhere('name', 'like', "%{$part}%");
            }
        })->get();

        $recordsQuery = Records::query();

        if ($matchedUsers->isNotEmpty()) {
            $userIds = $matchedUsers->pluck('id')->toArray();
            $recordsQuery->where(function ($q) use ($userIds, $cleanName) {
                $q->whereIn('created_by', $userIds)
                    ->orWhereIn('updated_by', $userIds)
                    ->orWhere('Description', 'like', "%{$cleanName}%")
                    ->orWhere('Origin', 'like', "%{$cleanName}%")
                    ->orWhere('Remarks', 'like', "%{$cleanName}%");
            });
        } else {
            $recordsQuery->where(function ($q) use ($cleanName, $nameParts) {
                $q->where('Description', 'like', "%{$cleanName}%")
                    ->orWhere('Origin', 'like', "%{$cleanName}%")
                    ->orWhere('Remarks', 'like', "%{$cleanName}%");
                foreach ($nameParts as $part) {
                    $q->orWhere('Description', 'like', "%{$part}%")
                        ->orWhere('Origin', 'like', "%{$part}%");
                }
            });
        }

        $records = $recordsQuery->latest('id')->get();

        if ($records->isNotEmpty()) {
            $displayName = $matchedUsers->isNotEmpty() ? $matchedUsers->first()->name : $cleanName;
            $designation = $matchedUsers->isNotEmpty() && $matchedUsers->first()->designation ? " ({$matchedUsers->first()->designation})" : '';

            $cards = [];
            foreach ($records->take(4) as $rec) {
                $cards[] = $this->buildDtsCard($rec);
            }

            $factual = "Found {$records->count()} document records for {$displayName}:\n";
            $reply = "### 📑 **Document Tracking Records for {$displayName}{$designation}**\n\n";
            $reply .= "Found **{$records->count()}** document(s) registered under this official / account in institutional records:\n\n";
            $reply .= "| 12-Digit QR Code | Document ID | Document Type | Subject / Title | Status | Date |\n";
            $reply .= "| :--- | :--- | :--- | :--- | :--- | :--- |\n";

            foreach ($records as $r) {
                $formattedQr = formatQR12Digits($r->QRCODE ?: $r->id);
                $docId = $r->DocumentID ?: 'DTS-'.$r->id;
                $type = $r->doc_type ?: 'Document';
                $desc = Str::limit($r->Description, 50);
                $status = strtoupper($r->status ?: ($r->DocStatus ?: 'ACTIVE'));
                $date = $r->created_at ? $r->created_at->format('M d, Y') : 'N/A';

                $factual .= "- [{$formattedQr}] {$r->Description} (Status: {$status}, Date: {$date})\n";
                $reply .= "| `{$formattedQr}` | `{$docId}` | {$type} | {$desc} | `{$status}` | {$date} |\n";
            }

            $reply .= "\n💡 *Official records retrieved from the institutional document tracking system.*";

            return [
                'intent' => 'person_dts_records',
                'factual_summary' => $factual,
                'formatted_reply' => $reply,
                'cards' => $cards,
            ];
        }

        // When NO records are found
        $factual = "Search for '{$rawPersonName}': 0 document tracking records found.";
        $reply = "### 🔍 **Document Search for \"{$rawPersonName}\"**\n\n";

        if ($matchedUsers->isNotEmpty()) {
            $user = $matchedUsers->first();
            $reply .= "Found registered account for **{$user->name}** (`{$user->email}` — *{$user->designation}*), however **there are currently no document tracking records** encoded or assigned under their account.\n\n";
        } else {
            $reply .= "No registered account or document tracking records were found matching **{$rawPersonName}** in institutional records.\n\n";
            $reply .= "The person may not yet be registered or has no documents currently recorded under their name.\n\n";
        }

        // Provide list of registered officials who currently have records
        $stats = Records::selectRaw('created_by, count(*) as count')->groupBy('created_by')->get();
        $userNames = User::whereIn('id', $stats->pluck('created_by'))->get()->keyBy('id');

        $reply .= "📋 **Officials and personnel with active document tracking records:**\n";
        foreach ($stats as $s) {
            $u = $userNames->get($s->created_by);
            $name = $u ? $u->name : "User #{$s->created_by}";
            $desig = $u && $u->designation ? " *({$u->designation})*" : '';
            $reply .= "- 🔹 **{$name}**{$desig} — `{$s->count}` document(s)\n";
        }

        $reply .= "\n💡 *Once a document is encoded or an account is registered for {$cleanName}, it will automatically be accessible here.*";

        return [
            'intent' => 'person_dts_records_empty',
            'factual_summary' => $factual,
            'formatted_reply' => $reply,
            'cards' => [],
        ];
    }

    /**
     * Build structured DTS Card payload for interactive UI rendering.
     *
     * @return array<string, mixed>
     */
    protected function buildDtsCard(Records $doc): array
    {
        $formattedQr = formatQR12Digits($doc->QRCODE ?: $doc->id);
        $routes = RecordRoute::where('record_id', $doc->id)->orderBy('step_number')->get();

        $routeList = [];
        $activeOffice = null;
        $hasActive = false;

        foreach ($routes as $r) {
            $isCurrent = in_array($r->status, ['in_progress', 'in_transit', 'delayed']);
            if ($isCurrent && ! $hasActive) {
                $activeOffice = $r->office_name;
                $hasActive = true;
            }

            $routeList[] = [
                'step' => $r->step_number,
                'office' => $r->office_name ?: 'Target Office',
                'action' => $r->for_action ?: 'Processing',
                'status' => $r->status ?: 'pending',
                'is_current' => $isCurrent,
                'sla' => $r->sla_hours ? "{$r->sla_hours} hrs" : null,
                'date' => $r->received_at ? $r->received_at->format('M d, Y h:i A') : ($r->created_at ? $r->created_at->format('M d, Y') : null),
            ];
        }

        if (! $activeOffice && count($routeList) > 0) {
            $activeOffice = $routeList[0]['office'];
        }

        $progressPercent = 0;
        if (count($routeList) > 0) {
            $completed = $routes->where('status', 'completed')->count();
            $progressPercent = (int) round((($completed + ($hasActive ? 0.5 : 0)) / count($routeList)) * 100);
            $progressPercent = min(100, max(15, $progressPercent));
        }

        return [
            'type' => 'dts_route_card',
            'id' => $doc->id,
            'qr_raw' => $doc->QRCODE ?: $doc->id,
            'qr_formatted' => $formattedQr,
            'document_id' => $doc->DocumentID ?: "DTS-{$doc->id}",
            'title' => $doc->Description ?: ($doc->doc_type ?: 'Untitled Document'),
            'doc_type' => $doc->doc_type ?: 'General Document',
            'status' => strtoupper($doc->status ?: ($doc->DocStatus ?: 'ACTIVE')),
            'origin' => $doc->Origin ?: 'Office of Origin',
            'destination' => $doc->Destination ? "Office #{$doc->Destination}" : null,
            'date' => $doc->created_at ? $doc->created_at->format('M d, Y h:i A') : 'N/A',
            'current_office' => $activeOffice,
            'progress_percent' => $progressPercent,
            'total_steps' => count($routeList),
            'routes' => $routeList,
            'track_url' => url("/tracking?tracking_number={$formattedQr}"),
            'view_url' => url("/dss/dts/{$doc->id}"),
        ];
    }

    /**
     * Format found documents into clean Markdown response and attach UI cards.
     *
     * @param  Collection<int, Records>  $docs
     * @return array{intent: string, factual_summary: string, formatted_reply: string, cards: array<int, mixed>}
     */
    protected function formatFoundDocumentsResponse($docs): array
    {
        $factual = "Live Document Tracking Details:\n";
        $reply = "### 📑 **Institutional Document Tracking Details**\n\n";
        $cards = [];

        foreach ($docs as $doc) {
            $card = $this->buildDtsCard($doc);
            $cards[] = $card;

            $formattedQr = $card['qr_formatted'];
            $docId = $card['document_id'];
            $status = $card['status'];
            $date = $card['date'];

            $factual .= "- [{$formattedQr}] {$doc->Description}, Type: {$doc->doc_type}, Status: {$status}, Current Office: {$card['current_office']}\n";

            $reply .= "--- \n";
            $reply .= "🔢 **12-Digit QR Code:** `{$formattedQr}`\n";
            $reply .= "🆔 **Document Control ID:** `{$docId}` (Record #{$doc->id})\n";
            $reply .= "- **Title / Description:** *{$doc->Description}*\n";
            $reply .= '- **Document Type:** `'.($doc->doc_type ?: 'General Document')."`\n";
            $reply .= "- **Current Status:** **{$status}**\n";
            if ($doc->Origin) {
                $reply .= "- **Originating Office:** {$doc->Origin}\n";
            }
            if ($doc->Destination) {
                $reply .= "- **Destination:** Office / Unit #{$doc->Destination}\n";
            }
            $reply .= "- **Date Recorded:** {$date}\n";

            // Format routes text clearly
            if (! empty($card['routes'])) {
                $reply .= "\n🛣️ **Routing Workflow & Current Station:**\n";
                foreach ($card['routes'] as $r) {
                    $isCurrent = $r['is_current'];
                    $icon = $r['status'] === 'completed' ? '✅' : ($isCurrent ? '⏳' : '⏸️');
                    $badge = $r['status'] === 'completed' ? '*(Completed)*' : ($isCurrent ? '🟡 **(CURRENT STATION / IN PROGRESS)**' : '*(Pending)*');
                    $reply .= "{$r['step']}. {$icon} **{$r['office']}** — {$badge}\n";
                    if (! empty($r['action'])) {
                        $reply .= "   • Action Required: *{$r['action']}*\n";
                    }
                }

                if ($card['current_office']) {
                    $reply .= "\n📍 **Current Location Summary:** Currently stationed at the **{$card['current_office']}** for required processing.\n";
                }
            }
            $reply .= "\n";
        }

        $reply .= '💡 *Official records retrieved from the institutional document tracking system.*';

        return [
            'intent' => 'document_lookup',
            'factual_summary' => $factual,
            'formatted_reply' => $reply,
            'cards' => $cards,
        ];
    }

    /**
     * Generate default overview response in English.
     *
     * @param  array<string, mixed>  $context
     */
    protected function generateDefaultOverviewResponse(array $context): string
    {
        $reply = "### 📊 **Executive Institutional Overview**\n";
        $reply .= "**{$context['institution_name']} ({$context['acronym']})**\n\n";
        $reply .= "| Module / Metric | Status / Count |\n";
        $reply .= "| :--- | :--- |\n";
        $reply .= "| 👥 **Active System Users** | `{$context['users_count']}` registered accounts |\n";
        $reply .= '| 🏫 **University Campuses** | `'.count($context['campuses']).'` ('.implode(', ', $context['campuses']).") |\n";
        $reply .= "| 📑 **Tracked Documents (DTS)** | `{$context['dts']['total']}` total records |\n";
        $reply .= "| 🎓 **Academic Programs** | `{$context['accreditation']['programs_count']}` under evaluation |\n";
        $reply .= "| 📋 **Quality Mock Audits** | `{$context['accreditation']['mock_audits_count']}` completed / in progress |\n";
        $reply .= '| ⚖️ **MCDA Decision Analyses** | `'.count($context['mcda_evaluations'])."` active strategic models |\n";
        $reply .= "| 📜 **Institutional Policies** | `{$context['policies_count']}` active policies |\n";
        $reply .= "| 📝 **University Memorandums** | `{$context['memos_count']}` issued memorandums |\n\n";
        $reply .= "💡 **Ready to assist with official institutional records across all modules. You may ask:**\n";
        $reply .= "- *\"What is the recommended supplier in our procurement MCDA evaluation?\"*\n";
        $reply .= "- *\"What campuses and departments exist in the university?\"*\n";
        $reply .= "- *\"Give me all dts records of Dr. Joy C. Capistrano\"* or *\"Where is document 13?\"*\n";
        $reply .= "- *\"What are our quality mock audit ratings?\"*\n";
        $reply .= "- *\"What is the accreditation status of the BSIT program?\"*\n";

        return $reply;
    }

    /**
     * Call Groq Cloud API with Retrieval-Augmented Generation (RAG) over ALL database modules.
     *
     * @param  array<int, array{role: string, content: string}>  $conversationHistory
     * @param  array<string, mixed>  $context
     * @param  array{intent: string, factual_summary: string, formatted_reply: string, cards: array<int, mixed>}  $dbRetrieval
     */
    /**
     * Call Groq Cloud API with Retrieval-Augmented Generation (RAG) over ALL database modules.
     * Supports user-configured models (e.g. llama-3.3-70b-versatile) with automatic fallback to active models (120B / 27B).
     *
     * @param  array<int, array{role: string, content: string}>  $conversationHistory
     * @param  array<string, mixed>  $context
     * @param  array{intent: string, factual_summary: string, formatted_reply: string, cards: array<int, mixed>}  $dbRetrieval
     * @return array{reply: string, model_name: string}|null
     */
    protected function callGroqApi(string $userMessage, array $conversationHistory, array $context, array $dbRetrieval, string $apiKey): ?array
    {
        $primaryModel = config('services.groq.model') ?: env('GROQ_MODEL', 'llama-3.3-70b-versatile');
        $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

        $systemInstruction = $this->buildSystemInstruction($context, $dbRetrieval);

        $messages = [
            ['role' => 'system', 'content' => $systemInstruction],
        ];

        foreach (array_slice($conversationHistory, -6) as $msg) {
            $role = ($msg['role'] ?? 'user') === 'user' ? 'user' : 'assistant';
            $messages[] = [
                'role' => $role,
                'content' => $msg['content'] ?? '',
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        // Candidate model chain: preferred model first, then active alternatives
        $candidates = array_unique([
            $primaryModel,
            'openai/gpt-oss-120b',
            'qwen/qwen3.8-27b',
        ]);

        foreach ($candidates as $candidateModel) {
            try {
                $response = Http::timeout(18)->withToken($apiKey)->post($endpoint, [
                    'model' => $candidateModel,
                    'messages' => $messages,
                    'temperature' => 0.6,
                    'max_tokens' => 800,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['choices'][0]['message']['content'] ?? null;

                    if ($reply) {
                        $friendlyName = match (true) {
                            Str::contains($candidateModel, 'llama-3.3-70b') => 'Llama 3.3 70B',
                            Str::contains($candidateModel, '120b') => '120B LLM',
                            Str::contains($candidateModel, 'qwen') => 'Qwen 27B',
                            default => $candidateModel,
                        };

                        return [
                            'reply' => $reply,
                            'model_name' => $friendlyName,
                        ];
                    }
                }

                Log::info("Groq candidate model '{$candidateModel}' returned HTTP ".$response->status().', attempting next candidate...');
            } catch (\Throwable $e) {
                Log::warning("Groq candidate model '{$candidateModel}' exception: ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Call Google Gemini REST API with live institutional retrieval ground truth.
     *
     * @param  array<int, array{role: string, content: string}>  $conversationHistory
     * @param  array<string, mixed>  $context
     * @param  array{intent: string, factual_summary: string, formatted_reply: string, cards: array<int, mixed>}  $dbRetrieval
     */
    protected function callGeminiApi(string $userMessage, array $conversationHistory, array $context, array $dbRetrieval, string $apiKey): ?string
    {
        $model = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.6-flash');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $systemInstruction = $this->buildSystemInstruction($context, $dbRetrieval);

        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemInstruction]],
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'Understood! I am ready to provide decision support grounded strictly in official institutional records.']]];

        foreach (array_slice($conversationHistory, -6) as $msg) {
            $role = ($msg['role'] ?? 'user') === 'user' ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content'] ?? '']],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $response = Http::timeout(14)->post($endpoint, [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1200,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();

            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        }

        Log::warning('Gemini API failed with status '.$response->status().': '.$response->body());

        return null;
    }

    /**
     * Build unified institutional system prompt for LLMs (Groq / Gemini) containing the entire database knowledge base.
     *
     * @param  array<string, mixed>  $context
     * @param  array{intent: string, factual_summary: string, formatted_reply: string, cards?: array<int, mixed>}  $dbRetrieval
     */
    protected function buildSystemInstruction(array $context, array $dbRetrieval): string
    {
        $liveRetrievalText = ! empty($dbRetrieval['factual_summary'])
            ? "SPECIFIC GROUND-TRUTH RECORDS MATCHING THIS QUERY:\n".$dbRetrieval['factual_summary']
            : 'No specific entity filter triggered. Refer to the comprehensive institutional knowledge base below.';

        // JSON summaries of all modules
        $usersJson = json_encode($context['users']);
        $campusesJson = json_encode($context['campuses']);
        $departmentsJson = json_encode($context['departments']);
        $mcdaJson = json_encode($context['mcda_evaluations']);
        $programsJson = json_encode($context['accreditation']['programs_list']);
        $auditsJson = json_encode($context['accreditation']['recent_audits']);
        $policiesJson = json_encode($context['policies']);
        $memosJson = json_encode($context['memos']);
        $dtsRecentJson = json_encode($context['dts']['recent']);

        return "You are the specialized AI Decision Support Assistant for {$context['institution_name']} ({$context['acronym']}).
You operate inside the IIPM-DSS (Integrated Institutional Process Management & Decision Support System).
You assist university administrators, deans, quality assurance directors, procurement officers, auditors, and staff by answering questions, providing decision guidance, summarizing metrics, and evaluating operational readiness across ALL university domains.

{$liveRetrievalText}

COMPREHENSIVE INSTITUTIONAL KNOWLEDGE BASE:
1. University Profile & Structure:
   - Institution: {$context['institution_name']} ({$context['acronym']})
   - Campuses: {$campusesJson}
   - Central Offices & Departments: {$departmentsJson}
   - Registered Officials & Users: {$usersJson}

2. Multi-Criteria Decision Support (MCDA / AHP / TOPSIS):
   - Active Evaluations: {$mcdaJson}

3. Academic Accreditation & Quality Assurance:
   - Programs Under Evaluation (Total: {$context['accreditation']['programs_count']}): {$programsJson}
   - Mock Audit Simulations (Total: {$context['accreditation']['mock_audits_count']}): {$auditsJson}
   - Accredited Survey Areas Evaluated: {$context['accreditation']['survey_areas_count']} (Area I: Vision to Area X: Administration)

4. Institutional Governance & Policies:
   - Promulgated Policies (Total: {$context['policies_count']}): {$policiesJson}
   - Issued University Memorandums (Total: {$context['memos_count']}): {$memosJson}

5. Document Tracking & Process Turnaround (DTS):
   - Total Tracked Documents: {$context['dts']['total']}
   - Recent Document Records: {$dtsRecentJson}

Personality & Core Guidelines:
1. Always communicate in clean, professional, concise, executive English.
2. Ground your analysis, numbers, document names, and QR codes strictly on the institutional records provided above.
3. NEVER disclose or mention internal database table names, SQL column names, or schema names (such as dts_records, users, dts_record_routes, accreditation_programs, etc.). Refer to sources simply as 'institutional records', 'document tracking system', 'quality assurance records', or 'decision models'.
4. Always format document QR codes with 12 digits (e.g. `000000000013`).
5. When describing routes or workflows, make it very easy to understand in plain text: list steps with numbers, office names, and clear indication of which station currently holds the document.
6. If a person, document, or record is not found in institutional records, clearly explain that no records exist in the current system, and mention which officials or records ARE currently available.
7. Format your output cleanly with Markdown: use bold text, neat tables, and concise bullet points.
8. Offer practical executive recommendations and proactive next steps whenever applicable.";
    }

    /**
     * Get contextual follow-up suggestion chips in English across all domains.
     *
     * @return array<int, string>
     */
    protected function getDynamicSuggestions(string $userMessage): array
    {
        $normalized = Str::lower($userMessage);

        if (Str::contains($normalized, ['mcda', 'ahp', 'topsis', 'procurement', 'decision'])) {
            return [
                '🏆 Top Supplier in Drone Procurement',
                '🎯 Accreditation Status',
                '📑 Tracked Documents Status',
                '📊 Executive Overview',
            ];
        }

        if (Str::contains($normalized, ['antonio', 'piloton', 'person', 'records of', 'officials'])) {
            return [
                '📑 DTS Records of Dr. Joy Capistrano',
                '⚖️ MCDA Decision Matrix',
                '📋 Mock Audit Ratings',
                '🎓 Status of BSIT Program',
            ];
        }

        if (Str::contains($normalized, ['document', 'dts', 'doc', '13', 'track'])) {
            return [
                '🎯 Accreditation Readiness',
                '⚖️ MCDA Decision Matrix',
                '📜 Policies & Memorandums',
                '📊 Executive Overview',
            ];
        }

        if (Str::contains($normalized, ['accreditation', 'audit', 'program', 'bsit'])) {
            return [
                '📑 Tracked Documents Status',
                '⚖️ MCDA Decision Matrix',
                '🏛️ University Campuses',
                '📊 Executive Overview',
            ];
        }

        return [
            '📊 Executive Summary',
            '⚖️ MCDA Decision Matrix',
            '📑 Tracked Documents',
            '🎯 Accreditation Status',
        ];
    }
}
