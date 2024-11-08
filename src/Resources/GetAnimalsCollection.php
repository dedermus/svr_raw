<?php

namespace Svr\Raw\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;


class GetAnimalsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => true,
            'data' => $this->collection,

            "message" => "Операция выполнена",
            "notifications" => [
                "count_new" => 0,
                "count_total" => $this->collection->count()
            ],
            "pagination" => [
                "total_records" => 0,
                "max_page" => 0,
                "cur_page" => 0,
                "per_page" => 0
            ],
        ];
    }
}
