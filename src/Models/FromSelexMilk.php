<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Core\Traits\GetTableName;
use Svr\Core\Traits\GetValidationRules;

/**
 * Модель: сырые данные из Селекс для молочных коров
 *
 * @package App\Models\Raw
 */
class FromSelexMilk extends BaseModel
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
     * @param Request|array $request
     *
     * @return void
     */
    public function createRaw(Request|array $request): void
    {
        if (!$request instanceof Request) {
            $request = Request::create(
                uri: '/',
                method: 'post'
            )->replace($request);
        }
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
        'nanimal',                      // животное - НЕ уникальный идентификатор
        'nanimal_time',                 // животное - уникальный идентификатор (наверное...)
        'ninv',                         // животное - инвентарный номер
        'klichka',                      // животное - кличка
        'pol',                          // животное - пол
        'npol',                         // животное - код пола
        'ngosregister',                 // животное - идентификационный номер РСХН
        'ninv1',                        // животное - номер в оборудовании
        'ninv3',                        // животное - электронная метка
        'animal_vid',                   // животное - вид животного
        'animal_vid_cod',               // животное - код вида животного (КРС - 26 / Овцы - 17)
        'mast',                         // животное - масть
        'nmast',                        // животное - код масти
        'por',                          // животное - порода
        'npor',                         // животное - код породы
        'date_rogd',                    // животное - дата рождения в формате YYYY.mm.dd
        'date_postupln',                // животное - дата поступления в формате YYYY.mm.dd
        'nhoz_rogd',                    // животное - хозяйство рождения (базовый индекс хозяйства)
        'nhoz',                         // животное - базовый индекс хозяйства (текущее хозяйство)
        'nobl',                         // животное - внутренний код области хозяйства (текущее хозяйство)
        'nrn',                          // животное - внутренний код района хозяйства (текущее хозяйство)
        'nident',                       // животное - импортный идентификатор
        'rogd_hoz',                     // животное - хозяйство рождения (название)
        'date_v',                       // животное - дата выбытия в формате YYYY.mm.dd
        'pv',                           // животное - причина выбытия
        'rashod',                       // животное - расход
        'gm_v',                         // животное - живая масса при выбытии (кг)
        'isp',                          // животное - использование (племенная ценность)
        'date_chip',                    // животное - дата электронного мечения в формате YYYY.mm.dd
        'date_ninv',                    // животное - дата мечения (инв. №) в формате YYYY.mm.dd
        'date_ngosregister',            // животное - дата мечения (№ РСХН) в формате YYYY.mm.dd
        'ninv_otca',                    // отец - инвентарный номер
        'ngosregister_otca',            // отец - идентификационный номер РСХН
        'por_otca',                     // отец - порода
        'npor_otca',                    // отец - код породы
        'date_rogd_otca',               // отец - дата рождения в формате YYYY.mm.dd
        'ninv_materi',                  // мать - инвентарный номер
        'ngosregister_materi',          // мать - идентификационный номер РСХН
        'por_materi',                   // мать - порода
        'npor_materi',                  // мать - код породы
        'date_rogd_materi',             // мать - дата рождения в формате YYYY.mm.dd
        'import_status',                // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
        'task',                         // код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы
        'guid_svr',                     // гуид животного, который генерирует СВР в момент создания этой записи
        'animals_json',                 // сырые данные из Селекс
        'created_at',                   // дата создания записи
        'updated_at',                   // дата создания/редактирования записи
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
        return [
            $this->primaryKey => [
                $request->isMethod('put') ? 'required' : '',
                Rule::exists('.' . $this->getTable(), $this->primaryKey),
            ],
            'nanimal'             => 'integer|nullable',
            'nanimal_time'        => 'max:128|nullable',
            'ninv'                => 'max:15|nullable',
            'klichka'             => 'max:50|nullable',
            'pol'                 => 'max:30|nullable',
            'npol'                => 'integer|nullable',
            'ngosregister'        => 'max:50|nullable',
            'ninv1'               => 'max:15|nullable',
            'ninv3'               => 'max:20|nullable',
            'animal_vid'          => 'max:50|nullable',
            'animal_vid_cod'      => 'required|integer',
            'mast'                => 'max:30|nullable',
            'nmast'               => 'integer|nullable',
            'por'                 => 'max:30|nullable',
            'npor'                => 'integer|nullable',
            'date_rogd'           => 'date|nullable',
            'date_postupln'       => 'date|nullable',
            'nhoz_rogd'           => 'integer|nullable',
            'nhoz'                => 'integer|nullable',
            'nobl'                => 'integer|nullable',
            'nrn'                 => 'integer|nullable',
            'nident'              => 'max:20|nullable',
            'rogd_hoz'            => 'max:50|nullable',
            'date_v'              => 'date|nullable',
            'pv'                  => 'max:60|nullable',
            'rashod'              => 'max:30|nullable',
            'gm_v'                => 'integer|nullable',
            'isp'                 => 'max:20|nullable',
            'date_chip'           => 'date|nullable',
            'date_ninv'           => 'date|nullable',
            'date_ngosregister'   => 'date|nullable',
            'ninv_otca'           => 'max:15|nullable',
            'ngosregister_otca'   => 'max:50|nullable',
            'por_otca'            => 'max:30|nullable',
            'npor_otca'           => 'integer|nullable',
            'date_rogd_otca'      => 'date|nullable',
            'ninv_materi'         => 'max:15|nullable',
            'ngosregister_materi' => 'max:50|nullable',
            'por_materi'          => 'max:30|nullable',
            'npor_materi'         => 'integer|nullable',
            'date_rogd_materi'    => 'date|nullable',
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
            'ninv'                => trans('svr-core-lang::validation'),
            'klichka'             => trans('svr-core-lang::validation'),
            'pol'                 => trans('svr-core-lang::validation'),
            'npol'                => trans('svr-core-lang::validation'),
            'ngosregister'        => trans('svr-core-lang::validation'),
            'ninv1'               => trans('svr-core-lang::validation'),
            'ninv3'               => trans('svr-core-lang::validation'),
            'animal_vid'          => trans('svr-core-lang::validation'),
            'animal_vid_cod'      => trans('svr-core-lang::validation'),
            'mast'                => trans('svr-core-lang::validation'),
            'nmast'               => trans('svr-core-lang::validation'),
            'por'                 => trans('svr-core-lang::validation'),
            'npor'                => trans('svr-core-lang::validation'),
            'date_rogd'           => trans('svr-core-lang::validation'),
            'date_postupln'       => trans('svr-core-lang::validation'),
            'nhoz_rogd'           => trans('svr-core-lang::validation'),
            'nhoz'                => trans('svr-core-lang::validation'),
            'nobl'                => trans('svr-core-lang::validation'),
            'nrn'                 => trans('svr-core-lang::validation'),
            'nident'              => trans('svr-core-lang::validation'),
            'rogd_hoz'            => trans('svr-core-lang::validation'),
            'date_v'              => trans('svr-core-lang::validation'),
            'pv'                  => trans('svr-core-lang::validation'),
            'rashod'              => trans('svr-core-lang::validation'),
            'gm_v'                => trans('svr-core-lang::validation'),
            'isp'                 => trans('svr-core-lang::validation'),
            'date_chip'           => trans('svr-core-lang::validation'),
            'date_ninv'           => trans('svr-core-lang::validation'),
            'date_ngosregister'   => trans('svr-core-lang::validation'),
            'ninv_otca'           => trans('svr-core-lang::validation'),
            'ngosregister_otca'   => trans('svr-core-lang::validation'),
            'por_otca'            => trans('svr-core-lang::validation'),
            'npor_otca'           => trans('svr-core-lang::validation'),
            'date_rogd_otca'      => trans('svr-core-lang::validation'),
            'ninv_materi'         => trans('svr-core-lang::validation'),
            'ngosregister_materi' => trans('svr-core-lang::validation'),
            'por_materi'          => trans('svr-core-lang::validation'),
            'npor_materi'         => trans('svr-core-lang::validation'),
            'date_rogd_materi'    => trans('svr-core-lang::validation'),
            'import_status'       => trans('svr-core-lang::validation'),
            'task'                => trans('svr-core-lang::validation'),
            'guid_svr'            => trans('svr-core-lang::validation'),
            'animals_json'        => trans('svr-core-lang::validation'),
        ];
    }
}
