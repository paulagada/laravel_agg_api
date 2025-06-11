<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    use HasFactory;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
             'wallet_id' => $this->faker->uuid,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // default password
            'profile_pic_url' => $this->faker->imageUrl(200, 200),
            'has_transaction_pin' => $this->faker->boolean(70),
            'phone' => $this->faker->phoneNumber,
            'commission_balance' => $this->faker->randomFloat(2, 0, 10000),
            'is_super_agent' => false,
            'aggregator_id' => null,
        ];
    }

    // State for super agent
    public function superAgent()
    {
        return $this->state(fn() => [
            'is_super_agent' => true,
            'aggregator_id' => null,
            'commission_percent' => 1.00, // 1% commission
        ]);
    }

    // State for sub agent (will set aggregator_id after creation)
    public function subAgent($aggregatorId)
    {
        return $this->state(fn() => [
            'is_super_agent' => false,
            'aggregator_id' => $aggregatorId,
            'commission_percent' => null,
        ]);
    }

}
