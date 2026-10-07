<?php

namespace Database\Factories;

use App\Models\SolutionIndustry;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolutionFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'solution_industry_id' => SolutionIndustry::factory(),
            'name' => ['ar' => $name, 'en' => $name],
            'slug' => str()->slug($name).'-'.$this->faker->unique()->numberBetween(1, 100000),
            'audience' => ['ar' => 'الجمهور المستهدف', 'en' => 'Target audience'],
            'summary' => null,
            'problem_points' => [
                ['ar' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            ],
            'features' => [
                'customer' => [['ar' => $this->faker->sentence(3), 'en' => $this->faker->sentence(3)]],
                'business_owner' => [['ar' => $this->faker->sentence(3), 'en' => $this->faker->sentence(3)]],
                'staff' => [],
            ],
            'deliverables' => [
                ['ar' => $this->faker->sentence(3), 'en' => $this->faker->sentence(3)],
            ],
            'demo_url' => null,
            'demo_credentials' => null,
            'is_flagship' => false,
            'is_published' => true,
            'is_draft' => false,
            'order' => 0,
        ];
    }
}
