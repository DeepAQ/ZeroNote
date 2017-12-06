<?php

namespace app\controller;

use app\util\AuthToken;
use app\util\Response;
use BestLang\core\controller\BLController;
use BestLang\core\util\BLRequest;

class Attachment extends BLController
{
    public function upload()
    {
        if (empty(AuthToken::getId())) {
            return $this->json(Response::notLoggedIn());
        }
        // $filename = BLRequest::bodyJson('filename');
        if (empty($_FILES['file'])) {
            return $this->json(Response::error('no file given'));
        }
        $newName = AuthToken::getId() . '_' . time() . '_' . basename($_FILES['file']['name']);
        $uploadFile = APP_ROOT . '../public/uploads/' . $newName;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            return $this->json(Response::success('http://' . BLRequest::header('Host') . '/uploads/' . $newName));
        } else {
            return $this->json(Response::unknownError());
        }
    }
}