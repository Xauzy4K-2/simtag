<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_name', 100)->unique();
            $table->decimal('setting_value', 14, 2);
            $table->text('description')->nullable();
            $table->foreignId('updated_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_settings');
    }
};
