@php
    if (!isset($meta)) {
        $meta = [
            'type_key' => 'activity_design',
            'type_label' => 'Activity Design',
            'code' => $design->design_code,
            'title' => $design->title,
            'unit' => $design->unit?->unit_name ?: 'Higher Education Institution',
            'leader_name' => $design->project_leader_name,
            'leader_title' => $design->project_leader_title ?: 'Project Leader',
            'leader_employee_id' => $design->project_leader_employee_id,
            'budget' => $design->formatted_budget_total,
            'status' => $design->status ?: 'Draft',
            'parent_ref' => ($proposal ?? null) ? ('Proposal: ' . $proposal->proposal_code) : null,
            'parent_url' => ($proposal ?? null) ? route('dss.rie.proposals.show', $proposal->getRouteKey()) : null,
            'back_url' => route('dss.rie.activity_designs.show', $design->getRouteKey()),
            'update_url' => route('dss.documents.signatories.update', ['type' => 'activity_design', 'id' => $design->getRouteKey()]),
            'suggest_url' => route('dss.documents.signatories.suggest', ['type' => 'activity_design', 'id' => $design->getRouteKey()]),
        ];
        $document = $design;
    }
@endphp

@include('dss.documents.configure_signatories')
