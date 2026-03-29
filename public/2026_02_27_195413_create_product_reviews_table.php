<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
                
            $table->foreignId('actor_id')
                ->constrained('actors')
                ->cascadeOnDelete();

       

            $table->unsignedTinyInteger('quality_rating');
            $table->unsignedTinyInteger('price_rating');
            $table->unsignedTinyInteger('delivery_rating');

            $table->decimal('global_rating', 3, 2)->nullable();

            $table->text('comment')->nullable();

            $table->timestamps();

            $table->unique(['order_id', 'product_id', 'actor_id']);
        });

        // Ajouter la colonne calculée (MySQL)
        DB::statement("
            ALTER TABLE product_reviews
            MODIFY global_rating DECIMAL(3,2)
            GENERATED ALWAYS AS (
                (quality_rating + price_rating + delivery_rating) / 3
            ) STORED
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};