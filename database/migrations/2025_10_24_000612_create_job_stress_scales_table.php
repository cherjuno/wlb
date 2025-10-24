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
        Schema::create('job_stress_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('assessment_date');
            
            // JSS Questions (1-5 scale)
            $table->integer('q1_workload')->nullable();
            $table->integer('q2_time_pressure')->nullable();
            $table->integer('q3_job_uncertainty')->nullable();
            $table->integer('q4_work_conflict')->nullable();
            $table->integer('q5_support_from_supervisor')->nullable();
            $table->integer('q6_peer_support')->nullable();
            $table->integer('q7_role_clarity')->nullable();
            $table->integer('q8_job_control')->nullable();
            $table->integer('q9_physical_demands')->nullable();
            $table->integer('q10_mental_demands')->nullable();
            
            // Calculated fields
            $table->decimal('average_score', 3, 2)->nullable();
            $table->enum('stress_level', ['very_low', 'low', 'moderate', 'high', 'very_high'])->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'assessment_date']);
            $table->index('stress_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_stress_scales');
    }
};
