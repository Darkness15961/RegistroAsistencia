<?php
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('welcome'); // tu blade donde está el #app
})->where('any', '.*');
