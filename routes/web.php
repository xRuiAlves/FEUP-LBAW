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

///////////////////////////////////////////////////
///////////////////////////////////////////////////
///////////////////////////////////////////////////

// Ours (Start deleting the above after the template was understood)

Route::get('/', 'HomepageController@display');
Route::view('faq', 'pages.faq')->name('faq');
Route::get('settings', 'SettingsController@show')->name('settings');

// Auth
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::get('logout', 'Auth\Login\Controller@logout')->name('logout');
Route::post('password/change', 'UserController@changePassword');

// Events
Route::get('/event/{id}', 'EventController@show')->where(['id' => '[0-9]+']);
Route::get('/event/create', 'EventController@create');
Route::post('/event/create', 'EventController@store');
Route::put('api/event/enable', 'AdminController@enableEvent');
Route::put('api/event/disable', 'AdminController@disableEvent');
Route::post('/event/category', 'EventController@storeCategory');
Route::put('/event/category/rename', 'EventController@renameCategory');

Route::get('/event/{id}/manage', 'EventController@manage')->where(['id' => '[0-9]+']);
Route::put('api/event/{id}/check-in', 'EventController@checkIn');

// Issues
Route::post('issue/create', 'IssueController@create');
Route::put('api/issue/solve', 'AdminController@solveIssue');

// Notifications
Route::get('notifications', 'NotificationsController@show')->name('notifications');
Route::put('api/notification/dismiss', 'NotificationsController@dismiss');

// User related routes
Route::get('dashboard', 'UserController@showDashboard')->name('dashboard');
Route::put('api/name/change', 'UserController@changeName');

// Comments
Route::post('api/comment', 'CommentController@store');

// Rating
Route::put('api/post/upvote', 'RatingController@upvote');
Route::put('api/post/downvote', 'RatingController@downvote');

// Admin dashboard routes
Route::get('admin', 'AdminController@users')->name('admin');
Route::get('admin/users', 'AdminController@users')->name('admin-users');
Route::get('admin/issues', 'AdminController@issues')->name('admin-issues');
Route::get('admin/events', 'AdminController@events')->name('admin-events');
Route::get('admin/categories', 'AdminController@categories')->name('admin-categories');