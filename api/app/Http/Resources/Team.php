<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Team extends JsonResource
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
            'name' => $this->name,
            'fund' => $this->fund,
            'country' => new Country($this->country),
            'user_id' => $this->user_id,
            'user_name' => empty($this->user) ? null : $this->user->name,
            'players' => Player::collection($this->players),
            'team_value' => $this->players->sum('price')
        );
    }
}