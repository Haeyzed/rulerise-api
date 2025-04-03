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
        Schema::create('candidate_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('level')->nullable(); // Using string instead of ENUM
            $table->string('proficiency_level')->nullable(); // Using string instead of ENUM
            $table->timestamps();

            $table->unique(['candidate_id', 'skill_id']);
        });

        Schema::create('candidate_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('institution');
            $table->string('degree');
            $table->string('field_of_study')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->string('grade', 50)->nullable();
            $table->text('activities')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('company_name');
            $table->string('job_title');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('issuing_organization');
            $table->date('issue_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('proficiency'); // Using string instead of ENUM
            $table->timestamps();
        });

        Schema::create('candidate_resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidate_profiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_resumes');
        Schema::dropIfExists('candidate_languages');
        Schema::dropIfExists('candidate_certifications');
        Schema::dropIfExists('candidate_projects');
        Schema::dropIfExists('candidate_experiences');
        Schema::dropIfExists('candidate_educations');
        Schema::dropIfExists('candidate_skills');
    }
};

