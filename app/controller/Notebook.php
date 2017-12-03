<?php

namespace app\controller;

use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

class Notebook extends BLController
{
    public function all()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::error('Not logged in or session expired'));
        }
        return $this->json(Response::success(
            \app\model\Notebook::query()->fields(['id', 'name'])->where('userid', AuthToken::getId())->get()
        ));
    }

    public function create()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::error('Not logged in or session expired'));
        }
        $name = trim(BLRequest::bodyJson('name'));
        if (empty($name)) {
            return $this->json(Response::error('Notebook name cannot be empty'));
        }
        $newId = \app\model\Notebook::insert([
            'name' => $name,
            'userid' => AuthToken::getId()
        ]);
        if (empty($newId)) {
            return $this->json(Response::error('Unknown error, retry later'));
        }
        return $this->json(Response::success($newId));
    }
}