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
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('password/change', 'UserController@changePassword');
Route::delete('account', 'UserController@deleteAccount');

// Events
Route::get('/event/{id}', 'EventController@show')->where(['id' => '[0-9]+']);
Route::get('/event/create', 'EventController@create');
Route::post('/event/create', 'EventController@store');
Route::put('api/event/enable', 'AdminController@enableEvent');
Route::post('api/event/favorite', 'UserController@markEventAsFavorite');
Route::delete('api/event/favorite', 'UserController@unmarkEventAsFavorite');
Route::put('api/event/disable', 'AdminController@disableEvent');
Route::post('/event/category', 'EventController@storeCategory');
Route::put('/event/category/rename', 'EventController@renameCategory');

// Issues
Route::post('issue/create', 'IssueController@create');
Route::put('api/issue/solve', 'AdminController@solveIssue');

// Notifications
Route::get('notifications', 'NotificationsController@show')->name('notifications');
Route::put('api/notification/dismiss', 'NotificationsController@dismiss');

// Users
Route::get('dashboard', 'UserController@showDashboard')->name('dashboard');
Route::put('api/name/change', 'UserController@changeName');
Route::put('api/admin/promote', 'UserController@promoteToAdmin');
Route::put('api/user/enable', 'UserController@enable');
Route::put('api/user/disable', 'UserController@disable');

// Comments
Route::post('api/comment', 'CommentController@store');

// Posts
Route::post('api/post', 'PostController@store');

// Rating
Route::put('api/post/upvote', 'RatingController@upvote');
Route::put('api/post/downvote', 'RatingController@downvote');

// Admin dashboard routes
Route::get('admin', 'AdminController@users')->name('admin');
Route::get('admin/users', 'AdminController@users')->name('admin-users');
Route::get('admin/issues', 'AdminController@issues')->name('admin-issues');
Route::get('admin/events', 'AdminController@events')->name('admin-events');
Route::get('admin/categories', 'AdminController@categories')->name('admin-categories');