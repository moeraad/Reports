<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web','auth']], function () {
    
    Route::get('/', ['uses' =>'DashboardController@index']);
    Route::get('/home', ['uses' =>'DashboardController@index']);
    
    Route::get('manage_courts/copy/{id}', ['as' => 'manage_courts.copy','uses' => 'ManageCourtsController@copy']);
    Route::get('manage_courts/data', ['as' => 'manage_courts.data','uses' => 'ManageCourtsController@data']);
    Route::resource('manage_courts','ManageCourtsController');

    Route::get('profile/{id}', ['as' => 'profile','uses' => 'ManageJudgesController@profile']);
    Route::post('manage_judges/filter', ['as' => 'filter','uses' => 'ManageJudgesController@filter']);
    Route::resource('manage_judges','ManageJudgesController');

    Route::get('manage_judge_court/copy/{id}', ['as' => 'manage_judge_court.copy','uses' => 'ManageJudgesCourtsController@copy']);
    Route::get('manage_judge_court/data', ['as' => 'manage_judge_court.data','uses' => 'ManageJudgesCourtsController@data']);
    Route::resource('manage_judge_court','ManageJudgesCourtsController');

    Route::delete('manage_judgement/{judge_court_id}/{month}/{year}','JudgementController@bulkDelete');
    Route::get('manage_judgement/{judge_court_id}/{month}/{year}', ['as' => 'manage_judgement.show_bulk','uses' => 'JudgementController@showBulk']);
    Route::get('manage_judgement/bulk_create', ['as' => 'manage_judgement.bulk_create','uses' => 'JudgementController@bulkCreate']);
    Route::post('judgments_bulk_save', ['as' => 'judgments_bulk_save','uses' => 'JudgementController@bulkSave']);
    Route::get('manage_judgement/data', ['as' => 'manage_judgement.data','uses' => 'JudgementController@data']);
    Route::get('manage_judgement/duplicate/{id}', ['as' => 'manage_judgement.duplicate','uses' => 'JudgementController@duplicate']);
    Route::resource('manage_judgement','JudgementController');

    Route::delete('monthly_reports/{judge_id}/{month}/{year}','monthlyReportsController@bulkDelete');
    Route::post('monthly_reports_bulk_save', ['as' => 'monthly_reports_bulk_save','uses' => 'monthlyReportsController@bulkSave']);
    Route::get('monthly_reports/data', ['as' => 'monthly_reports.data','uses' => 'monthlyReportsController@data']);
    Route::get('monthly_reports/{judge_id}/{month}/{year}', ['as' => 'monthly_reports.show_bulk','uses' => 'monthlyReportsController@showBulk']);
    Route::get('monthly_reports/bulk_create', ['as' => 'monthly_reports.bulk_create','uses' => 'monthlyReportsController@bulkCreate']);
    Route::get('manage_monthly_report/duplicate/{id}', ['as' => 'monthly_reports.duplicate','uses' => 'monthlyReportsController@duplicate']);
    Route::resource('monthly_reports','monthlyReportsController');

    Route::get('manage_court_fields/{name}/{type}', ['as' => 'manage_court_fields.displayCourtFields','uses' => 'ManageCourtFieldsController@displayCourtFields']);
    Route::resource('manage_court_fields','ManageCourtFieldsController');
    
    Route::resource('manage_configs','ConfigsController');
    
    Route::resource('clerk','ClerksController');
    Route::resource('clerk_courts','ClerkCourtsController');
    Route::resource('judge_clerks','judgeClerksController');
    
    Route::resource('user_profile','UserProfileController');
    
    //reports
    Route::get('reports/judgments_average','ReportsController@judgmentsAverage');
    Route::get('reports/reports_stats','ReportsController@reportsStats');
    Route::get('reports/users_stats','ReportsController@usersStats');
    Route::get('reports/judges_distribution','ReportsController@judgesDistribution');
    Route::get('reports/user_logs','ReportsController@userLogs');
    Route::get('reports/full_report','ReportsController@fullReport');
    Route::get('reports/judges_by_occupation','ReportsController@judgesByOccupation');
    Route::resource('swap','monthlyReportsController@swap');
    
    //ajax
    Route::get('load_saved_judgements', ['uses' => 'JudgementController@LoadSavedJudgements']);
    Route::get('delete_judgment_record', ['uses' => 'JudgementController@DeleteJudgmentRecord']);
    Route::get('save_judgment_record', ['uses' => 'JudgementController@SaveJudgmentRecord']);
    Route::get('refresh_judgement_form', ['uses' => 'JudgementController@RefreshJudgementForm']);
    Route::get('refresh_fields', ['uses' => 'monthlyReportsController@RefreshFields']);
    Route::get('refresh_fields_bulk', ['uses' => 'monthlyReportsController@RefreshFieldsBulk']);
    Route::get('registerData',  ['as' => 'register.data','uses' => 'DashboardController@RegisterTable']);
    Route::post('load_full_report', ['uses' => 'ReportsController@fullAjaxReport']);
});
    Route::post('reports/stats_data',['as' => 'reports.stats_data','uses' => 'ReportsController@reportsStatsData']);
    
Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::get('/home', 'HomeController@index');
});