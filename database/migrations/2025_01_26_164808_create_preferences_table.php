<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->json('preferred_sources')->nullable();  // Store preferred sources as a JSON array
            $table->json('preferred_categories')->nullable();  // Store preferred categories as a JSON array
            $table->json('preferred_authors')->nullable();  // Store preferred authors as a JSON array
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferences');
    }
};
