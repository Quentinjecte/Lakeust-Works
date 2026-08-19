<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/three-test', function () {
    return view('three-test');
});

Route::get('/welcome-cinematic', function () {
    return view('pages.welcome-cinematic', [
        'homeUrl' => route('welcome'),
    ]);
})->name('welcome.cinematic');

Route::get('/forest-cinematic', function () {
    return view('pages.forest-cinematic', [
        'homeUrl' => route('welcome'),
    ]);
})->name('forest.cinematic');

Route::get('/a-propos', function () {
    return view('pages.about');
})->name('about');

Route::get('/travaux', function () {
    return view('pages.works');
})->name('works');

Route::get('/projet', function () {
    return view('pages.project');
})->name('project');

Route::get('/laboratoire', function () {
    return view('pages.lab');
})->name('lab');

/* Pages secondaires — HTML/CSS/JS vanilla. 
Route::view('/a-propos', 'pages.about')->name('about');
Route::view('/travaux', 'pages.works')->name('works');
Route::view('/projet', 'pages.project')->name('project');
*/
