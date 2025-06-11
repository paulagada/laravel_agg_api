<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agent;
use App\Models\Terminal;
use App\Models\Transaction;

class DashboardSeeder extends Seeder
{
    public function run()
    {
        // 1. Create 10 super agents
        $superAgents = Agent::factory()->count(10)->superAgent()->create();

        // 2. Create 90 sub agents, evenly distributed among super agents
        $subAgents = collect();
        foreach ($superAgents as $superAgent) {
            // 9 sub agents per super agent
            $subAgents = $subAgents->merge(
                Agent::factory()
                    ->count(9)
                    ->subAgent($superAgent->id)
                    ->create()
            );
        }

        // 3. Create 150 terminals for sub agents only
        $terminals = collect();
        $subAgentIds = $subAgents->pluck('id')->toArray();

        for ($i = 0; $i < 150; $i++) {
            $terminals->push(
                Terminal::factory()->create([
                    'agent_id' => $subAgentIds[array_rand($subAgentIds)],
                ])
            );
        }

        $terminalIds = $terminals->pluck('terminal_id')->toArray();

        // 4. Create 500 transactions
        for ($i = 0; $i < 500; $i++) {
            // Pick a random sub-agent
            $agent = $subAgents->random();

            // Get their super agent
            $superAgent = Agent::find($agent->aggregator_id);

            // Get the commission percent (default 0 if null)
            $commissionPercent = $superAgent?->commission_percent ?? 0;

            // Create a random amount
            $amount = fake()->randomFloat(2, 10, 1000);

            // Calculate agg_commission
            $aggCommission = round(($commissionPercent / 100) * $amount, 2);

            // Create the transaction
            Transaction::factory()->create([
                'agent_id' => $agent->id,
                'terminal_id' => $terminalIds[array_rand($terminalIds)],
                'date' => now()->subDays(rand(0, 14)),
                'amount' => $amount,
                'agg_commission' => $aggCommission,
            ]);
        }
    }
}
