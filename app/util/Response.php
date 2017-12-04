<?php

namespace app\util;

class Response
{
    public static function success($data)
    {
        return [
            'success' => 1,
            'data' => $data
        ];
    }

    public static function error($msg, $data = null)
    {
        return [
            'success' => 0,
            'msg' => $msg,
            'data' => $data
        ];
    }

    public static function unknownError()
    {
        return self::error('Unknown error, retry later');
    }

    public static function notLoggedIn()
    {
        return self::error('Not logged in or session expired');
    }
}