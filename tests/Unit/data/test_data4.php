<?php

use Svr\Core\Enums\ImportStatusEnum;

return [
    [
        //животное - уникальный идентификатор
        'NANIMAL' => null,
        //животное - уникальный идентификатор (наверное...)
        'NANIMAL_TIME' => null,
        //животное - инвентарный номер
        'NINV' => null,
        //животное - кличка
        'KLICHKA' => null,
        //животное - пол
        'POL' => null,
        //животное - код пола
        'NPOL' => null,
        //животное - идентификационный номер РСХН
        'NGOSREGISTER' => null,
        //животное - номер в оборудовании
        'NINV1' => null,
        //животное - электронная метка
        'NINV3' => null,
        //животное - вид животного
        'ANIMAL_VID' => null,
        //животное - код вида животного (КРС - 26 / Овцы - 17)
        'ANIMAL_VID_COD' => null,
        //животное - масть
        'MAST' => null,
        //животное - код масти
        'NMAST' => null,
        //животное - порода
        'POR' => null,
        //животное - код породы
        'NPOR' => null,
        //животное - дата рождения в формате YYYY-mm-dd
        'DATE_ROGD' => null,
        //животное - дата поступления в формате YYYY-mm-dd
        'DATE_POSTUPLN' => null,
        //животное - хозяйство рождения (базовый индекс хозяйства)
        'NHOZ_ROGD' => null,
        //животное - базовый индекс хозяйства (текущее хозяйство)
        'NHOZ' => null,
        //животное - внутренний код области хозяйства (текущее хозяйство)
        'NOBL' => null,
        //животное - внутренний код района хозяйства (текущее хозяйство)
        'NRN' => null,
        //животное - импортный идентификатор
        'NIDENT' => null,
        //животное - хозяйство рождения (название)
        'ROGD_HOZ' => null,
        //животное - дата выбытия в формате YYYY-mm-dd
        'DATE_V' => null,
        //животное - причина выбытия
        'PV' => null,
        //животное - расход
        'RASHOD' => null,
        //животное - живая масса при выбытии (кг)
        'GM_V' => null,
        //животное - использование (племенная ценность)
        'ISP' => null,
        //животное - дата электронного мечения в формате YYYY-mm-dd
        'DATE_CHIP' => null,
        //животное - дата мечения (инв. №) в формате YYYY-mm-dd
        'DATE_NINV' => null,
        //животное - дата мечения (№ РСХН) в формате YYYY-mm-dd
        'DATE_NGOSREGISTER' => null,
        //отец - инвентарный номер
        'NINV_OTCA' => null,
        //отец - идентификационный номер РСХН
        'NGOSREGISTER_OTCA' => null,
        //отец - порода
        'POR_OTCA' => null,
        //отец - код породы
        'NPOR_OTCA' => null,
        //отец - дата рождения в формате YYYY-mm-dd
        'DATE_ROGD_OTCA' => null,
        //мать - инвентарный номер
        'NINV_MATERI' => null,
        //мать - идентификационный номер РСХН
        'NGOSREGISTER_MATERI' => null,
        //мать - порода
        'POR_MATERI' => null,
        //мать - код породы
        'NPOR_MATERI' => null,
        //мать - дата рождения в формате YYYY-mm-dd
        'DATE_ROGD_MATERI' => null,
        //ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
        'IMPORT_STATUS' => ImportStatusEnum::COMPLETED->value,
        //код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы')
        'TASK' => null,
        //гуид животного, который генерирует СВР в момент создания этой записи
        'GUID_SVR' => null,
        //сырые данные из Селекс в формате JSON.
        'ANIMALS_JSON' => null,
    ]

];
