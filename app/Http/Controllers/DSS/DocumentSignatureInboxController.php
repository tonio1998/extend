<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\Policy\InstitutionalPolicy;
use App\Models\Policy\UniversityMemorandum;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentSignatory;
use App\Services\DSS\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentSignatureInboxController extends Controller
{
    /**
     * Display the "For Signature" queue / inbox.
     * Shows all institutional documents requiring electronic signature/endorsement from the logged-in user.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $tenant = TenantContext::getTenant();
        $tenantId = $tenant?->id ?? 1;
        $institution = TenantContext::getInstitution();

        $activeTab = $request->query('tab', 'pending');
        $search = trim((string) $request->query('search', ''));
        $campusId = $request->query('campus_id');
        $docFilter = $request->query('doc_filter');

        // Base query scoped to logged-in signatory user
        $baseQuery = DocumentSignatory::query()
            ->with(['document', 'dtsRecord', 'campus', 'employee', 'signedByUser', 'documentType'])
            ->where('tenant_id', $tenantId)
            ->forSignatoryUser($user);

        // Compute counts
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $signedCount = (clone $baseQuery)->where('status', 'signed')->count();

        // Apply tab filter
        $query = clone $baseQuery;
        if ($activeTab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($activeTab === 'signed') {
            $query->where('status', 'signed');
        }

        // Campus filter
        if ($campusId) {
            $query->where('campus_id', $campusId);
        }

        // Doc type filter
        if ($docFilter) {
            $query->where('document_type', 'like', "%{$docFilter}%");
        }

        // Search filter across signatory info, DTS tracking, and polymorphic document fields
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('role_label', 'like', "%{$search}%")
                    ->orWhere('tier_role', 'like', "%{$search}%")
                    ->orWhere('signatory_name', 'like', "%{$search}%")
                    ->orWhere('signature_hash', 'like', "%{$search}%")
                    ->orWhereHas('dtsRecord', function ($dtsQ) use ($search) {
                        $dtsQ->where('DocumentID', 'like', "%{$search}%")
                            ->orWhere('QRCODE', 'like', "%{$search}%")
                            ->orWhere('Subject', 'like', "%{$search}%")
                            ->orWhere('DocumentNumber', 'like', "%{$search}%");
                    })
                    ->orWhereHasMorph('document', [
                        ExtensionProposal::class,
                        ExtensionPlan::class,
                        UniversityMemorandum::class,
                        InstitutionalPolicy::class,
                    ], function ($morphQ, $type) use ($search) {
                        if ($type === ExtensionProposal::class) {
                            $morphQ->where('title', 'like', "%{$search}%")
                                ->orWhere('proposal_code', 'like', "%{$search}%")
                                ->orWhere('tracking_number', 'like', "%{$search}%");
                        } elseif ($type === ExtensionPlan::class) {
                            $morphQ->where('extension_activity', 'like', "%{$search}%")
                                ->orWhere('plan_code', 'like', "%{$search}%");
                        } elseif ($type === UniversityMemorandum::class) {
                            $morphQ->where('subject', 'like', "%{$search}%")
                                ->orWhere('memo_number', 'like', "%{$search}%");
                        } elseif ($type === InstitutionalPolicy::class) {
                            $morphQ->where('title', 'like', "%{$search}%")
                                ->orWhere('policy_number', 'like', "%{$search}%");
                        }
                    });
            });
        }

        $items = $query->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $campuses = Campus::where('tenant_id', $tenantId)->orderBy('campus_name')->get();

        return view('dss.signatures.index', compact(
            'items',
            'user',
            'tenant',
            'institution',
            'activeTab',
            'pendingCount',
            'signedCount',
            'campuses',
            'campusId',
            'docFilter',
            'search'
        ));
    }
}
