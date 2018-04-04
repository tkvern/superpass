<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use MongoDB\BSON\ObjectID;
use App\Traits\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use JsonResponse;

    public function objectIDFormat(object $items = null)
    {
        $arr = [];
        if (!is_null($items)) {
            foreach ($items as $item) {
                $arr[] = new ObjectID($item['_id']);
            }
        }
        return $arr;
    }
}
