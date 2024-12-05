<?php

namespace Svr\Raw\Models;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Svr\Core\Enums\ImportStatusEnum;

/**
 * Модель: сырые данные из Селекс для мясных коров
 *
 * @package App\Models\Raw
 */
class FromSelexBeef extends BaseModel
{

    /**
     * Точное название таблицы с учетом схемы
     * @var string
     */
    protected $table = 'raw.raw_from_selex_beef';


    /**
     * Первичный ключ таблицы (автоинкремент)
     * @var string
     */
    protected $primaryKey = 'raw_from_selex_beef_id';


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
        'nanimal',                      // Животное - уникальный идентификатор
        'nanimal_time',                 // Животное - уникальный идентификатор (наверное...)
        'ninv',                         // Животное - инвентарный номер
        'klichka',                      // Животное - кличка
        'pol',                          // Животное - пол
        'npol',                         // Животное - код пола
        'ngosregister',                 // Животное - идентификационный номер РСХН
        'ninv1',                        // Животное - номер в оборудовании
        'ninv3',                        // Животное - электронная метка
        'animal_vid',                   // Животное - вид животного
        'animal_vid_cod',               // Животное - код вида животного (КРС - 26 / Овцы - 17
        'mast',                         // Животное - масть
        'nmast',                        // Животное - код масти
        'por',                          // Животное - порода
        'npor',                         // Животное - код породы
        'date_rogd',                    // Животное - дата рождения в формате YYYY.mm.dd
        'date_postupln',                // Животное - дата поступления в формате YYYY.mm.dd
        'nhoz_rogd',                    // Животное - хозяйство рождения (базовый индекс хозяйства)
        'nhoz',                         // Животное - базовый индекс хозяйства (текущее хозяйство)
        'nobl',                         // Животное - внутренний код области хозяйства (текущее хозяйство)
        'nrn',                          // Животное - внутренний код района хозяйства (текущее хозяйство)
        'nident',                       // Животное - импортный идентификатор
        'rogd_hoz',                     // Животное - хозяйство рождения (название)
        'date_v',                       // Животное - дата выбытия
        'pv',                           // Животное - причина выбытия
        'rashod',                       // Животное - расход
        'gm_v',                         // Животное - живая масса при выбытии (кг)
        'isp',                          // Животное - использование (племенная ценность)
        'date_chip',                    // Животное - дата электронного мечения
        'date_ninv',                    // Животное - дата мечения (инв. №)
        'date_ngosregister',            // Животное - дата мечения (№ РСХН)
        'ninv_otca',                    // Оотец - инвентарный номер
        'ngosregister_otca',            // Оотец - идентификационный номер РСХН
        'por_otca',                     // Оотец - порода
        'npor_otca',                    // Отец - код породы
        'date_rogd_otca',               // Отец - дата рождения
        'ninv_materi',                  // Мать - инвентарный номер
        'ngosregister_materi',          // Мать - идентификационный номер РСХН
        'por_materi',                   // Мать - порода
        'npor_materi',                  // Мать - код породы
        'date_rogd_materi',             // Мать - дата рождения
        'import_status',                // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
        'task',                         // Код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы
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
        'raw_from_selex_beef_id'
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
                        'nanimal.integer'     => trans('svr-core-lang::validation.integer'),
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
