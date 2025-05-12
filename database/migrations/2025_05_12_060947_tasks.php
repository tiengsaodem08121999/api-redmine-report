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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_id')->nullable();
            $table->string('task_name')->nullable();
            $table->string('task_status')->nullable();
            $table->string('task_type')->nullable();
            $table->string('task_priority')->nullable();
            $table->string('task_author')->nullable();
            $table->date('logtime')->nullable();     
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
