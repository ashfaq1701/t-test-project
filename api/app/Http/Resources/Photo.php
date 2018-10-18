<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Photo extends JsonResource
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
            'file_name' => $this->file_name,
            'file_size' => filesize(public_path('/uploads/images/' . $this->file_name)),
            'user_id' => $this->user_id,
            'created_at' => $this->created_at
        );
    }
}