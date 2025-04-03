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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->integer('duration_days')->default(0); // 0 means unlimited
            $table->integer('job_posts_limit')->default(0);
            $table->integer('featured_jobs_limit')->default(0);
            $table->integer('resume_views_limit')->default(0);
            $table->boolean('job_alerts')->default(false);
            $table->boolean('candidate_search')->default(false);
            $table->boolean('resume_access')->default(false);
            $table->boolean('company_profile')->default(true);
            $table->string('support_level')->default('basic');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('features')->nullable();
            $table->timestamps();
        });

        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('payment_status')->default('pending'); // Using string instead of ENUM
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id')->nullable();
            $table->integer('job_posts_used')->default(0);
            $table->integer('featured_jobs_used')->default(0);
            $table->integer('resume_views_used')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};

