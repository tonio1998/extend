<?php

namespace Database\Factories\RIE;

use App\Models\RIE\ExtensionPlan;
use App\Models\RIE\ExtensionPlanActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExtensionPlanActivity>
 */
class ExtensionPlanActivityFactory extends Factory
{
    protected $model = ExtensionPlanActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'extension_plan_id' => ExtensionPlan::factory(),
            'sort_order' => $this->faker->numberBetween(1, 10),
            'title' => $this->faker->sentence(4),
            'target_date' => $this->faker->date('F d, Y'),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'is_na' => false,
            'status' => 'pending',
            'remarks' => null,
        ];
    }
}
