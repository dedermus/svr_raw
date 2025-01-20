<?php

namespace Svr\Raw\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Core\Enums\SystemTaskEnum;
use Svr\Raw\Models\FromSelexBeef;


class FromSelexBeefFactory extends Factory
{


    /**
     * Название модели, соответствующей фабрике.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = FromSelexBeef::class;

    /**
     * Define the model"s default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        "nanimal"             => $this->faker->randomNumber(9, true) . $this->faker->randomNumber(4, true),     // "животное - уникальный идентификатор"
        "nanimal_time"        => $this->faker->randomNumber(9, true) . $this->faker->randomNumber(6, true),     // "животное - уникальный идентификатор (наверное...)"
        "ninv"                => $this->faker->randomNumber(5, true),                                                             // "животное - инвентарный номер"
        "klichka"             => $this->faker->word(),                                                                                              // "животное - кличка"
        "pol"                 => "Корова",                                                                                                          // "животное - пол"
        "npol"                => 4,                                                                                                                 // "животное - код пола"
        "ngosregister"        => "RU".$this->faker->randomNumber(4, true)."a".$this->faker->randomNumber(4),            // "животное - идентификационный номер РСХН"
        "ninv1"               => $this->faker->randomNumber(6, true),                                                             // "животное - номер в оборудовании"
        "ninv3"               => $this->faker->randomNumber(9, true) . $this->faker->randomNumber(6, true),     // "животное - электронная метка"
        "animal_vid"          => "Мясной скот",                                                                                                     // "животное - вид животного"
        "animal_vid_cod"      => 26,                                                                                                                // "животное - код вида животного (КРС - 26 / Овцы - 17"
        "mast"                => $this->faker->randomElement(["Красная", "Белая", "Пёстрая", "Красно-пестрая", "Черная"]),                   // "животное - масть"
        "nmast"               => $this->faker->randomNumber(1),                                                                           // "животное - код масти"
        "por"                 => $this->faker->randomElement(["Герефордская", "Абердин ангусская"]),                                         // "животное - порода"
        "npor"                => $this->faker->randomNumber(2),                                                                           // "животное - код породы"
        "date_rogd"           => $this->faker->date(),                                                                                              // "животное - дата рождения в формате YYYY.mm.dd"
        "date_postupln"       => $this->faker->date(),                                                                                              // "животное - дата поступления в формате YYYY.mm.dd"
        "nhoz_rogd"           => '1874040', //$this->faker->randomNumber(6),                                                                           // "животное - хозяйство рождения (базовый индекс хозяйства)"
        "nhoz"                => '1874040', //$this->faker->randomNumber(6),                                                                           // "животное - базовый индекс хозяйства (текущее хозяйство)"
        "nobl"                => 6, //$this->faker->randomNumber(2),                                                                           // "животное - внутренний код области хозяйства (текущее хозяйство)"
        "nrn"                 => 11, //$this->faker->randomNumber(4),                                                                           // "животное - внутренний код района хозяйства (текущее хозяйство)"
        "nident"              => "RU".$this->faker->randomNumber(4)."a".$this->faker->randomNumber(4),                          // "животное - импортный идентификатор"
        "rogd_hoz"            => "Россия",                                                                                                          // "животное - хозяйство рождения (название)"
        "date_v"              => $this->faker->date(),                                                                                              // "животное - дата выбытия в формате YYYY.mm.dd"
        "pv"                  => null,                                                                                                              // "животное - причина выбытия"
        "rashod"              => null,                                                                                                              // "животное - расход"
        "gm_v"                => $this->faker->randomNumber(3),                                                                           // "животное - живая масса при выбытии (кг)"
        "isp"                 => "Пользовательное",                                                                                                 // "животное - использование (племенная ценность)"
        "date_chip"           => $this->faker->date(),                                                                                              // "животное - дата электронного мечения в формате YYYY.mm.dd"
        "date_ninv"           => $this->faker->date(),                                                                                              // "животное - дата мечения (инв. №) в формате YYYY.mm.dd"
        "date_ngosregister"   => $this->faker->date(),                                                                                              // "животное - дата мечения (№ РСХН) в формате YYYY.mm.dd"
        "ninv_otca"           => $this->faker->word(),                                                                                              // "отец - инвентарный номер"
        "ngosregister_otca"   => $this->faker->word(),                                                                                              // "отец - идентификационный номер РСХН"
        "por_otca"            => $this->faker->randomElement(["Герефордская", "Абердин ангусская"]),                                         // "отец - порода"
        "npor_otca"           => $this->faker->randomNumber(2),                                                                           // "отец - код породы"
        "date_rogd_otca"      => $this->faker->date(),                                                                                              // "отец - дата рождения в формате YYYY.mm.dd"
        "ninv_materi"         => $this->faker->randomNumber(6),                                                                           // "мать - инвентарный номер"
        "ngosregister_materi" => $this->faker->word(),                                                                                              // "мать - идентификационный номер РСХН"
        "por_materi"          => $this->faker->randomElement(["Герефордская", "Абердин ангусская"]),                                         // "мать - порода"
        "npor_materi"         => $this->faker->randomNumber(2),                                                                           // "мать - код породы"
        "date_rogd_materi"    => $this->faker->date(),                                                                                              // "мать - дата рождения в формате YYYY.mm.dd"
        "import_status"       => ImportStatusEnum::NEW->value, //$this->faker->randomElement(ImportStatusEnum::get_value_list()),                                            // "ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)"
        "task"                => SystemTaskEnum::BEEF->value, //6,                                                                                                                 // "код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы"
        "guid_svr"            => $this->faker->uuid(),                                                                                              // "гуид животного, который генерирует СВР в момент создания этой записи"
        "animals_json" => json_encode([
            "nident" => "RU".$this->faker->randomNumber(4)."a".$this->faker->randomNumber(4),
            "ninv" => $this->faker->randomNumber(6),
            "ngosregister" => $this->faker->word(),
            "por" => $this->faker->randomElement(["Герефордская", "Абердин ангусская"]),
            "npor" => $this->faker->randomNumber(2),
            $this->faker->word() => [
                $this->faker->word().'1' => $this->faker->randomNumber(6),
                $this->faker->word().'1' => $this->faker->randomNumber(6),
                $this->faker->word().'1' => $this->faker->randomNumber(6),
                $this->faker->word().'1' => $this->faker->randomNumber(6),
            ],
        ]),// "сырые данные из Селекс"
        ];
    }
}
