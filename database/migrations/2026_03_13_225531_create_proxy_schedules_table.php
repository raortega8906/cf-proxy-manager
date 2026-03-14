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
        Schema::create('proxy_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('type');                         // laliga_match | ssl_renewal | manual
            $table->string('description')->nullable();
            $table->dateTime('disable_at');
            $table->dateTime('enable_at');
            $table->string('status')->default('pending');   // pending | active | completed | failed
            $table->json('site_ids');                       // Array de IDs de sites afectados
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_schedules');
    }
};
