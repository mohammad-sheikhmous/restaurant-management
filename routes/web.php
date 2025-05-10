<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return messageJson('You are logged in');
});
