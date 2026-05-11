<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stop_densities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stop_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedTinyInteger('hour');
            $table->decimal('density_score', 5, 2)->default(0);
            $table->unsignedInteger('passenger_estimate')->default(0);
            $table->timestamps();

            $table->unique(['stop_id', 'date', 'hour']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stop_densities');
    }
};
