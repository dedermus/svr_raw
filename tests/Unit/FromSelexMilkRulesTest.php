<?php

namespace Svr\Raw\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Svr\Core\Enums\ImportStatusEnum;
use Svr\Raw\Models\FromSelexMilk;
use Tests\TestCase;


/**
 * Тестируем правила валидации модели FromSelexMilk
 * Данный класс тестирует объекта класса Request (запрос) на валидацию правил модели FromSelexMilk
 * В классе собраны положительные тест-куйсы на пограничные значения полей модели
 *
 * @package Svr\Raw\Tests\Unit
 */
class FromSelexMilkRulesTest extends TestCase
{

    /**
     * Тестируем правила валидации модели FromSelexMilk
     * @throws ValidationException
     * @expectedException ValidationException
     */
    #[Test]
    public function test_rules_post_method()
    {
        //Создадим объект request класса Request
        $request = Request::create(
            uri: '/v1/selex/get_animals/',
            method: 'post'
        );

        // В $request->input() помещаем входящие данные
        $request->replace([
            'raw_from_selex_milk_id' => 1,
            'ANIMAL_VID_COD' => 26,
            'IMPORT_STATUS' => ImportStatusEnum::COMPLETED->value,
        ]);

        // тестируем метод rulesReturnWithBag в классе FromSelexMilk
        $dmodel = new FromSelexMilk();
        try {
            $result = null;
            $dmodel->rulesReturnWithBag($request);
        } catch (ValidationException $e) {
            $result = $e->errors();
        }
        // проверяем результат
        // Если валидация прошла успешно, то $result будет равен null
        // assertNull проверяет, что $result равен null, если $result не равен null, то тест провалится с описанием ошибки
        $this->assertNull($result);
    }
}
