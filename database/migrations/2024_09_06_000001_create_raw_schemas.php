<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Svr\Core\Enums\ApplicationStatusEnum;
use Svr\Core\Enums\HerriotErrorTypesEnum;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Core\Enums\SystemBreedingValueEnum;
use Svr\Core\Enums\SystemNotificationsTypesEnum;
use Svr\Core\Enums\SystemParticipationsTypesEnum;
use Svr\Core\Enums\SystemSexEnum;
use Svr\Core\Enums\SystemStatusConfirmEnum;
use Svr\Core\Enums\SystemStatusDeleteEnum;
use Svr\Core\Enums\SystemStatusEnum;
use Svr\Core\Enums\SystemStatusNotificationEnum;
use Svr\Core\Enums\ApplicationAnimalStatusEnum;


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
