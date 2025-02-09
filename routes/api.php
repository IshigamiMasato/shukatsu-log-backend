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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => ['cors']], function () use ($router) {
    $router->options('/{any:.*}', function () use ($router) {
        // クロスドメイン環境からのOPTIONSメソッド許可用のルーティング
    });
});

$router->group(['middleware' => ['cors']], function () use ($router) {
    $router->post('/login', ['uses' => 'AuthController@login']);
    $router->post('/token/refresh', ['uses' => 'AuthController@refreshToken']);

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('/logout', ['uses' => 'AuthController@logout']);
        $router->get('/auth/check', ['uses' => 'AuthController@checkAuth']);
        $router->get('/user', ['uses' => 'UserController@show']);

        $router->group(['prefix' => 'event'], function () use ($router) {
            $router->get('/', ['uses' => 'EventController@index']);
            $router->post('/', ['uses' => 'EventController@store']);
            $router->put('/{eventId}', ['uses' => 'EventController@update']);
            $router->delete('/{eventId}', ['uses' => 'EventController@delete']);
        });

        $router->group(['prefix' => 'company'], function () use ($router) {
            $router->get('/', ['uses' => 'CompanyController@index']);
            $router->get('/{companyId}', ['uses' => 'CompanyController@show']);
            $router->post('/', ['uses' => 'CompanyController@store']);
            $router->put('/{companyId}', ['uses' => 'CompanyController@update']);
            $router->delete('/{companyId}', ['uses' => 'CompanyController@delete']);
        });
    });
});
