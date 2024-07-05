<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyEntrieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_entrie' => $this->id_entrie,
            'description' => $this->description,
            'value' => $this->value,
            'entrie_date' => $this->entrie_date
        ];
    }
}
