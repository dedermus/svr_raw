<?php

namespace Svr\Raw\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelexGetAnimalsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {

        return [
            $this->resource['guid_svr'] => [
                'guid_svr' => $this->resource['guid_svr'],
                'guid_horriot' => $this->resource['guid_horriot'],
                'message' => $this->resource['message']
            ]
        ];
    }
}
