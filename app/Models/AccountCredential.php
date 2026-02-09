<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCredential extends Model
{
    protected $fillable = [
        'account_id',
        'login_type',
        'username',
        'email',
        'password',
        'note',
    ];

    protected $hidden = [
        'password',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
