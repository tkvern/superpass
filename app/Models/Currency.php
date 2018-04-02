<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Currency extends Eloquent
{
    protected $collection = 'currency';

    public function exchange()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id');
    }

    public function currencyInfo()
    {
        return $this->belongsTo(CurrencyInfo::class, 'symbol', 'symbol');
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
