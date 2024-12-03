<?php

namespace Svr\Raw\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelexGetAnimalsCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        // Получаем массив по ключу list_animals из ресурсов
        // $listAnimals = $this->list_animals;
        $listAnimals = $this->resource["result_animals"];

        // Форматируем каждый элемент массива по ресурсу SelexSendAnimalsResource
        $formattedListAnimals = collect($listAnimals)->map(function ($item) {
            return new SelexGetAnimalsResource($item);
        });

        return [
            $formattedListAnimals
        ];
    }
}
