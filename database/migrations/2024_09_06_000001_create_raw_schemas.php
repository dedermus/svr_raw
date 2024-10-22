<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверить существование схемы raw (если нет, то создать ее)
        DB::statement('CREATE SCHEMA IF NOT EXISTS raw');
        DB::statement("COMMENT ON SCHEMA raw IS 'Необработанные данные'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS raw CASCADE');

    }
};
