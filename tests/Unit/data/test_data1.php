<?php

use Svr\Core\Enums\ImportStatusEnum;

return [
    [
        //животное - уникальный идентификатор
        'nanimal' => '1',
        //животное - уникальный идентификатор (наверное...)
        'nanimal_time' => 'xx6hPWlHiEE0Hc1i2IOqyEcrcVHr6vVJv6ULEyoJS5yBzSz1xRST0y1ESj8MBJZNKmanooM5pkrTeQvPbxSRd6Byn5tTdZfJhc7QmkZXMSFfBY5VVPbReJ6ag9YTzNer',
        //животное - инвентарный номер
        'ninv' => 'yCYMXcc13lmA4oI',
        //животное - кличка
        'klichka' => 'kWL0pP3w1kH0ryWq5Qe2SxQZtkQii8Z92gvcMSm9CcNixXcQ31',
        //животное - пол
        'pol' => 'VSFjXykkCbBuNBvTyyXo8CsOzA1Mzx',
        //животное - код пола
        'npol' => '1',
        //животное - идентификационный номер РСХН
        'ngosregister' => '1i9CX7AiLqk7ydMP2wslt2l7F5ltbinOj11dDnFrLx7b9RH0Jd',
        //животное - номер в оборудовании
        'ninv1' => 'znRzEa366O7su0B',
        //животное - электронная метка
        'ninv3' => 'ZKyzXmewpJznCBP7S1X6',
        //животное - вид животного
        'animal_vid' => '1i9CX7AiLqk7ydMP2wslt2l7F5ltbinOj11dDnFrLx7b9RH0Jd',
        //животное - код вида животного (КРС - 26 / Овцы - 17)
        'animal_vid_cod' => 32767,
        //животное - масть
        'mast' => 'yhSpCmNTqNk0OcSF0zSfATE8E7FJYW',
        //животное - код масти
        'nmast' => 1,
        //животное - порода
        'por' => 'yhSpCmNTqNk0OcSF0zSfATE8E7FJYW',
        //животное - код породы
        'npor' => 1,
        //животное - дата рождения в формате YYYY-mm-dd
        'date_rogd' => '2015-01-01',
        //животное - дата поступления в формате YYYY-mm-dd
        'date_postupln' => '2015-01-01',
        //животное - хозяйство рождения (базовый индекс хозяйства)
        'nhoz_rogd' => 99999,
        //животное - базовый индекс хозяйства (текущее хозяйство)
        'nhoz' => 99999,
        //животное - внутренний код области хозяйства (текущее хозяйство)
        'nobl' => 99999,
        //животное - внутренний код района хозяйства (текущее хозяйство)
        'nrn' => 99999,
        //животное - импортный идентификатор
        'nident' => 'ZKyzXmewpJznCBP7S1X6',
        //животное - хозяйство рождения (название)
        'rogd_hoz' => '1i9CX7AiLqk7ydMP2wslt2l7F5ltbinOj11dDnFrLx7b9RH0Jd',
        //животное - дата выбытия в формате YYYY-mm-dd
        'date_v' => '2015-01-01',
        //животное - причина выбытия
        'pv' => 'cUVxy0CT0yxWwy9MJs0MrQaMJbUqld0wnBUgaowCtVa57HQLZ2yNILKXREvx',
        //животное - расход
        'rashod' => 'yhSpCmNTqNk0OcSF0zSfATE8E7FJYW',
        //животное - живая масса при выбытии (кг)
        'gm_v' => 99999,
        //животное - использование (племенная ценность)
        'isp' => 'ZKyzXmewpJznCBP7S1X6',
        //животное - дата электронного мечения в формате YYYY-mm-dd
        'date_chip' => '2015-01-01',
        //животное - дата мечения (инв. №) в формате YYYY-mm-dd
        'date_ninv' => '2015-01-01',
        //животное - дата мечения (№ РСХН) в формате YYYY-mm-dd
        'date_ngosregister' => '2015-01-01',
        //отец - инвентарный номер
        'ninv_otca' => 'znRzEa366O7su0B',
        //отец - идентификационный номер РСХН
        'ngosregister_otca' => '1i9CX7AiLqk7ydMP2wslt2l7F5ltbinOj11dDnFrLx7b9RH0Jd',
        //отец - порода
        'por_otca' => 'yhSpCmNTqNk0OcSF0zSfATE8E7FJYW',
        //отец - код породы
        'npor_otca' => 32767,
        //отец - дата рождения в формате YYYY-mm-dd
        'date_rogd_otca' => '2015-01-01',
        //мать - инвентарный номер
        'ninv_materi' => 'znRzEa366O7su0B',
        //мать - идентификационный номер РСХН
        'ngosregister_materi' => '1i9CX7AiLqk7ydMP2wslt2l7F5ltbinOj11dDnFrLx7b9RH0Jd',
        //мать - порода
        'por_materi' => 'yhSpCmNTqNk0OcSF0zSfATE8E7FJYW',
        //мать - код породы
        'npor_materi' => 32767,
        //мать - дата рождения в формате YYYY-mm-dd
        'date_rogd_materi' => '2015-01-01',
        //ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)
        'import_status' => ImportStatusEnum::COMPLETED->value,
        //код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы')
        'task' => 1,
        //гуид животного, который генерирует СВР в момент создания этой записи
        'guid_svr' => 'dxP7aqEMkjrG0oiId4ce53QdOIg5V3Bk97gi9sojwNd1QThGwQCjDVVMb5ZzlZ3Z',
        //сырые данные из Селекс в формате JSON.
        'animals_json' => null,
    ]
];
