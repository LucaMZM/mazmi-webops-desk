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
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->text('summary');
            $table->unsignedInteger('completed_tasks_count')->default(0);
            $table->unsignedInteger('resolved_tickets_count')->default(0);
            $table->unsignedInteger('pending_tickets_count')->default(0);
            $table->text('recommendations');
            $table->enum('general_status', ['good', 'attention', 'critical'])->default('good')->index();
            $table->timestamps();
            $table->unique(['client_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
