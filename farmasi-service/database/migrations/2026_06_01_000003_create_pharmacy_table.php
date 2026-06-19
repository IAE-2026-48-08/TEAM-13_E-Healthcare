<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('patient_id')->nullable();
            $table->string('appointment_id')->nullable();
            $table->string('medicine_name');
            $table->string('dosage');
            $table->string('frequency');
            $table->integer('quantity');
            $table->text('instructions')->nullable();
            $table->enum('status', ['PENDING', 'PREPARING', 'READY_TO_PICKUP', 'DISPENSED'])->default('PENDING');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pharmacy');
    }
};
