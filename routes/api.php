<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix("user")->group(function(){
    Route::namespace("V1\Auth")->group(function(){
        Route::post("/login", 'LoginController@index');
        Route::get('/login', 'LoginController@unauthenticatedResponse')->name('login');

        Route::prefix("registration")->group(function(){
            Route::post("/create", 'RegistrationController@create');
        });
    });
});

Route::group(['middleware' => ['auth:api']], function () {

    Route::prefix('course')->group(function () {
            Route::get('/create', 'V1\CourseController@create');
            Route::post('/register', 'V1\CourseController@register');
            Route::get('/registered', 'V1\CourseController@all');
            Route::get('/export', 'V1\CourseController@export');
    });
});
