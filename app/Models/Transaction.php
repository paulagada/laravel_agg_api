<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
    public function terminal()
    {
        return $this->belongsTo(Terminal::class, "terminal_id");
    }
}
