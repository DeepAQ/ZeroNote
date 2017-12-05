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

    public function single() {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('Note does not exist'));
        }
        return $this->json(Response::success($note));
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

    public function delete() {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $id = BLRequest::bodyJson('id');
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('Note does not exist'));
        }
        if (!empty(\app\model\Note::delete($id))) {
            return $this->json(Response::success($note->id));
        } else {
            return Response::unknownError();
        }
    }

    public function updatecontent() {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('Note does not exist'));
        }
        $note->content = BLRequest::bodyJson('content');
        $note->updated = time();
        if ($note->save() > 0) {
            return $this->json(Response::success($note->id));
        } else {
            return Response::unknownError();
        }
    }

    public function updatetitle() {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        if (empty($note) || $note->userid != AuthToken::getId()) {
            return $this->json(Response::error('Note does not exist'));
        }
        $note->title = BLRequest::bodyJson('title');
        $note->updated = time();
        if ($note->save() > 0) {
            return $this->json(Response::success($note->id));
        } else {
            return Response::unknownError();
        }
    }
}