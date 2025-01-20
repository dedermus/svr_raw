<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Core\Traits\GetTableName;
use Svr\Core\Traits\GetValidationRules;

/**
 * Модель: сырые данные из Селекс для овец
 *
 * @package App\Models\Raw
 */
class FromSelexSheep extends BaseModel
{
    use GetTableName;
    use HasFactory;
    use GetValidationRules;

    /**
     * Точное название таблицы с учетом схемы
     * @var string
     */
    protected $table = 'raw.raw_from_selex_sheep';


    /**
     * Первичный ключ таблицы (автоинкремент)
     * @var string
     */
    protected $primaryKey = 'raw_from_selex_sheep_id';


    /**
     * Поле даты создания строки
     * @var string
     */
    const CREATED_AT = 'created_at';


    /**
     * Поле даты обновления строки
     * @var string
     */
    const UPDATED_AT = 'updated_at';


    /**
     * Значения полей по умолчанию
     * @var array
     */
    protected $attributes = [];


    protected array $dates = [
        'created_at',
        'updated_at',
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
        'nanimal',                      // Жживотное - уникальный идентификатор
        'nanimal_time',                 // Животное - уникальный идентификатор (наверное...)
        'ninvleft',                     // Животное - инвентарный номер, левое ухо
        'ninvright',                    // Животное - инвентарный номер, правое ухо
        'ngosregister',                 // Животное - идентификационный номер РСХН
        'ninv3',                        // Животное - электронная метка
        'taty',                         // Животное - тату
        'animal_vid',                   // Животное - вид животного
        'animal_vid_cod',               // Животное - код вида животного (КРС - 26 / Овцы - 17)
        'klichka',                      // Животное - кличка
        'pol',                          // Животное - пол
        'npol',                         // Животное - код пола
        'por',                          // Животное - порода
        'npor',                         // Животное - код породы
        'osn_okras',                    // Животное - окрас
        'date_rogd',                    // Животное - дата рождения
        'date_postupln',                // Животное - дата поступления
        'nhoz_rogd',                    // Животное - хозяйство рождения (базовый индекс хозяйства)
        'nhoz',                         // Животное - базовый индекс хозяйства (текущее хозяйство)
        'nobl',                         // Животное - внутренний код области хозяйства (текущее хозяйство)
        'nrn',                          // Животное - внутренний код района хозяйства (текущее хозяйство)
        'nident',                       // Животное - импортный идентификатор
        'nsoderganie',                  // Животное - тип содержания (система содержания)
        'soderganie_im',                // Животное - название типа содержания (система содержания)
        'date_v',                       // Животное - дата выбытия
        'pv',                           // Животное - причина выбытия
        'rashod',                       // Животное - расход
        'gm_v',                         // Животное - живая масса при выбытии (кг)
        'isp',                          // Животное - использование (племенная ценность)
        'date_chip',                    // Животное - дата электронного мечения
        'date_ninvright',               // Животное - дата мечения (инв. №, правое ухо)
        'date_ninvleft',                // Животное - дата мечения (инв. №, левое ухо)
        'date_ngosregister',            // Животное - дата мечения (№ РСХН)
        'ninvright_otca',               // отец - инвентарный номер, правое ухо
        'ninvleft_otca',                // отец - инвентарный номер, левое ухо
        'ngosregister_otca',            // отец - идентификационный номер РСХН
        'ninvright_materi',             // мать - инвентарный номер, правое ухо
        'ninvleft_materi',              // мать - инвентарный номер, левое ухо
        'ngosregister_materi',          // мать - идентификационный номер РСХН
        'import_status',                // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)'
        'task',                         // Код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы)
        'guid_svr',                     // Гуид животного, который генерирует СВР в момент создания этой записи
        'animals_json',                 // Сырые данные из Селекс
        'created_at',                   // Дата создания записи
        'updated_at',                   // Дата удаления записи
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
            'nanimal'             => 'integer|nullable',
            'nanimal_time'        => 'max:128|nullable',
            'ninvleft'            => 'max:20|nullable',
            'ninvright'           => 'max:20|nullable',
            'ngosregister'        => 'max:50|nullable',
            'ninv3'               => 'max:20|nullable',
            'taty'                => 'max:12|nullable',
            'animal_vid'          => 'max:50|nullable',
            'animal_vid_cod'      => 'required|integer',
            'klichka'             => 'max:50|nullable',
            'pol'                 => 'max:30|nullable',
            'npol'                => 'integer|nullable',
            'por'                 => 'max:30|nullable',
            'npor'                => 'integer|nullable',
            'osn_okras'           => 'max:30|nullable',
            'date_rogd'           => 'date|nullable',
            'date_postupln'       => 'date|nullable',
            'nhoz_rogd'           => 'integer|nullable',
            'nhoz'                => 'integer|nullable',
            'nobl'                => 'integer|nullable',
            'nrn'                 => 'integer|nullable',
            'nident'              => 'max:30|nullable',
            'nsoderganie'         => 'integer|nullable',
            'soderganie_im'       => 'max:40|nullable',
            'date_v'              => 'date|nullable',
            'pv'                  => 'max:60|nullable',
            'rashod'              => 'max:30|nullable',
            'gm_v'                => 'integer|nullable',
            'isp'                 => 'max:20|nullable',
            'date_chip'           => 'date|nullable',
            'date_ninvright'      => 'date|nullable',
            'date_ninvleft'       => 'date|nullable',
            'date_ngosregister'   => 'date|nullable',
            'ninvright_otca'      => 'max:15|nullable',
            'ninvleft_otca'       => 'max:15|nullable',
            'ngosregister_otca'   => 'max:50|nullable',
            'ninvright_materi'    => 'max:15|nullable',
            'ninvleft_materi'     => 'max:15|nullable',
            'ngosregister_materi' => 'max:50|nullable',
            'import_status'       => ['nullable',        Rule::enum(ImportStatusEnum::class)],
            'task'                => 'integer|nullable',
            'guid_svr'            => 'max:64|nullable',
            'animals_json'        => 'json|nullable',
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
            'nanimal'             => trans('svr-core-lang::validation'),
            'nanimal_time'        => trans('svr-core-lang::validation'),
            'ninvleft'            => trans('svr-core-lang::validation'),
            'ninvright'           => trans('svr-core-lang::validation'),
            'ngosregister'        => trans('svr-core-lang::validation'),
            'ninv3'               => trans('svr-core-lang::validation'),
            'taty'                => trans('svr-core-lang::validation'),
            'animal_vid'          => trans('svr-core-lang::validation'),
            'animal_vid_cod'      => trans('svr-core-lang::validation'),
            'klichka'             => trans('svr-core-lang::validation'),
            'pol'                 => trans('svr-core-lang::validation'),
            'npol'                => trans('svr-core-lang::validation'),
            'por'                 => trans('svr-core-lang::validation'),
            'npor'                => trans('svr-core-lang::validation'),
            'osn_okras'           => trans('svr-core-lang::validation'),
            'date_rogd'           => trans('svr-core-lang::validation'),
            'date_postupln'       => trans('svr-core-lang::validation'),
            'nhoz_rogd'           => trans('svr-core-lang::validation'),
            'nhoz'                => trans('svr-core-lang::validation'),
            'nobl'                => trans('svr-core-lang::validation'),
            'nrn'                 => trans('svr-core-lang::validation'),
            'nident'              => trans('svr-core-lang::validation'),
            'nsoderganie'         => trans('svr-core-lang::validation'),
            'soderganie_im'       => trans('svr-core-lang::validation'),
            'date_v'              => trans('svr-core-lang::validation'),
            'pv'                  => trans('svr-core-lang::validation'),
            'rashod'              => trans('svr-core-lang::validation'),
            'gm_v'                => trans('svr-core-lang::validation'),
            'isp'                 => trans('svr-core-lang::validation'),
            'date_chip'           => trans('svr-core-lang::validation'),
            'date_ninvright'      => trans('svr-core-lang::validation'),
            'date_ninvleft'       => trans('svr-core-lang::validation'),
            'date_ngosregister'   => trans('svr-core-lang::validation'),
            'ninvright_otca'      => trans('svr-core-lang::validation'),
            'ninvleft_otca'       => trans('svr-core-lang::validation'),
            'ngosregister_otca'   => trans('svr-core-lang::validation'),
            'ninvright_materi'    => trans('svr-core-lang::validation'),
            'ninvleft_materi'     => trans('svr-core-lang::validation'),
            'ngosregister_materi' => trans('svr-core-lang::validation'),
            'import_status'       => trans('svr-core-lang::validation'),
            'task'                => trans('svr-core-lang::validation'),
            'guid_svr'            => trans('svr-core-lang::validation'),
            'animals_json'        => trans('svr-core-lang::validation'),
        ];
    }
}
