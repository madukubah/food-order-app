<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code', 
    ];

    public function user()
    {
        return $this->morphOne('App\User', 'userable');
    }
    public function orders()
    {
        return $this->hasMany('App\Model\Order');
    }
    public function payments()
    {
        return $this->hasMany('App\Model\Payment');
    }
}
