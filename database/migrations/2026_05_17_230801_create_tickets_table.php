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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('restrict');
            $table->foreignId('seat_id')->constrained('studio_seats')->onDelete('restrict');
            $table->integer('price');
            $table->enum('status', ['active', 'used', 'refunded'])->default('active');
            $table->timestamps();

            $table->unique(['schedule_id', 'seat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
