<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Slim\App;
use utility\Session;
require_once dirname(__FILE__) . "/../v1/model/User.php";
require_once dirname(__FILE__) . "/../v1/library/Session.php";
require_once dirname(__FILE__) . "/../v1/model/Guestbook.php";
require_once dirname(__FILE__) . "/../v1/controller/SQLpost.php";
require_once dirname(__FILE__) . "/../v1/controller/SQLuser.php";
//require '../vendor/autoload.php';

/**
 * Path problem hack - applicable to using Vagrant in rsync mode (with fucked up symlinks).
 */
$autoloadPath = dirname(__FILE__) . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    $autoloadPath = dirname(__FILE__) . '/../vendor/codeception/codeception/autoload.php';
}
require_once $autoloadPath;

$app = new App(['settings' => ['displayErrorDetails' => true]]);
$container = $app->getContainer();

$container['Session'] = function () {
    $session = new Session();
    return $session;
};

$container['User'] = function () {
    $userInfo = new User();
    return $userInfo;
};

$container['Guestbook'] = function () {
    $guestbook = new Guestbook();
    return $guestbook;
};

$container['SQLpost'] = function ($container) {
    $session = $container->get('Session');
    $user = $container->get('User');
    $guestbook = $container->get('Guestbook');
    $controller = new SQLpost($session, $user, $guestbook);
    return $controller;
};

$container['SQLuser'] = function ($container) {
    $session = $container->get('Session');
    $user = $container->get('User');
    $controller = new SQLuser($session, $user);
    return $controller;
};

require_once dirname(__FILE__) . "/../v1/routes.php";

// Run app
$app->run();
