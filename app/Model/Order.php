<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'code', 
        'customer_id', 
        'phone_ref', // reference
        'address', //reference
        'latitude',
        'longitude',
    ];

    public static function createOrder( $data = [] )
    {
        $last = Order::latest()->first();
        $last = ( $last != NULL ) ? $last->id : 0;
        $last++;
        $code = "ORDER_".date('mY');
        $code = $code.str_pad( $last, 5, "0", STR_PAD_LEFT);
        // dd( $code );die;
        $data['code'] = $code;

        return Order::create($data);
    }

    public function orderDetails()
    {
        return $this->hasMany('App\Model\OrderDetail');
    }

    public function customer()
    {
        return $this->BelongsTo('App\Model\Customer');
    }

    public function payment()
    {
        return $this->hasOne('App\Model\Payment');
    }
}
