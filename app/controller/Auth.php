<?php

namespace app\controller;

use app\model\User;
use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;
use BestLang\ext\token\BLToken;

class Auth extends BLController
{
    public function login()
    {
        $vo = BLRequest::bodyJson();
        if (!isset($vo['email']) || !isset($vo['password'])) {
            return $this->json(Response::error('Please type your E-mail and password'));
        }
        $user = User::query()->where('email', $vo['email'])->limit(1)->get();
        if (empty($user)) {
            return $this->json(Response::error('This E-mail has not been registered'));
        }
        if ($user[0]->password != hash('sha256', $vo['password'])) {
            return $this->json(Response::error('Incorrect password, please try again'));
        }
        return $this->json(Response::success([
            'nickname' => $user[0] -> nickname,
            'token' => AuthToken::sign($user[0]->id)
        ]));
    }

    public function reg()
    {
        $vo = BLRequest::bodyJson();
        if (!isset($vo['email']) || !isset($vo['password'])) {
            return $this->json(Response::error('Please type your E-mail and password'));
        }
        $id = User::insert([
            'email' => $vo['email'],
            'password' => hash('sha256', $vo['password']),
            'nickname' => $vo['nickname']
        ]);
        if ($id > 0) {
            return $this->json(Response::success([
                'token' => AuthToken::sign($id)
            ]));
        } else {
            return $this->json(Response::error('System error, try again later'));
        }
    }
}