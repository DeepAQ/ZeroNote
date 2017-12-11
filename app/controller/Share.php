<?php

namespace app\controller;

use app\model\Sharing;
use app\model\User;
use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

class Share extends BLController
{
    public function single()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $noteid = BLRequest::bodyJson('noteid');
        $note = \app\model\Note::get($noteid);
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('note does not exist'));
        }
        $shares = Sharing::query()->where('noteid', $noteid)->get();
        if (empty($shares)) {
            return $this->json(Response::success(['public' => $note->public]));
        }
        $userids = [];
        foreach ($shares as $share) {
            $userids[] = $share->touserid;
        }
        $useridMap = [];
        foreach (User::query()->fields(['id', 'email', 'nickname'])
                     ->whereRaw('id IN (' . join(',', $userids) . ')')->get() as $user) {
            $useridMap[$user->id] = $user;
        }
        $result = [];
        foreach ($shares as $share) {
            $result[] = array_merge($share->data(), ['touser' => $useridMap[$share->touserid]]);
        }
        return $this->json(Response::success(['public' => $note->public, 'shared' => $result]));
    }

    public function tome()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $shares = Sharing::query()->where('touserid', AuthToken::getId())->get();
        if (empty($shares)) {
            return $this->json(Response::success([]));
        }
        $noteids = [];
        foreach ($shares as $share) {
            $noteids[] = $share->noteid;
        }
        $notes = \app\model\Note::query()->fields(['id', 'title'])
            ->whereRaw('id IN (' . join(',', $noteids) . ')')->get();
        return $this->json(Response::success($notes));
    }

    public function setpublic()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $noteid = BLRequest::bodyJson('noteid');
        $note = \app\model\Note::get($noteid);
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('note does not exist'));
        }
        $note->public = BLRequest::bodyJson('public') ? 1 : 0;
        if (!empty($note->save())) {
            return $this->json(Response::success($noteid));
        } else {
            return $this->json(Response::unknownError());
        }
    }

    public function add()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $noteid = BLRequest::bodyJson('noteid');
        $note = \app\model\Note::get($noteid);
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('note does not exist'));
        }
        $email = BLRequest::bodyJson('email');
        $user = User::query()->where('email', $email)->get();
        if (empty($user)) {
            return $this->json(Response::error('User does not exist'));
        }
        if (Sharing::query()->where('noteid', $noteid)->where('touserid', $user[0]->id)->count() > 0) {
            return $this->json(Response::error('Already shared to this user'));
        }
        $newId = Sharing::insert([
            'noteid' => $noteid,
            'touserid' => $user[0]->id,
            'permission' => BLRequest::bodyJson('editable') ? 1 : 0
        ]);
        if (empty($newId)) {
            return $this->json(Response::unknownError());
        } else {
            return $this->json(Response::success($newId));
        }
    }

    public function revoke()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $share = Sharing::get(BLRequest::bodyJson('id'));
        if (empty($share)) {
            return $this->json(Response::error('sharing does not exist'));
        }
        $note = \app\model\Note::get($share->noteid);
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('sharing does not exist'));
        }
        if ($share->remove()) {
            return $this->json(Response::success($share->id));
        } else {
            return $this->json(Response::unknownError());
        }
    }
}