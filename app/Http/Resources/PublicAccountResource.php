<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicAccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'game_label' => $this->game_label,
            'name' => $this->name,
            'description' => $this->description,

            'price_original' => $this->price_original,
            'discount_percent' => $this->discount_percent,
            'price_final' => $this->price_final,

            'status' => $this->status,

            'images' => $this->images?->sortBy('sort_order')->values()->map(fn($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'image_url' => $img->image_url,
                'sort_order' => $img->sort_order,
            ]),

            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
