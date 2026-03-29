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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('code', 100)->unique();
            $table->text('description')->nullable();
            
            // Foreign Keys
            $table->foreignId('product_type_id')->constrained('product_types')->onDelete('cascade');
            $table->foreignId('speculation_id')->constrained('speculations')->onDelete('cascade');
            $table->foreignId('unit_of_measure_id')->references('id')->on('unite_of_measures')->onDelete('cascade');
            $table->foreignId('production_area_id')->constrained('production_areas')->onDelete('cascade');
            $table->foreignId('actor_id')->constrained('actors')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('updated_by')->nullable();
            
            $table->integer('quantity')->default(0);
            $table->integer('price')->default(0);
            $table->string('origin', 255)->nullable();
            $table->string('shape', 100)->nullable();
            $table->string('measure_used')->nullable();
            $table->string('photo', 255)->nullable();
            $table->date('production_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
