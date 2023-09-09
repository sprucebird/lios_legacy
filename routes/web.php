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

Auth::routes();
// Route::get('/app', 'HomeController@index')->name('home');
Route::get('/app/{vue_capture?}', function () {
 return view('home');
})->where('vue_capture', '[\/\w\.-]*');


Route::prefix('/api')->group(function () {
    Route::prefix('/stats')->group(function () {
        Route::get('/transport', 'StatisticsController@get_active_transport');
        Route::get('/reports', 'StatisticsController@get_active_reports');
        Route::post('/reports', 'StatisticsController@get_active_reports');
        Route::get('/reports/{period}', 'StatisticsController@get_active_reports_last_month');
    });

    Route::get('/transport/all', 'TransportController@index')->name('transport.index');
    Route::get('/transport/{id}', 'TransportController@show')->name('transport.show');
    Route::any('/transport/{id}/update', 'TransportController@update')->name('transport.update');
    Route::any('/transport/{id}/remove', 'TransportController@destroy')->name('transport.destroy');
    Route::any('/transport/store', 'TransportController@store')->name('transport.store');
    Route::post("/transport/all/except", "TransportController@except");

    Route::get('/drivers/all', 'DriversController@index');
    Route::post('/drivers/store', 'DriversController@store');
    Route::post('/drivers/{id}/update', 'DriversController@update');
    Route::get('/drivers/{id}', 'DriversController@show');
    Route::post('/drivertable/verify', 'DriversController@verify');

    Route::post('/media/upload/', 'TransportStatusReportController@image')->name('api.media.upload');
    Route::post('/media/delete/', 'TransportStatusReportController@imgDelete');



    // Route::get('/reports/transport/check', 'TransportStatusReportController@index')->name('reports.transport.index');
    Route::get('/reports/transport/check/calendar', 'TransportStatusReportController@calendar');
    Route::post('/reports/transport/check/table/update', 'TransportStatusReportController@tableUpdate');
    Route::get('/reports/transport/check/table', 'TransportStatusReportController@table');



    Route::post('/media/download', 'TransportStatusReportController@download');
    Route::any('/reports/transport/check/store', 'TransportStatusReportController@store')->name('reports.transport.store');
    Route::post('/reports/transport/check/delete', 'TransportStatusReportController@destroy')->name('reports.transport.store');
    Route::post('/reports/transport/check/update', 'TransportStatusReportController@update');
    Route::get('/showtest', 'TransportStatusReportController@test');
    Route::post("/check/filter", "TransportStatusReportController@filter");
    Route::post('/calendarFind/{delbool}', 'TransportStatusReportController@calendarFind');
    Route::post('/media/thumbs', 'TransportStatusReportController@thumbs');

    Route::get('/users', 'UserSessionsController@users');
    Route::get('/users/new/generate', 'UserSessionsController@generateNewUserUrl');
    Route::get('/trag', "TransportStatusReportController@trag");
    Route::any('/system/errors', "DriversController@errors");
    //Route::any('/dev/media/resolution', 'TransportStatusReportController@thumbs');
});


// Route::get('/{vue_capture?}', function () {
//     return view('home');
//  })->where('vue_capture', '[\/\w\.-]*');
