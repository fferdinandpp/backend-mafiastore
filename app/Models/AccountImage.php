<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AccountImage extends Model
{
    protected $fillable = [
        'account_id',
        'path',
        'sort_order',
    ];

    protected $appends = ['image_url'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->path) return null;

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($this->path);
    }
}
