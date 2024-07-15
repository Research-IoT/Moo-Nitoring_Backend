<?php
namespace App\Helpers;

class ApiHelpers
{
    protected static $response = [
        'code' => null,
        'status' => null,
        'message' => null,
    ];

    /**
     * When the request is successful
     */
    public static function ok($data = null, $message = null, $code = 200)
    {
        self::$response['code'] = $code;
        self::$response['status'] = 'success';
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['code']);
    }

    /**
     * When the request is successful added data
     */
    public static function success($data = null, $message = null, $code = 201)
    {
        self::$response['code'] = $code;
        self::$response['status'] = 'success';
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['code']);
    }

    /**
     * Whe the request is failed or error
     */
    public static function badRequest($error = null, $message = null, $code = 400)
    {
        self::$response['code'] = $code;
        self::$response['status'] = 'error';
        self::$response['message'] = $message;
        self::$response['error'] = $error;

        return response()->json(self::$response, self::$response['code']);
    }

    /**
     * Whe the request is already exist
     */
    public static function alredy($error = null, $message = null, $code = 401)
    {
        self::$response['code'] = $code;
        self::$response['status'] = 'error';
        self::$response['message'] = $message;
        self::$response['error'] = $error;

        return response()->json(self::$response, self::$response['code']);
    }

        /**
     * Whe the request is Internal Server Error
     */
    public static function internalServer($error = null, $message = null, $code = 500)
    {
        self::$response['code'] = $code;
        self::$response['status'] = 'error';
        self::$response['message'] = $message;
        self::$response['error'] = $error;

        return response()->json(self::$response, self::$response['code']);
    }
}
