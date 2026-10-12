<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->string('billing_period', 7); // format: YYYY-MM
            $table->decimal('water_usage_m3', 10, 2);
            $table->decimal('water_rate', 14, 2);
            $table->decimal('administration_fee', 14, 2)->default(0);
            $table->decimal('penalty_fee', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2);
            $table->date('due_date')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
