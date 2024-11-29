<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Core\Traits\GetValidationRules;
use Svr\Core\Traits\GetTableName;

/**
 * Модель: сырые данные из Селекс для молочных коров
 *
 * @package App\Models\Raw
 */
class FromSelexMilk extends Model
{
    use GetTableName;
    use HasFactory;
    use GetValidationRules;

    /**
     * Точное название таблицы с учетом схемы
     * @var string
     */
    protected $table = 'raw.raw_from_selex_milk';


    /**
     * Первичный ключ таблицы (автоинкремент)
     * @var string
     */
    protected $primaryKey = 'raw_from_selex_milk_id';


    /**
     * Поле даты создания строки
     * @var string
     */
    const CREATED_AT = 'created_at';


    /**
     * Поле даты обновления строки
     * @var string
     */
    const UPDATED_AT = 'update_at';


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
     * @param Request $request
     *
     * @return void
     */
    public function createRaw(Request $request): void
    {
        $this->validateRequest($request);
        $data = $request->all();
        self::create($data);
    }

    /**
     * Обновить RAW запись
     * @param Request $request
     *
     * @return void
     */
    public function updateRaw(Request $request): void
    {
        // валидация
        $this->validateRequest($request);
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
    protected $hidden = [];


    /**
     * Получить правила валидации
     * @param Request $request
     * @return array
     */
    private function getValidationRules(Request $request): array
    {
        $id = $request->input($this->primaryKey);

        return [
            $this->primaryKey => [
                $request->isMethod('put') ? 'required' : '',
                Rule::exists('.' . $this->getTable(), $this->primaryKey),
            ],
            $this->primaryKey => 'required|exists:.' . $this->getTable() . ',' . $this->primaryKey,
            'NANIMAL' => 'integer|nullable',
            'NANIMAL_TIME' => 'max:128|nullable',
            'NINV' => 'max:15|nullable',
            'KLICHKA' => 'max:50|nullable',
            'POL' => 'max:30|nullable',
            'NPOL' => 'integer|nullable',
            'NGOSREGISTER' => 'max:50|nullable',
            'NINV1' => 'max:15|nullable',
            'NINV3' => 'max:20|nullable',
            'ANIMAL_VID' => 'max:50|nullable',
            'ANIMAL_VID_COD' => 'required|integer',
            'MAST' => 'max:30|nullable',
            'NMAST' => 'integer|nullable',
            'POR' => 'max:30|nullable',
            'NPOR' => 'integer|nullable',
            'DATE_ROGD' => 'date|nullable',
            'DATE_POSTUPLN' => 'date|nullable',
            'NHOZ_ROGD' => 'integer|nullable',
            'NHOZ' => 'integer|nullable',
            'NOBL' => 'integer|nullable',
            'NRN' => 'integer|nullable',
            'NIDENT' => 'max:20|nullable',
            'ROGD_HOZ' => 'max:50|nullable',
            'DATE_V' => 'date|nullable',
            'PV' => 'max:60|nullable',
            'RASHOD' => 'max:30|nullable',
            'GM_V' => 'integer|nullable',
            'ISP' => 'max:20|nullable',
            'DATE_CHIP' => 'date|nullable',
            'DATE_NINV' => 'date|nullable',
            'DATE_NGOSREGISTER' => 'date|nullable',
            'NINV_OTCA' => 'max:15|nullable',
            'NGOSREGISTER_OTCA' => 'max:50|nullable',
            'POR_OTCA' => 'max:30|nullable',
            'NPOR_OTCA' => 'integer|nullable',
            'DATE_ROGD_OTCA' => 'date|nullable',
            'NINV_MATERI' => 'max:15|nullable',
            'NGOSREGISTER_MATERI' => 'max:50|nullable',
            'POR_MATERI' => 'max:30|nullable',
            'NPOR_MATERI' => 'integer|nullable',
            'DATE_ROGD_MATERI' => 'date|nullable',
            'IMPORT_STATUS' => ['required',
                Rule::enum(ImportStatusEnum::class)],
            'TASK' => 'integer|nullable',
            'GUID_SVR' => 'max:64|nullable',
            'ANIMALS_JSON' => 'json|nullable',
        ];
    }

    /**
     * Получить сообщения об ошибках валидации
     * @return array
     */
    private function getValidationMessages(): array
    {
        return [
            // Объединяем все сообщения об ошибках в один массив
            $this->primaryKey . '.required' => trans('svr-core-lang::validation.required'),
            $this->primaryKey . '.exists' => trans('svr-core-lang::validation.exists'),
            'NANIMAL' => trans('svr-core-lang::validation'),
            'NANIMAL_TIME' => trans('svr-core-lang::validation'),
            'NINV' => trans('svr-core-lang::validation'),
            'KLICHKA' => trans('svr-core-lang::validation'),
            'POL' => trans('svr-core-lang::validation'),
            'NPOL' => trans('svr-core-lang::validation'),
            'NGOSREGISTER' => trans('svr-core-lang::validation'),
            'NINV1' => trans('svr-core-lang::validation'),
            'NINV3' => trans('svr-core-lang::validation'),
            'ANIMAL_VID' => trans('svr-core-lang::validation'),
            'ANIMAL_VID_COD' => trans('svr-core-lang::validation'),
            'MAST' => trans('svr-core-lang::validation'),
            'NMAST' => trans('svr-core-lang::validation'),
            'POR' => trans('svr-core-lang::validation'),
            'NPOR' => trans('svr-core-lang::validation'),
            'DATE_ROGD' => trans('svr-core-lang::validation'),
            'DATE_POSTUPLN' => trans('svr-core-lang::validation'),
            'NHOZ_ROGD' => trans('svr-core-lang::validation'),
            'NHOZ' => trans('svr-core-lang::validation'),
            'NOBL' => trans('svr-core-lang::validation'),
            'NRN' => trans('svr-core-lang::validation'),
            'NIDENT' => trans('svr-core-lang::validation'),
            'ROGD_HOZ' => trans('svr-core-lang::validation'),
            'DATE_V' => trans('svr-core-lang::validation'),
            'PV' => trans('svr-core-lang::validation'),
            'RASHOD' => trans('svr-core-lang::validation'),
            'GM_V' => trans('svr-core-lang::validation'),
            'ISP' => trans('svr-core-lang::validation'),
            'DATE_CHIP' => trans('svr-core-lang::validation'),
            'DATE_NINV' => trans('svr-core-lang::validation'),
            'DATE_NGOSREGISTER' => trans('svr-core-lang::validation'),
            'NINV_OTCA' => trans('svr-core-lang::validation'),
            'NGOSREGISTER_OTCA' => trans('svr-core-lang::validation'),
            'POR_OTCA' => trans('svr-core-lang::validation'),
            'NPOR_OTCA' => trans('svr-core-lang::validation'),
            'DATE_ROGD_OTCA' => trans('svr-core-lang::validation'),
            'NINV_MATERI' => trans('svr-core-lang::validation'),
            'NGOSREGISTER_MATERI' => trans('svr-core-lang::validation'),
            'POR_MATERI' => trans('svr-core-lang::validation'),
            'NPOR_MATERI' => trans('svr-core-lang::validation'),
            'DATE_ROGD_MATERI' => trans('svr-core-lang::validation'),
            'IMPORT_STATUS' => trans('svr-core-lang::validation'),
            'TASK' => trans('svr-core-lang::validation'),
            'GUID_SVR' => trans('svr-core-lang::validation'),
            'ANIMALS_JSON' => trans('svr-core-lang::validation'),
        ];
    }
}
