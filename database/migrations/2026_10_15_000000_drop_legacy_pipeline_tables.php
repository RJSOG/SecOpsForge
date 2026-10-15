<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the tables of the removed build pipeline (transactions) and of
     * Sanctum (personal_access_tokens). No-op on a fresh database.
     */
    public function up(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('personal_access_tokens');
    }

    public function down(): void
    {
        //
    }
};
