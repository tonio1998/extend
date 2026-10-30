<?php

namespace Database\Seeders;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Employee;
use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionProposal;
use App\Models\RIE\SustainableDevelopmentGoal;
use App\Models\SaaS\Campus;
use App\Models\SaaS\DocumentSignatory;
use App\Models\SaaS\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtensionWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::find(1) ?? Tenant::first();
        $tenantId = $tenant?->id ?? 1;

        Campus::updateOrCreate(
            ['id' => 1],
            [
                'tenant_id' => $tenantId,
                'campus_code' => 'MAIN',
                'campus_name' => 'Surigao City Main Campus',
                'campus_type' => 'Main Campus',
                'location' => 'Narciso Street, Surigao City',
                'head_name' => 'Dr. Joy C. Capistrano',
                'status' => 'active',
            ]
        );

        Campus::updateOrCreate(
            ['id' => 2],
            [
                'tenant_id' => $tenantId,
                'campus_code' => 'DEL_CARMEN',
                'campus_name' => 'Del Carmen Campus',
                'campus_type' => 'Satellite Campus',
                'location' => 'Del Carmen, Siargao Island, Surigao del Norte',
                'head_name' => 'Dr. Alan P. Martinez',
                'status' => 'active',
            ]
        );

        Campus::updateOrCreate(
            ['id' => 3],
            [
                'tenant_id' => $tenantId,
                'campus_code' => 'TAGANA_AN',
                'campus_name' => 'Tagana-an Campus',
                'campus_type' => 'Satellite Campus',
                'location' => 'Tagana-an, Surigao del Norte',
                'head_name' => 'Dr. Diane B. Remot',
                'status' => 'active',
            ]
        );

        Campus::updateOrCreate(
            ['id' => 4],
            [
                'tenant_id' => $tenantId,
                'campus_code' => 'MALIMONO',
                'campus_name' => 'Malimono Campus',
                'campus_type' => 'Satellite Campus',
                'location' => 'Malimono, Surigao del Norte',
                'head_name' => 'Engr. Nelson V. Robles',
                'status' => 'active',
            ]
        );

        $proponentUser = User::where('email', 'apiloton1@snsu.edu.ph')->first() ?? User::find(7);
        $proponentEmp = Employee::find($proponentUser?->employee_id) ?? Employee::find(7);

        $facultyUser2 = User::where('email', 'm.villanueva@adssu.edu.ph')->first() ?? User::find(12);
        $facultyEmp2 = Employee::find($facultyUser2?->employee_id) ?? Employee::find(8);

        $facultyUser3 = User::where('email', 'r.delacruz@adssu.edu.ph')->first() ?? User::find(14);
        $facultyEmp3 = Employee::find($facultyUser3?->employee_id) ?? Employee::find(9);

        $facultyUser4 = User::where('email', 'j.fernandez@adssu.edu.ph')->first() ?? User::find(13);
        $facultyEmp4 = Employee::find($facultyUser4?->employee_id) ?? Employee::find(10);

        $notedEmp = Employee::find(6);
        $deanEmp = Employee::find(4);
        $directorEmp = Employee::find(5);
        $vpEmp = Employee::find(3);
        $presidentEmp = Employee::find(2);

        $progBsit = AcademicProgram::where('program_code', 'BSIT')->first() ?? AcademicProgram::find(1);
        $progBsce = AcademicProgram::where('program_code', 'BSCE')->first() ?? AcademicProgram::find(7);
        $progBsabe = AcademicProgram::where('program_code', 'BSABE')->first() ?? AcademicProgram::find(10);
        $progBsee = AcademicProgram::where('program_code', 'BSEE')->first() ?? AcademicProgram::find(8);
        $progBscs = AcademicProgram::where('program_code', 'BSCS')->first() ?? AcademicProgram::find(2);

        $sdg4 = SustainableDevelopmentGoal::where('goal_number', 4)->first();
        $sdg9 = SustainableDevelopmentGoal::where('goal_number', 9)->first();
        $sdg11 = SustainableDevelopmentGoal::where('goal_number', 11)->first();
        $sdg13 = SustainableDevelopmentGoal::where('goal_number', 13)->first();
        $sdg14 = SustainableDevelopmentGoal::where('goal_number', 14)->first();
        $sdg15 = SustainableDevelopmentGoal::where('goal_number', 15)->first();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DocumentSignatory::where('document_type', ExtensionPlan::class)->orWhere('document_type', ExtensionProposal::class)->delete();
        DB::table('extension_proposal_sdgs')->truncate();
        ExtensionProposal::truncate();
        ExtensionPlan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $plan1 = ExtensionPlan::create([
            'tenant_id' => $tenantId,
            'campus_id' => 1,
            'academic_program_id' => $progBsit?->id,
            'plan_code' => 'SNSU-EXT-2026-0001',
            'program_name' => $progBsit?->program_name ?? 'Bachelor of Science in Information Technology',
            'extension_activity' => 'e-Barangay: Community ICT Literacy & Digital Public Records Automation for Coastal Barangays',
            'objectives' => "1. Train 100 barangay functionaries on basic computer operations and secure data entry.\n2. Deploy a lightweight, web-based barangay recordkeeping and certification system.\n3. Establish ongoing technical support and community feedback mechanisms.",
            'sub_extension_proposal_date' => Carbon::now()->subDays(60),
            'sub_activity_design_date' => Carbon::now()->subDays(50),
            'sub_purchase_request_date' => Carbon::now()->subDays(40),
            'moa_signing_date' => Carbon::now()->subDays(30),
            'materials_arrival_mode' => 'range',
            'materials_arrival_start' => Carbon::now()->subDays(25),
            'materials_arrival_end' => Carbon::now()->subDays(20),
            'conduct_activity_mode' => 'range',
            'conduct_activity_start' => Carbon::now()->subDays(15),
            'conduct_activity_end' => Carbon::now()->subDays(10),
            'sub_narrative_report_date' => Carbon::now()->subDays(5),
            'monitoring_feedback_date' => Carbon::now()->addDays(10),
            'beneficiaries' => 'Barangay Officials, BHWs, and Sangguniang Kabataan of Barangay Lipata, Surigao City',
            'target_beneficiaries_count' => 100,
            'proposed_budget' => 95000.00,
            'extensionists_count' => 6,
            'prepared_by_user_id' => $proponentUser?->id,
            'prepared_by_employee_id' => $proponentEmp?->id,
            'prepared_by_name' => $proponentEmp ? $proponentEmp->full_name : ($proponentUser?->name ?? 'Engr. Antonio Piloton Jr.'),
            'prepared_by_designation' => 'Faculty Proponent / Project Leader',
            'prepared_at' => Carbon::now()->subDays(65),
            'noted_by_employee_id' => $notedEmp?->id,
            'noted_by_name' => $notedEmp ? $notedEmp->full_name : 'Dr. Diane B. Remot',
            'noted_by_designation' => 'Extension Coordinator',
            'noted_at' => Carbon::now()->subDays(63),
            'approved_by_employee_id' => $deanEmp?->id,
            'approved_by_name' => $deanEmp ? $deanEmp->full_name : 'Dr. Alan P. Martinez',
            'approved_by_designation' => 'College Dean, College of Engineering and Technology',
            'approved_at' => Carbon::now()->subDays(60),
            'status' => 'approved',
            'remarks' => 'Approved and included in the Official Annual RIE Extension Calendar.',
        ]);

        $this->seedPlanSignatories($plan1, $proponentEmp, $notedEmp, $deanEmp, true, true);

        $proposal1 = ExtensionProposal::create([
            'tenant_id' => $tenantId,
            'campus_id' => 1,
            'unit_id' => $proponentEmp?->department_id ?? 1,
            'academic_program_id' => $progBsit?->id,
            'extension_plan_id' => $plan1->id,
            'proposal_code' => 'SNSU-PROP-2026-0001',
            'tracking_number' => '202610240001',
            'title' => 'e-Barangay: Community ICT Literacy & Digital Public Records Automation for Coastal Barangays',
            'banner_program' => 'Digital Literacy & Information Technology Transfer',
            'agenda_priorities' => ['Smart Technologies & Digital Transformation', 'Community Empowerment & Governance'],
            'beneficiaries' => 'Barangay Officials, BHWs, and Sangguniang Kabataan of Barangay Lipata, Surigao City',
            'target_beneficiaries_data' => [
                'direct' => 100,
                'indirect' => 3500,
                'sectors' => ['Barangay Officials', 'Barangay Health Workers', 'Youth & SK'],
            ],
            'implementing_agency' => 'Surigao del Norte State University (SNSU)',
            'implementing_colleges' => ['College of Engineering and Technology', 'College of Information & Communications Technology'],
            'lead_agency' => 'Surigao del Norte State University',
            'collaborating_agencies' => 'City Government of Surigao, Department of Information and Communications Technology (DICT - Caraga)',
            'collaborating_agencies_data' => [
                ['name' => 'City Government of Surigao', 'role' => 'Logistical support and community venue coordination'],
                ['name' => 'DICT Caraga', 'role' => 'Free Wi-Fi access points and technical assistance'],
            ],
            'duration_start' => Carbon::now()->subDays(15),
            'duration_end' => Carbon::now()->addDays(45),
            'duration_text' => '2 Months (October 2026 – December 2026)',
            'duration_months' => 2,
            'project_location' => 'Barangay Lipata Gymnasium, Surigao City',
            'budget_institution_share' => '75,000.00',
            'fund_source_internal' => '75,000.00',
            'budget_partner_share' => '20,000.00',
            'fund_source_external' => '20,000.00',
            'budget_total_display' => '95,000.00',
            'budget_cash_amount' => 95000.00,
            'project_leader_user_id' => $proponentUser?->id,
            'project_leader_employee_id' => $proponentEmp?->id,
            'project_leader_name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.',
            'project_leader_title' => 'Associate Professor / Project Leader',
            'project_leader_gender' => 'Male',
            'asst_project_leader_name' => 'Maria Elena S. Villanueva',
            'asst_project_leader_title' => 'Assistant Project Leader',
            'proponents' => [
                ['name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.', 'role' => 'Project Leader', 'employee_id' => $proponentEmp?->id, 'type' => 'faculty'],
                ['name' => 'Maria Elena S. Villanueva', 'role' => 'Assistant Project Leader', 'employee_id' => $facultyEmp2?->id, 'type' => 'faculty'],
                ['name' => 'Rodrigo T. Dela Cruz Jr.', 'role' => 'Technical Facilitator', 'employee_id' => $facultyEmp3?->id, 'type' => 'faculty'],
            ],
            'introduction' => 'Information and Communications Technology (ICT) plays an indispensable role in community-level governance. In coastal barangays of Surigao City, manual recordkeeping continues to present bottlenecks in resident profiling, health records management, and disaster aid allocation. This project equips local officials with modern digital tools to enhance administrative transparency and service delivery.',
            'objectives' => [
                ['title' => 'Capacity Development', 'description' => 'Conduct 40 hours of hands-on training on digital literacy and office automation.'],
                ['title' => 'System Deployment', 'description' => 'Install and configure a localized electronic barangay record system.'],
                ['title' => 'Impact Assessment', 'description' => 'Evaluate citizen satisfaction and operational turnaround time before and after implementation.'],
            ],
            'general_objective' => 'To modernize frontline public service delivery and administrative recordkeeping across coastal barangays in Surigao City through digital literacy training and automated local governance systems.',
            'specific_objectives' => [
                'Train 100 barangay staff and SK leaders on digital document creation and spreadsheet management.',
                'Establish a secure offline-capable database of residents, health indices, and clearance issuances.',
                'Formulate a community-led maintenance protocol to ensure continuous operation.',
            ],
            'expected_outputs' => [
                '100 fully certified barangay personnel in digital public administration.',
                '1 fully operational e-Barangay Records Information System.',
                '1 signed Memorandum of Agreement with LGU Surigao City for sustained tech support.',
            ],
            'relevance_narrative' => 'Directly aligns with the Philippine Development Plan (PDP) 2023-2028 Chapter on Expanding Digital Infrastructure and Enhancing Social Inclusion, alongside the Regional Development Plan of Caraga.',
            'sdg_alignments' => [
                'selected' => [4, 9, 11],
                'narrative' => 'Addresses SDG 4 (Quality Education) through practical digital skills training, SDG 9 (Industry, Innovation & Infrastructure) by providing IT infrastructure, and SDG 11 (Sustainable Communities) through resilient municipal e-governance.',
            ],
            'development_gap' => 'Disparity in digital literacy between urban centers and coastal frontline communities, resulting in backlogs, inaccurate resident profiles, and delays during emergency disaster response.',
            'pdp_responsiveness' => 'Supports Chapter 14: Promoting Inclusive and Sustainable Digital Infrastructure and Transforming Governance.',
            'schedule_of_activities' => [
                ['phase' => 'Phase I: Needs Assessment & LGU Consultation', 'target_month' => 'Week 1-2', 'outputs' => 'Hardware readiness audit, signed MOA'],
                ['phase' => 'Phase II: Hands-on ICT Workshop', 'target_month' => 'Week 3-5', 'outputs' => 'Completed modular training, skill assessment'],
                ['phase' => 'Phase III: System Rollout & Data Migration', 'target_month' => 'Week 6-7', 'outputs' => 'Encoded resident database, pilot operations'],
                ['phase' => 'Phase IV: Monitoring & Closeout', 'target_month' => 'Week 8', 'outputs' => 'Final evaluation report, user turnover'],
            ],
            'people_involved' => [
                'columns' => [
                    ['id' => 'name', 'label' => 'Extensionist / Collaborator', 'width' => 35],
                    ['id' => 'role', 'label' => 'Project Role / Title', 'width' => 25],
                    ['id' => 'responsibilities', 'label' => 'Specific Responsibilities & Tasks', 'width' => 40],
                ],
                'rows' => [
                    ['id' => 'r1', 'name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.', 'role' => 'Project Leader', 'responsibilities' => 'Overall project oversight, curriculum formulation, and institutional coordination.'],
                    ['id' => 'r2', 'name' => 'Maria Elena S. Villanueva', 'role' => 'Assistant Project Leader', 'responsibilities' => 'Field logistics, training materials preparation, and participant assessment.'],
                    ['id' => 'r3', 'name' => 'Rodrigo T. Dela Cruz Jr.', 'role' => 'Technical Facilitator', 'responsibilities' => 'Network setup, software installation, and lab support.'],
                ],
            ],
            'org_structure' => [
                'mode' => 'dynamic',
                'nodes' => [
                    ['id' => 'lead', 'title' => 'Project Leader', 'name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.', 'parent' => null],
                    ['id' => 'asst', 'title' => 'Assistant Project Leader', 'name' => 'Maria Elena S. Villanueva', 'parent' => 'lead'],
                    ['id' => 'tech', 'title' => 'Technical Lead', 'name' => 'Rodrigo T. Dela Cruz Jr.', 'parent' => 'asst'],
                ],
            ],
            'sustainability_measures' => 'The project turnover includes a complete administrator manual, video tutorials, and a dedicated student volunteer helpdesk hosted at the SNSU College of Engineering and Technology.',
            'budgetary_requirements' => [
                'sections' => [
                    'A' => [
                        'code' => 'A',
                        'title' => 'Personal Services / Honoraria',
                        'items' => [
                            ['item' => 'Expert Resource Speaker Honoraria', 'basis' => '40 hrs @ 500/hr', 'amount' => 20000],
                            ['item' => 'Technical Facilitator Assistance', 'basis' => '40 hrs @ 250/hr', 'amount' => 10000],
                        ],
                    ],
                    'B_TRAIN' => [
                        'code' => 'B_TRAIN',
                        'title' => 'Training Supplies & Materials',
                        'items' => [
                            ['item' => 'Participant Training Kits & Workbooks', 'basis' => '100 sets @ 250/set', 'amount' => 25000],
                            ['item' => 'Training Venue Meals & Refreshments', 'basis' => '100 pax @ 300/pax', 'amount' => 30000],
                        ],
                    ],
                    'D' => [
                        'code' => 'D',
                        'title' => 'Contingency & Miscellaneous',
                        'items' => [
                            ['item' => 'Documentation, Certificates & Transportation', 'basis' => 'Lump sum', 'amount' => 10000],
                        ],
                    ],
                ],
            ],
            'prepared_by_user_id' => $proponentUser?->id,
            'prepared_by_employee_id' => $proponentEmp?->id,
            'prepared_by_name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.',
            'prepared_by_designation' => 'Faculty Proponent / Project Leader',
            'prepared_at' => Carbon::now()->subDays(15),
            'status' => 'approved',
            'remarks' => 'Fully approved by the University President with allocation from GAA Extension Allotment.',
        ]);

        if ($sdg4 && $sdg9 && $sdg11) {
            $proposal1->sdgs()->sync([$sdg4->id, $sdg9->id, $sdg11->id]);
        }

        $this->seedProposalSignatories($proposal1, [
            ['role_label' => 'Prepared by', 'tier_role' => 'prepared', 'emp' => $proponentEmp, 'user' => $proponentUser, 'status' => 'signed', 'days_ago' => 15, 'col' => 1, 'align' => 'left'],
            ['role_label' => 'Noted by', 'tier_role' => 'noted', 'emp' => $deanEmp, 'user' => User::where('employee_id', $deanEmp?->id)->first(), 'status' => 'signed', 'days_ago' => 12, 'col' => 2, 'align' => 'right'],
            ['role_label' => 'Concurred by', 'tier_role' => 'concurred', 'emp' => $directorEmp, 'user' => User::where('employee_id', $directorEmp?->id)->first(), 'status' => 'signed', 'days_ago' => 10, 'col' => 1, 'align' => 'left'],
            ['role_label' => 'Recommended by', 'tier_role' => 'recommended', 'emp' => $vpEmp, 'user' => User::where('employee_id', $vpEmp?->id)->first(), 'status' => 'signed', 'days_ago' => 7, 'col' => 2, 'align' => 'right'],
            ['role_label' => 'Approved by', 'tier_role' => 'approved', 'emp' => $presidentEmp, 'user' => User::where('employee_id', $presidentEmp?->id)->first(), 'status' => 'signed', 'days_ago' => 3, 'col' => 1, 'align' => 'center'],
        ]);

        $plan2 = ExtensionPlan::create([
            'tenant_id' => $tenantId,
            'campus_id' => 1,
            'academic_program_id' => $progBsce?->id,
            'plan_code' => 'SNSU-EXT-2026-0002',
            'program_name' => $progBsce?->program_name ?? 'Bachelor of Science in Civil Engineering',
            'extension_activity' => 'Ligtas-Kalamidad: Structural Health Evaluation & Disaster-Resilient Evacuation Planning',
            'objectives' => "1. Inspect structural integrity of 5 coastal public elementary schools.\n2. Design cost-effective retrofitting solutions for storm surge vulnerability.\n3. Train barangay DRRM volunteers on structural hazard identification.",
            'sub_extension_proposal_date' => Carbon::now()->subDays(45),
            'sub_activity_design_date' => Carbon::now()->subDays(35),
            'sub_purchase_request_date' => Carbon::now()->subDays(25),
            'moa_signing_date' => Carbon::now()->subDays(20),
            'materials_arrival_mode' => 'range',
            'materials_arrival_start' => Carbon::now()->subDays(15),
            'materials_arrival_end' => Carbon::now()->subDays(10),
            'conduct_activity_mode' => 'range',
            'conduct_activity_start' => Carbon::now()->subDays(5),
            'conduct_activity_end' => Carbon::now()->addDays(5),
            'sub_narrative_report_date' => Carbon::now()->addDays(15),
            'monitoring_feedback_date' => Carbon::now()->addDays(30),
            'beneficiaries' => 'Barangay DRRM Council and Coastal Public School Teachers of Barangay Mabua',
            'target_beneficiaries_count' => 60,
            'proposed_budget' => 115000.00,
            'extensionists_count' => 5,
            'prepared_by_user_id' => $facultyUser2?->id,
            'prepared_by_employee_id' => $facultyEmp2?->id,
            'prepared_by_name' => $facultyEmp2 ? $facultyEmp2->full_name : 'Maria Elena S. Villanueva',
            'prepared_by_designation' => 'Faculty Proponent / Lead Engineer',
            'prepared_at' => Carbon::now()->subDays(48),
            'noted_by_employee_id' => $notedEmp?->id,
            'noted_by_name' => $notedEmp ? $notedEmp->full_name : 'Dr. Diane B. Remot',
            'noted_by_designation' => 'Extension Coordinator',
            'noted_at' => Carbon::now()->subDays(46),
            'approved_by_employee_id' => $deanEmp?->id,
            'approved_by_name' => $deanEmp ? $deanEmp->full_name : 'Dr. Alan P. Martinez',
            'approved_by_designation' => 'College Dean, College of Engineering and Technology',
            'approved_at' => Carbon::now()->subDays(45),
            'status' => 'approved',
            'remarks' => 'Endorsed for high-priority funding due to climate disaster vulnerability.',
        ]);

        $this->seedPlanSignatories($plan2, $facultyEmp2, $notedEmp, $deanEmp, true, true);

        $proposal2 = ExtensionProposal::create([
            'tenant_id' => $tenantId,
            'campus_id' => 1,
            'unit_id' => $facultyEmp2?->department_id ?? 1,
            'academic_program_id' => $progBsce?->id,
            'extension_plan_id' => $plan2->id,
            'proposal_code' => 'SNSU-PROP-2026-0002',
            'tracking_number' => '202610240002',
            'title' => 'Ligtas-Kalamidad: Structural Health Evaluation & Disaster-Resilient Evacuation Planning',
            'banner_program' => 'Disaster Risk Reduction & Climate Change Adaptation',
            'agenda_priorities' => ['Climate Resilience & Environmental Protection', 'Community Safety & Infrastructure'],
            'beneficiaries' => 'Barangay DRRM Council and Coastal Public School Teachers of Barangay Mabua',
            'target_beneficiaries_data' => ['direct' => 60, 'indirect' => 2000],
            'implementing_agency' => 'Surigao del Norte State University (SNSU)',
            'lead_agency' => 'Surigao del Norte State University',
            'collaborating_agencies' => 'MDRRMO Surigao City, Department of Public Works and Highways (DPWH)',
            'duration_start' => Carbon::now()->subDays(5),
            'duration_end' => Carbon::now()->addDays(55),
            'duration_text' => '2 Months',
            'duration_months' => 2,
            'project_location' => 'Barangay Mabua, Surigao City',
            'budget_institution_share' => '90,000.00',
            'fund_source_internal' => '90,000.00',
            'budget_partner_share' => '25,000.00',
            'fund_source_external' => '25,000.00',
            'budget_total_display' => '115,000.00',
            'budget_cash_amount' => 115000.00,
            'project_leader_user_id' => $facultyUser2?->id,
            'project_leader_employee_id' => $facultyEmp2?->id,
            'project_leader_name' => $facultyEmp2 ? $facultyEmp2->full_name : 'Maria Elena S. Villanueva',
            'project_leader_title' => 'Assistant Professor / Project Leader',
            'project_leader_gender' => 'Female',
            'proponents' => [
                ['name' => $facultyEmp2 ? $facultyEmp2->full_name : 'Maria Elena S. Villanueva', 'role' => 'Project Leader', 'employee_id' => $facultyEmp2?->id, 'type' => 'faculty'],
                ['name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.', 'role' => 'Co-Proponent', 'employee_id' => $proponentEmp?->id, 'type' => 'faculty'],
            ],
            'introduction' => 'Coastal public infrastructure in Northern Mindanao faces recurrent degradation from seismic activity, tropical cyclones, and storm surges. Through comprehensive non-destructive structural diagnostics and community evacuation routing, this program safeguards lives and critical educational assets.',
            'objectives' => [
                ['title' => 'Structural Assessment', 'description' => 'Conduct rebound hammer and ultrasonic pulse velocity tests on 5 evacuation facilities.'],
                ['title' => 'Retrofitting Blueprints', 'description' => 'Deliver schematic structural improvement recommendations.'],
            ],
            'general_objective' => 'To enhance structural resilience and community disaster evacuation preparedness of public educational buildings in Barangay Mabua, Surigao City.',
            'specific_objectives' => [
                'Evaluate masonry, reinforcement, and roofing vulnerabilities of 5 public structures.',
                'Establish localized evacuation route mappings with community DRRM personnel.',
            ],
            'expected_outputs' => [
                '5 complete structural engineering vulnerability assessments.',
                '1 evacuation roadmap handbook distributed to the barangay council.',
            ],
            'relevance_narrative' => 'Addresses SDG 11 (Resilient Infrastructure) and SDG 13 (Climate Action).',
            'sdg_alignments' => [
                'selected' => [11, 13],
                'narrative' => 'Targets building safety and disaster risk mitigation.',
            ],
            'schedule_of_activities' => [
                ['phase' => 'Field Testing & Structural Diagnostics', 'target_month' => 'Week 1-3', 'outputs' => 'Testing data sheets'],
                ['phase' => 'Engineering Computation & Report Writing', 'target_month' => 'Week 4-6', 'outputs' => 'Draft structural reports'],
                ['phase' => 'Stakeholders Turnover Workshop', 'target_month' => 'Week 7-8', 'outputs' => 'Disaster blueprint handover'],
            ],
            'prepared_by_user_id' => $facultyUser2?->id,
            'prepared_by_employee_id' => $facultyEmp2?->id,
            'prepared_by_name' => $facultyEmp2 ? $facultyEmp2->full_name : 'Maria Elena S. Villanueva',
            'prepared_by_designation' => 'Faculty Proponent / Lead Engineer',
            'prepared_at' => Carbon::now()->subDays(5),
            'status' => 'concurred',
            'remarks' => 'Signed by Project Leader, Dean, and Extension Director. Currently on VP RIE desk for administrative endorsement.',
        ]);

        if ($sdg11 && $sdg13) {
            $proposal2->sdgs()->sync([$sdg11->id, $sdg13->id]);
        }

        $this->seedProposalSignatories($proposal2, [
            ['role_label' => 'Prepared by', 'tier_role' => 'prepared', 'emp' => $facultyEmp2, 'user' => $facultyUser2, 'status' => 'signed', 'days_ago' => 5, 'col' => 1, 'align' => 'left'],
            ['role_label' => 'Noted by', 'tier_role' => 'noted', 'emp' => $deanEmp, 'user' => User::where('employee_id', $deanEmp?->id)->first(), 'status' => 'signed', 'days_ago' => 4, 'col' => 2, 'align' => 'right'],
            ['role_label' => 'Concurred by', 'tier_role' => 'concurred', 'emp' => $directorEmp, 'user' => User::where('employee_id', $directorEmp?->id)->first(), 'status' => 'signed', 'days_ago' => 2, 'col' => 1, 'align' => 'left'],
            ['role_label' => 'Recommended by', 'tier_role' => 'recommended', 'emp' => $vpEmp, 'user' => User::where('employee_id', $vpEmp?->id)->first(), 'status' => 'pending', 'days_ago' => null, 'col' => 2, 'align' => 'right'],
            ['role_label' => 'Approved by', 'tier_role' => 'approved', 'emp' => $presidentEmp, 'user' => User::where('employee_id', $presidentEmp?->id)->first(), 'status' => 'pending', 'days_ago' => null, 'col' => 1, 'align' => 'center'],
        ]);

        $plan3 = ExtensionPlan::create([
            'tenant_id' => $tenantId,
            'campus_id' => 2,
            'academic_program_id' => $progBsabe?->id,
            'plan_code' => 'SNSU-EXT-2026-0003',
            'program_name' => $progBsabe?->program_name ?? 'Bachelor of Science in Agricultural and Biosystems Engineering',
            'extension_activity' => 'Buhay Mangrove: Community-Based Mangrove Reforestation & Coastal Bio-Shield Protocol',
            'objectives' => "1. Propagate 10,000 Rhizophora mangrove propagules in Del Carmen, Siargao.\n2. Organize and train 40 fisherfolk families as environmental stewards.\n3. Establish permanent ecological nursery sites.",
            'sub_extension_proposal_date' => Carbon::now()->subDays(20),
            'sub_activity_design_date' => Carbon::now()->subDays(15),
            'sub_purchase_request_date' => Carbon::now()->subDays(10),
            'moa_signing_date' => Carbon::now()->subDays(5),
            'materials_arrival_mode' => 'range',
            'materials_arrival_start' => Carbon::now()->addDays(2),
            'materials_arrival_end' => Carbon::now()->addDays(7),
            'conduct_activity_mode' => 'range',
            'conduct_activity_start' => Carbon::now()->addDays(12),
            'conduct_activity_end' => Carbon::now()->addDays(18),
            'sub_narrative_report_date' => Carbon::now()->addDays(30),
            'monitoring_feedback_date' => Carbon::now()->addDays(60),
            'beneficiaries' => 'Del Carmen Small Fisherfolk Federation and Women Seaweed Growers Guild',
            'target_beneficiaries_count' => 85,
            'proposed_budget' => 140000.00,
            'extensionists_count' => 7,
            'prepared_by_user_id' => $facultyUser3?->id,
            'prepared_by_employee_id' => $facultyEmp3?->id,
            'prepared_by_name' => $facultyEmp3 ? $facultyEmp3->full_name : 'Rodrigo T. Dela Cruz Jr.',
            'prepared_by_designation' => 'Faculty Researcher / Extensionist',
            'prepared_at' => Carbon::now()->subDays(25),
            'noted_by_employee_id' => $notedEmp?->id,
            'noted_by_name' => $notedEmp ? $notedEmp->full_name : 'Dr. Diane B. Remot',
            'noted_by_designation' => 'Extension Coordinator',
            'noted_at' => Carbon::now()->subDays(22),
            'approved_by_employee_id' => $deanEmp?->id,
            'approved_by_name' => $deanEmp ? $deanEmp->full_name : 'Dr. Alan P. Martinez',
            'approved_by_designation' => 'Campus Director, Del Carmen Campus',
            'approved_at' => Carbon::now()->subDays(20),
            'status' => 'approved',
            'remarks' => 'Approved Plan: Ready for Extension Proposal creation by proponent.',
        ]);

        $this->seedPlanSignatories($plan3, $facultyEmp3, $notedEmp, $deanEmp, true, true);

        $plan4 = ExtensionPlan::create([
            'tenant_id' => $tenantId,
            'campus_id' => 3,
            'academic_program_id' => $progBsee?->id,
            'plan_code' => 'SNSU-EXT-2026-0004',
            'program_name' => $progBsee?->program_name ?? 'Bachelor of Science in Electrical Engineering',
            'extension_activity' => 'Solar-Kuryente: Off-Grid Solar PV Installation & Battery Maintenance for Island Communities',
            'objectives' => "1. Train 50 youth and island residents on solar panel wiring and DC inverter maintenance.\n2. Install pilot solar charging stations in off-grid fishing sitios.\n3. Distribute safety and troubleshooting quick-reference kits.",
            'sub_extension_proposal_date' => Carbon::now()->addDays(5),
            'sub_activity_design_date' => Carbon::now()->addDays(15),
            'sub_purchase_request_date' => Carbon::now()->addDays(25),
            'moa_signing_date' => Carbon::now()->addDays(35),
            'materials_arrival_mode' => 'range',
            'materials_arrival_start' => Carbon::now()->addDays(40),
            'materials_arrival_end' => Carbon::now()->addDays(45),
            'conduct_activity_mode' => 'range',
            'conduct_activity_start' => Carbon::now()->addDays(50),
            'conduct_activity_end' => Carbon::now()->addDays(55),
            'sub_narrative_report_date' => Carbon::now()->addDays(65),
            'monitoring_feedback_date' => Carbon::now()->addDays(90),
            'beneficiaries' => 'Off-grid Fisherfolk Families and Youth Electricians of Tagana-an',
            'target_beneficiaries_count' => 75,
            'proposed_budget' => 88000.00,
            'extensionists_count' => 4,
            'prepared_by_user_id' => $facultyUser4?->id,
            'prepared_by_employee_id' => $facultyEmp4?->id,
            'prepared_by_name' => $facultyEmp4 ? $facultyEmp4->full_name : 'Jocelyn M. Fernandez',
            'prepared_by_designation' => 'Associate Professor / Project Leader',
            'prepared_at' => Carbon::now()->subDays(5),
            'noted_by_employee_id' => $notedEmp?->id,
            'noted_by_name' => $notedEmp ? $notedEmp->full_name : 'Dr. Diane B. Remot',
            'noted_by_designation' => 'Extension Coordinator',
            'noted_at' => Carbon::now()->subDays(2),
            'approved_by_employee_id' => null,
            'approved_by_name' => null,
            'approved_by_designation' => 'Dean',
            'approved_at' => null,
            'status' => 'noted',
            'remarks' => 'Noted by Extension Coordinator. Currently pending Dean review and approval before proposals can be initiated.',
        ]);

        $this->seedPlanSignatories($plan4, $facultyEmp4, $notedEmp, $deanEmp, true, false);

        $plan5 = ExtensionPlan::create([
            'tenant_id' => $tenantId,
            'campus_id' => 4,
            'academic_program_id' => $progBscs?->id,
            'plan_code' => 'SNSU-EXT-2026-0005',
            'program_name' => $progBscs?->program_name ?? 'Bachelor of Science in Computer Science',
            'extension_activity' => 'CodeKids: Scratch Programming & STEM Discovery Camp for Remote Elementary Schools',
            'objectives' => "1. Introduce 150 public school children to logic, computational thinking, and block-based coding.\n2. Equip teachers with modular coding curricula and offline learning kits.",
            'sub_extension_proposal_date' => Carbon::now()->addDays(20),
            'sub_activity_design_date' => Carbon::now()->addDays(30),
            'sub_purchase_request_date' => Carbon::now()->addDays(40),
            'moa_signing_date' => Carbon::now()->addDays(50),
            'materials_arrival_mode' => 'range',
            'materials_arrival_start' => Carbon::now()->addDays(55),
            'materials_arrival_end' => Carbon::now()->addDays(60),
            'conduct_activity_mode' => 'range',
            'conduct_activity_start' => Carbon::now()->addDays(65),
            'conduct_activity_end' => Carbon::now()->addDays(70),
            'sub_narrative_report_date' => Carbon::now()->addDays(80),
            'monitoring_feedback_date' => Carbon::now()->addDays(110),
            'beneficiaries' => 'Grade 5 & 6 Pupils and Science Teachers of Malimono Central Elementary School',
            'target_beneficiaries_count' => 150,
            'proposed_budget' => 65000.00,
            'extensionists_count' => 5,
            'prepared_by_user_id' => $proponentUser?->id,
            'prepared_by_employee_id' => $proponentEmp?->id,
            'prepared_by_name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.',
            'prepared_by_designation' => 'Faculty Proponent / Project Leader',
            'prepared_at' => Carbon::now()->subDays(1),
            'noted_by_designation' => 'Extension Coordinator',
            'approved_by_designation' => 'Dean',
            'status' => 'submitted',
            'remarks' => 'Submitted by faculty end-user. In-transit to Extension Coordinator.',
        ]);

        $this->seedPlanSignatories($plan5, $proponentEmp, $notedEmp, $deanEmp, false, false);

        $plan6 = ExtensionPlan::create([
            'tenant_id' => $tenantId,
            'campus_id' => 1,
            'academic_program_id' => $progBsit?->id,
            'plan_code' => 'SNSU-EXT-2026-0006',
            'program_name' => $progBsit?->program_name ?? 'Bachelor of Science in Information Technology',
            'extension_activity' => 'CyberLigtas: Cyber Hygiene & Anti-Scam Orientation for Public Market Merchants and Senior Citizens',
            'objectives' => '1. Protect 120 senior citizens and market vendors against phishing, SMS fraud, and illegal mobile lending schemes.',
            'sub_extension_proposal_date' => Carbon::now()->addDays(35),
            'sub_activity_design_date' => Carbon::now()->addDays(45),
            'sub_purchase_request_date' => Carbon::now()->addDays(55),
            'moa_signing_date' => Carbon::now()->addDays(65),
            'materials_arrival_mode' => 'range',
            'materials_arrival_start' => Carbon::now()->addDays(70),
            'materials_arrival_end' => Carbon::now()->addDays(75),
            'conduct_activity_mode' => 'range',
            'conduct_activity_start' => Carbon::now()->addDays(80),
            'conduct_activity_end' => Carbon::now()->addDays(85),
            'sub_narrative_report_date' => Carbon::now()->addDays(95),
            'monitoring_feedback_date' => Carbon::now()->addDays(125),
            'beneficiaries' => 'Surigao City Public Market Vendors Association and Senior Citizens Federation',
            'target_beneficiaries_count' => 120,
            'proposed_budget' => 50000.00,
            'extensionists_count' => 4,
            'prepared_by_user_id' => $proponentUser?->id,
            'prepared_by_employee_id' => $proponentEmp?->id,
            'prepared_by_name' => $proponentEmp ? $proponentEmp->full_name : 'Engr. Antonio Piloton Jr.',
            'prepared_by_designation' => 'Faculty Proponent / Project Leader',
            'prepared_at' => null,
            'noted_by_designation' => 'Extension Coordinator',
            'approved_by_designation' => 'Dean',
            'status' => 'draft',
            'remarks' => null,
        ]);
    }

    private function seedPlanSignatories(ExtensionPlan $plan, ?Employee $preparedEmp, ?Employee $notedEmp, ?Employee $deanEmp, bool $isNoted, bool $isApproved): void
    {
        $tenantId = $plan->tenant_id ?: 1;

        DocumentSignatory::create([
            'document_type' => ExtensionPlan::class,
            'document_id' => $plan->id,
            'tenant_id' => $tenantId,
            'campus_id' => $plan->campus_id,
            'role_label' => 'Prepared by',
            'tier_role' => 'prepared',
            'assignment_type' => 'specific_employee',
            'employee_id' => $preparedEmp?->id,
            'user_id' => $plan->prepared_by_user_id,
            'signatory_name' => $plan->prepared_by_name,
            'designation' => $plan->prepared_by_designation ?: 'Faculty Proponent',
            'office' => $preparedEmp?->department?->unit_name ?: 'Academic Department',
            'sort_order' => 1,
            'position_column' => 1,
            'col_span' => 1,
            'alignment' => 'left',
            'status' => $plan->status !== 'draft' ? 'signed' : 'pending',
            'signed_at' => $plan->prepared_at,
            'signed_by_user_id' => $plan->prepared_by_user_id,
            'requires_digital_signature' => true,
            'is_mandatory' => true,
        ]);

        DocumentSignatory::create([
            'document_type' => ExtensionPlan::class,
            'document_id' => $plan->id,
            'tenant_id' => $tenantId,
            'campus_id' => $plan->campus_id,
            'role_label' => 'Noted by',
            'tier_role' => 'noted',
            'assignment_type' => 'specific_employee',
            'employee_id' => $notedEmp?->id,
            'signatory_name' => $plan->noted_by_name ?: ($notedEmp?->full_name ?: 'Dr. Diane B. Remot'),
            'designation' => $plan->noted_by_designation ?: ($notedEmp?->designation ?: 'Extension Coordinator'),
            'office' => $notedEmp?->department?->unit_name ?: 'Extension Office',
            'sort_order' => 2,
            'position_column' => 2,
            'col_span' => 1,
            'alignment' => 'right',
            'status' => $isNoted ? 'signed' : 'pending',
            'signed_at' => $isNoted ? $plan->noted_at : null,
            'requires_digital_signature' => true,
            'is_mandatory' => true,
        ]);

        DocumentSignatory::create([
            'document_type' => ExtensionPlan::class,
            'document_id' => $plan->id,
            'tenant_id' => $tenantId,
            'campus_id' => $plan->campus_id,
            'role_label' => 'Approved by',
            'tier_role' => 'approved',
            'assignment_type' => 'specific_employee',
            'employee_id' => $deanEmp?->id,
            'signatory_name' => $plan->approved_by_name ?: ($deanEmp?->full_name ?: 'Dr. Alan P. Martinez'),
            'designation' => $plan->approved_by_designation ?: ($deanEmp?->designation ?: 'College Dean'),
            'office' => $deanEmp?->department?->unit_name ?: 'Office of the Dean',
            'sort_order' => 3,
            'position_column' => 1,
            'col_span' => 2,
            'alignment' => 'center',
            'status' => $isApproved ? 'signed' : 'pending',
            'signed_at' => $isApproved ? $plan->approved_at : null,
            'requires_digital_signature' => true,
            'is_mandatory' => true,
        ]);
    }

    private function seedProposalSignatories(ExtensionProposal $proposal, array $signatories): void
    {
        $tenantId = $proposal->tenant_id ?: 1;

        foreach ($signatories as $idx => $sig) {
            $emp = $sig['emp'] ?? null;
            $user = $sig['user'] ?? null;
            $isSigned = ($sig['status'] ?? 'pending') === 'signed';
            $daysAgo = $sig['days_ago'] ?? null;
            $signedAt = ($isSigned && $daysAgo !== null) ? Carbon::now()->subDays($daysAgo) : null;

            DocumentSignatory::create([
                'document_type' => ExtensionProposal::class,
                'document_id' => $proposal->id,
                'tenant_id' => $tenantId,
                'campus_id' => $proposal->campus_id,
                'role_label' => $sig['role_label'],
                'tier_role' => $sig['tier_role'],
                'assignment_type' => 'specific_employee',
                'employee_id' => $emp?->id,
                'user_id' => $user?->id,
                'signatory_name' => $emp ? $emp->full_name : ($user?->name ?? 'Authorized Official'),
                'designation' => $emp?->designation ?: 'Institutional Officer',
                'office' => $emp?->department?->unit_name ?: 'Administrative Office',
                'sort_order' => $idx + 1,
                'position_column' => $sig['col'] ?? 1,
                'col_span' => 1,
                'alignment' => $sig['align'] ?? 'left',
                'status' => $sig['status'] ?? 'pending',
                'signed_at' => $signedAt,
                'signed_by_user_id' => $isSigned ? $user?->id : null,
                'requires_digital_signature' => true,
                'is_mandatory' => true,
            ]);
        }
    }
}
