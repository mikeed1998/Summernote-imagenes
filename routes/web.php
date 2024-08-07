<?php

    use App\Http\Controllers\PostController;

    Route::get('/', 'PostController@index');
    Route::get('/create', 'PostController@create');
    Route::post('/store', 'PostController@store');                                                                  
    Route::get('/show/{post}', 'PostController@show');
    Route::get('/edit/{post}', 'PostController@edit');
    Route::post('/update/{post}', 'PostController@update');
    Route::get('/delete/{post}', 'PostController@destroy');


