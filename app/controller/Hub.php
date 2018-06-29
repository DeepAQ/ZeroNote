<?php

namespace app\controller;

use app\model\Upvote;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\model\BLSql;
use BestLang\core\util\BLRequest;

class Hub extends BLController
{
    public function trends() {
        $latest = [];
        $latestNotes = \app\model\Note::query()->fields(['id', 'title', 'updated', 'content'])
            ->where('public', 1)
            ->orderBy('updated desc')
            ->limit(10)
            ->get();
        foreach ($latestNotes as $note) {
            $latest[] = [
                'id' => $note->id,
                'title' => $note->title,
                'updated' => $note->updated,
                'preview' => mb_substr($note->content, 0, 100),
                'upvotes' => Upvote::query()->where('noteid', $note->id)->count()
            ];
        }

        $popular = [];
        $noteUpvotes = BLSql::exec('SELECT noteid, count(1) upvotes FROM upvote GROUP BY noteid ORDER BY upvotes DESC LIMIT 10')->fetchAll();
        $noteids = [];
        $upvoteMap = [];
        foreach ($noteUpvotes as $noteUpvote) {
            $noteids[] = $noteUpvote['noteid'];
            $upvoteMap[$noteUpvote['noteid']] = $noteUpvote['upvotes'];
        }
        $popularNotes = \app\model\Note::query()->fields(['id', 'title', 'updated', 'content'])
            ->whereRaw('id IN (' . join(',', $noteids) . ')')
            ->get();
        $popular = [];
        foreach ($popularNotes as $note) {
            $popular[] = [
                'id' => $note->id,
                'title' => $note->title,
                'updated' => $note->updated,
                'preview' => mb_substr($note->content, 0, 100),
                'upvotes' => $upvoteMap[$note->id]
            ];
        }

        return $this->json(Response::success([
            'latest' => $latest,
            'popular' => $popular
        ]));
    }

    public function search() {
        $kw = BLRequest::bodyJson('kw');
        $notes = \app\model\Note::query()->fields(['id', 'title', 'updated', 'content'])
            ->where('public', 1)
            ->where('title', 'like', '%' . str_replace(' ', '%', $kw) . '%')
            ->orderBy('updated desc')
            ->get();

        $results = [];
        foreach ($notes as $note) {
            $results[] = [
                'id' => $note->id,
                'title' => $note->title,
                'updated' => $note->updated,
                'preview' => mb_substr($note->content, 0, 300),
                'upvotes' => Upvote::query()->where('noteid', $note->id)->count()
            ];
        }

        return $this->json(Response::success($results));
    }
}