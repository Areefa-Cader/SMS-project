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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('service_name');
            $table->decimal('total_amount',10,2);
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();

            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
