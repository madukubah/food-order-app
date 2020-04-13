<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'url',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
