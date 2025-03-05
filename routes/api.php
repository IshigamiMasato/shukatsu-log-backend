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

        $router->group(['prefix' => 'apply'], function () use ($router) {
            $router->get('/status-summary', ['uses' => 'ApplyController@getStatusSummary']);
            $router->get('/', ['uses' => 'ApplyController@index']);
            $router->get('/{applyId}', ['uses' => 'ApplyController@show']);
            $router->post('/', ['uses' => 'ApplyController@store']);
            $router->put('/{applyId}', ['uses' => 'ApplyController@update']);
            $router->delete('/{applyId}', ['uses' => 'ApplyController@delete']);

            $router->group(['prefix' => '{applyId}'], function () use ($router) {
                $router->group(['prefix' => '/document'], function () use ($router) {
                    $router->get('/{documentId}', ['uses' => 'DocumentController@show']);
                    $router->post('/', ['uses' => 'DocumentController@store']);
                    $router->put('/{documentId}', ['uses' => 'DocumentController@update']);
                    $router->delete('/{documentId}', ['uses' => 'DocumentController@delete']);
                });

                $router->group(['prefix' => '/exam'], function () use ($router) {
                    $router->get('/{examId}', ['uses' => 'ExamController@show']);
                    $router->post('/', ['uses' => 'ExamController@store']);
                    $router->put('/{examId}', ['uses' => 'ExamController@update']);
                    $router->delete('/{examId}', ['uses' => 'ExamController@delete']);
                });

                $router->group(['prefix' => '/interview'], function () use ($router) {
                    $router->get('/{interviewId}', ['uses' => 'InterviewController@show']);
                    $router->post('/', ['uses' => 'InterviewController@store']);
                    $router->put('/{interviewId}', ['uses' => 'InterviewController@update']);
                    $router->delete('/{interviewId}', ['uses' => 'InterviewController@delete']);
                });

                $router->group(['prefix' => '/offer'], function () use ($router) {
                    $router->get('/{offerId}', ['uses' => 'OfferController@show']);
                    $router->post('/', ['uses' => 'OfferController@store']);
                    $router->put('/{offerId}', ['uses' => 'OfferController@update']);
                    $router->delete('/{offerId}', ['uses' => 'OfferController@delete']);
                });

                $router->group(['prefix' => '/final_result'], function () use ($router) {
                    $router->get('/{finalResultId}', ['uses' => 'FinalResultController@show']);
                    $router->post('/', ['uses' => 'FinalResultController@store']);
                    $router->put('/{finalResultId}', ['uses' => 'FinalResultController@update']);
                    $router->delete('/{finalResultId}', ['uses' => 'FinalResultController@delete']);
                });

                $router->get('/process', ['uses' => 'ApplyController@getProcess']);
            });
        });
    });
});
