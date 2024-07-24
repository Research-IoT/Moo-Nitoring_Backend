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
        Schema::create('devices_dashboard', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Devices::class)->constrained();
            $table->string('temperature');
            $table->string('humidity');
            $table->string('ammonia');
            $table->string('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_dashboard');
    }
};
