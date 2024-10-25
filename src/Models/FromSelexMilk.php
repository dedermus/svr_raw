<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Модель: сырые данные из Селекс для молочных коров
 *
 * @package App\Models\Raw
 */
class FromSelexMilk extends Model
{
    use HasFactory;


    /**
     * Точное название таблицы с учетом схемы
     * @var string
     */
    protected $table								= 'raw.raw_from_selex_milk';


    /**
     * Первичный ключ таблицы (автоинкремент)
     * @var string
     */
    protected $primaryKey						    = 'raw_from_selex_milk_id';


    /**
     * Поле даты создания строки
     * @var string
     */
    const CREATED_AT                                = 'created_at';


    /**
     * Поле даты обновления строки
     * @var string
     */
    const UPDATED_AT                                = 'update_at';


    /**
     * Значения полей по умолчанию
     * @var array
     */
    protected $attributes                           = [];


    protected array $dates = [
        'created_at',
        'update_at',
    ];

    /**
     * Формат хранения столбцов даты модели.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * @var bool
     */
    public $timestamps = true;


    /**
     * Создать запись
     *
     * @param $request
     *
     * @return void
     */
    public function createRaw(Application|Request $request): void
    {
        $this->rules($request);
        $data = $request->all();
        self::create($data);
    }

    /**
     * Обновить RAW запись
     * @param $request
     *
     * @return void
     */
    public function updateRaw(Application|Request $request): void
    {
        // валидация
        $this->rules($request);
        // получаем массив полей и значений и формы
        $data = $this->fill($request->all());
        $data = $request->all();
        // получаем id
        $id = (isset($data[$this->primaryKey])) ? $data[$this->primaryKey] : null;
        // готовим сущность для обновления
        $modules_data = $this->find($id);
        // обновляем запись
        $modules_data->update($data);
    }

    /**
     * Поля, которые можно менять сразу массивом
     * @var array
     */
    protected $fillable                     = [
        'raw_from_selex_milk_id',       // инкремент
        'NANIMAL',                      // животное - НЕ уникальный идентификатор
        'NANIMAL_TIME',                 // животное - уникальный идентификатор (наверное...)
        'NINV',                         // животное - инвентарный номер
        'KLICHKA',                      // животное - кличка
        'POL',                          // животное - пол
        'NPOL',                         // животное - код пола
        'NGOSREGISTER',                 // животное - идентификационный номер РСХН
        'NINV1',                        // животное - номер в оборудовании
        'NINV3',                        // животное - электронная метка
        'ANIMAL_VID',                   // животное - вид животного
        'ANIMAL_VID_COD',               // животное - код вида животного (КРС - 26 / Овцы - 17)
        'MAST',                         // животное - масть
        'NMAST',                        // животное - код масти
        'POR',                          // животное - порода
        'NPOR',                         // животное - код породы
        'DATE_ROGD',                    // животное - дата рождения в формате YYYY.mm.dd
        'DATE_POSTUPLN',                // животное - дата поступления в формате YYYY.mm.dd
        'NHOZ_ROGD',                    // животное - хозяйство рождения (базовый индекс хозяйства)
        'NHOZ',                         // животное - базовый индекс хозяйства (текущее хозяйство)
        'NOBL',                         // животное - внутренний код области хозяйства (текущее хозяйство)
        'NRN',                          // животное - внутренний код района хозяйства (текущее хозяйство)
        'NIDENT',                       // животное - импортный идентификатор
        'ROGD_HOZ',                     // животное - хозяйство рождения (название)
        'DATE_V',                       // животное - дата выбытия в формате YYYY.mm.dd
        'PV',                           // животное - причина выбытия
        'RASHOD',                       // животное - расход
        'GM_V',                         // животное - живая масса при выбытии (кг)
        'ISP',                          // животное - использование (племенная ценность)
        'DATE_CHIP',                    // животное - дата электронного мечения в формате YYYY.mm.dd
        'DATE_NINV',                    // животное - дата мечения (инв. №) в формате YYYY.mm.dd
        'DATE_NGOSREGISTER',            // животное - дата мечения (№ РСХН) в формате YYYY.mm.dd
        'NINV_OTCA',                    // отец - инвентарный номер
        'NGOSREGISTER_OTCA',            // отец - идентификационный номер РСХН
        'POR_OTCA',                     // отец - порода
        'NPOR_OTCA',                    // отец - код породы
        'DATE_ROGD_OTCA',               // отец - дата рождения в формате YYYY.mm.dd
        'NINV_MATERI',                  // мать - инвентарный номер
        'NGOSREGISTER_MATERI',          // мать - идентификационный номер РСХН
        'POR_MATERI',                   // мать - порода
        'NPOR_MATERI',                  // мать - код породы
        'DATE_ROGD_MATERI',             // мать - дата рождения в формате YYYY.mm.dd
        'IMPORT_STATUS',                // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
        'TASK',                         // код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы
        'GUID_SVR',                     // гуид животного, который генерирует СВР в момент создания этой записи
        'ANIMALS_JSON',                 // сырые данные из Селекс
        'created_at',                   // дата создания записи
        'update_at',                    // дата создания/редактирования записи
    ];


