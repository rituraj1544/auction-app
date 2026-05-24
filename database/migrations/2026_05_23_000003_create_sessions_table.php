<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // Sessions table is already created in the base 0001_01_01_000000_create_users_table migration
    // This migration is kept as a no-op for migration history consistency
    
    public function up(): void
    {
        // No-op: sessions table already exists from base migration
    }

    public function down(): void
    {
        // No-op: don't modify the sessions table here
    }
};
