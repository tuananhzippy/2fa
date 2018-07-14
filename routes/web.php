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
Route::get('/settings', 'Auth\SettingController@index')->name('settings');
Route::post('/settings', 'Auth\SettingController@store')->name('change2fa');
Route::get('/otp-verify', 'Auth\LoginController@otpVerify')->name('otpVerify');
Route::post('/otp-verify', 'Auth\LoginController@handleOtpVerify')->name('handleOtpVerify');
Route::get('/token-verify', 'Auth\LoginController@tokenVerify')->name('tokenVerify');
Route::post('/token-verify', 'Auth\LoginController@handleTokenVerify')->name('handleTokenVerify');
