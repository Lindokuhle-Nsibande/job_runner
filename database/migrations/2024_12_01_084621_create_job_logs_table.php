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
        Schema::create('job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->string('method_name');
            $table->json('parameters')->nullable();
            $table->enum('status', ['pending', 'running', 'success', 'failure', 'cancelled']);
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('priority')->default(0);
            $table->string('process_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_logs');
    }
};