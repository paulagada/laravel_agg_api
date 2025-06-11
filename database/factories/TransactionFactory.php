<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    use HasFactory;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['withdrawal']; // example types
        $statuses = ['success', 'pending', 'failed'];
        

        return [
            'type' => $this->faker->randomElement($types),
            'reference' => strtoupper(Str::random(10)),
            'date' => $this->faker->dateTimeBetween('-2 weeks', 'now'),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'agent_id' => null,    // assign later in seeder
            'terminal_id' => null, // assign later in seeder
            'agg_commission' => null, // calculated in seeder
            'rrr' => strtoupper(Str::random(12)),
            'status_code' => $this->faker->randomElement($statuses),
            'masked_pan' => '**** **** **** ' . $this->faker->randomNumber(4),
        ];
    }
}
