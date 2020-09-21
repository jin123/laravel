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
    $router->any('/mockBrand/index/create', 'MockBrandController@createBrand')->name('admin.mockBrand.create');
    $router->any('/mockBrand/add', 'MockBrandController@add')->name('admin.mockBrand.add');
    $router->any('/mockBrand/index', 'MockBrandController@index')->name('admin.mockBrand.index');
    $router->any('/mockBrand/addOpt', 'MockBrandController@addRes')->name('admin.mockBrand.addOpt');
    $router->post('/mockBrand/post', 'MockBrandController@post')->name('admin.mockBrand.addOpt');
    $router->any('/mockBrand/index/{id}', 'MockBrandController@rowsUpdate')->name('admin.mockBrand.rowsUpdate');
    $router->get('/mockBrand/editBrand', 'MockBrandController@editBrand')->name('admin.mockBrand.editBrand');
    $router->post('/mockBrand/editBrandOpt', 'MockBrandController@editBrandOpt')->name('admin.mockBrand.editBrandOpt');
    $router->get('/mock/index', 'MockController@index')->name('admin.mock.index');
    $router->any('/mock/index/{id}', 'MockController@rowsUpdate')->name('admin.mock.rowsUpdate');
    $router->get('/mock/editMockLink', 'MockController@editMockLink')->name('admin.mock.editMockLink');
    $router->get('/mock/index/create', 'MockController@add')->name('admin.mock.add');
    $router->post('/mock/addLink', 'MockController@addLink')->name('admin.mock.addLink');
    $router->post('/mock/editOpt', 'MockController@editOpt')->name('admin.mock.editOpt');
});
