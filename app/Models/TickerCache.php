<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class TickerCache extends Eloquent
{
    protected $collection = 'ticker_cache';
}
