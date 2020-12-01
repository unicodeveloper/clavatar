<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Support\Str;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('key', function () {
     return Str::random(32);
});

$router->get('hash', function () {

    $email = 'prosperotemuyiwa@gmail.com';

    return md5(strtolower(trim($email)));

});

$router->post('users', 'UserController@createUser');
$router->post('login', 'UserController@loginUser');

$router->get('{hash}', 'UserController@getProfile');
$router->get('avatar/{hash}', 'AvatarController@retrieveAvatar');

$router->post('upload', 'AvatarController@uploadImage');