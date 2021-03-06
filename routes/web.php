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
Auth::routes();

// Events
Route::get('/event/{id}', 'EventController@show')->where(['id' => '[0-9]+']);
Route::get('/event/create', 'EventController@create');
Route::post('/event/create', 'EventController@store');
Route::get('/event/{id}/attend', 'EventController@showAttendPage')->where(['id' => '[0-9]+']);
Route::post('event/{id}/attend', 'EventController@attend')->where(['id' => '[0-9]+']);
Route::put('api/event/enable', 'AdminController@enableEvent');
Route::post('api/event/favorite', 'UserController@markEventAsFavorite');
Route::delete('api/event/favorite', 'UserController@unmarkEventAsFavorite');
Route::put('api/event/disable', 'AdminController@disableEvent');
Route::delete('api/event/{id}/ticket', 'UserController@removeOwnTicket')->where(['id' => '[0-9]+']);
Route::post('/event/category', 'EventController@storeCategory');
Route::put('/event/category/rename', 'EventController@renameCategory');

Route::get('/event/{id}/edit', 'EventController@edit')->where(['id' => '[0-9]+']);
Route::post('/event/{id}/edit', 'EventController@store')->where(['id' => '[0-9]+']);
Route::get('/event/{id}/manage', 'EventController@manage')->where(['id' => '[0-9]+']);
Route::get('/event/{id}/add-organizer', 'EventController@addOrganizerPage')->where(['id' => '[0-9]+']);
Route::get('/event/{id}/invite', 'EventController@invitePage')->where(['id' => '[0-9]+']);
Route::get('/event/{id}/tickets', 'UserController@showTicketsForEvent')->where(['id' => '[0-9]+']);

Route::get('/event/{id}/generate-vouchers', 'EventController@generateVouchersPage')->where(['id' => '[0-9]+']);
Route::put('api/event/{id}/check-in', 'EventController@checkIn');
Route::delete('api/event/{id}', 'EventController@delete');
Route::delete('api/event/{id}/attendee', 'EventController@removeAttendee');
Route::delete('api/event/{id}/organizer', 'EventController@removeOrganizer');
Route::put('api/event/{id}/quit-organization', 'EventController@quitOrganization');
Route::put('api/event/{id}/organizer', 'EventController@addOrganizer');
Route::put('api/event/{id}/invite', 'EventController@invite');
Route::post('api/event/{id}/vouchers', 'EventController@generateVouchers');

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
Route::delete('api/post', 'PostController@delete');

// Announcements
Route::delete('api/announcement', 'PostController@delete');

// Rating
Route::put('api/post/upvote', 'RatingController@upvote');
Route::put('api/post/downvote', 'RatingController@downvote');

// Admin dashboard routes
Route::get('admin', 'AdminController@users')->name('admin');
Route::get('admin/users', 'AdminController@users')->name('admin-users');
Route::get('admin/issues', 'AdminController@issues')->name('admin-issues');
Route::get('admin/events', 'AdminController@events')->name('admin-events');
Route::get('admin/categories', 'AdminController@categories')->name('admin-categories');