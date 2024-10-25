<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Модель: сырые данные из Селекс для мясных коров
 *
 * @package App\Models\Raw
 */
class FromSelexBeef extends Model
{
    use HasFactory;


    /**
     * Точное название таблицы с учетом схемы
     * @var string
     */
    protected $table                                    = 'raw.raw_from_selex_beef';


    /**
     * Первичный ключ таблицы (автоинкремент)
     * @var string
     */
    protected $primaryKey                               = 'raw_from_selex_beef_id';


    /**
     * Поле даты создания строки
     * @var string
     */
    const CREATED_AT                                    = 'created_at';


    /**
     * Поле даты обновления строки
     * @var string
     */
    const UPDATED_AT                                    = 'update_at';


    /**
     * Значения полей по умолчанию
     * @var array
     */
    protected $attributes = [];



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
    protected $fillable = [
        'raw_from_selex_beef_id',       // Инкремент
        'NANIMAL',                      // Животное - уникальный идентификатор
        'NANIMAL_TIME',                 // Животное - уникальный идентификатор (наверное...)
        'NINV',                         // Животное - инвентарный номер
        'KLICHKA',                      // Животное - кличка
        'POL',                          // Животное - пол
        'NPOL',                         // Животное - код пола
        'NGOSREGISTER',                 // Животное - идентификационный номер РСХН
        'NINV1',                        // Животное - номер в оборудовании
        'NINV3',                        // Животное - электронная метка
        'ANIMAL_VID',                   // Животное - вид животного
        'ANIMAL_VID_COD',               // Животное - код вида животного (КРС - 26 / Овцы - 17
        'MAST',                         // Животное - масть
        'NMAST',                        // Животное - код масти
        'POR',                          // Животное - порода
        'NPOR',                         // Животное - код породы
        'DATE_ROGD',                    // Животное - дата рождения в формате YYYY.mm.dd
        'DATE_POSTUPLN',                // Животное - дата поступления в формате YYYY.mm.dd
        'NHOZ_ROGD',                    // Животное - хозяйство рождения (базовый индекс хозяйства)
        'NHOZ',                         // Животное - базовый индекс хозяйства (текущее хозяйство)
        'NOBL',                         // Животное - внутренний код области хозяйства (текущее хозяйство)
        'NRN',                          // Животное - внутренний код района хозяйства (текущее хозяйство)
        'NIDENT',                       // Животное - импортный идентификатор
        'ROGD_HOZ',                     // Животное - хозяйство рождения (название)
        'DATE_V',                       // Животное - дата выбытия
        'PV',                           // Животное - причина выбытия
        'RASHOD',                       // Животное - расход
        'GM_V',                         // Животное - живая масса при выбытии (кг)
        'ISP',                          // Животное - использование (племенная ценность)
        'DATE_CHIP',                    // Животное - дата электронного мечения
        'DATE_NINV',                    // Животное - дата мечения (инв. №)
        'DATE_NGOSREGISTER',            // Животное - дата мечения (№ РСХН)
        'NINV_OTCA',                    // Оотец - инвентарный номер
        'NGOSREGISTER_OTCA',            // Оотец - идентификационный номер РСХН
        'POR_OTCA',                     // Оотец - порода
        'NPOR_OTCA',                    // Отец - код породы
        'DATE_ROGD_OTCA',               // Отец - дата рождения
        'NINV_MATERI',                  // Мать - инвентарный номер
        'NGOSREGISTER_MATERI',          // Мать - идентификационный номер РСХН
        'POR_MATERI',                   // Мать - порода
        'NPOR_MATERI',                  // Мать - код породы
        'DATE_ROGD_MATERI',             // Мать - дата рождения
        'IMPORT_STATUS',                // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
        'TASK',                         // Код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы
        'GUID_SVR',                     // Гуид животного, который генерирует СВР в момент создания этой записи
        'ANIMALS_JSON',                 // Сырые данные из Селекс
        'created_at',                   // Дата создания записи
        'update_at',                    // Дата удаления записи
    ];


    /**
     * Поля, которые нельзя менять сразу массивом
     * @var array
     */
    protected $guarded = [
        'raw_from_selex_beef_id'
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
            ['NGOSREGISTER' => trans('svr-core-lang::validation')],
        );

        // NINV1
        $request->validate(
            ['NINV1' => 'max:15|nullable'],
            ['NINV1' => trans('svr-core-lang::validation')],
        );

        // NINV3
        $request->validate(
            ['NINV3' => 'max:20|nullable'],
            ['NINV3' => trans('svr-core-lang::validation')],
        );

        // ANIMAL_VID
        $request->validate(
            ['ANIMAL_VID' => 'max:50|nullable'],
            ['ANIMAL_VID' => trans('svr-core-lang::validation')],
        );

        // ANIMAL_VID_COD
        $request->validate(
            ['ANIMAL_VID_COD' => 'required|integer'],
            ['ANIMAL_VID_COD' => trans('svr-core-lang::validation')],
        );

        // MAST
        $request->validate(
            ['MAST' => 'max:30|nullable'],
            ['MAST' => trans('svr-core-lang::validation')],
        );

        // NMAST
        $request->validate(
            ['NMAST' => 'integer|nullable'],
            ['NMAST' => trans('svr-core-lang::validation')],
        );

