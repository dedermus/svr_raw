<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    /**
     * Поля, которые можно менять сразу массивом
     * @var array
     */
    protected $fillable                     = [
        'raw_from_selex_milk_id',           // инкремент
        'NANIMAL',                          // животное - НЕ уникальный идентификатор
        'NANIMAL_TIME',                     // животное - уникальный идентификатор (наверное...)
        'NINV',                             // животное - инвентарный номер
        'KLICHKA',                          // животное - кличка
        'POL',                              // животное - пол
        'NPOL',                             // животное - код пола
        'NGOSREGISTER',                     // животное - идентификационный номер РСХН
        'NINV1',                            // животное - номер в оборудовании
        'NINV3',                            // животное - электронная метка
        'ANIMAL_VID',                       // животное - вид животного
        'ANIMAL_VID_COD',                   // животное - код вида животного (КРС - 26 / Овцы - 17)
        'MAST',                             // животное - масть
        'NMAST',                            // животное - код масти
        'POR',                              // животное - порода
        'NPOR',                             // животное - код породы
        'DATE_ROGD',                        // животное - дата рождения в формате YYYY.mm.dd
        'DATE_POSTUPLN',                    // животное - дата поступления в формате YYYY.mm.dd
        'NHOZ_ROGD',                        // животное - хозяйство рождения (базовый индекс хозяйства)
        'NHOZ',                             // животное - базовый индекс хозяйства (текущее хозяйство)
        'NOBL',                             // животное - внутренний код области хозяйства (текущее хозяйство)
        'NRN',                              // животное - внутренний код района хозяйства (текущее хозяйство)
        'NIDENT',                           // животное - импортный идентификатор
        'ROGD_HOZ',                         // животное - хозяйство рождения (название)
        'DATE_V',                           // животное - дата выбытия в формате YYYY.mm.dd
        'PV',                               // животное - причина выбытия
        'RASHOD',                           // животное - расход
        'GM_V',                             // животное - живая масса при выбытии (кг)
        'ISP',                              // животное - использование (племенная ценность)
        'DATE_CHIP',                        // животное - дата электронного мечения в формате YYYY.mm.dd
        'DATE_NINV',                        // животное - дата мечения (инв. №) в формате YYYY.mm.dd
        'DATE_NGOSREGISTER',                // животное - дата мечения (№ РСХН) в формате YYYY.mm.dd
        'NINV_OTCA',                        // отец - инвентарный номер
        'NGOSREGISTER_OTCA',                // отец - идентификационный номер РСХН
        'POR_OTCA',                         // отец - порода
        'NPOR_OTCA',                        // отец - код породы
        'DATE_ROGD_OTCA',                   // отец - дата рождения в формате YYYY.mm.dd
        'NINV_MATERI',                      // мать - инвентарный номер
        'NGOSREGISTER_MATERI',              // мать - идентификационный номер РСХН
        'POR_MATERI',                       // мать - порода
        'NPOR_MATERI',                      // мать - код породы
        'DATE_ROGD_MATERI',                 // мать - дата рождения в формате YYYY.mm.dd
        'IMPORT_STATUS',                    // ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
        'TASK',                             // код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы
        'GUID_SVR',                         // гуид животного, который генерирует СВР в момент создания этой записи
        'ANIMALS_JSON',                     // сырые данные из Селекс
        'created_at',                       // дата создания записи
        'update_at',                        // дата создания/редактирования записи
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

}
