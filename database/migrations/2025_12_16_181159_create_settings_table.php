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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('organization_acronym', 100);
            $table->string('organization_name', 255);
            $table->string('system_acronym', 100);
            $table->string('system_name', 255);
            $table->text('system_description')->nullable();
            $table->string('system_slogan', 255)->nullable();
            $table->string('system_logo', 255)->nullable();
            $table->string('organization_address', 255)->nullable();
            $table->string('organization_email', 255)->nullable();
            $table->string('organization_phone', 20)->nullable();
            $table->string('organization_whatsapp', 20)->nullable();
            $table->string('organization_level_code', 100)->nullable();
            $table->string('organization_locality', 255)->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
