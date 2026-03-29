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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table-> foreignId('buyer_id')->constrained('actors')->onDelete('cascade');
            $table-> foreignId('seller_id')->constrained('actors')->onDelete('cascade');
            $table->boolean('status');
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }
  
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('orders');
        Schema::enableForeignKeyConstraints();
    }
};
