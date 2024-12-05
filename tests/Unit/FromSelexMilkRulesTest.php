<?php

namespace Svr\Raw\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
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
     * Тестируем правила валидации модели FromSelexMilk на максимальных значениях
     * @throws ValidationException
     * @expectedException ValidationException
     */
    #[Test]
    #[DataProvider('provideTestData')]
    public function test_rules_post_method(array $data)
    {
        echo "test_rules_post_method - Тест rules валидации модели FromSelexMilk.";

        //Создадим объект request класса Request
        $request = Request::create(
            uri: '/',
            method: 'post'
        );
        // В $request->input() помещаем входящие данные
        $request->replace($data);

        // тестируем метод rulesReturnWithBag в классе FromSelexMilk

        $fromSelexMilk = new FromSelexMilk();

        // Используем Reflection API для доступа к приватному методу
        $reflectionClass = new ReflectionClass(FromSelexMilk::class);
        $method = $reflectionClass->getMethod('validateRequest');
        $method->setAccessible(true);  // Разрешаем доступ к приватному методу


        // $result = $method->invoke($fromSelexMilk, $request);

        try {
            // $result = null;
            $method->invoke($fromSelexMilk, $request);

        } catch (ValidationException $e) {
            $result = $e->errors();
        }

        // проверяем результат
        // Если валидация прошла успешно, то $result будет равен null
        // assertNull проверяет, что $result равен null, если $result не равен null, то тест провалится с описанием ошибки
        $this->assertNull($result,
            "Тест правил валидации модели FromSelexMilk провален, ошибка: " . $result);
    }

    public static function provideTestData(): array
    {
        $dataFiles = [
            __DIR__ . '/data/test_data1.php',
            __DIR__ . '/data/test_data2.php',
            __DIR__ . '/data/test_data3.php',
            __DIR__ . '/data/test_data4.php',
            __DIR__ . '/data/test_data5.php',
        ];

        $data = [];
        foreach ($dataFiles as $file) {
            if (file_exists($file)) {
//                echo $file . "\n";
                $fileData = include $file;
                if (is_array($fileData)) {
                    $data[] = $fileData;
                }
            }
        }
        return $data;
    }
}
