<?php

namespace app\util;

use BestLang\core\controller\BLInterceptor;
use BestLang\core\util\BLRequest;

class Interceptor implements BLInterceptor
{
    public function before()
    {
        header('Access-Control-Allow-Origin:*');
        if (BLRequest::method() == 'OPTIONS') {
            header('Access-Control-Allow-Headers:Content-Type,X-Auth-Token');
            exit();
        }
    }

    public function after()
    {

    }
}