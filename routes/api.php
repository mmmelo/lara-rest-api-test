<?php


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
//ini_set('max_execution_time', '600');

Route::resource('portingScores', 'Porting\ScoresController');
Route::resource('portingLog', 'PortingLog\PortingLogController');


Route::post('search_prediction_school','Groups@search_prediction');

Route::post('register', 'User\UserController@register');
Route::post('login', 'User\UserController@login');


Route::group(['middleware' => ['auth:api']], function(){
    Route::get( 'logout', 'User\UserController@logout');
    Route::get( 'login', 'User\UserController@index');
    Route::post( 'setUserStatus', 'User\UserController@setUserStatus');

    Route::post('porting/getPorting',       'Porting\PortingController@getPorting');
    Route::post('porting/setAmArea',        'Porting\PortingController@setAmArea');
    Route::post('porting/updAmArea',        'Porting\PortingController@updAmArea');
    Route::post('porting/delAmArea',        'Porting\PortingController@delAmArea');

    Route::post('porting/scores/import',    'Porting\ScoresController@import');
    Route::post('porting/listScores',       'Porting\ScoresController@listScores');

    Route::post('porting/schedule/import',  'Porting\ScheduleController@import');

    Route::post('schools/getRatioSchool',   'Schools\SchoolsMapping@getRatioSchool');
    Route::post('schools/getAm',            'Schools\SchoolsMapping@getAreaManagement');
    Route::post('schools/addSchoolToAm',    'Schools\SchoolsMapping@addSchoolToAm');
    Route::post('schools/delSchoolToAm',    'Schools\SchoolsMapping@delSchoolToAm');
    Route::post('schools/removeSchoolMapping', 'Schools\SchoolsMapping@removeSchoolMapping');

    Route::get('tools/createColorsAmAreas', 'Tools\ToolsController@createColorsAmAreas');

    Route::resource('states',               'States\StatesController');
    Route::resource('sports_type',          'Sports\SportTypeController');

    Route::post('schedules/find_schedules', 'Schedules\ScheduleController@find_schedule');

    Route::post('dropzone/upload',          'Dropzone\Upload@dropzone');

    //Articles

    Route::get('articles/listAll',          'NewsArticle\Articles@index');
    Route::post('articles/media',           'NewsArticle\Articles@getMedia');
    Route::post('articles/saveArticle',     'NewsArticle\Articles@createArticle');

    //Media

    Route::post('media/getmedia',           'Media\MediaController@find');
    Route::post('media/cropped',            'Media\MediaController@saveImageCropped');


});


Route::get('echo', 'TestApi\TestApiController@echo');



