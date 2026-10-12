<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The application-specific users table is created by the later
        // 2026_09_11_082839_create_users_table migration, after customers.
    }

    public function down(): void
    {
        // No table is created by this compatibility migration.
    }
};
