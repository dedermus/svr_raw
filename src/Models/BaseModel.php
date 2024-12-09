<?php

namespace Svr\Raw\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Svr\Core\Traits\GetTableName;
use Svr\Core\Traits\GetValidationRules;

/**
 * Базовая модель
 * @package Svr\Raw\Models
 */
class BaseModel extends Model
{
    /**
     * Возвращает массив уникальных значений.
     * Где ключ - порядковый номер, значение - уникальное значение поля таблицы
     *
     * @param string $column_name
     *
     * @return array
     */
    public static function get_array_unique_value(string $column_name): array
    {
        return DB::table(self::getTableName())
            ->select($column_name)
            ->distinct()
            ->get()
            ->filter(function ($item) use ($column_name) {
                return $item->$column_name; // Отбрасываем значения null
            })
            ->keyBy(function ($item, $key) {
                return $key; // Используем порядковый номер как ключ
            })
            ->map(function ($item) use ($column_name) {
                return $item->$column_name; // Возвращаем значение поля npol
            })
            ->toArray();
    }
}
