<?php

namespace app\controller;

use app\model\Sharing;
use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

class Note extends BLController
{
    public function get()
    {
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
            // ->orderBy('updated desc')
            ->get();
        return $this->json(Response::success($notes));
    }

    /**
     * @param $note
     * @return int, 0: no permission, 1: owner, 2: readonly shared, 3: editable shared
     */
    private function checkPermission($note)
    {
        if (empty($note)) {
            return 0;
        }
        if ($note->userid != AuthToken::getId()) {
            $sharing = Sharing::query()->where('noteid', $note->id)->where('touserid', AuthToken::getId())->get();
            if (!empty($sharing)) {
                return 2 + $sharing[0]->permission;
            } else {
                return 0;
            }
        }
        return 1;
    }

    public function single()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        $permission = $this->checkPermission($note);
        if ($permission > 0) {
            return $this->json(Response::success(array_merge($note->data(), ['share' => $permission >= 2])));
        } else {
            return $this->json(Response::error('Note does not exist'));
        }
    }

    public function poll()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $lastUpdate = BLRequest::bodyJson('lastUpdate', 0);
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        $permission = $this->checkPermission($note);
        if ($permission > 0) {
            if ($note->updated > $lastUpdate) {
                return $this->json(Response::success($note));
            } else {
                return $this->json(Response::success(false));
            }
        } else {
            return $this->json(Response::error('Note does not exist'));
        }
    }

    public function create()
    {
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

    public function delete()
    {
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

    public function updatecontent()
    {
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

    public function updatetitle()
    {
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