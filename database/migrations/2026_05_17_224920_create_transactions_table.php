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
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('restrict');
            $table->integer('total_tickets');
            $table->integer('total_price');
            $table->integer('amount_paid');
            $table->integer('change_amount')->default(0);
            $table->enum('payment_method', ['cash', 'transfer']);
            $table->enum('status', ['pending', 'success', 'cancelled'])->default('success');
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
