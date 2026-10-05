<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    public function definition(): array
    {
        return [
            'faqable_type' => null,
            'faqable_id' => null,
            'question' => ['ar' => $this->faker->sentence(), 'en' => $this->faker->sentence()],
            'answer' => ['ar' => $this->faker->paragraph(), 'en' => $this->faker->paragraph()],
            'is_published' => true,
            'is_draft' => false,
            'order' => 0,
        ];
    }
}
