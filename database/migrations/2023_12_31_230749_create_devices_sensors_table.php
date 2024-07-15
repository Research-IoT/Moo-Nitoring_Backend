<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Devices;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices_sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Devices::class)->constrained();
            $table->string('year');
            $table->string('month');
            $table->string('day');
            $table->string('timestamp');
            $table->string('temperature');
            $table->string('humidity');
            $table->string('ammonia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_sensors');
    }
};
