<?php

namespace app\util;

use BestLang\core\util\BLRequest;
use BestLang\ext\token\BLToken;

class AuthToken
{
    public static function sign($id)
    {
        return BLToken::sign($id, 365 * 24 * 3600);
    }

    public static function getId()
    {
        return BLToken::unsign(BLRequest::header('X-Auth-Token'));
    }
}