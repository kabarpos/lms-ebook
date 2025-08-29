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
        // pricing_id column was already removed in a previous migration
        // This migration is just for consistency and rollback capability
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to add pricing_id back as it's no longer used
    }
};
