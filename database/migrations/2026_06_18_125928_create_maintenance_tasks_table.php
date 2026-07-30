<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['backups', 'updates', 'security', 'performance', 'content', 'seo', 'other'])->index();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->index();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'blocked'])->default('pending')->index();
            $table->dateTime('scheduled_at')->nullable()->index();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_tasks');
    }
};
