<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const PHOTO_PATH = "uploads/transfers";

    protected $fillable = [
        'code',
        'order_id',
        'customer_id', //reference
        'payment_method', 
        'total', 
        'status', // SUSPEND, FUNDED, CANCELLED
        // 'trf_file',
    ];
    public static function createPayment( $data = [] )
    {
        $last = Payment::latest()->first();
        $last = ( $last != NULL ) ? $last->id : 0;
        $last++;
        $code = "PAYMENT_".date('mY');
        $code = $code.str_pad( $last, 5, "0", STR_PAD_LEFT);
        // dd( $code );die;
        $data['code'] = $code;

        return Payment::create($data);
    }

    public function order()
    {
        return $this->BelongsTo('App\Model\Order');
    }

    public function customer()
    {
        return $this->BelongsTo('App\Model\Customer');
    }

    public function trf_img()
    {
        return $this->morphOne('App\Model\Image', 'imageable');
    }
}
