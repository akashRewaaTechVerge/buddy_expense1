<?php

use Illuminate\Http\Request;
use App\http\controllers\UserController;
use Illuminate\Support\Facades\Route;  


$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/register', 'UserController@register');
    $router->post('/login', 'UserController@login');
});