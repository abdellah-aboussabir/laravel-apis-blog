<?php

namespace App\Http\Traits;

/**
 *
 */
trait GeneraleTrait
{
    public static function returnError($errorNum, $msg, $error)
    {
        return response()->json([
            "status" => false,
            "code" => $errorNum,
            "msg" => $msg,
            "error" => $error,
        ]);
    }

    public static function returnSuccessMessage($successNum, $msg)
    {
        return response()->json([
            "status" => true,
            "code" => $successNum,
            "msg" => $msg,
        ]);
    }

    public static function  returnData($key, $value, $successNum, $msg)
    {
        return response()->json([
            "status" => true,
            "code" => $successNum,
            "msg" => $msg,
            $key => $value,
        ]);
    }
}
