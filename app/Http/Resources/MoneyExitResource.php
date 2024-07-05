<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyExitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_exit' => $this->id_exit,
            'description' => $this->description,
            'value' => $this->value,
            'exit_date' => $this->exit_date
        ];
    }
}
