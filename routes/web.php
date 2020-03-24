<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/doc', function() { return view('vendor.l5-swagger.index'); } )->name('doc');




//Dropzone Routes

Route::get('/drop_zone','Filesystem@index');
Route::get('/drop_zone/confirmation/{id}','Schedule@media_added');
Route::post('/upload_file','Filesystem@file_upload')->name('file.upload');
Route::post('/upload/dropzone','Filesystem@dropzone')->name('dropzone');

//
//Schedules
Route::get('schedule','Schedule@index');
Route::get('schedule/find/{id}','Schedule@find');
Route::get('schedule/find_v2','Schedule@find_v2');
Route::get('schedule_filter','Schedule@find_schedule');
Route::post('schedule/save_score','Schedule@save_score');

//Groups
Route::get('groups','Groups@index');
Route::get('groups/find','Groups@find');
Route::get('groups/slug/{slug}','Groups@get_slug');
Route::get('team/search','Groups@team_search');

//Media

Route::get('media','MediaFiles@find');
Route::post('update','MediaFiles@update');

//Autoprediction Typeahead

Route::get('search_prediction_school','Groups@search_prediction');

//Tags

Route::get('tag_feed','Tag_feed@find');
Route::post('tag_post','Tag_feed@add_new');
Route::get('tag_list','Tag_feed@list');
Route::get('tag_find_match','Tag_feed@find_match');

//Qr code

Route::get('qr_code','Qrcode_encoder@test');
Route::get('qr_code/create','Qrcode_encoder@create');

Route::get('test','test@newsArticle');

