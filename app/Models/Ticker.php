<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Ticker extends Eloquent
{
    protected $collection = 'ticker';

    public function exchange()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id', '_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', '_id');
    }
}
