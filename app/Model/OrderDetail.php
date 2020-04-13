<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'product_id', //reference
        'product_name', 
        'product_price', 
        'quantity', 
        'note', 
    ];

    public function order()
    {
        return $this->BelongsTo('App\Model\Order');
    }
}
