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
            return $this->json(Response::notLoggedIn());
        }
        return $this->json(Response::success(array_merge(
            [['id' => 0, 'name' => '']],
            \app\model\Notebook::query()->fields(['id', 'name'])->where('userid', AuthToken::getId())->get()
        )));
    }

    public function create()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
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
            return $this->json(Response::unknownError());
        }
        return $this->json(Response::success($newId));
    }
}