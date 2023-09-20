<?php

namespace App\Http\Traits;

/**
 *
 */
trait GeneraleTrait
{
    public function returnError($errorNum, $msg)
    {
        return response()->json([
            "status" => false,
            "code" => $errorNum,
            "msg" => $msg,
        ]);
    }

    public function returnSuccessMessage($successNum, $msg)
    {
        return response()->json([
            "status" => true,
            "code" => $successNum,
            "msg" => $msg,
        ]);
    }

    public function returnData($key, $value, $successNum, $msg)
    {
        return response()->json([
            "status" => true,
            "code" => $successNum,
            "msg" => $msg,
            $key => $value,
        ]);
    }
}
