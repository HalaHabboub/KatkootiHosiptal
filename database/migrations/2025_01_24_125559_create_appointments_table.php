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
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('appointment_id')->primary(); // Using UUID for the primary key
            $table->unsignedBigInteger('patient_id'); // Foreign key for patients table
            $table->unsignedBigInteger('doctor_id'); // Foreign key for doctors table
            $table->unsignedBigInteger('admin_id')->nullable(); // Foreign key for admins table (nullable)
            $table->dateTime('date_time'); // Appointment date and time
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending'); // Status of the appointment
            $table->text('message')->nullable(); // Optional message
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('doctor_id')->on('doctors')->onDelete('cascade');
            $table->foreign('admin_id')->references('admin_id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
