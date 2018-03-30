<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public function exchange()
    {
        return $this->belongsTo(Exchange::class, 'exchange', '_id');
    }
}
