<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/openapi.json', function () {
    return response(file_get_contents(storage_path('api-docs/api-docs.json')), 200, [
        'Content-Type' => 'application/json',
    ]);
});
