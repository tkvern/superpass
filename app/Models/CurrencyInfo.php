<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CurrencyInfo extends Eloquent
{
    protected $collection = 'currency_info';

    public function currencies()
    {
        return $this->hasMany(Currency::class, 'symbol', 'symbol');
    }
}
