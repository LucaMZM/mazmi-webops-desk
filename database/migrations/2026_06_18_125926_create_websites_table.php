<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('url');
            $table->enum('technology', ['WordPress', 'PrestaShop', 'Laravel', 'PHP custom', 'Static HTML', 'Other'])->index();
            $table->string('hosting_provider')->nullable();
            $table->date('domain_expires_at')->nullable()->index();
            $table->date('hosting_expires_at')->nullable()->index();
            $table->enum('ssl_status', ['active', 'expiring', 'expired', 'unknown'])->default('unknown');
            $table->enum('maintenance_plan', ['basic', 'standard', 'premium', 'none'])->default('none')->index();
            $table->enum('status', ['stable', 'review', 'incident', 'critical'])->default('stable')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
