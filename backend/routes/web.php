<?php

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

$router->group(['prefix' => 'api/v1/'], function () use ($router) {
    $router->get('technicals/get_super_breakout_stock_list', [
        'as' => 'getSuperBreakoutStockList', 'uses' => 'MovingAverageController@getSuperBreakoutStockList'
    ]);

    $router->get('technicals/get_stock_list_by_vol_and_dp', [
        'as' => 'getSuperBreakoutStockList', 'uses' => 'MovingAverageController@getStockListByVolAndDP'
    ]);

    $router->get('technicals/single_moving_avg/{period}', [
        'as' => 'getStockListBySingleMovingAvg', 'uses' => 'MovingAverageController@getStockListBySingleMovingAvg'
    ]);
});
