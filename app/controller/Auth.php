<?php

namespace app\controller;

use app\model\User;
use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

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
            'nickname' => $user[0]->nickname,
            'token' => AuthToken::sign($user[0]->id)
        ]));
    }

    public function reg()
    {
        $vo = BLRequest::bodyJson();
        if (!isset($vo['email']) || !isset($vo['password'])) {
            return $this->json(Response::error('Please type your E-mail and password'));
        }
        if (User::query()->where('email', $vo['email'])->count() > 0) {
            return $this->json(Response::error('This E-mail has already been registered'));
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

    public function changenickname()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $user = User::get(AuthToken::getId());
        if (empty($user)) {
            return $this->json(Response::error('User does not exist'));
        }
        $user->nickname = BLRequest::bodyJson('nickname');
        if (!empty($user->save())) {
            return $this->json(Response::success($user->id));
        } else {
            return $this->json(Response::unknownError());
        }
    }

    public function changepassword()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $user = User::get(AuthToken::getId());
        if (empty($user)) {
            return $this->json(Response::error('User does not exist'));
        }
        $pass = BLRequest::bodyJson('password');
        $newPass = BLRequest::bodyJson('newpassword');
        if ($user->password != hash('sha256', $pass)) {
            return $this->json(Response::error('Current password mismatch'));
        }
        $user->password = hash('sha256', $newPass);
        if (!empty($user->save())) {
            return $this->json(Response::success($user->id));
        } else {
            return $this->json(Response::unknownError());
        }
    }
}