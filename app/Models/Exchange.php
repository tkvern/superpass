<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Exchange extends Eloquent
{
    protected $collection = 'exchange';

    public function currencies()
    {
        return $this->hasMany(Currency::class);
    }

    public function tickers()
    {
        return $this->hasMany(Ticker::class);
    }

    public function volume24hours()
    {
        return $this->hasMany(Volume24Hour::class);
    }
}
