<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->bigIncrements('doctor_id');
            $table->unsignedBigInteger('department_id'); // Changed from dept_id to department_id
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20);
            $table->string('password', 255);
            $table->string('specialization', 100);
            $table->text('qualification');
            $table->enum('status', ['active', 'inactive', 'on_leave']);
            $table->timestamps();

            // Add the foreign key constraint
            $table->foreign('department_id')
                ->references('department_id')
                ->on('departments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
