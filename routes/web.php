<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/poli', function () {
    return view('poli.index');
});

Route::get('/poli/tambah', function () {
    return view('poli.add');
});
