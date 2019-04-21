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


// Route::get('/', function () {
//     return redirect('login');
// });


// Cards
Route::get('cards', 'CardController@list');
Route::get('cards/{id}', 'CardController@show');

// API
Route::put('api/cards', 'CardController@create');
Route::delete('api/cards/{card_id}', 'CardController@delete');
Route::put('api/cards/{card_id}/', 'ItemController@create');
Route::post('api/item/{id}', 'ItemController@update');
Route::delete('api/item/{id}', 'ItemController@delete');

///////////////////////////////////////////////////
///////////////////////////////////////////////////
///////////////////////////////////////////////////

// Ours (Start deleting the above after the template was understood)

Route::get('/', 'HomepageController@display');
Route::view('faq', 'pages.faq')->name('faq');

// Auth
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// Events
Route::get('/event/{id}', 'EventController@show')->where(['id' => '[0-9]+']);
Route::get('/event/create', 'EventController@create');
Route::post('/event/create', 'EventController@store');
Route::put('api/event/enable', 'AdminController@enableEvent');
Route::put('api/event/disable', 'AdminController@disableEvent');

// User stuff
Route::get('notifications', 'NotificationsController@show')->name('notifications');
Route::get('dashboard', 'UserController@showDashboard')->name('dashboard');
// Auth::routes();

Route::get('admin', 'AdminController@users')->name('admin');
Route::get('admin/users', 'AdminController@users')->name('admin-users');
Route::get('admin/issues', 'AdminController@issues')->name('admin-issues');
Route::get('admin/events', 'AdminController@events')->name('admin-events');
Route::get('admin/categories', 'AdminController@categories')->name('admin-categories');


