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
        Schema::create('studio_seats', function (Blueprint $table) {
            $table->id();
            $table->string('seat_code', 10);
            $table->integer('row');
            $table->integer('col');
            $table->enum('type', ['regular', 'vip', 'disabled'])->default('regular');
            $table->foreignId('studio_id')->constrained('studios')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_seats');
    }
};
