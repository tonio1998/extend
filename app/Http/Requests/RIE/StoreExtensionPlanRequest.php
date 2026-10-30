<?php

namespace App\Http\Requests\RIE;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExtensionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_name' => ['required', 'string', 'max:255'],
            'academic_program_id' => ['nullable', 'integer', 'exists:academic_programs,id'],
            'tracking_number' => ['nullable', 'string', 'max:50'],
            'extension_activity' => ['required', 'string', 'max:255'],
            'objectives' => ['required', 'string'],
            'sub_extension_proposal_date' => ['nullable', 'date'],
            'sub_activity_design_date' => ['nullable', 'date'],
            'sub_purchase_request_date' => ['nullable', 'date'],
            'moa_signing_date' => ['nullable', 'date'],

            // Arrival of Materials Calendar Inputs
            'materials_arrival_mode' => ['nullable', 'string', 'in:single,range,multiple'],
            'materials_arrival_start' => ['nullable', 'date'],
            'materials_arrival_end' => ['nullable', 'date', 'after_or_equal:materials_arrival_start'],
            'materials_arrival_dates' => ['nullable'],
            'materials_arrival_date' => ['nullable', 'string', 'max:100'],

            // Conduct of Extension Activity Calendar Inputs
            'conduct_activity_mode' => ['nullable', 'string', 'in:single,range,multiple'],
            'conduct_activity_start' => ['nullable', 'date'],
            'conduct_activity_end' => ['nullable', 'date', 'after_or_equal:conduct_activity_start'],
            'conduct_activity_dates' => ['nullable'],
            'conduct_activity_date' => ['nullable', 'string', 'max:100'],

            'sub_narrative_report_date' => ['nullable', 'date'],
            'monitoring_feedback_date' => ['nullable', 'date'],
            'monitoring_feedback_end_date' => ['nullable', 'date'],

            'beneficiaries' => ['required', 'string'],
            'target_beneficiaries_count' => ['required', 'integer', 'min:1'],
            'proposed_budget' => ['required', 'numeric', 'min:0'],
            'extensionists_count' => ['required', 'integer', 'min:1'],
            'reviewed_by_employee_id' => ['nullable', 'integer'],
            'reviewed_by_name' => ['nullable', 'string', 'max:191'],
            'reviewed_by_designation' => ['nullable', 'string', 'max:191'],
            'noted_by_employee_id' => ['nullable', 'integer'],
            'noted_by_name' => ['nullable', 'string', 'max:191'],
            'noted_by_designation' => ['nullable', 'string', 'max:191'],
            'approved_by_employee_id' => ['nullable', 'integer'],
            'approved_by_name' => ['nullable', 'string', 'max:191'],
            'approved_by_designation' => ['nullable', 'string', 'max:191'],
            'concurred_by_employee_id' => ['nullable', 'integer'],
            'concurred_by_name' => ['nullable', 'string', 'max:191'],
            'concurred_by_designation' => ['nullable', 'string', 'max:191'],
            'concurred_by_2_employee_id' => ['nullable', 'integer'],
            'concurred_by_2_name' => ['nullable', 'string', 'max:191'],
            'concurred_by_2_designation' => ['nullable', 'string', 'max:191'],
            'action_type' => ['nullable', 'string', 'in:draft,submit'],
        ];
    }

    public function messages(): array
    {
        return [
            'program_name.required' => 'Please select or specify the Academic Program.',
            'extension_activity.required' => 'The Extension Activity title or name is required.',
            'objectives.required' => 'Please provide the Objectives for the Extension Plan.',
            'beneficiaries.required' => 'Please specify the Target Beneficiaries.',
            'target_beneficiaries_count.required' => 'Please enter the target number of beneficiaries.',
            'proposed_budget.required' => 'Please provide the proposed budget.',
            'extensionists_count.required' => 'Please provide the number of extensionists.',
            'noted_by_employee_id.required' => 'Please select the employee for Noted By (Extension Head).',
            'approved_by_employee_id.required' => 'Please select the employee for Approved By (Dean).',
            'conduct_activity_end.after_or_equal' => 'The conduct activity end date must be on or after the start date.',
            'materials_arrival_end.after_or_equal' => 'The materials arrival end date must be on or after the start date.',
        ];
    }
}
