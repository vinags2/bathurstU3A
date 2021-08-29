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

// Home screen routes
Route::view('/home', 'welcome')->middleware('verified') ->name('home');
Route::get('/', function() { return redirect()->route('home'); });

// Routes for authorizing the user
Auth::routes(['verify' => true]);
Route::view('/join', 'notDoneYet') ->name('join');
Route::get('/logout', function() {
        Auth::logout();
        return view('welcome');
    }) ->name('logout');
Route::get('/verify', function() {
        Auth::logout();
        return view('auth.verify');
    }) ->name('verify');
Route::view('/verified', 'auth.verified');

// Route for running reports
Route::get('/report/{id}', 'ReportController@index') ->middleware('verified') ->name('report');
Route::view('/member/{id}', 'reports.memberDetails') ->middleware('verified') ->name('member');

// Route for generating PDFs
Route::get('/report/pdf/{id}', 'ReportController@index') ->middleware('verified') ->name('pdf');
Route::get('/report/pdf/show/{id}', 'GenericPdfReportController@show');

// Route for generating CSVs
Route::get('/report/csv/{id}', 'ReportController@index') ->middleware('verified') ->name('csv');

// Routes for data entry
Route::get('person/edit/{id?}', 'PersonController@edit') -> middleware('verified')->name('person.edit');
Route::get('person/editContactDetails/{id?}', 'PersonController@editContactDetails') -> middleware('verified')->name('person.editContactDetails');
Route::resource('person', 'PersonController') -> except(['edit']) -> middleware('verified');
Route::get('settings', 'SettingController@edit') -> middleware('verified')->name('settings');
Route::resource('setting', 'SettingController') -> except(['edit']) -> middleware('verified');
Route::get('termDates/{id?}', 'TermDatesController@edit') -> middleware('verified')->name('termDates');
Route::post('termDates/{id?}', 'TermDatesController@store') -> middleware('verified')->name('storeTermDates');

// Routes for AJAX calls from logged in user.
Route::get('/namesearch','PersonController@search') ->middleware('verified')->name('namesearch');
Route::get('/closenamesearch','PersonController@closesearch') ->middleware('verified')->name('closenamesearch');
Route::get('/addresssearch','AddressController@search') ->middleware('verified')->name('addresssearch');
Route::get('/coursesearch','CourseController@search') ->middleware('verified')->name('coursesearch');
Route::get('/venuesearch','VenueController@search') ->middleware('verified')->name('venuesearch');

// Other routes
Route::view('/testPage', 'testPage') -> name('testPage');