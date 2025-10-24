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
        Schema::create('wlb_matrix_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('calculation_date');
            $table->string('period_type')->default('monthly'); // monthly, weekly, quarterly
            $table->date('period_start');
            $table->date('period_end');
            
            // WLB Score Components
            $table->decimal('wlb_score', 5, 2)->default(0);
            $table->enum('wlb_level', ['high', 'low']);
            $table->integer('overtime_hours')->default(0);
            $table->integer('late_count')->default(0);
            $table->integer('leave_days')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0);
            
            // JSS Score Components
            $table->decimal('jss_score', 3, 2)->default(2.5);
            $table->enum('stress_level', ['high', 'low']);
            
            // Matrix Position
            $table->tinyInteger('quadrant'); // 1,2,3,4
            $table->string('quadrant_name');
            $table->text('recommendation')->nullable();
            
            // Metadata
            $table->json('detailed_metrics')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'calculation_date'], 'wlb_matrix_user_date_idx');
            $table->index(['period_type', 'period_start', 'period_end'], 'wlb_matrix_period_idx');
            $table->index('quadrant', 'wlb_matrix_quadrant_idx');
            
            // Unique constraint to prevent duplicate calculations
            $table->unique(['user_id', 'calculation_date', 'period_type'], 'wlb_matrix_unique_calc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wlb_matrix_calculations');
    }
};
