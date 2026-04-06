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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('movie_id')->constrained('movies')->onDelete('cascade');
            $table->foreignId('studio_id')->constrained('studios')->onDelete('cascade');
            $table->date('show_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
