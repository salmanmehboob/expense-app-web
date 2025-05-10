<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'category'    => $this->category->name ?? null,
            'amount'      => $this->amount,
            'quantity'    => $this->quantity,
            'date'        => $this->date,
            'description' => $this->description,
            'is_approved' => $this->is_approved,
            'created_by'  => $this->creator->name ?? null,
            'created_at'  => $this->created_at->toDateTimeString(),
        ];
    }
}
