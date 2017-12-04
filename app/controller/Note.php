<?php

namespace app\controller;

use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

class Note extends BLController
{
    public function get() {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $nbid = BLRequest::bodyJson('nbid');
        if (is_null($nbid)) {
            return $this->json(Response::error('Notebook id not given'));
        }
        $notes = \app\model\Note::query()
            ->fields(['id', 'title', 'updated'])
            ->where('nbid', $nbid)
            ->where('userid', AuthToken::getId())
            ->orderBy('updated desc')
            ->get();
        return $this->json(Response::success($notes));
    }

    public function create() {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $nbid = BLRequest::bodyJson('nbid');
        $newId = \app\model\Note::insert([
            'nbid' => $nbid,
            'userid' => AuthToken::getId()
        ]);
        if (empty($newId)) {
            return $this->json(Response::unknownError());
        }
        return $this->json(Response::success($newId));
    }
}