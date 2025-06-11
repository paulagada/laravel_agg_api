<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasApiTokens, HasFactory;

    public function terminals()
    {
        return $this->hasMany(Terminal::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function childrenAgents()
    {
        return $this->hasMany(Agent::class, 'aggregator_id');
    }
    public function superAgent()
    {
        return $this->belongsTo(self::class, 'aggregator_id');
    }

    protected $casts = [
        'has_transaction_pin' => 'boolean',
        'is_super_agent' => 'boolean',
    ];
}
