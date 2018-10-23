<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Player extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = array(
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'age' => $this->age,
            'price' => $this->price,
            'country' => new Country($this->country),
            'player_role' => new PlayerRole($this->playerRole),
            'team_id' => $this->team_id,
            'team_name' => null
        );
        if (!empty($this->team))
        {
            $data['team_name'] = $this->team->name;
        }
        return $data;
    }
}