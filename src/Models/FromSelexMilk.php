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
     * @param $request
     *
     * @return void
     */
    public function createRaw(Application|Request $request): void
    {
        $this->rulesReturnWithBag($request);
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
     * Валидация входных данных
     *
     * @param $request
     *
     * @return void
     */
    private function rules(Application|Request $request): void
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


    /**
     * Валидация входных данных
     * Проверка не прерывается на первой ошибке.
     *
     * @param $request
     *
     */
    private function rulesReturnWithBag(Application|Request $request)
    {
        // получаем поля со значениями
        $data = $request->all();

        // получаем значение первичного ключа
        $id = (isset($data[$this->primaryKey])) ? $data[$this->primaryKey] : null;

        // Объединяем все правила в один массив
        $rules = [
            $this->primaryKey => 'required|exists:.' . $this->getTable() . ',' . $this->primaryKey,
            'NANIMAL'             => 'integer|nullable',
            'NANIMAL_TIME'        => 'max:128|nullable',
            'NINV'                => 'max:15|nullable',
            'KLICHKA'             => 'max:50|nullable',
            'POL'                 => 'max:30|nullable',
            'NPOL'                => 'integer|nullable',
            'NGOSREGISTER'        => 'max:50|nullable',
            'NINV1'               => 'max:15|nullable',
            'NINV3'               => 'max:20|nullable',
            'ANIMAL_VID'          => 'max:50|nullable',
            'ANIMAL_VID_COD'      => 'required|integer',
            'MAST'                => 'max:30|nullable',
            'NMAST'               => 'integer|nullable',
            'POR'                 => 'max:30|nullable',
            'NPOR'                => 'integer|nullable',
            'DATE_ROGD'           => 'date|nullable',
            'DATE_POSTUPLN'       => 'date|nullable',
            'NHOZ_ROGD'           => 'integer|nullable',
            'NHOZ'                => 'integer|nullable',
            'NOBL'                => 'integer|nullable',
            'NRN'                 => 'integer|nullable',
            'NIDENT'              => 'max:20|nullable',
            'ROGD_HOZ'            => 'max:50|nullable',
            'DATE_V'              => 'date|nullable',
            'PV'                  => 'max:60|nullable',
            'RASHOD'              => 'max:30|nullable',
            'GM_V'                => 'integer|nullable',
            'ISP'                 => 'max:20|nullable',
            'DATE_CHIP'           => 'date|nullable',
            'DATE_NINV'           => 'date|nullable',
            'DATE_NGOSREGISTER'   => 'date|nullable',
            'NINV_OTCA'           => 'max:15|nullable',
            'NGOSREGISTER_OTCA'   => 'max:50|nullable',
            'POR_OTCA'            => 'max:30|nullable',
            'NPOR_OTCA'           => 'integer|nullable',
            'DATE_ROGD_OTCA'      => 'date|nullable',
            'NINV_MATERI'         => 'max:15|nullable',
            'NGOSREGISTER_MATERI' => 'max:50|nullable',
            'POR_MATERI'          => 'max:30|nullable',
            'NPOR_MATERI'         => 'integer|nullable',
            'DATE_ROGD_MATERI'    => 'date|nullable',
            'IMPORT_STATUS'       => 'required',
            'TASK'                => 'integer|nullable',
            'GUID_SVR'            => 'max:64|nullable',
            'ANIMALS_JSON'        => 'json|nullable',
        ];

        // Объединяем все сообщения об ошибках в один массив
        $messages = [
             $this->primaryKey         .  '.required'   => trans('svr-core-lang::validation.required'),
            $this->primaryKey         .  '.exists'      => trans('svr-core-lang::validation.exists'),
            'NANIMAL.integer'         => trans('svr-core-lang::validation.integer'),
            'NANIMAL_TIME.max'        => trans('svr-core-lang::validation.max'),
            'NINV.max'                => trans('svr-core-lang::validation.max'),
            'KLICHKA.max'             => trans('svr-core-lang::validation.max'),
            'POL.max'                 => trans('svr-core-lang::validation.max'),
            'NPOL.integer'            => trans('svr-core-lang::validation.integer'),
            'NGOSREGISTER.max'        => trans('svr-core-lang::validation.max'),
            'NINV1.max'               => trans('svr-core-lang::validation.max'),
            'NINV3.max'               => trans('svr-core-lang::validation.max'),
            'ANIMAL_VID.max'          => trans('svr-core-lang::validation.max'),
            'ANIMAL_VID_COD.required' => trans('svr-core-lang::validation.required'),
            'ANIMAL_VID_COD.integer'  => trans('svr-core-lang::validation.integer'),
            'MAST.max'                => trans('svr-core-lang::validation.max'),
            'NMAST.integer'           => trans('svr-core-lang::validation.integer'),
            'POR.max'                 => trans('svr-core-lang::validation.max'),
            'NPOR.integer'            => trans('svr-core-lang::validation.integer'),
            'DATE_ROGD.date'          => trans('svr-core-lang::validation.date'),
            'DATE_POSTUPLN.date'      => trans('svr-core-lang::validation.date'),
            'NHOZ_ROGD.integer'       => trans('svr-core-lang::validation.integer'),
            'NHOZ.integer'            => trans('svr-core-lang::validation.integer'),
            'NOBL.integer'            => trans('svr-core-lang::validation.integer'),
            'NRN.integer'             => trans('svr-core-lang::validation.integer'),
            'NIDENT.max'              => trans('svr-core-lang::validation.max'),
            'ROGD_HOZ.max'            => trans('svr-core-lang::validation.max'),
            'DATE_V.date'             => trans('svr-core-lang::validation.date'),
            'PV.max'                  => trans('svr-core-lang::validation.max'),
            'RASHOD.max'              => trans('svr-core-lang::validation.max'),
            'GM_V.integer'            => trans('svr-core-lang::validation.integer'),
            'ISP.max'                 => trans('svr-core-lang::validation.max'),
            'DATE_CHIP.date'          => trans('svr-core-lang::validation.date'),
            'DATE_NINV.date'          => trans('svr-core-lang::validation.date'),
            'DATE_NGOSREGISTER.date'  => trans('svr-core-lang::validation.date'),
            'NINV_OTCA.max'           => trans('svr-core-lang::validation.max'),
            'NGOSREGISTER_OTCA.max'   => trans('svr-core-lang::validation.max'),
            'POR_OTCA.max'            => trans('svr-core-lang::validation.max'),
            'NPOR_OTCA.integer'       => trans('svr-core-lang::validation.integer'),
            'DATE_ROGD_OTCA.date'     => trans('svr-core-lang::validation.date'),
            'NINV_MATERI.max'         => trans('svr-core-lang::validation.max'),
            'NGOSREGISTER_MATERI.max' => trans('svr-core-lang::validation.max'),
            'POR_MATERI.max'          => trans('svr-core-lang::validation.max'),
            'NPOR_MATERI.integer'     => trans('svr-core-lang::validation.integer'),
            'DATE_ROGD_MATERI.date'   => trans('svr-core-lang::validation.date'),
            'IMPORT_STATUS.required'  => trans('svr-core-lang::validation.required'),
            'TASK.integer'            => trans('svr-core-lang::validation.integer'),
            'GUID_SVR.max'            => trans('svr-core-lang::validation.max'),
            'ANIMALS_JSON.json'       => trans('svr-core-lang::validation.json'),
        ];

        try {
            // Используем validateWithBag для получения всех ошибок
            $validated = $request->validateWithBag('default', $rules, $messages);
        } catch (ValidationException $e) {
            // Перенаправляем обратно с ошибками
            return redirect()->back()
                ->withErrors($e->validator, 'default')
                ->withInput();
        }
    }
}
