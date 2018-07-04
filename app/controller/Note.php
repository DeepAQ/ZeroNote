<?php

namespace app\controller;

use app\model\Cursor;
use app\model\Sharing;
use app\model\Upvote;
use app\model\User;
use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;
use DiffMatchPatch\DiffMatchPatch;

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
     * @return int, 0: no permission, 1: readonly shared, 2: editable shared, 3: owner
     */
    private function checkPermission($note)
    {
        if (empty($note)) {
            return 0;
        }
        if ($note->userid != AuthToken::getId()) {
            $sharing = Sharing::query()->where('noteid', $note->id)->where('touserid', AuthToken::getId())->get();
            if (!empty($sharing)) {
                return 1 + $sharing[0]->permission;
            } else if ($note->public == 1) {
                return 1;
            } else {
                return 0;
            }
        }
        return 3;
    }

    private function updateCursor($note, $pos)
    {
        if (empty($note)) {
            return false;
        }
        $cursor = Cursor::query()->where('noteid', $note->id)->where('userid', AuthToken::getId())->get();
        if (empty($cursor)) {
            $cursor = new Cursor([
                'noteid' => $note->id,
                'userid' => AuthToken::getId()
            ]);
        } else {
            $cursor = $cursor[0];
        }
        $cursor->pos = $pos;
        $cursor->updated = time();
        return $cursor->save();
    }

    public function single()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        $permission = $this->checkPermission($note);
        if ($permission > 0) {
            $upvotes = Upvote::query()->where('noteid', $note->id)->count();
            $upvoted = Upvote::query()->where('noteid', $note->id)->where('userid', AuthToken::getId())->count();
            return $this->json(Response::success(array_merge($note->data(), [
                'upvotes' => $upvotes,
                'upvoted' => $upvoted > 0,
                'permission' => $permission
            ])));
        } else {
            return $this->json(Response::error('Note does not exist'));
        }
    }

    public function poll()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        $permission = $this->checkPermission($note);
        if ($permission > 0) {
            if ($permission >= 2) {
                $curPos = BLRequest::bodyJson('cursor');
                if (!is_null($curPos)) {
                    $this->updateCursor($note, $curPos);
                }
            }
            $cursors = Cursor::query()
                ->fields(['userid', 'pos'])
                ->where('noteid', $note->id)
                ->where('userid', '<>', AuthToken::getId())
                ->where('updated', '>', time() - 10)
                ->orderBy('pos')
                ->get();
            $cursorsResult = [];
            foreach ($cursors as $cursor) {
                $cursorsResult[] = [
                    'email' => User::get($cursor->userid)->email,
                    'pos' => $cursor->pos
                ];
            }
            $dmp = new DiffMatchPatch();
            return $this->json(Response::success([
                'patch' => $dmp->patch_toText($dmp->patch_make(BLRequest::bodyJson('content', ''), $note->content)),
                'cursors' => $cursorsResult
            ]));
        } else {
            return $this->json(Response::error('Note does not exist'));
        }
    }

    public function create()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $nbid = BLRequest::bodyJson('nbid', 0);
        $newId = \app\model\Note::insert([
            'nbid' => $nbid,
            'userid' => AuthToken::getId(),
            'title' => BLRequest::bodyJson('title'),
            'content' => BLRequest::bodyJson('content')
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
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        if ($this->checkPermission($note) < 3) {
            return $this->json(Response::error('Note does not exist'));
        }
        if (!empty($note->remove())) {
            return $this->json(Response::success($note->id));
        } else {
            return Response::unknownError();
        }
    }

//    public function updatecontent()
//    {
//        if (empty(AuthToken::getId())) {
//            return $this->json(Response::notLoggedIn());
//        }
//        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
//        if ($this->checkPermission($note) < 2) {
//            return $this->json(Response::error('No permission'));
//        }
//        $note->content = BLRequest::bodyJson('content');
//        $note->updated = time();
//        if ($note->save() > 0) {
//            return $this->json(Response::success($note->id));
//        } else {
//            return Response::unknownError();
//        }
//    }

    public function patch()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        if ($this->checkPermission($note) < 2) {
            return $this->json(Response::error('No permission'));
        }
        $curPos = BLRequest::bodyJson('cursor');
        if (!is_null($curPos)) {
            $this->updateCursor($note, $curPos);
        }
        $dmp = new DiffMatchPatch();
        $note->content = $dmp->patch_apply($dmp->patch_fromText(BLRequest::bodyJson('patch')), $note->content)[0];
        $note->updated = time();
        if ($note->save() > 0) {
            return $this->json(Response::success(null));
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
        if ($this->checkPermission($note) < 2) {
            return $this->json(Response::error('No permission'));
        }
        $note->title = BLRequest::bodyJson('title');
        $note->updated = time();
        if ($note->save() > 0) {
            return $this->json(Response::success($note->id));
        } else {
            return Response::unknownError();
        }
    }

    public function updatetags()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $note = \app\model\Note::get(BLRequest::bodyJson('id'));
        if ($this->checkPermission($note) < 2) {
            return $this->json(Response::error('No permission'));
        }
        $note->tags = BLRequest::bodyJson('tags');
        $note->updated = time();
        if ($note->save() > 0) {
            return $this->json(Response::success($note->id));
        } else {
            return Response::unknownError();
        }
    }

    public function updownvote()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $noteId = BLRequest::bodyJson('noteid');
        $upvote = Upvote::query()->where('noteid', $noteId)->where('userid', AuthToken::getId())->get();
        $count = Upvote::query()->where('noteid', $noteId)->count();
        if (!empty($upvote)) {
            if (!empty($upvote[0]->remove())) {
                return $this->json(Response::success(['upvoted' => false, 'upvotes' => $count - 1]));
            } else {
                return $this->json(Response::unknownError());
            }
        } else {
            if (!empty(Upvote::insert([
                'userid' => AuthToken::getId(),
                'noteid' => $noteId
            ]))) {
                return $this->json(Response::success(['upvoted' => true, 'upvotes' => $count + 1]));
            } else {
                return $this->json(Response::unknownError());
            }
        }
    }
}