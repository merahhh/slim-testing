<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Slim\App;
use utility\Session;
require_once "../v1/model/User.php";
require_once "../v1/library/Session.php";
require_once "../v1/SQLpost.php";

require '../vendor/autoload.php';
$app = new App(['settings' => ['displayErrorDetails' => true]]);
$container = $app->getContainer();

$container['Session'] = function($container) {
    $session = new Session();
    return $session;
};

$container['User'] = function($container) {
    $userInfo = new User();
    return $userInfo;
};

$container['SQLpost'] = function($container) {
    $session = $container->get('Session');
    $controller = new SQLpost($session);
    return $controller;
};

require('../v1/posts.php');

// Run app
$app->run();
