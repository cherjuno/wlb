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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->unique()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->date('hire_date')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('hire_date');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
            $table->text('address')->nullable()->after('gender');
            $table->foreignId('department_id')->nullable()->constrained()->after('address');
            $table->foreignId('position_id')->nullable()->constrained()->after('department_id');
            $table->foreignId('manager_id')->nullable()->constrained('users')->after('position_id');
            $table->integer('annual_leave_quota')->default(12)->after('manager_id');
            $table->integer('remaining_leave')->default(12)->after('annual_leave_quota');
            $table->boolean('is_active')->default(true)->after('remaining_leave');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn([
                'employee_id', 'phone', 'hire_date', 'birth_date', 'gender', 
                'address', 'department_id', 'position_id', 'manager_id',
                'annual_leave_quota', 'remaining_leave', 'is_active'
            ]);
        });
    }
};
