<?php

namespace app\util;

use BestLang\core\controller\BLInterceptor;

class Interceptor implements BLInterceptor
{
    public function before()
    {
    }

    public function after()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:Content-Type,X-Auth-Token');
    }
}