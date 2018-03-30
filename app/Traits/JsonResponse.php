<?php

namespace App\Traits;

/**
 *
 */
trait JsonResponse
{
    protected function successJsonResponse(array $data = null, $statusCode = 200)
    {
        $result = [
           'err_code' => '0',
           'err_msg' => 'success',
       ];

        if (!is_null($data)) {
            $result = array_merge($result, $data);
        }
        return response()->json($result, $statusCode);
    }

    protected function errorJsonResponse($errCode, $errors, $statusCode = 200)
    {
        info("api error response: $errors");
        return response()->json([
            'err_code' => $errCode,
            'err_msg' => $errors,
        ], $statusCode);
    }

    protected function paginateJsonResponse($data, $statusCode = 200)
    {
        $result = [];
        $result['total'] = $data->total();
        $result['current'] = $data->currentPage();
        $result['per_page'] = $data->perPage();
        $result['has_more_page'] = $data->hasMorePages();
        $result['last_page'] = $data->lastPage();
        $result['list'] = $data->values();
        return $this->successJsonResponse($result, $statusCode);
    }
}
