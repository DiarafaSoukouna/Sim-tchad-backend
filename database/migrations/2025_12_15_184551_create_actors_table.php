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
        Schema::create('actors', function (Blueprint $table) {
            $table->id();
            $table->string('actor');
            $table->string('actor_sigle');
            $table->string('email')->nullable();
            $table->string('phone')->unique();
            $table->string('whatsapp')->nullable();
            $table->foreignId('actor_type_id')->constrained('actor_types')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->string('headquarter_photo')->nullable();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('password');
            $table->string('updated_by')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();    
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actors');
    }
};
