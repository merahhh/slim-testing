<?php
require dirname(__FILE__) . '/../vendor/autoload.php';

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Container;

$container = new Container([
    App::class => function (ContainerInterface $c) {
        $app = new App($c);

        // routes and middlewares here
        $app->group('/v1', function (App $app) {

            #lists all posts
            $app->get('/posts', SQLpost::class. ':getPosts');

            #create post
            $app->post('/posts/create', SQLpost::class. ':createPost');

            #delete post
            $app->delete('/posts/delete/{id}', SQLpost::class. ':deletePost');

            #edit post
            $app->put('/posts/edit/{id}', SQLpost::class. ':editPost');

            #login user
            $app->post('/users/login', SQLuser::class. ':loginUser');

            #register user
            $app->post('/users/register', SQLuser::class. ':registerUser');

            #logout user
            $app->post('/users/logout', SQLuser::class. ':logoutUser');

            #delete user
            $app->delete('/users/delete/{id}', SQLuser::class. ':deleteUser');

        });
    return $app;
    }
]);

return $container;