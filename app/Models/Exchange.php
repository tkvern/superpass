<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Exchange extends Eloquent
{
    protected $collection = 'exchange';

    public function currencies()
    {
        return $this->hasMany(Currency::class, 'exchange', '_id');
    }
}
