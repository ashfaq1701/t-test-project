<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => Role::collection($this->roles),
            'profile_photo' => new Photo($this->profile_photo),
            'team' => new Team($this->team)
        );
    }
}
