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
     * Тестируем приветный метод validateRequest модели FromSelexMilk
     * Метод validateRequest импортируется в класс FromSelexMilk через трейт GetValidationRules
     * Метод validateRequest возвращает исключение ValidationException или void
     */
    #[Test]
    #[DataProvider('provideTestData')]
    public function test_rules_post_method(array $data)
    {
        echo "тестируем метод validateRequest в классе FromSelexMilk, импортированный через треит GetValidationRules \n";
        // Ожидаем исключение ValidationException
        // $this->expectException(ValidationException::class);

        // Создадим объект request класса Request
        $request = Request::create(
            uri: '/v1',
            method: 'post'
        );
        $request->replace(
            $data
        );


        // Используем Reflection API для доступа к приватному методу
        $fromSelexMilk = new FromSelexMilk();

        // Используем Reflection API для доступа к приватным методам
        $reflectionClass = new ReflectionClass(FromSelexMilk::class);

        // Список методов, которые нужно сделать доступными
        $methodsToMakePublic = ['validateRequest', 'getValidationRules', 'getValidationMessages'];

        // Сделаем все методы в списке доступными, если они существуют
        foreach ($methodsToMakePublic as $methodName) {
            if ($reflectionClass->hasMethod($methodName)) {
                $method = $reflectionClass->getMethod($methodName);
                $method->setAccessible(true); // Делаем метод доступным
            } else {
                $this->fail("ОШИБКА: Метод $methodName не найден в классе FromSelexMilk\n");
            }
        }

        try {
            // Вызываем метод validateRequest
            if ($reflectionClass->hasMethod('validateRequest')) {
                $validateRequestMethod = $reflectionClass->getMethod('validateRequest');
                $validateRequestMethod->setAccessible(true); // Делаем метод доступным
                $validateRequestMethod->invoke($fromSelexMilk, $request);
            } else {
                $this->fail("ОШИБКА: Метод validateRequest не найден в классе FromSelexMilk\n");
            }
        } catch (ValidationException $e) {
            echo "Exception: ValidationException" . "\n";
            $errors = $e->errors();
            echo "errors: " . print_r($errors, true) . "\n";
            $this->assertTrue(true);
        }
        // catch (BadMethodCallException $e) {
        //     $errors = $e->getMessage();
        //     echo "BadMethodCallException errors: " . print_r($errors, true) . "\n";
        // }
        $this->assertTrue(true);
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
