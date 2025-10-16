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
            $table->integer('month'); // 1-12
            $table->integer('year'); // e.g., 2025
            
            // 10 pertanyaan Job Stress Scale (Parker, 1983)
            $table->tinyInteger('question_1')->comment('I have too much work to do in too little time');
            $table->tinyInteger('question_2')->comment('I often have to work very fast');
            $table->tinyInteger('question_3')->comment('I feel overloaded with the amount of work I must do');
            $table->tinyInteger('question_4')->comment('The deadlines set for my job are usually too tight');
            $table->tinyInteger('question_5')->comment('My job requires working under constant time pressure');
            $table->tinyInteger('question_6')->comment('I rarely have enough time to finish everything I need to do at work');
            $table->tinyInteger('question_7')->comment('The pace of work in my job is very high');
            $table->tinyInteger('question_8')->comment('I have to do more work than I can handle comfortably');
            $table->tinyInteger('question_9')->comment('I often have to work extra hours to meet the demands of my job');
            $table->tinyInteger('question_10')->comment('I feel that the demands of my job interfere with the quality of my work');
            
            $table->integer('total_score'); // Sum of all 10 questions (10-50)
            $table->enum('stress_level', ['low', 'moderate', 'high'])->comment('low: 10-20, moderate: 21-35, high: 36-50');
            
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi per bulan per user
            $table->unique(['user_id', 'month', 'year']);
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
