<?php

namespace app\util;

use BestLang\core\util\BLRequest;
use BestLang\ext\token\BLToken;

class AuthToken
{
    private static $_id = false;

    public static function sign($id)
    {
        return BLToken::sign($id, 365 * 24 * 3600);
    }

    public static function getId()
    {
        if (!self::$_id) {
            self::$_id = BLToken::unsign(BLRequest::header('X-Auth-Token'));
        }
        return self::$_id;
    }
}