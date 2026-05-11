<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('speed_kmh', 5, 2)->default(0);
            $table->dateTime('recorded_at');
            $table->timestamps();

            $table->index(['bus_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_logs');
    }
};
