<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('sequence');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->unsignedInteger('avg_travel_seconds_from_prev')->default(90);
            $table->timestamps();

            $table->index(['route_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stops');
    }
};
