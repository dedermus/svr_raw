<?php

namespace Svr\Raw\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelexSendAnimalsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'nanimal' => $this->resource['nanimal'],
            'nanimal_time' => $this->resource['nanimal_time'],
            'guid_svr' => $this->resource['guid_svr'],
            'status' => $this->resource['status'],
            'double' => $this->resource['double']
        ];
    }
}
