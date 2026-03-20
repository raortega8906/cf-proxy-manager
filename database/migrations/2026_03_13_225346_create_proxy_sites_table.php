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
        Schema::create('proxy_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain');
            $table->text('cloudflare_zone_id');
            $table->text('cloudflare_dns_record_id')->nullable();
            $table->boolean('proxy_enabled');
            $table->boolean('ssl_auto_renewal')->default(false);
            $table->date('ssl_next_renewal')->nullable();
            $table->boolean('affected_by_laliga')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxy_sites');
    }
};
