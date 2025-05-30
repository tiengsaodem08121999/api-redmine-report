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
        Schema::create('report_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('cr_font')->nullable();
            $table->string('bug_font')->nullable();
            $table->string('cr_cms')->nullable();
            $table->string('bug_cms')->nullable(); 
            $table->string('cr_api')->nullable(); 
            $table->string('bug_api')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_summaries');
    }
};
