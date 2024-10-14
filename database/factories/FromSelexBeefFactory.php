<?php

namespace Svr\Raw\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Raw\Models\FromSelexBeef;


class FromSelexBeefFactory extends Factory
{

    protected $model = FromSelexBeef::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'NANIMAL'  => $this->faker->randomNumber(13), // 'животное - уникальный идентификатор'
        'NANIMAL_TIME'  => $this->faker->randomNumber(13), // 'животное - уникальный идентификатор (наверное...)'
        'NINV' => $this->faker->randomNumber(5), // 'животное - инвентарный номер'
        'KLICHKA' => $this->faker->name(),  // 'животное - кличка'
        'POL' => 'Корова',  // 'животное - пол'
        'NPOL' => 4,  // 'животное - код пола'
        'NGOSREGISTER' => 'RU'.$this->faker->randomNumber(4).'a'.$this->faker->randomNumber(4),  // 'животное - идентификационный номер РСХН'
        'NINV1' => $this->faker->randomNumber(6),  // 'животное - номер в оборудовании'
        'NINV3' => $this->faker->randomNumber(15),  // 'животное - электронная метка'
        'ANIMAL_VID' => 'Мясной скот',  // 'животное - вид животного'
        'ANIMAL_VID_COD' => 26,  // 'животное - код вида животного (КРС - 26 / Овцы - 17'
        'MAST' => $this->faker->randomElements(['Красная', 'Белая', 'Пёстрая', 'Красно-пестрая', 'Черная']),  // 'животное - масть'
        'NMAST' => $this->faker->randomNumber(1),  // 'животное - код масти'
        'POR' => $this->faker->randomElements(['Герефордская', 'Абердин ангусская']),  // 'животное - порода'
        'NPOR' => $this->faker->randomNumber(2),  // 'животное - код породы'
        'DATE_ROGD' => $this->faker->date(),  // 'животное - дата рождения в формате YYYY.mm.dd'
        'DATE_POSTUPLN' => $this->faker->date(),  // 'животное - дата поступления в формате YYYY.mm.dd'
        'NHOZ_ROGD' => $this->faker->randomNumber(6),  // 'животное - хозяйство рождения (базовый индекс хозяйства)'
        'NHOZ' => $this->faker->randomNumber(6),  // 'животное - базовый индекс хозяйства (текущее хозяйство)'
        'NOBL' => $this->faker->randomNumber(2),  // 'животное - внутренний код области хозяйства (текущее хозяйство)'
        'NRN' => $this->faker->randomNumber(4),  // 'животное - внутренний код района хозяйства (текущее хозяйство)'
        'NIDENT' => 'RU'.$this->faker->randomNumber(4).'a'.$this->faker->randomNumber(4),  // 'животное - импортный идентификатор'
        'ROGD_HOZ' => 'Россия',  // 'животное - хозяйство рождения (название)'
        'DATE_V' => $this->faker->name(),  // 'животное - дата выбытия в формате YYYY.mm.dd'
        'PV' => null,  // 'животное - причина выбытия'
        'RASHOD' => null,  // 'животное - расход'
        'GM_V' => $this->faker->randomNumber(3),  // 'животное - живая масса при выбытии (кг)'
        'ISP' => 'Пользовательное',  // 'животное - использование (племенная ценность)'
        'DATE_CHIP' => $this->faker->date(),  // 'животное - дата электронного мечения в формате YYYY.mm.dd'
        'DATE_NINV' => $this->faker->date(),  // 'животное - дата мечения (инв. №) в формате YYYY.mm.dd'
        'DATE_NGOSREGISTER' => $this->faker->date(),  // 'животное - дата мечения (№ РСХН) в формате YYYY.mm.dd'
        'NINV_OTCA' => $this->faker->name(),  // 'отец - инвентарный номер'
        'NGOSREGISTER_OTCA' => $this->faker->name(),  // 'отец - идентификационный номер РСХН'
        'POR_OTCA' => $this->faker->randomElements(['Герефордская', 'Абердин ангусская']),  // 'отец - порода'
        'NPOR_OTCA' => $this->faker->randomNumber(2),  // 'отец - код породы'
        'DATE_ROGD_OTCA' => $this->faker->name(),  // 'отец - дата рождения в формате YYYY.mm.dd'
        'NINV_MATERI' => $this->faker->randomNumber(6),  // 'мать - инвентарный номер'
        'NGOSREGISTER_MATERI' => $this->faker->name(),  // 'мать - идентификационный номер РСХН'
        'POR_MATERI' => $this->faker->randomElements(['Герефордская', 'Абердин ангусская']),  // 'мать - порода'
        'NPOR_MATERI' => $this->faker->randomNumber(2),  // 'мать - код породы'
        'DATE_ROGD_MATERI' => $this->faker->date(),  // 'мать - дата рождения в формате YYYY.mm.dd'
        'IMPORT_STATUS' => $this->faker->randomElements(ImportStatusEnum::get_value_list()),// 'ENUM - состояние обработки записи (new - новая / in_progress - в процессе / error - ошибка / completed - обработана)'
        'TASK' => 6,// 'код задачи берется из таблицы TASKS.NTASK (1 – молоко / 6- мясо / 4 - овцы'
        'GUID_SVR' => $this->faker->uuid(),// 'гуид животного, который генерирует СВР в момент создания этой записи'
        'ANIMALS_JSON' => [$this->faker->word() => [
            $this->faker->word(),
            $this->faker->word(),
            $this->faker->word()],
            $this->faker->word() => [
                $this->faker->word(),
                $this->faker->word(),
                $this->faker->word()
            ],
            $this->faker->word() => [
                $this->faker->word(),
                $this->faker->word(),
                $this->faker->word()
            ]
        ],// 'сырые данные из Селекс'
        'created_at' => now(),// 'Дата создания записи'
        'update_at' => now(),// 'Дата удаления записи'
        ];
    }
}
