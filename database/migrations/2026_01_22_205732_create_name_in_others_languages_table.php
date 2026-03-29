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
        Schema::create('name_in_others_languages', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // ex: 'actor', 'product', etc.
            $table->unsignedBigInteger('entity_id'); // ID de l'entité dans sa table respective
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade'); // Référence à la table des langues
            $table->string('name'); // Nom dans la langue spécifiée
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('name_in_others_languages');
    }
};
