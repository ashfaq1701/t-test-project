<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Transfer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array(
            'id' => $this->id,
            'asking_price' => $this->asking_price,
            'player' => new Player($this->player),
            'placed_from_id' => $this->placed_from_id,
            'transfer_completed_at' => $this->transfer_completed_at,
            'transferred_to_id' => new Team($this->transferredTo),
            'is_notified' => $this->is_notified
        );
    }
}