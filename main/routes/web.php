<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api/v1/'], function () use ($router) {
    Route::get('technicals/get_super_breakout_stock_list', [
        'as' => 'getSuperBreakoutStockList', 'uses' => 'MovingAverageController@getSuperBreakoutStockList'
    ]);
});