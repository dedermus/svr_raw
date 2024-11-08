<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Svr\Core\Enums\ImportStatusEnum;

/**
 * Модель: сырые данные из Селекс для овец
 *
 * @package App\Models\Raw
 */
class FromSelexSheep extends Model
{
    use HasFactory;


    /**
     * Точное название таблицы с учетом схемы
     * @var string
     */
    protected $table                                = 'raw.raw_from_selex_sheep';


    /**
     * Первичный ключ таблицы (автоинкремент)
     * @var string
     */
    protected $primaryKey                           = 'raw_from_selex_sheep_id';


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
        $this->rulesReturnWithBag($request);
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
        $this->rulesReturnWithBag($request);
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

        'raw_from_selex_sheep_id',      // Инкремент
        'NANIMAL',                      // Жживотное - уникальный идентификатор
        'NANIMAL_TIME',                 // Животное - уникальный идентификатор (наверное...)
        'NINVLEFT',                     // Животное - инвентарный номер, левое ухо
        'NINVRIGHT',                    // Животное - инвентарный номер, правое ухо
        'NGOSREGISTER',                 // Животное - идентификационный номер РСХН
        'NINV3',                        // Животное - электронная метка
        'TATY',                         // Животное - тату
        'ANIMAL_VID',                   // Животное - вид животного
        'ANIMAL_VID_COD',               // Животное - код вида животного (КРС - 26 / Овцы - 17)
        'KLICHKA',                      // Животное - кличка
        'POL',                          // Животное - пол
        'NPOL',                         // Животное - код пола
        'POR',                          // Животное - порода
        'NPOR',                         // Животное - код породы
        'OSN_OKRAS',                    // Животное - окрас
        'DATE_ROGD',                    // Животное - дата рождения
        'DATE_POSTUPLN',                // Животное - дата поступления
        'NHOZ_ROGD',                    // Животное - хозяйство рождения (базовый индекс хозяйства)
        'NHOZ',                         // Животное - базовый индекс хозяйства (текущее хозяйство)
        'NOBL',                         // Животное - внутренний код области хозяйства (текущее хозяйство)
        'NRN',                          // Животное - внутренний код района хозяйства (текущее хозяйство)
        'NIDENT',                       // Животное - импортный идентификатор
        'NSODERGANIE',                  // Животное - тип содержания (система содержания)
        'SODERGANIE_IM',                // Животное - название типа содержания (система содержания)
        'DATE_V',                       // Животное - дата выбытия
        'PV',                           // Животное - причина выбытия
        'RASHOD',                       // Животное - расход
        'GM_V',                         // Животное - живая масса при выбытии (кг)
        'ISP',                          // Животное - использование (племенная ценность)
        'DATE_CHIP',                    // Животное - дата электронного мечения
        'DATE_NINVRIGHT',               // Животное - дата мечения (инв. №, правое ухо)
        'DATE_NINVLEFT',                // Животное - дата мечения (инв. №, левое ухо)
        'DATE_NGOSREGISTER',            // Животное - дата мечения (№ РСХН)
        'NINVRIGHT_OTCA',               // отец - инвентарный номер, правое ухо
        'NINVLEFT_OTCA',                // отец - инвентарный номер, левое ухо
        'NGOSREGISTER_OTCA',            // отец - идентификационный номер РСХН
        'NINVRIGHT_MATERI',             // мать - инвентарный номер, правое ухо
        'NINVLEFT_MATERI',              // мать - инвентарный номер, левое ухо
        'NGOSREGISTER_MATERI',          // мать - идентификационный номер РСХН
        'IMPORT_STATUS',                // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)'
        'TASK',                         // Код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы)
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
        'raw_from_selex_sheep_id'
    ];


    /**
     * Массив системных скрытых полей
     * @var array
     */
    protected $hidden                           = [];


    /**
     * Валидация входных данных
     * Проверка не прерывается на первой ошибке.
     *
     * @param Request $request
     */
    public function rulesReturnWithBag(Request $request): void
{
    // получаем поля со значениями
    $data = $request->all();

    // получаем значение первичного ключа
    $id = (isset($data[$this->primaryKey])) ? $data[$this->primaryKey] : null;

    // Объединяем все правила в один массив
    $rules = [

    ];

    // Объединяем все сообщения об ошибках в один массив
    $messages = [

    ];

        $validated = $request->validateWithBag('default', $rules, $messages);
    }



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
        'NANIMAL'             => 'integer|nullable',
        'NANIMAL_TIME'        => 'max:128|nullable',
        'NINVLEFT'            => 'max:20|nullable',
        'NINVRIGHT'           => 'max:20|nullable',
        'NGOSREGISTER'        => 'max:50|nullable',
        'NINV3'               => 'max:20|nullable',
        'TATY'                => 'max:12|nullable',
        'ANIMAL_VID'          => 'max:50|nullable',
        'ANIMAL_VID_COD'      => 'required|integer',
        'KLICHKA'             => 'max:50|nullable',
        'POL'                 => 'max:30|nullable',
        'NPOL'                => 'integer|nullable',
        'POR'                 => 'max:30|nullable',
        'NPOR'                => 'integer|nullable',
        'OSN_OKRAS'           => 'max:30|nullable',
        'DATE_ROGD'           => 'date|nullable',
        'DATE_POSTUPLN'       => 'date|nullable',
        'NHOZ_ROGD'           => 'integer|nullable',
        'NHOZ'                => 'integer|nullable',
        'NOBL'                => 'integer|nullable',
        'NRN'                 => 'integer|nullable',
        'NIDENT'              => 'max:30|nullable',
        'NSODERGANIE'         => 'integer|nullable',
        'SODERGANIE_IM'       => 'max:40|nullable',
        'DATE_V'              => 'date|nullable',
        'PV'                  => 'max:60|nullable',
        'RASHOD'              => 'max:30|nullable',
        'GM_V'                => 'integer|nullable',
        'ISP'                 => 'max:20|nullable',
        'DATE_CHIP'           => 'date|nullable',
        'DATE_NINVRIGHT'      => 'date|nullable',
        'DATE_NINVLEFT'       => 'date|nullable',
        'DATE_NGOSREGISTER'   => 'date|nullable',
        'NINVRIGHT_OTCA'      => 'max:15|nullable',
        'NINVLEFT_OTCA'       => 'max:15|nullable',
        'NGOSREGISTER_OTCA'   => 'max:50|nullable',
        'NINVRIGHT_MATERI'    => 'max:15|nullable',
        'NINVLEFT_MATERI'     => 'max:15|nullable',
        'NGOSREGISTER_MATERI' => 'max:50|nullable',
        'IMPORT_STATUS'       => ['required',
                                 Rule::enum(ImportStatusEnum::class)],
        'TASK'                => 'integer|nullable',
        'GUID_SVR'            => 'max:64|nullable',
        'ANIMALS_JSON'        => 'json|nullable',
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
        'NANIMAL.integer'       => trans('svr-core-lang::validation.integer'),
        'NANIMAL_TIME'          => trans('svr-core-lang::validation'),
        'NINVLEFT'              => trans('svr-core-lang::validation'),
        'NINVRIGHT'             => trans('svr-core-lang::validation'),
        'NGOSREGISTER'          => trans('svr-core-lang::validation'),
        'NINV3'                 => trans('svr-core-lang::validation'),
        'TATY'                  => trans('svr-core-lang::validation'),
        'ANIMAL_VID'            => trans('svr-core-lang::validation'),
        'ANIMAL_VID_COD'        => trans('svr-core-lang::validation'),
        'KLICHKA'               => trans('svr-core-lang::validation'),
        'POL'                   => trans('svr-core-lang::validation'),
        'NPOL'                  => trans('svr-core-lang::validation'),
        'POR'                   => trans('svr-core-lang::validation'),
        'NPOR'                  => trans('svr-core-lang::validation'),
        'OSN_OKRAS'             => trans('svr-core-lang::validation'),
        'DATE_ROGD'             => trans('svr-core-lang::validation'),
        'DATE_POSTUPLN'         => trans('svr-core-lang::validation'),
        'NHOZ_ROGD'             => trans('svr-core-lang::validation'),
        'NHOZ'                  => trans('svr-core-lang::validation'),
        'NOBL'                  => trans('svr-core-lang::validation'),
        'NRN'                   => trans('svr-core-lang::validation'),
        'NIDENT'                => trans('svr-core-lang::validation'),
        'NSODERGANIE'           => trans('svr-core-lang::validation'),
        'SODERGANIE_IM'         => trans('svr-core-lang::validation'),
        'DATE_V'                => trans('svr-core-lang::validation'),
        'PV'                    => trans('svr-core-lang::validation'),
        'RASHOD'                => trans('svr-core-lang::validation'),
        'GM_V'                  => trans('svr-core-lang::validation'),
        'ISP'                   => trans('svr-core-lang::validation'),
        'DATE_CHIP'             => trans('svr-core-lang::validation'),
        'DATE_NINVRIGHT'        => trans('svr-core-lang::validation'),
        'DATE_NINVLEFT'         => trans('svr-core-lang::validation'),
        'DATE_NGOSREGISTER'     => trans('svr-core-lang::validation'),
        'NINVRIGHT_OTCA'        => trans('svr-core-lang::validation'),
        'NINVLEFT_OTCA'         => trans('svr-core-lang::validation'),
        'NGOSREGISTER_OTCA'     => trans('svr-core-lang::validation'),
        'NINVRIGHT_MATERI'      => trans('svr-core-lang::validation'),
        'NINVLEFT_MATERI'       => trans('svr-core-lang::validation'),
        'NGOSREGISTER_MATERI'   => trans('svr-core-lang::validation'),
        'IMPORT_STATUS'         => trans('svr-core-lang::validation'),
        'TASK'                  => trans('svr-core-lang::validation'),
        'GUID_SVR'              => trans('svr-core-lang::validation'),
        'ANIMALS_JSON'          => trans('svr-core-lang::validation'),
        ];
    }
}
