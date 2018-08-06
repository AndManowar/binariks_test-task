<?php

use Dingo\Api\Routing\Router;

/**@var Router $api */
$api = app(Router::class);

$api->version('v1', ['namespace' => 'App\Api\V1\Http\Controllers'], function (Router $api) {

    // Аутентификация
    $api->group(['prefix' => 'auth'], function (Router $api) {
        $api->post('/login', 'Auth\AuthController@login');
        $api->post('/register', 'Auth\AuthController@register');
        $api->post('/logout', 'Auth\AuthController@logout')->middleware('jwt.auth');
    });

    // Организации
    $api->group(['prefix' => 'organization'], function (Router $api) {
        $api->get('/get-all', 'Organization\OrganizationController@index');
        $api->get('/get/{id}', 'Organization\OrganizationController@get');
        $api->post('/create', 'Organization\OrganizationController@create');
        $api->post('/invite', 'Organization\OrganizationController@inviteToOrganization');
        $api->put('/update/{id}', 'Organization\OrganizationController@update');
        $api->delete('/delete/{id}', 'Organization\OrganizationController@delete');
    });

    // Таски
    $api->group(['prefix' => 'task'], function (Router $api) {
        $api->get('/get-all', 'Task\TaskController@index');
        $api->get('/get/{id}', 'Task\TaskController@get');
        $api->get('/statuses', 'Task\TaskController@statuses');
        $api->post('/create', 'Task\TaskController@create');
        $api->put('/update/{id}', 'Task\TaskController@update');
        $api->put('/change-status/{id}', 'Task\TaskController@changeStatus');
        $api->put('/set-performer/{id}', 'Task\TaskController@setPerformer');
        $api->delete('/delete/{id}', 'Task\TaskController@delete');
    });
});

