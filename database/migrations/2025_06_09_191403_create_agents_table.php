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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profile_pic_url')->nullable();
            $table->boolean('has_transaction_pin')->default(false);
            $table->string('phone');
            $table->decimal('commission_balance', 15, 2)->default(0);
            $table->boolean('is_super_agent')->default(false);
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->foreignId('aggregator_id')->nullable()->constrained('agents');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
