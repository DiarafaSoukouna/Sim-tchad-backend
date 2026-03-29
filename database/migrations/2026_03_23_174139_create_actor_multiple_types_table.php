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
        Schema::create('actor_multiple_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_type_id')->constrained('actor_types')->cascadeOnDelete();
            $table->unique(['actor_id', 'actor_type_id']);
            $table->timestamps();
        });
        DB::table('actors')->get()->each(function ($actor) {
        if ($actor->actor_type_id) {
            DB::table('actor_multiple_types')->insert([
                'actor_id' => $actor->id,
                'actor_type_id' => $actor->actor_type_id
            ]);
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actor_multiple_types');
    }
};
