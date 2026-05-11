<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->string('plate_number')->unique();
            $table->string('driver_name')->nullable();
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->decimal('speed_kmh', 5, 2)->default(25);
            $table->enum('status', ['idle', 'in_service', 'out_of_service'])->default('idle');
            $table->json('simulation_state')->nullable();
            $table->dateTime('last_position_at')->nullable();
            $table->timestamps();

            $table->index(['route_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
