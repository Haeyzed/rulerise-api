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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('resume_id')->nullable()->constrained('candidate_resumes')->nullOnDelete()->cascadeOnUpdate();
            $table->text('cover_letter')->nullable();
            $table->string('status')->default('pending'); // Using string instead of ENUM
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->string('salary_currency', 3)->nullable()->default('USD');
            $table->date('availability_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_viewed')->default(false);
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'candidate_id']);
            $table->index('status');
        });

        Schema::create('job_application_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('job_applications')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status'); // Using string instead of ENUM
            $table->text('notes')->nullable();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('job_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['job_id', 'user_id']);
        });

        Schema::create('candidate_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['candidate_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_bookmarks');
        Schema::dropIfExists('job_bookmarks');
        Schema::dropIfExists('job_application_status_history');
        Schema::dropIfExists('job_applications');
    }
};

