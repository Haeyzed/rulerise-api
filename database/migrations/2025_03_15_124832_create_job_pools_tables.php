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
        Schema::create('job_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('target_hiring_count')->nullable();
            $table->integer('current_hiring_count')->default(0);
            $table->string('department', 100)->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'slug']);
            $table->index('is_active');
            $table->index('is_public');
        });

        Schema::create('job_pool_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_pool_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['job_pool_id', 'job_id']);
        });

        Schema::create('job_pool_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_pool_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('importance')->default('required'); // Using string instead of ENUM
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['job_pool_id', 'skill_id']);
        });

        Schema::create('candidate_job_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('job_pool_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status')->default('pending'); // Using string instead of ENUM
            $table->text('notes')->nullable();
            $table->foreignId('added_by_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['candidate_id', 'job_pool_id']);
            $table->index('status');
        });

        Schema::create('job_pool_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_job_pool_id')->constrained('candidate_job_pools')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status'); // Using string instead of ENUM
            $table->text('notes')->nullable();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_pool_status_history');
        Schema::dropIfExists('candidate_job_pools');
        Schema::dropIfExists('job_pool_skills');
        Schema::dropIfExists('job_pool_jobs');
        Schema::dropIfExists('job_pools');
    }
};

