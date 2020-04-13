<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const PHOTO_PATH = "uploads/products";

    protected $fillable = [
        'menu_id', 
        'code', 
        'name', 
        'description', 
        'price', 
    ];

    public function images()
    {
        return $this->morphMany('App\Model\Image', 'imageable');
    }

    public function menu()
    {
        return $this->belongsTo('App\Model\Menu');
    }

    public static function createProduct( $data = [] )
    {
        $last = Product::latest()->first();
        $last = ( $last != NULL ) ? $last->id : 0;
        $last++;
        $code = "PRODUCT_".date('mY');
        $code = $code.str_pad( $last, 5, "0", STR_PAD_LEFT);
        // dd( $code );die;
        $data['code'] = $code;

        return Product::create($data);
    }
}