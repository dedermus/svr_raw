<?php

namespace Svr\Raw\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAnimalsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            $this->GUID_SVR => [
                'guid_svr'=> $this->GUID_SVR,
                'guid_horriot'=> $this->GUID_HORRIOT,
                'message'=> 'Животное еще не добавлено в заявку'
            ]
        ];
    }
}
