<?php

use Illuminate\Support\Facades\Route;


// Language switch Route
Route::get('locale/{locale}','LanguageController@swap')->name('locale');


// Auth::routes();
Auth::routes([
  'register' => false, // Registration Routes...
  'reset' => false, // Password Reset Routes...
  'verify' => false, // Email Verification Routes...
]);

Route::get('/no-approval', 'HomeController@approval')->name('approval')->middleware('auth');

Route::group(['middleware' => ['auth']], function () {

    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/uplaoddb', 'HomeController@uplaoddb')->name('uplaoddb');

	Route::get('/setting', 'SettingController@index')->name('setting.index')->middleware('can:Setting Index');
	Route::put('/setting/update', 'SettingController@update')->name('setting.update')->middleware('can:Setting Index');

    require __DIR__.'/user-management.php';

    require __DIR__.'/invoice-management.php';

    require __DIR__.'/prescription-management.php';

    require __DIR__.'/labor-management.php';
    
    require __DIR__.'/echo-management.php';

    require __DIR__.'/patient-management.php';
    
    require __DIR__.'/doctor-management.php';
    
    require __DIR__.'/medicine-management.php';
    
    require __DIR__.'/location-management.php';

});


