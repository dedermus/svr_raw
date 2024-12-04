<?php

namespace Svr\Raw\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Raw\Models\FromSelexSheep;

class FromSelexSheepFactory extends Factory
{
    /**
     * Название модели, соответствующей фабрике.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = FromSelexSheep::class;

    /**
     * Define the model"s default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nanimal"             => $this->faker->randomNumber(9, true) . $this->faker->randomNumber(4, true),          // "животное - уникальный идентификатор"
            "nanimal_time"        => $this->faker->randomNumber(9, true) . $this->faker->randomNumber(4, true),          // "животное - уникальный идентификатор (наверное...)"
            'ninvleft'            => null,                                                                                                                   // Животное - инвентарный номер, левое ухо
            'ninvright'           => null,                                                                                                                   // Животное - инвентарный номер, правое ухо
            "ngosregister"        => "RU" . $this->faker->randomNumber(4, true) . "a" . $this->faker->randomNumber(4),           // "животное - идентификационный номер РСХН"
            "ninv3"               => $this->faker->randomNumber(9, true) . $this->faker->randomNumber(6, true),          // "животное - электронная метка"
            'taty'                => null,                                                                                                                   // Животное - тату
            "animal_vid"          => "Мясной скот",                                                                                                          // "животное - вид животного"
            "animal_vid_cod"      => 17,                                                                                                                     // "животное - код вида животного (КРС - 26 / Овцы - 17"
            "klichka"             => $this->faker->word(),                                                                                                   // "животное - кличка"
            "pol"                 => $this->faker->randomElement(SheepPolENUM::class),                                                                // "животное - пол"
            "npol"                => 4,                                                                                                                      // "животное - код пола"
            "por"                 => $this->faker->randomElement(SheepPorENUM::class),                                                                // "животное - порода"
            "npor"                => $this->faker->randomNumber(2),                                                                                // "животное - код породы"
            'osn_okras'           => null,                                                                                                                   // Животное - окрас
            "date_rogd"           => $this->faker->date(),                                                                                                   // "животное - дата рождения в формате YYYY.mm.dd"
            "date_postupln"       => $this->faker->date(),                                                                                                   // "животное - дата поступления в формате YYYY.mm.dd"
            "nhoz_rogd"           => $this->faker->randomNumber(6),                                                                                // "животное - хозяйство рождения (базовый индекс хозяйства)"
            "nhoz"                => $this->faker->randomNumber(6),                                                                                // "животное - базовый индекс хозяйства (текущее хозяйство)"
            "nobl"                => $this->faker->randomNumber(2),                                                                                // "животное - внутренний код области хозяйства (текущее хозяйство)"
            "nrn"                 => $this->faker->randomNumber(4),                                                                                // "животное - внутренний код района хозяйства (текущее хозяйство)"
            "nident"              => "RU" . $this->faker->randomNumber(4) . "a" . $this->faker->randomNumber(4),                         // "животное - импортный идентификатор"
            'nsoderganie'         => null,                                                                                                                   // Животное - тип содержания (система содержания)
            'soderganie_im'       => null,                                                                                                                   // Животное - название типа содержания (система содержания)
            "date_v"              => $this->faker->date(),                                                                                                   // "животное - дата выбытия в формате YYYY.mm.dd"
            "pv"                  => null,                                                                                                                   // "животное - причина выбытия"
            "rashod"              => null,                                                                                                                   // "животное - расход"
            "gm_v"                => $this->faker->randomNumber(3),                                                                                // "животное - живая масса при выбытии (кг)"
            "isp"                 => "Пользовательное",                                                                                                      // "животное - использование (племенная ценность)"
            "date_chip"           => $this->faker->date(),                                                                                                   // "животное - дата электронного мечения в формате YYYY.mm.dd"
            'date_ninvright'      => null,                                                                                                                   // Животное - дата мечения (инв. №, правое ухо)
            'date_ninvleft'       => null,                                                                                                                   // Животное - дата мечения (инв. №, левое ухо)
            "date_ngosregister"   => $this->faker->date(),                                                                                                   // "животное - дата мечения (№ РСХН) в формате YYYY.mm.dd"
            'ninvright_otca'      => null,                                                                                                                   // отец - инвентарный номер, правое ухо
            'ninvleft_otca'       => null,                                                                                                                   // отец - инвентарный номер, левое ухо
            "ngosregister_otca"   => $this->faker->word(),                                                                                                   // "отец - идентификационный номер РСХН"
            'ninvright_materi'    => null,                                                                                                                   // мать - инвентарный номер, правое ухо
            'ninvleft_materi'     => null,                                                                                                                   // мать - инвентарный номер, левое ухо
            "ngosregister_materi" => $this->faker->word(),                                                                                                   // "мать - идентификационный номер РСХН"
            "import_status"       => $this->faker->randomElement(ImportStatusEnum::class),                                                            // "ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)"
            "task"                => 4,                                                                                                                      // "код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы"
            "guid_svr"            => $this->faker->uuid(),                                                                                                   // "гуид животного, который генерирует СВР в момент создания этой записи"

            "animals_json" => json_encode([
                "nident" => "RU" . $this->faker->randomNumber(4) . "a" . $this->faker->randomNumber(4),
                "ninv" => $this->faker->randomNumber(6),
                "ngosregister" => $this->faker->word(),
                "por" => $this->faker->randomElement(SheepPorENUM::class),
                "npor" => $this->faker->randomNumber(2),
                $this->faker->word() => [
                    $this->faker->word() . '1' => $this->faker->randomNumber(6),
                    $this->faker->word() . '1' => $this->faker->randomNumber(6),
                    $this->faker->word() . '1' => $this->faker->randomNumber(6),
                    $this->faker->word() . '1' => $this->faker->randomNumber(6),
                ],
            ]),// "сырые данные из Селекс"
        ];
    }
}


enum SheepPorENUM: string
{
    case Цигайская = 'Цигайская';
    case Рамбулье = 'Рамбулье';
    case Прекос = 'Прекос';
    case Шевиот = 'Шевиот';
    case Романовская = 'Романовская';
    case Дорпер = 'Дорпер';
    case Меринос = 'Меринос';
    case Гиссарские = 'Гиссарские';
    case Эдильбаевская = 'Эдильбаевская';
    case Куйбышевская = 'Куйбышевская';
    case Катумская = 'Катумская';
    case Каракульская = 'Каракульская';
    case Тексель = 'Тексель';
    case Карачаевская = 'Карачаевская';
}

enum SheepMastENUM: string
{
    case Рыжая = 'Рыжая';
    case Серая = 'Серая';
    case Белая = 'Белая';
    case Черная = 'Черная';
}

enum SheepPolENUM: string
{
    case Овца = 'Овца';
    case Баран = 'Баран';
}
