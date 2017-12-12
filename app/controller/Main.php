<?php

namespace app\controller;

use BestLang\core\controller\BLController;

class Main extends BLController
{
    public function index()
    {
        header('Location:/index.html');
        return true;
    }

    public function phpinfo()
    {
        phpinfo();
        return true;
    }
}