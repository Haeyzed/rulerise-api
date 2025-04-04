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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('job_applications')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('scheduled_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('interview_date');
            $table->integer('duration_minutes')->nullable();
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->boolean('is_online')->default(false);
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // Using string instead of ENUM
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};

