<?php

namespace Database\Seeders;

use App\Models\Accreditation\AcademicProgram;
use App\Models\Dts\Records;
use App\Models\Employee;
use App\Models\RIE\ExtensionPlan;
use App\Models\SaaS\Campus;
use App\Models\SaaS\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtensionPlanSeeder extends Seeder
{
    /**
     * Seed 500 Extension Plans across the institution.
     */
    public function run(): void
    {
        $tenant = Tenant::find(1) ?? Tenant::first();
        if (! $tenant) {
            $this->command?->error('No tenant found.');

            return;
        }

        $tenantId = $tenant->id;
        $acronym = strtoupper(trim($tenant->institutionSetting?->school_acronym ?: 'ADSSU'));
        $year = (string) ($tenant->institutionSetting?->current_fy ?: date('Y'));

        $campuses = Campus::where('tenant_id', $tenantId)->get();
        if ($campuses->isEmpty()) {
            $campuses = Campus::all();
        }

        $programs = AcademicProgram::where('tenant_id', $tenantId)->get();
        if ($programs->isEmpty()) {
            $programs = AcademicProgram::all();
        }

        $employees = Employee::where('tenant_id', $tenantId)->get();
        if ($employees->isEmpty()) {
            $employees = Employee::all();
        }

        $users = User::where('tenant_id', $tenantId)->get();
        if ($users->isEmpty()) {
            $users = User::all();
        }

        $firstUser = $users->first();

        // Topic pools for realistic extension projects in SUCs
        $activityTemplates = [
            [
                'prefix' => 'DigiTech Kapwa',
                'themes' => [
                    'Digital Literacy and E-Governance Training for Barangay Officials',
                    'Cyber-Safety and Online Privacy Workshop for Public School Teachers',
                    'Basic Office Productivity and Automated Record Keeping for Rural Cooperatives',
                    'AI and Mobile Applications for High School STEM Educators',
                    'Social Media Marketing and E-Commerce Onboarding for Agrarian Reform Beneficiaries',
                ],
                'beneficiaries' => ['Barangay LGU Officials', 'Public School Teachers', 'Rural Cooperative Officers', 'Agrarian Reform Beneficiaries'],
            ],
            [
                'prefix' => 'Agri-Galing',
                'themes' => [
                    'Organic Fertilizer Formulation and Trichoderma Inoculation for Rice Farmers',
                    'High-Value Vegetable Production and Drip Irrigation in Upland Communities',
                    'Mushroom Spawn Production and Commercial Cultivation for Homemakers',
                    'Native Chicken Breeding and Feed Formulation Technology Transfer',
                    'Post-Harvest Cacao Fermentation and Artisan Tablea Processing',
                ],
                'beneficiaries' => ['Rice Farmers Association', 'Upland Agrarian Farmers', 'Rural Homemakers Club', 'Smallholder Poultry Raisers'],
            ],
            [
                'prefix' => 'Kalusugan sa Pamayanan',
                'themes' => [
                    'Community First-Aid, BLS-CPR, and Disaster Medical Response Training',
                    'Maternal and Child Nutrition Enhancement via Local Indigenous Food Ingredients',
                    'Water Sanitation, Rainwater Harvesting, and Household Hygiene Advocacy',
                    'Mental Health Literacy and Psychological First Aid for Community Volunteers',
                    'Herbal Medicine Processing and Essential Oils Distillation for Barangay Health Workers',
                ],
                'beneficiaries' => ['Barangay Health Workers (BHWs)', 'Rural Mothers and Infants', 'Community DRRM Volunteers', 'Barangay Nutrition Scholars'],
            ],
            [
                'prefix' => 'Green Guardians',
                'themes' => [
                    'Mangrove Reforestation and Coastal Bioshield Rehabilitation Protocol',
                    'Community-Based Disaster Risk Reduction and Early Warning System Installation',
                    'Bamboo Propagation and Riverbank Riparian Zone Stabilization',
                    'Solid Waste Segregation, Composting, and Upcycling for Island Communities',
                    'Watershed Protection and Endemic Tree Species Nurseries Establishment',
                ],
                'beneficiaries' => ['Coastal Fisherfolks Federation', 'Barangay DRRM Council', 'Indigenous Forest Wardens', 'Youth Environmental Advocates'],
            ],
            [
                'prefix' => 'Basa-Bata & Talino',
                'themes' => [
                    'Early Childhood Reading Remediation and Phonics Enrichment for IP Learners',
                    'Robotics and Hands-on STEM Discovery Camps for Rural Elementary Pupils',
                    'Inclusive Special Education Teaching Methodologies for Multigrade Teachers',
                    'Indigenous Culture, Folk Arts, and Oral History Documentation Workshop',
                    'Financial Literacy and Micro-Savings Habits for Out-of-School Youth',
                ],
                'beneficiaries' => ['Indigenous Peoples (IP) Learners', 'Elementary School Pupils', 'Multigrade Educators', 'Out-of-School Youth (OSY)'],
            ],
            [
                'prefix' => 'Negosyo at Asenso',
                'themes' => [
                    'Good Manufacturing Practices (GMP) and Food Packaging for Micro-Entrepreneurs',
                    'Financial Bookkeeping, Cash Flow Management, and Tax Compliance for MSMEs',
                    'Handicrafts Innovation Using Indigenous Fiber (Abaca and Water Hyacinth)',
                    'Meat and Fish Processing Technology for Women Micro-Enterprises',
                    'Eco-Tourism Guiding and Cultural Heritage Interpretation Training',
                ],
                'beneficiaries' => ['Women Micro-Entrepreneurs', 'Local Abaca Weavers Guild', 'Fish Vendors Association', 'Community Eco-Tour Guides'],
            ],
            [
                'prefix' => 'Solar & Inobasyon',
                'themes' => [
                    'Community Solar-Powered Water Pumping Installation and Maintenance',
                    'Low-Cost IoT-Based Crop Monitoring System Deployment for Farmers',
                    'Fabrication of Pedal-Powered Threshers for Remote Upland Communities',
                    'Small Engine Repair and Basic Electrical Safety for Barangay Technicians',
                    'Biogas Digester Construction from Farm Waste for Community Energy',
                ],
                'beneficiaries' => ['Upland Farming Communities', 'Barangay Maintenance Technicians', 'Smallholder Livestock Raisers', 'Rural Youth Technicians'],
            ],
            [
                'prefix' => 'Katarungan at Gabay',
                'themes' => [
                    'Katarungang Pambarangay: Paralegal Training on Mediation and Dispute Settlement',
                    'Anti-VAWC and Child Protection Rights Orientation for Community Leaders',
                    'Land Tenurial Rights and Agrarian Law Literacy for Tribal Chieftains',
                    'Consumer Rights, Weights and Measures Calibration for Public Market Vendors',
                    'Responsible Citizenship, Good Governance, and Anti-Corruption Literacy',
                ],
                'beneficiaries' => ['Lupong Tagapamayapa Mediators', 'Barangay VAWC Desk Officers', 'Tribal Chieftains and Elders', 'Market Vendors Association'],
            ],
        ];

        $municipalities = [
            'Bunawan', 'Trento', 'Bayugan City', 'Prosperidad', 'San Francisco',
            'Rosario', 'Talacogon', 'Loreto', 'Esperanza', 'Sibagat', 'Veruela',
            'Santa Josefa', 'La Paz', 'Surigao City', 'Mainit', 'Tubod', 'Placer',
        ];

        $statuses = ['approved', 'approved', 'submitted', 'submitted', 'noted', 'draft'];

        $existingCount = ExtensionPlan::where('tenant_id', $tenantId)->count();
        $targetTotal = 500;
        $toCreate = max(0, $targetTotal - $existingCount);

        if ($toCreate <= 0) {
            $this->command?->info("Tenant {$tenantId} already has {$existingCount} Extension Plans. Seeding skipped.");

            return;
        }

        $this->command?->info("Seeding {$toCreate} Extension Plans for Tenant {$tenantId} ({$acronym})...");

        $plansData = [];
        $dtsData = [];
        $now = Carbon::now();

        // Get starting max DTS ID to avoid duplicates
        $lastDtsId = Records::max('id') ?? 1000;

        for ($i = 1; $i <= $toCreate; $i++) {
            $seq = $existingCount + $i;
            $planCode = sprintf('%s-EXT-%s-%04d', $acronym, $year, $seq);

            $tmpl = $activityTemplates[($i - 1) % count($activityTemplates)];
            $themeIndex = ($i - 1) % count($tmpl['themes']);
            $themeText = $tmpl['themes'][$themeIndex];
            $municipality = $municipalities[($i - 1) % count($municipalities)];
            $activityTitle = sprintf('%s: %s in Municipality of %s', $tmpl['prefix'], $themeText, $municipality);

            $targetGroup = $tmpl['beneficiaries'][($i - 1) % count($tmpl['beneficiaries'])];
            $beneficiariesStr = sprintf('%s, Barangay Poblacion & Satellite Communities, %s', $targetGroup, $municipality);

            $program = $programs->isNotEmpty() ? $programs->random() : null;
            $campus = $campuses->isNotEmpty() ? $campuses->random() : null;
            $emp = $employees->isNotEmpty() ? $employees->random() : null;
            $usr = $users->isNotEmpty() ? $users->random() : $firstUser;

            $status = $statuses[($i - 1) % count($statuses)];

            // Spread dates over the past 180 days
            $daysAgo = ($toCreate - $i) % 180;
            $createdDate = $now->copy()->subDays($daysAgo)->subHours(($i * 7) % 24);
            $proposalDate = $createdDate->copy()->addDays(3);
            $activityDesignDate = $proposalDate->copy()->addDays(4);
            $purchaseDate = $activityDesignDate->copy()->addDays(5);
            $materialsStart = $purchaseDate->copy()->addDays(7);
            $materialsEnd = $materialsStart->copy()->addDays(2);
            $conductStart = $materialsEnd->copy()->addDays(3);
            $conductEnd = $conductStart->copy()->addDays(2);
            $narrativeDate = $conductEnd->copy()->addDays(7);
            $monitoringDate = $narrativeDate->copy()->addDays(14);

            $budget = (float) (rand(20, 250) * 1000); // 20k to 250k
            $benCount = rand(30, 250);
            $extCount = rand(3, 12);

            $proponentName = $emp ? ($emp->first_name.' '.$emp->last_name) : ($usr ? $usr->name : 'Project Leader');
            $proponentDesignation = $emp?->position ?: 'Faculty Extensionist / Project Leader';

            // Generate DTS record if not draft
            $dtsRecordId = null;
            if ($status !== 'draft') {
                $lastDtsId++;
                $dtsTrackingNo = sprintf('%012d', $lastDtsId);
                $dtsRecordId = $lastDtsId;

                $dtsData[] = [
                    'id' => $lastDtsId,
                    'tenant_id' => $tenantId,
                    'UnitID' => $campus?->id ?? 1,
                    'DocumentID' => $dtsTrackingNo,
                    'QRCODE' => $dtsTrackingNo,
                    'ConnectQR' => $dtsTrackingNo,
                    'type' => 'Internal',
                    'routing_as' => 'Unit',
                    'doc_type' => 'Extension Plan Form',
                    'Description' => 'Extension Plan: '.substr($activityTitle, 0, 150),
                    'Pages' => 1,
                    'forAction' => 'For Review and Endorsement',
                    'TransactionType' => 'Complex',
                    'Priority' => 1,
                    'TrailStatus' => $status === 'approved' ? 'Done' : 'Pending',
                    'DocStatus' => 'active',
                    'status' => 'active',
                    'archived' => 0,
                    'locked' => 0,
                    'created_by' => $usr?->id ?? 1,
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate,
                ];
            }

            $plansData[] = [
                'tenant_id' => $tenantId,
                'campus_id' => $campus?->id ?? 1,
                'academic_program_id' => $program?->id,
                'dts_record_id' => $dtsRecordId,
                'plan_code' => $planCode,
                'program_name' => $program?->program_name ?? 'General Higher Education Extension',
                'extension_activity' => $activityTitle,
                'objectives' => "1. Capacitate participants with essential competencies and practical tools.\n2. Establish community-based monitoring and sustainable implementation.\n3. Strengthen university-community partnership and measurable socioeconomic outcomes.",
                'sub_extension_proposal_date' => $proposalDate->toDateString(),
                'sub_activity_design_date' => $activityDesignDate->toDateString(),
                'sub_purchase_request_date' => $purchaseDate->toDateString(),
                'moa_signing_date' => $purchaseDate->copy()->addDays(2)->toDateString(),
                'materials_arrival_mode' => 'range',
                'materials_arrival_start' => $materialsStart->toDateString(),
                'materials_arrival_end' => $materialsEnd->toDateString(),
                'conduct_activity_mode' => 'range',
                'conduct_activity_start' => $conductStart->toDateString(),
                'conduct_activity_end' => $conductEnd->toDateString(),
                'sub_narrative_report_date' => $narrativeDate->toDateString(),
                'monitoring_feedback_date' => $monitoringDate->toDateString(),
                'beneficiaries' => $beneficiariesStr,
                'target_beneficiaries_count' => $benCount,
                'proposed_budget' => $budget,
                'extensionists_count' => $extCount,
                'prepared_by_user_id' => $usr?->id ?? 1,
                'prepared_by_employee_id' => $emp?->id,
                'prepared_by_name' => $proponentName,
                'prepared_by_designation' => $proponentDesignation,
                'prepared_at' => $createdDate,
                'noted_by_name' => 'Dr. Maria Elena Santos',
                'noted_by_designation' => 'College Head for Extension',
                'noted_at' => in_array($status, ['noted', 'approved']) ? $createdDate->copy()->addDays(1) : null,
                'approved_by_name' => 'Dr. Fernando Gomez, PECE',
                'approved_by_designation' => 'College Dean / RIE Director',
                'approved_at' => ($status === 'approved') ? $createdDate->copy()->addDays(3) : null,
                'status' => $status,
                'remarks' => $status === 'approved' ? 'Officially reviewed, endorsed, and funded under institutional extension allotment.' : null,
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
            ];

            // Batch insert every 100 records for performance
            if (count($plansData) >= 100) {
                if (! empty($dtsData)) {
                    DB::table('dts_records')->insert($dtsData);
                    $dtsData = [];
                }
                DB::table('extension_plans')->insert($plansData);
                $plansData = [];
                $this->command?->info("Inserted {$i}/{$toCreate} plans...");
            }
        }

        // Remaining batch
        if (! empty($dtsData)) {
            DB::table('dts_records')->insert($dtsData);
        }
        if (! empty($plansData)) {
            DB::table('extension_plans')->insert($plansData);
        }

        $finalCount = ExtensionPlan::where('tenant_id', $tenantId)->count();
        $this->command?->info("Finished! Total Extension Plans for Tenant {$tenantId}: {$finalCount}");
    }
}
