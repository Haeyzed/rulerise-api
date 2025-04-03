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
        Schema::create('job_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->string('keywords')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('job_categories')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('job_type_id')->nullable()->constrained('job_types')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('experience_level_id')->nullable()->constrained('experience_levels')->nullOnDelete()->cascadeOnUpdate();
            $table->string('location')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_remote')->nullable();
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->string('frequency')->default('weekly'); // Using string instead of ENUM
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_alerts');
    }
};

