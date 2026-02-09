<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'game_label',
        'name',
        'description',
        'price_original',
        'discount_percent',
        'status',
    ];

    protected $casts = [
        'price_original' => 'integer',
        'discount_percent' => 'integer',
        'price_final' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function (Account $account) {
            $account->discount_percent = (int) ($account->discount_percent ?? 0);

            if (!$account->code || $account->isDirty('game_label')) {
                $account->code = self::generateCode($account->game_label);
            }

            if (
                !$account->price_final ||
                $account->isDirty('price_original') ||
                $account->isDirty('discount_percent')
            ) {
                $account->price_final = self::calcFinal(
                    (int) $account->price_original,
                    (int) $account->discount_percent
                );
            }
        });
    }

    public function images()
    {
        return $this->hasMany(AccountImage::class);
    }

    public function credential()
    {
        return $this->hasOne(AccountCredential::class);
    }

    public static function gamePrefix(string $gameLabel): string
    {
        return match ($gameLabel) {
            'efootball' => 'EF',
            'fc_mobile' => 'FC',
            default => 'XX',
        };
    }

    public static function generateCode(string $gameLabel): string
    {
        $prefix = self::gamePrefix($gameLabel);

        for ($i = 0; $i < 10; $i++) {
            $rand = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $code = "{$prefix}-{$rand}";

            if (!self::where('code', $code)->exists()) {
                return $code;
            }
        }

        return "{$prefix}-" . time();
    }

    public static function calcFinal(int $original, int $discountPercent): int
    {
        $discountPercent = max(0, min(100, $discountPercent));

        if ($discountPercent === 0) return max(0, $original);

        $final = $original - (int) round($original * ($discountPercent / 100));
        return max(0, $final);
    }
}
