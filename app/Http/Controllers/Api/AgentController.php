<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Agent;
use App\Models\Terminal;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    public function getTransactions(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $agent = Auth::user();

        $query = Transaction::where('agent_id', $agent->id);

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', Carbon::parse($request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', Carbon::parse($request->to));
        }

        $transactions = $query->orderBy('date', 'desc')->paginate();

        return response()->json($transactions);
    }

    public function getSuperAgentTransactions(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $superAgent = Auth::user();

        // Ensure only super agents can access this
        if (!$superAgent->is_super_agent) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Get agent IDs under this super agent
        $agentIds = $superAgent->childrenAgents()->pluck('id');

        // Build the query
        $query = Transaction::whereIn('agent_id', $agentIds);

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->input('to'));
        }

        $transactions = $query->orderBy('date', 'desc')->paginate();

        

        return response()->json([
            'super_agent_id' => $superAgent->id,
            'agents_count' => $agentIds->count(),
            'transactions_count' => $transactions->count(),
            'transactions' => $transactions,
        ]);
    }

    public function me()
    {
        $agent = Auth::user();
        if (!$agent->is_super_agent) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Get agent IDs under this super agent
        $agentIds = $agent->childrenAgents()->pluck('id');

        // Build the query
        $transactions = Transaction::whereIn('agent_id', $agentIds);
        $terminals = Terminal::whereIn('agent_id', $agentIds);

        // $transactions = $agent->transactions();
        // $terminals = $agent->terminals;

        $monthlyTransactions = $transactions
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        $monthlyAmount = $monthlyTransactions->sum('amount');
        $monthlyCount = $monthlyTransactions->count();

        $response = [
            'id' => $agent->id,
            'name' => $agent->first_name . ' ' . $agent->last_name,
            'walletId' => $agent->wallet_id,
            'address' => $agent->address ?? '',
            'phone' => $agent->phone,
            'email' => $agent->email,
            'aggregator_id' => $agent->aggregator_id ?? '',
            'is_super_agent' => $agent->is_super_agent,
            'terminals_count' => $terminals->count(),
            'transactions_count' => $transactions->count(),
            'terminals' => $terminals,
            'commission_balance' => $agent->commission_balance,
            'has_transaction_pin' => $agent->has_transaction_pin,
            'monthly_transactions_amount' => $monthlyAmount,
            'monthly_transactions_count' => $monthlyCount,
        ];

        return response()->json($response);
    }

    public function topActiveAgents(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $superAgent = Auth::user();

        if (!$superAgent->is_super_agent) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Get all agent IDs under this super agent
        $agentIds = Agent::where('aggregator_id', $superAgent->id)->pluck('id');

        // Query top terminals based on transactions
        $query = Transaction::select(
            'terminal_id',
            DB::raw('MAX(agent_id) as agent_id'), // in case multiple agents used same terminal (unlikely)
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(agg_commission) as total_commission'),
            DB::raw('COUNT(*) as transaction_count')
        )->whereIn('agent_id', $agentIds);

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', Carbon::parse($request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', Carbon::parse($request->to));
        }

        $query->groupBy('terminal_id')
            ->orderByDesc('total_amount')
            ->limit(5); // top 4 terminals

        $topTerminals = $query->get();

        // Format response
        $response = $topTerminals->map(function ($record) {
            $agent = Agent::find($record->agent_id);

            return [
                'agent_id' => $record->agent_id,
                'agent_name' => $agent ? $agent->first_name . ' ' . $agent->last_name : null,
                'terminal_id' => $record->terminal_id,
                'total_amount' => $record->total_amount,
                'total_commission' => $agent ? $record->total_commission : 0, // or calculate differently
                'transaction_count' => $record->transaction_count,
            ];
        });

        return response()->json($response);
    }
}
