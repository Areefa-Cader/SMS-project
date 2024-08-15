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
        Schema::create('service__reports', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->string('service_category');
            $table->string('staff_name');
            $table->decimal('service_price',8,2);
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service__reports');
    }
};
