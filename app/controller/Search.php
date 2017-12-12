<?php

namespace app\controller;

use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

class Search extends BLController
{
    public function aggregate()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $userid = AuthToken::getId();
        $kw = BLRequest::bodyJson('kw');
        $notebooks = \app\model\Notebook::query()
            ->where('userid', $userid)
            ->where('name', 'like', '%' . $kw . '%')
            ->fields(['id', 'name'])
            ->get();
        $notes = \app\model\Note::query()
            ->where('userid', $userid)
            ->where('title', 'like', '%' . $kw . '%')
            ->fields(['id', 'nbid', 'title'])
            ->get();
        $notesFullText = \app\model\Note::query()
            ->where('userid', $userid)
            ->where('content', 'like', '%' . $kw . '%')
            ->fields(['id', 'nbid', 'title'])
            ->get();
        return $this->json(Response::success(['notebooks' => $notebooks, 'notes' => $notes, 'notesFullText' => $notesFullText]));
    }
}