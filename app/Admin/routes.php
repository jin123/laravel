<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    'as' => config('admin.route.prefix') . '.',
        ], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/mockBrand/index', 'MockBrandController@index')->name('admin.mockBrand.index');
    $router->get('/mockBrand/editBrand', 'MockBrandController@editBrand')->name('admin.mockBrand.editBrand');
    #$router->get('/mockBrand/index', 'MockBrandController@index')->name('brank_lists');
    $router->get('/mock/index', 'MockController@index')->name('admin.mock.index');
});