    /**
     * Поля, которые нельзя менять сразу массивом
     * @var array
     */
    protected $guarded = [
        'raw_from_selex_milk_id'
    ];


    /**
     * Массив системных скрытых полей
     * @var array
     */
    protected $hidden								= [];


    /**
     * Валидация входных данных
     *
     * @param $request
     *
     * @return void
     */
    private function rules( Application|Request $request): void
    {
        // получаем поля со значениями
        $data = $request->all();

        // получаем значение первичного ключа
        $id = (isset($data[$this->primaryKey])) ? $data[$this->primaryKey] : null;

        // id - Первичный ключ
        if (!is_null($id)) {
            $request->validate(
                [$this->primaryKey => 'required|exists:.' . $this->getTable() . ',' . $this->primaryKey],
                [$this->primaryKey => trans('svr-core-lang::validation.required')],
            );
        }

        // NANIMAL
        $request->validate(
            ['NANIMAL' => 'integer|nullable'],
            ['NANIMAL' => trans('svr-core-lang::validation')],
        );

        // NANIMAL_TIME
        $request->validate(
            ['NANIMAL_TIME' => 'max:128|nullable'],
            ['NANIMAL_TIME' => trans('svr-core-lang::validation')],
        );

        // NINV
        $request->validate(
            ['NINV' => 'max:15|nullable'],
            ['NINV' => trans('svr-core-lang::validation')],
        );

        // KLICHKA
        $request->validate(
            ['KLICHKA' => 'max:50|nullable'],
            ['KLICHKA' => trans('svr-core-lang::validation')],
        );

        // POL
        $request->validate(
            ['POL' => 'max:30|nullable'],
            ['POL' => trans('svr-core-lang::validation')],
        );

        // NPOL
        $request->validate(
            ['NPOL' => 'integer|nullable'],
            ['NPOL' => trans('svr-core-lang::validation')],
        );

        // NGOSREGISTER
        $request->validate(
            ['NGOSREGISTER' => 'max:50|nullable'],
            ['NGOSREGISTER' => trans('svr-core-lang::validation')]
        );

        // NINV1
        $request->validate(
            ['NINV1' => 'max:15|nullable'],
            ['NINV1' => trans('svr-core-lang::validation')]
        );

        // NINV3
        $request->validate(
            ['NINV3' => 'max:20|nullable'],
            ['NINV3' => trans('svr-core-lang::validation')]
        );

        // ANIMAL_VID
        $request->validate(
            ['ANIMAL_VID' => 'max:50|nullable'],
            ['ANIMAL_VID' => trans('svr-core-lang::validation')]
        );

        // ANIMAL_VID_COD
        $request->validate(
            ['ANIMAL_VID_COD' => 'required|integer'],
            ['ANIMAL_VID_COD' => trans('svr-core-lang::validation')]
        );

        // MAST
        $request->validate(
            ['MAST' => 'max:30|nullable'],
            ['MAST' => trans('svr-core-lang::validation')]
        );

        // NMAST
        $request->validate(
            ['NMAST' => 'integer|nullable'],
            ['NMAST' => trans('svr-core-lang::validation')]
        );

        // POR
        $request->validate(
            ['POR' => 'max:30|nullable'],
            ['POR' => trans('svr-core-lang::validation')]
        );

        // NPOR
        $request->validate(
            ['NPOR' => 'integer|nullable'],
            ['NPOR' => trans('svr-core-lang::validation')]
        );

        // DATE_ROGD
        $request->validate(
            ['DATE_ROGD' => 'data|nullable'],
            ['DATE_ROGD' => trans('svr-core-lang::validation')]
        );

        // DATE_POSTUPLN
        $request->validate(
            ['DATE_POSTUPLN' => 'data|nullable'],
            ['DATE_POSTUPLN' => trans('svr-core-lang::validation')]
        );

        // NHOZ_ROGD
        $request->validate(
            ['NHOZ_ROGD' => 'integer|nullable'],
            ['NHOZ_ROGD' => trans('svr-core-lang::validation')]
        );

        // NHOZ
        $request->validate(
            ['NHOZ' => 'integer|nullable'],
            ['NHOZ' => trans('svr-core-lang::validation')]
        );

        // NOBL
        $request->validate(
            ['NOBL' => 'integer|nullable'],
            ['NOBL' => trans('svr-core-lang::validation')]
        );

        // NRN
        $request->validate(
            ['NRN' => 'integer|nullable'],
            ['NRN' => trans('svr-core-lang::validation')]
        );

        // NIDENT
        $request->validate(
            ['NIDENT' => 'max:20|nullable'],
            ['NIDENT' => trans('svr-core-lang::validation')]
        );

        // ROGD_HOZ
        $request->validate(
            ['ROGD_HOZ' => 'max:50|nullable'],
            ['ROGD_HOZ' => trans('svr-core-lang::validation')]
        );

        // DATE_V
        $request->validate(
            ['DATE_V' => 'data|nullable'],
            ['DATE_V' => trans('svr-core-lang::validation')]
        );

        // PV
        $request->validate(
            ['PV' => 'max:60|nullable'],
            ['PV' => trans('svr-core-lang::validation')]
        );

        // RASHOD
        $request->validate(
            ['RASHOD' => 'max:30|nullable'],
            ['RASHOD' => trans('svr-core-lang::validation')]
        );

        // GM_V
        $request->validate(
            ['GM_V' => 'integer|nullable'],
            ['GM_V' => trans('svr-core-lang::validation')]
        );

        // ISP
        $request->validate(
            ['ISP' => 'max:20|nullable'],
            ['ISP' => trans('svr-core-lang::validation')]
        );

        // DATE_CHIP
        $request->validate(
            ['DATE_CHIP' => 'data|nullable'],
            ['DATE_CHIP' => trans('svr-core-lang::validation')]
        );

        // DATE_NINV
        $request->validate(
            ['DATE_NINV' => 'data|nullable'],
            ['DATE_NINV' => trans('svr-core-lang::validation')]
        );

        // DATE_NGOSREGISTER
        $request->validate(
            ['DATE_NGOSREGISTER' => 'data|nullable'],
            ['DATE_NGOSREGISTER' => trans('svr-core-lang::validation')]
        );

        // NINV_OTCA
        $request->validate(
            ['NINV_OTCA' => 'max:15|nullable'],
            ['NINV_OTCA' => trans('svr-core-lang::validation')]
        );

        // NGOSREGISTER_OTCA
        $request->validate(
            ['NGOSREGISTER_OTCA' => 'max:50|nullable'],
            ['NGOSREGISTER_OTCA' => trans('svr-core-lang::validation')]
        );

        // POR_OTCA
        $request->validate(
            ['POR_OTCA' => 'max:30|nullable'],
            ['POR_OTCA' => trans('svr-core-lang::validation')]
        );

        // NPOR_OTCA
        $request->validate(
            ['NPOR_OTCA' => 'integer|nullable'],
            ['NPOR_OTCA' => trans('svr-core-lang::validation')]
        );

        // DATE_ROGD_OTCA
        $request->validate(
            ['DATE_ROGD_OTCA' => 'data|nullable'],
            ['DATE_ROGD_OTCA' => trans('svr-core-lang::validation')]
        );

        // NINV_MATERI
        $request->validate(
            ['NINV_MATERI' => 'max:15|nullable'],
            ['NINV_MATERI' => trans('svr-core-lang::validation')]
        );

        // NGOSREGISTER_MATERI
        $request->validate(
            ['NGOSREGISTER_MATERI' => 'max:50|nullable'],
            ['NGOSREGISTER_MATERI' => trans('svr-core-lang::validation')]
        );

        // POR_MATERI
        $request->validate(
            ['POR_MATERI' => 'max:30|nullable'],
            ['POR_MATERI' => trans('svr-core-lang::validation')]
        );

        // NPOR_MATERI
        $request->validate(
            ['NPOR_MATERI' => 'integer|nullable'],
            ['NPOR_MATERI' => trans('svr-core-lang::validation')]
        );

        // DATE_ROGD_MATERI
        $request->validate(
            ['DATE_ROGD_MATERI' => 'data|nullable'],
            ['DATE_ROGD_MATERI' => trans('svr-core-lang::validation')]
        );

        // IMPORT_STATUS
        $request->validate(
            ['IMPORT_STATUS' => 'required'],
            ['IMPORT_STATUS' => trans('svr-core-lang::validation')]
        );

        // TASK
        $request->validate(
            ['TASK' => 'integer|nullable'],
            ['TASK' => trans('svr-core-lang::validation')]
        );

        // GUID_SVR
        $request->validate(
            ['GUID_SVR' => 'max:64|nullable'],
            ['GUID_SVR' => trans('svr-core-lang::validation')]
        );

        // ANIMALS_JSON
        $request->validate(
            ['ANIMALS_JSON' => 'json|nullable'],
            ['ANIMALS_JSON' => trans('svr-core-lang::validation')]
        );
    }
}
