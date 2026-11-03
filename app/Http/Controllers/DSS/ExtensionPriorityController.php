<?php

namespace App\Http\Controllers\DSS;

use App\Http\Controllers\Controller;
use App\Models\RIE\ExtensionAgenda;
use App\Models\RIE\ExtensionBannerProgram;
use App\Models\RIE\ExtensionProposal;
use App\Services\DSS\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtensionPriorityController extends Controller
{
    /**
     * Display Master Institutional Extension Agendas & Banner Programs Studio
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::getTenant();
        $institution = TenantContext::getInstitution();
        $theme = TenantContext::getTheme();

        $activeTab = $request->query('tab', 'banner_programs');

        // Retrieve banner programs and agendas
        $bannerPrograms = ExtensionBannerProgram::ordered()->get();
        $agendas = ExtensionAgenda::ordered()->get();

        // Retrieve all extension proposals with essential linkage fields
        $proposals = ExtensionProposal::select(
            'id', 'title', 'banner_program', 'banner_program_other', 'agenda_priorities', 'extension_plan_id'
        )->get();

        // Calculate statistics
        $totalBannerPrograms = $bannerPrograms->count();
        $activeBannerPrograms = $bannerPrograms->where('is_active', true)->count();
        $inactiveBannerPrograms = $totalBannerPrograms - $activeBannerPrograms;

        $totalAgendas = $agendas->count();
        $activeAgendas = $agendas->where('is_active', true)->count();
        $inactiveAgendas = $totalAgendas - $activeAgendas;

        // Calculate involved extensions per banner program
        $bannerProposalCounts = [];
        $bannerPlanCounts = [];
        foreach ($bannerPrograms as $program) {
            $code = mb_strtolower(trim($program->code ?? ''));
            $name = mb_strtolower(trim($program->name ?? ''));

            $matched = $proposals->filter(function ($proposal) use ($code, $name) {
                $val = mb_strtolower(trim((string) ($proposal->banner_program === 'Others' && ! empty($proposal->banner_program_other) ? $proposal->banner_program_other : $proposal->banner_program)));

                return ($code !== '' && $val === $code) || ($name !== '' && $val === $name);
            });

            $bannerProposalCounts[$program->id] = $matched->count();
            $bannerPlanCounts[$program->id] = $matched->whereNotNull('extension_plan_id')->pluck('extension_plan_id')->unique()->count();
        }

        // Calculate involved extensions per agenda
        $agendaProposalCounts = [];
        $agendaPlanCounts = [];
        foreach ($agendas as $agenda) {
            $code = mb_strtolower(trim($agenda->code ?? ''));
            $name = mb_strtolower(trim($agenda->name ?? ''));

            $matched = $proposals->filter(function ($proposal) use ($code, $name) {
                $priorities = $proposal->agenda_priorities;
                if (is_string($priorities)) {
                    $priorities = json_decode($priorities, true) ?: [];
                }
                if (! is_array($priorities)) {
                    return false;
                }
                foreach ($priorities as $priority) {
                    $val = mb_strtolower(trim((string) $priority));
                    if (($code !== '' && $val === $code) || ($name !== '' && $val === $name)) {
                        return true;
                    }
                }

                return false;
            });

            $agendaProposalCounts[$agenda->id] = $matched->count();
            $agendaPlanCounts[$agenda->id] = $matched->whereNotNull('extension_plan_id')->pluck('extension_plan_id')->unique()->count();
        }

        $totalInvolvedProposals = $proposals->count();
        $totalInvolvedPlans = $proposals->whereNotNull('extension_plan_id')->pluck('extension_plan_id')->unique()->count();
        $totalPriorities = $totalBannerPrograms + $totalAgendas;
        $activePriorities = $activeBannerPrograms + $activeAgendas;
        $inactivePriorities = $inactiveBannerPrograms + $inactiveAgendas;

        return view('dss.settings.rie_priorities.index', compact(
            'tenant',
            'institution',
            'theme',
            'activeTab',
            'bannerPrograms',
            'agendas',
            'totalBannerPrograms',
            'activeBannerPrograms',
            'inactiveBannerPrograms',
            'totalAgendas',
            'activeAgendas',
            'inactiveAgendas',
            'bannerProposalCounts',
            'bannerPlanCounts',
            'agendaProposalCounts',
            'agendaPlanCounts',
            'totalInvolvedProposals',
            'totalInvolvedPlans',
            'totalPriorities',
            'activePriorities',
            'inactivePriorities'
        ));
    }

    /**
     * Store a newly created Banner Program
     */
    public function storeBannerProgram(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:extension_banner_programs,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? (ExtensionBannerProgram::max('sort_order') + 1);

        $program = ExtensionBannerProgram::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Banner program '{$program->name}' created successfully.",
                'program' => $program,
            ]);
        }

        return redirect()->route('dss.settings.rie_priorities.index', ['tab' => 'banner_programs'])
            ->with('success', "Banner program '{$program->name}' created successfully.");
    }

    /**
     * Update an existing Banner Program
     */
    public function updateBannerProgram(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $program = ExtensionBannerProgram::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:extension_banner_programs,code,'.$program->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', $program->is_active);

        $program->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Banner program '{$program->name}' updated successfully.",
                'program' => $program,
            ]);
        }

        return redirect()->route('dss.settings.rie_priorities.index', ['tab' => 'banner_programs'])
            ->with('success', "Banner program '{$program->name}' updated successfully.");
    }

    /**
     * Toggle Banner Program active status
     */
    public function toggleBannerProgramStatus(Request $request, int $id): JsonResponse
    {
        $program = ExtensionBannerProgram::findOrFail($id);
        $program->is_active = ! $program->is_active;
        $program->save();

        return response()->json([
            'success' => true,
            'is_active' => $program->is_active,
            'message' => 'Banner program status changed to '.($program->is_active ? 'Active' : 'Inactive').'.',
        ]);
    }

    /**
     * Delete a Banner Program
     */
    public function destroyBannerProgram(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $program = ExtensionBannerProgram::findOrFail($id);
        $name = $program->name;
        $program->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Banner program '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('dss.settings.rie_priorities.index', ['tab' => 'banner_programs'])
            ->with('success', "Banner program '{$name}' deleted successfully.");
    }

    /**
     * Store a newly created Extension Agenda / Thrust
     */
    public function storeAgenda(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:extension_agendas,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? (ExtensionAgenda::max('sort_order') + 1);

        $agenda = ExtensionAgenda::create($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Extension Agenda '{$agenda->name}' registered successfully.",
                'agenda' => $agenda,
            ]);
        }

        return redirect()->route('dss.settings.rie_priorities.index', ['tab' => 'agendas'])
            ->with('success', "Extension Agenda '{$agenda->name}' registered successfully.");
    }

    /**
     * Update an existing Extension Agenda
     */
    public function updateAgenda(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $agenda = ExtensionAgenda::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:extension_agendas,code,'.$agenda->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', $agenda->is_active);

        $agenda->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Extension Agenda '{$agenda->name}' updated successfully.",
                'agenda' => $agenda,
            ]);
        }

        return redirect()->route('dss.settings.rie_priorities.index', ['tab' => 'agendas'])
            ->with('success', "Extension Agenda '{$agenda->name}' updated successfully.");
    }

    /**
     * Toggle Agenda active status
     */
    public function toggleAgendaStatus(Request $request, int $id): JsonResponse
    {
        $agenda = ExtensionAgenda::findOrFail($id);
        $agenda->is_active = ! $agenda->is_active;
        $agenda->save();

        return response()->json([
            'success' => true,
            'is_active' => $agenda->is_active,
            'message' => 'Extension Agenda status changed to '.($agenda->is_active ? 'Active' : 'Inactive').'.',
        ]);
    }

    /**
     * Delete an Extension Agenda
     */
    public function destroyAgenda(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $agenda = ExtensionAgenda::findOrFail($id);
        $name = $agenda->name;
        $agenda->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Extension Agenda '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('dss.settings.rie_priorities.index', ['tab' => 'agendas'])
            ->with('success', "Extension Agenda '{$name}' deleted successfully.");
    }
}
