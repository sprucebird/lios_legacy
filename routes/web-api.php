<?php



Route::post('/vapi/viber', 'ViberController@respond');
Route::get('/vapi/viber', 'ViberController@setweb');
Route::get('vapi/reload', 'ViberController@callReload');
Route::get('vapi/fuel', 'ViberController@setFuel');
Route::post('/vapi/fuel', 'ViberController@fuelRespond');
