<?php

namespace Svr\Raw\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class SelexGetAnimalsCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): Collection
    {
        // Получаем массив по ключу list_animals из ресурсов
        // $listAnimals = $this->list_animals;
        $listAnimals = $this->resource["data"];
        // Форматируем каждый элемент массива по ресурсу SelexSendAnimalsResource
        $formattedListAnimals = collect($listAnimals)->map(function ($item) {
            return SelexGetAnimalsResource::make($item);
        });

        return
            $formattedListAnimals
        ;
    }
}
