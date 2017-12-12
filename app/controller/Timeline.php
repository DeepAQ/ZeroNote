<?php

namespace app\controller;

use app\model\Follow;
use app\model\Upvote;
use app\model\User;
use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\model\BLSql;
use BestLang\core\util\BLRequest;

class Timeline extends BLController
{
    public function fetch()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $followings = User::query()->fields(['id', 'email', 'nickname'])
            ->whereRaw('id in (SELECT touserid FROM follow WHERE fromuserid = ?)', [AuthToken::getId()])->get();
        if (empty($followings)) {
            return $this->json(Response::success([]));
        }
        $userIds = [];
        $userMap = [];
        foreach ($followings as $following) {
            $userIds[] = $following->id;
            $userMap[$following->id] = $following;
        }
        $notes = \app\model\Note::query()->fields(['id', 'userid', 'title', 'updated'])
            ->whereRaw('userid IN (' . join(',', $userIds) . ')')
            ->where('public', 1)
            ->orderBy('updated desc')
            ->get();
        $result = [];
        foreach ($notes as $note) {
            $result[] = array_merge($note->data(), ['user' => $userMap[$note->userid]]);
        }
        return $this->json(Response::success($result));
    }

    public function followers()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $followers = User::query()->fields(['email', 'nickname'])
            ->whereRaw('id in (SELECT fromuserid FROM follow WHERE touserid = ?)', [AuthToken::getId()])->get();
        return $this->json(Response::success($followers));
    }

    public function followings()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $followings = User::query()->fields(['id', 'email', 'nickname'])
            ->whereRaw('id in (SELECT touserid FROM follow WHERE fromuserid = ?)', [AuthToken::getId()])->get();
        return $this->json(Response::success($followings));
    }

    public function follow()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $toEmail = BLRequest::bodyJson('email');
        $user = User::query()->where('email', $toEmail)->get();
        if (empty($user)) {
            return $this->json(Response::error('User does not exist'));
        }
        if (!empty(Follow::query()->where('fromuserid', AuthToken::getId())->where('touserid', $user[0]->id)->get())) {
            return $this->json(Response::error('already followed this user'));
        }
        if (!empty(Follow::insert(['fromuserid' => AuthToken::getId(), 'touserid' => $user[0]->id]))) {
            return $this->json(Response::success(null));
        } else {
            return $this->json(Response::unknownError());
        }
    }

    public function unfollow()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $userId = BLRequest::bodyJson('userid');
        $follow = Follow::query()->where('fromuserid', AuthToken::getId())->where('touserid', $userId)->get();
        if (empty($follow)) {
            return $this->json(Response::error('You have not followed this user'));
        }
        if (!empty($follow[0]->remove())) {
            return $this->json(Response::success(null));
        } else {
            return $this->json(Response::unknownError());
        }
    }

    public function leaderboard()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        $noteUpvotes = BLSql::exec('SELECT noteid, count(1) upvotes FROM upvote GROUP BY noteid ORDER BY upvotes DESC')->fetchAll();
        if (empty($noteUpvotes)) {
            return $this->json(Response::success([]));
        }
        $noteids = [];
        $upvoteMap = [];
        foreach ($noteUpvotes as $noteUpvote) {
            $noteids[] = $noteUpvote['noteid'];
            $upvoteMap[$noteUpvote['noteid']] = $noteUpvote['upvotes'];
        }
        $notes = \app\model\Note::query()->fields(['id', 'userid', 'title', 'updated'])
            ->whereRaw('id IN (' . join(',', $noteids) . ')')
            ->get();
        $result = [];
        foreach ($notes as $note) {
            $result[] = array_merge($note->data(), ['upvotes' => $upvoteMap[$note->id]]);
        }
        return $this->json(Response::success($result));
    }
}