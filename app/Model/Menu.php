<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name', 'code'
    ];

    public static function createMenu( $data = [] )
    {
        $code = "MENU_".time();
        $data['code'] = $code;

        return Menu::create($data);
    }

    public function products()
    {
        return $this->hasMany('App\Model\Product');
    }

    public function menus()
    {
        return $this->hasMany('App\Model\Menu');
    }
}
