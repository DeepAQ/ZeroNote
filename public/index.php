<?php

/**
 * 应用入口文件
 */

error_reporting(E_WARNING | E_ERROR);
// 定义根目录
define('APP_ROOT', __DIR__ . '/../app/');

// 加载启动器
require_once '../vendor/autoload.php';
\BestLang\core\BLApp::start();
