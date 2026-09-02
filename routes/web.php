<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/blackhole', function () {
    return view('pages.web.cinematic.blackhole');
})->name('blackhole');

Route::get('/blackhole-cinematic', function () {
    return view('pages.web.cinematic.blackhole-cinematic', [
        'homeUrl' => route('welcome'),
    ]);
})->name('welcome.cinematic');

Route::get('/home-cinematic', function () {
    return view('pages.web.cinematic.orbital-cinematic', [
        'homeUrl' => route('welcome'),
    ]);
})->name('home.cinematic');

Route::get('/forest-cinematic', function () {
    return view('pages.web.cinematic.forest-cinematic', [
        'homeUrl' => route('welcome'),
    ]);
})->name('forest.cinematic');



//======================
// WEB
//======================
Route::get('/web-a-propos', function () {
    return view('pages.web.about');
})->name('web.about');

Route::get('/web-travaux', function () {
    return view('pages.web.works');
})->name('web.works');

Route::get('/web-projet', function () {
    return view('pages.web.project');
})->name('web.project');

Route::get('/web-lab', function () {
    return view('pages.web.lab');
})->name('web.lab');

Route::get('/mentions-legales', function () {
    return view('pages.legal');
})->name('legal');



//======================
/* STUDIO*/
//======================
Route::get('/studio-a-propos', function () {
    return view('pages.studio.about');
})->name('studio.about');

Route::get('/studio-travaux', function () {
    return view('pages.studio.works');
})->name('studio.works');

Route::get('/studio-lab', function () {
    return view('pages.studio.lab');
})->name('studio.lab');

Route::get('/studio-projet01', function () {
    return view('pages.studio.project.project01');
})->name('studio.project01');

Route::get('/studio-projet02', function () {
    return view('pages.studio.project.project02');
})->name('studio.project02');



//======================
// LABS
//======================
Route::get('/scroll-lab', function () {
    return view('pages.web.labs.scoll-lab');
})->name('scroll.lab');

Route::get('/three-lab', function () {
    return view('pages.web.labs.three-lab');
})->name('three.lab');

Route::get('/barba-lab', function () {
    return view('pages.web.labs.barba-lab');
})->name('barba.lab');

Route::get('/animation-lab', function () {
    return view('pages.web.labs.animation-lab');
})->name('animation-lab');

Route::get('/carousel-lab', function () {
    return view('pages.web.labs.carousel-lab');
})->name('carousel.lab');

Route::get('/visual-effect', function () {
    return view('pages.studio.labs.visual-effect');
})->name('visual-effect.lab');

Route::get('/csharp-code', function () {
    return view('pages.studio.labs.csharp-code');
})->name('csharp-code.lab');


/* Pages secondaires — HTML/CSS/JS vanilla.
Route::view('/a-propos', 'pages.about')->name('about');
Route::view('/travaux', 'pages.works')->name('works');
Route::view('/projet', 'pages.project')->name('project');
Route::get('/three-test', function () {
    return view('three-test');
});

*/
