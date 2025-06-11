<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->decimal('agg_commission', 10, 2)->nullable();
            $table->string('reference')->unique();
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');

            $table->string('terminal_id', 7); // Alphanumeric terminal_id
            $table->foreign('terminal_id')->references('terminal_id')->on('terminals')->onDelete('cascade');

            $table->string('rrr')->nullable();
            $table->string('status_code')->nullable();
            $table->string('masked_pan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