        // POR
        $request->validate(
            ['POR' => 'max:30|nullable'],
            ['POR' => trans('svr-core-lang::validation')],
        );

        // NPOR
        $request->validate(
            ['NPOR' => 'integer|nullable'],
            ['NPOR' => trans('svr-core-lang::validation')],
        );

        // DATE_ROGD
        $request->validate(
            ['DATE_ROGD' => 'date|nullable'],
            ['DATE_ROGD' => trans('svr-core-lang::validation')],
        );

        // DATE_POSTUPLN
        $request->validate(
            ['DATE_POSTUPLN' => 'date|nullable'],
            ['DATE_POSTUPLN' => trans('svr-core-lang::validation')],
        );

        // NHOZ_ROGD
        $request->validate(
            ['NHOZ_ROGD' => 'integer|nullable'],
            ['NHOZ_ROGD' => trans('svr-core-lang::validation')],
        );

        // NHOZ
        $request->validate(
            ['NHOZ' => 'integer|nullable'],
            ['NHOZ' => trans('svr-core-lang::validation')],
        );

        // NOBL
        $request->validate(
            ['NOBL' => 'integer|nullable'],
            ['NOBL' => trans('svr-core-lang::validation')],
        );

        // NRN
        $request->validate(
            ['NRN' => 'integer|nullable'],
            ['NRN' => trans('svr-core-lang::validation')],
        );

        // NIDENT
        $request->validate(
            ['NIDENT' => 'max:20|nullable'],
            ['NIDENT' => trans('svr-core-lang::validation')],
        );

        // ROGD_HOZ
        $request->validate(
            ['ROGD_HOZ' => 'max:50|nullable'],
            ['ROGD_HOZ' => trans('svr-core-lang::validation')],
        );

        // DATE_V
        $request->validate(
            ['DATE_V' => 'date|nullable'],
            ['DATE_V' => trans('svr-core-lang::validation')],
        );

        // PV
        $request->validate(
            ['PV' => 'max:60|nullable'],
            ['PV' => trans('svr-core-lang::validation')],
        );

        // RASHOD
        $request->validate(
            ['RASHOD' => 'max:30|nullable'],
            ['RASHOD' => trans('svr-core-lang::validation')],
        );

        // GM_V
        $request->validate(
            ['GM_V' => 'integer|nullable'],
            ['GM_V' => trans('svr-core-lang::validation')],
        );

        // ISP
        $request->validate(
            ['ISP' => 'max:20|nullable'],
            ['ISP' => trans('svr-core-lang::validation')],
        );

        // DATE_CHIP
        $request->validate(
            ['DATE_CHIP' => 'date|nullable'],
            ['DATE_CHIP' => trans('svr-core-lang::validation')],
        );

        // DATE_NINV
        $request->validate(
            ['DATE_NINV' => 'date|nullable'],
            ['DATE_NINV' => trans('svr-core-lang::validation')],
        );

        // DATE_NGOSREGISTER
        $request->validate(
            ['DATE_NGOSREGISTER' => 'date|nullable'],
            ['DATE_NGOSREGISTER' => trans('svr-core-lang::validation')],
        );

        // NINV_OTCA
        $request->validate(
            ['NINV_OTCA' => 'max:15|nullable'],
            ['NINV_OTCA' => trans('svr-core-lang::validation')],
        );

        // NGOSREGISTER_OTCA
        $request->validate(
            ['NGOSREGISTER_OTCA' => 'max:50|nullable'],
            ['NGOSREGISTER_OTCA' => trans('svr-core-lang::validation')],
        );

        // POR_OTCA
        $request->validate(
            ['POR_OTCA' => 'max:30|nullable'],
            ['POR_OTCA' => trans('svr-core-lang::validation')],
        );

        // NPOR_OTCA
        $request->validate(
            ['NPOR_OTCA' => 'integer|nullable'],
            ['NPOR_OTCA' => trans('svr-core-lang::validation')],
        );

        // DATE_ROGD_OTCA
        $request->validate(
            ['DATE_ROGD_OTCA' => 'date|nullable'],
            ['DATE_ROGD_OTCA' => trans('svr-core-lang::validation')],
        );

        // NINV_MATERI
        $request->validate(
            ['NINV_MATERI' => 'max:15|nullable'],
            ['NINV_MATERI' => trans('svr-core-lang::validation')],
        );

        // NGOSREGISTER_MATERI
        $request->validate(
            ['NGOSREGISTER_MATERI' => 'max:50|nullable'],
            ['NGOSREGISTER_MATERI' => trans('svr-core-lang::validation')],
        );

        // POR_MATERI
        $request->validate(
            ['POR_MATERI' => 'max:30|nullable'],
            ['POR_MATERI' => trans('svr-core-lang::validation')],
        );

        // NPOR_MATERI
        $request->validate(
            ['NPOR_MATERI' => 'integer|nullable'],
            ['NPOR_MATERI' => trans('svr-core-lang::validation')],
        );

        // DATE_ROGD_MATERI
        $request->validate(
            ['DATE_ROGD_MATERI' => 'date|nullable'],
            ['DATE_ROGD_MATERI' => trans('svr-core-lang::validation')],
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
