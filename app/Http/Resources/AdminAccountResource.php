<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminAccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_by' => $this->created_by,

            'code' => $this->code,
            'game_label' => $this->game_label,
            'name' => $this->name,
            'description' => $this->description,

            'price_original' => $this->price_original,
            'discount_percent' => $this->discount_percent,
            'price_final' => $this->price_final,

            'status' => $this->status,

            'images' => $this->images?->map(fn($img) => [
                'id' => $img->id,
                'image' => $img->image,
            ]),

            'credential' => $this->credential ? [
                'id' => $this->credential->id,
                'login_type' => $this->credential->login_type,
                'username' => $this->credential->username,
                'email' => $this->credential->email,
                'password' => $this->credential->getRawOriginal('password'),
                'note' => $this->credential->note,
            ] : null,

            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
