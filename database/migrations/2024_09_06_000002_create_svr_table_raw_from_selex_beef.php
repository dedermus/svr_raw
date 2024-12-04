<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Core\Traits\PostgresGrammar;

return new class extends Migration
{
    use PostgresGrammar;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->enumExists();

        if (!Schema::hasTable('raw.raw_from_selex_beef'))
        {
            Schema::create('raw.raw_from_selex_beef', function (Blueprint $table) {
                $table->comment('сырые данные из Селекс для мясных коров');
                $table->increments('raw_from_selex_beef_id')->comment('Инкремент');
                $table->bigInteger('nanimal')->nullable(true)->default(null)->comment('животное - уникальный идентификатор');
                $table->string('nanimal_time', 128)->nullable(true)->default(null)->comment('животное - уникальный идентификатор (наверное...)');
                $table->string('ninv', 15)->nullable(true)->default(null)->comment('животное - инвентарный номер');
                $table->string('klichka', 50)->nullable(true)->default(null)->comment('животное - кличка');
                $table->string('pol', 30)->nullable(true)->default(null)->comment('животное - пол');
                $table->smallInteger('npol')->nullable(true)->default(null)->comment('животное - код пола');
                $table->string('ngosregister', 50)->nullable(true)->default(null)->comment('животное - идентификационный номер РСХН');
                $table->string('ninv1', 15)->nullable(true)->default(null)->comment('животное - номер в оборудовании');
                $table->string('ninv3', 20)->nullable(true)->default(null)->comment('животное - электронная метка');
                $table->string('animal_vid', 50)->nullable(true)->default(null)->comment('животное - вид животного');
                $table->smallInteger('animal_vid_cod')->nullable(true)->default(null)->comment('животное - код вида животного (КРС - 26 / Овцы - 17');
                $table->string('mast', 30)->nullable(true)->default(null)->comment('животное - масть');
                $table->smallInteger('nmast')->nullable(true)->default(null)->comment('животное - код масти');
                $table->string('por', 30)->nullable(true)->default(null)->comment('животное - порода');
                $table->smallInteger('npor')->nullable(true)->default(null)->comment('животное - код породы');
                $table->date('date_rogd')->nullable(true)->default(null)->comment('животное - дата рождения в формате YYYY-mm-dd');
                $table->date('date_postupln')->nullable(true)->default(null)->comment('животное - дата поступления в формате YYYY-mm-dd');
                $table->integer('nhoz_rogd')->nullable(true)->default(null)->comment('животное - хозяйство рождения (базовый индекс хозяйства)');
                $table->integer('nhoz')->nullable(true)->default(null)->comment('животное - базовый индекс хозяйства (текущее хозяйство)');
                $table->integer('nobl')->nullable(true)->default(null)->comment('животное - внутренний код области хозяйства (текущее хозяйство)');
                $table->integer('nrn')->nullable(true)->default(null)->comment('животное - внутренний код района хозяйства (текущее хозяйство)');
                $table->string('nident', 20)->nullable(true)->default(null)->comment('животное - импортный идентификатор');
                $table->string('rogd_hoz', 50)->nullable(true)->default(null)->comment('животное - хозяйство рождения (название)');
                $table->date('date_v')->nullable(true)->default(null)->comment('животное - дата выбытия в формате YYYY-mm-dd');
                $table->string('pv', 60)->nullable(true)->default(null)->comment('животное - причина выбытия');
                $table->string('rashod', 30)->nullable(true)->default(null)->comment('животное - расход');
                $table->integer('gm_v')->nullable(true)->default(null)->comment('животное - живая масса при выбытии (кг)');
                $table->string('isp', 20)->nullable(true)->default(null)->comment('животное - использование (племенная ценность)');
                $table->date('date_chip')->nullable(true)->default(null)->comment('животное - дата электронного мечения в формате YYYY-mm-dd');
                $table->date('date_ninv')->nullable(true)->default(null)->comment('животное - дата мечения (инв. №) в формате YYYY-mm-dd');
                $table->date('date_ngosregister')->nullable(true)->default(null)->comment('животное - дата мечения (№ РСХН) в формате YYYY-mm-dd');
                $table->string('ninv_otca', 15)->nullable(true)->default(null)->comment('отец - инвентарный номер');
                $table->string('ngosregister_otca', 50)->nullable(true)->default(null)->comment('отец - идентификационный номер РСХН');
                $table->string('por_otca', 30)->nullable(true)->default(null)->comment('отец - порода');
                $table->smallInteger('npor_otca')->nullable(true)->default(null)->comment('отец - код породы');
                $table->date('date_rogd_otca')->nullable(true)->default(null)->comment('отец - дата рождения в формате YYYY-mm-dd');
                $table->string('ninv_materi', 15)->nullable(true)->default(null)->comment('мать - инвентарный номер');
                $table->string('ngosregister_materi', 50)->nullable(true)->default(null)->comment('мать - идентификационный номер РСХН');
                $table->string('por_materi', 30)->nullable(true)->default(null)->comment('мать - порода');
                $table->smallInteger('npor_materi')->nullable(true)->default(null)->comment('мать - код породы');
                $table->date('date_rogd_materi')->nullable(true)->default(null)->comment('мать - дата рождения в формате YYYY-mm-dd');
                $table->addColumn('system.import_status', 'import_status')->nullable(false)->default(ImportStatusEnum::NEW->value)->comment('ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)');
                $table->smallInteger('task')->nullable(true)->default(null)->comment('код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы');
                $table->string('guid_svr', 64)->nullable(true)->default(null)->comment('гуид животного, который генерирует СВР в момент создания этой записи');
                $table->jsonb('animals_json')->nullable(true)->default(null)->comment('сырые данные из Селекс');
                $table->timestamp('created_at')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Дата создания записи');
                $table->timestamp('updated_at')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Дата удаления записи');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw.raw_from_selex_beef');
    }
};

