<?php

namespace app\controller;

use app\model\User;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

class Admin extends BLController
{
    public function getall()
    {
        return $this->json(Response::success(User::all()));
    }

    public function setdisabled()
    {
        $user = User::get(BLRequest::bodyJson('id'));
        $user->disabled = BLRequest::bodyJson('disabled');
        $user->save();
        return $this->json(Response::success(null));
    }
}