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
        Schema::create('geo_guard_ip_blacklist', function (Blueprint $table) {
            $table->uuid()->primary()->unique();
            $table->ipAddress();
            $table->string('country_code',2);
            $table->string('country_name');
            $table->string('path');
            $table->timestamp('banned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_guard_ip_blacklist');
    }
};
