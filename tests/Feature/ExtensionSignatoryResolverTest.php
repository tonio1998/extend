<?php

namespace Tests\Feature;

use App\Models\Dts\Unit;
use App\Models\Employee;
use App\Models\RIE\ExtensionPlan;
use App\Models\SaaS\DocumentSignatory;
use App\Services\DSS\TenantContext;
use App\Services\PDF\DocumentPdfGenerator;
use App\Services\RIE\ExtensionSignatoryResolver;
use Tests\TestCase;

class ExtensionSignatoryResolverTest extends TestCase
{
    public function test_resolves_signatories_based_on_project_leader_employee_unit(): void
    {
        // 1. Test Project Leader from CCIS (Unit ID 17)
        $ccisUnit = Unit::where('unit_code', 'CCIS')->firstOrFail();
        $ccisLeader = new Employee([
            'first_name' => 'Alan',
            'last_name' => 'Turing',
            'designation' => 'Assistant Professor IV',
            'department_id' => $ccisUnit->id,
        ]);

        $ccisSignatories = ExtensionSignatoryResolver::resolve($ccisLeader);

        $this->assertEquals('Alan Turing', $ccisSignatories['prepared']['name']);
        $this->assertEquals('Assistant Professor IV', $ccisSignatories['prepared']['designation']);
        $this->assertEquals('JESSA G. HAMBRE', $ccisSignatories['reviewed']['name']);
        $this->assertEquals('VIRNILLE C. FRANCISCO, PhEdD', $ccisSignatories['noted']['name']);
        $this->assertEquals('EVER JOY G. RABADAN, MAEM, MSc.', $ccisSignatories['concurred_1']['name']);
        $this->assertEquals('ALVIE ELMA C. PLAZA, MA', $ccisSignatories['concurred_2']['name']);

        // 2. Test Project Leader from CBT (Unit ID 19)
        $cbtUnit = Unit::where('unit_code', 'CBT')->firstOrFail();
        $cbtLeader = new Employee([
            'first_name' => 'Adam',
            'last_name' => 'Smith',
            'designation' => 'Associate Professor I',
            'department_id' => $cbtUnit->id,
        ]);

        $cbtSignatories = ExtensionSignatoryResolver::resolve($cbtLeader);

        $this->assertEquals('Adam Smith', $cbtSignatories['prepared']['name']);
        $this->assertEquals('MR. KENNETH CHARLS E. FEROL', $cbtSignatories['reviewed']['name']);
        $this->assertEquals('MARILOU B. CARNICER, PhD', $cbtSignatories['noted']['name']);

        // 3. Test Project Leader from College of Engineering (COE)
        $coeUnit = Unit::where('unit_code', 'COE')->firstOrFail();
        $coeLeader = new Employee([
            'first_name' => 'Nikola',
            'last_name' => 'Tesla',
            'designation' => 'Professor II',
            'department_id' => $coeUnit->id,
        ]);

        $coeSignatories = ExtensionSignatoryResolver::resolve($coeLeader);

        $this->assertEquals('Nikola Tesla', $coeSignatories['prepared']['name']);
        $this->assertEquals('ENGR. ROSANNE ANDALUZ, PhD.', $coeSignatories['reviewed']['name']);
        $this->assertEquals('ENGR. CATHERINE M. VERALLO, MScPe, PCpE', $coeSignatories['noted']['name']);
    }

    public function test_resolves_all_units_with_signatories(): void
    {
        $units = ExtensionSignatoryResolver::getAllUnitsWithSignatories();
        $this->assertGreaterThanOrEqual(8, $units->count());

        $unitCodes = $units->pluck('unit_code')->toArray();
        $this->assertContains('CCIS', $unitCodes);
        $this->assertContains('GS', $unitCodes);
        $this->assertContains('CBT', $unitCodes);
        $this->assertContains('CTE', $unitCodes);
        $this->assertContains('CAS', $unitCodes);
        $this->assertContains('COE', $unitCodes);
        $this->assertContains('MAINIT', $unitCodes);
        $this->assertContains('MALIMONO', $unitCodes);
    }

    public function test_snapshot_to_document_saves_to_separated_table(): void
    {
        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in test db');
        }

        $records = ExtensionSignatoryResolver::snapshotToDocument($plan);
        $this->assertGreaterThanOrEqual(4, $records->count());

        $dbRecords = DocumentSignatory::where('document_type', $plan->getMorphClass())
            ->where('document_id', $plan->id)
            ->get();

        $this->assertGreaterThanOrEqual(4, $dbRecords->count());
        $roles = $dbRecords->pluck('tier_role')->toArray();
        $this->assertContains('prepared', $roles);
        $this->assertContains('reviewed', $roles);
        $this->assertContains('noted', $roles);
        $this->assertContains('concurred', $roles);
    }

    public function test_extension_plan_pdf_renders_with_12_column_signatories(): void
    {
        $plan = ExtensionPlan::first();
        if (! $plan) {
            $this->markTestSkipped('No extension plan in test db');
        }

        $institution = TenantContext::getInstitution();
        $pdf = DocumentPdfGenerator::fromExtensionPlan($plan, $institution, '8.5x13', 'executive');

        $this->assertNotNull($pdf);
        $output = $pdf->Output('S');
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF', $output);
        $this->assertGreaterThan(10000, strlen($output));
    }
}
