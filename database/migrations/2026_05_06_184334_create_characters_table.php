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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('pasive_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('imagen')->nullable();
            $table->integer('hp');
            $table->integer('max_hp');
            $table->integer('physical_attack');
            $table->integer('special_attack');
            $table->integer('physical_defense');
            $table->integer('special_defense');
            $table->integer('speed');
            $table->integer('exp')->default(0);
            $table->integer('level')->default(1);
            $table->boolean('recruited')->default(false);
            $table->boolean('alive')->default(true);
            $table->timestamps();
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->index('game_id');
            $table->index('pasive_id');
            $table->index('alive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
