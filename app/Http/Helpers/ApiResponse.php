<?php

namespace App\Http\Helpers;

class ApiResponse
{
    public static function success($data = null, $message = 'Success', $code = 200, $res = 'data')
    {
        return response()->json([
            'success'   => true,
            'message'   => $message,
            $res        => $data,
        ], $code);
    }

    public static function error($message = 'Error', $code = 400, $errors = null)
    {
        return response()->json([
            'success'   => false,
            'message'   => $message,
            'errors'    => $errors,
        ], $code);
    }

    public static function paginated($paginator, $code = 200, $res = 'data')
    {
        return response()->json([
            'total'     => $paginator->total(),
            'next'      => $paginator->nextPageUrl(),
            'previous'  => $paginator->previousPageUrl(),
            $res        => $paginator->items(),
        ], $code);
    }
}
