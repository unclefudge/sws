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

Route::get('/', 'HomeController@index');

// Site Login
Route::get('/login/site/{site_code}', function ($site_code) {
    Auth::logout();

    return redirect('/checkin');
    /*
    $worksite = \App\Models\Site\Site::where(['code' => $site_code])->first();
		Session::put('siteID', $worksite->id);

    return view('auth/login-site', compact('worksite'));
    */
});

// Authentication routes...
Route::get('/login', 'Auth\SessionController@create')->name('login');
Route::post('/login', 'Auth\SessionController@store');
Route::get('/logout', 'Auth\SessionController@destroy')->name('logout');

// Signup routes.. Pre Login
Route::get('/signup', 'Auth\RegistrationController@create')->name('register');
Route::get('/signup/ref/{key}', 'Auth\RegistrationController@refCreate');
Route::get('/signup/primary/{key}', 'Auth\RegistrationController@primaryCreate');
Route::post('/signup/primary', 'Auth\RegistrationController@primaryStore');

// Password Reset Routes...
Route::get('/password/reset', 'Auth\PasswordResetController@forgotForm');
Route::post('/password/email', 'Auth\PasswordResetController@resetEmail');
Route::get('/password/reset/{token}', 'Auth\PasswordResetController@resetForm');
Route::post('/password/reset', 'Auth\PasswordResetController@reset');

//Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
//Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset.token');
//Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// Logged in Routes
Route::group(['middleware' => 'auth'], function () {
    // Signup routes.. Post Login
    Route::get('/signup/user/{id}', 'Company\CompanySignUpController@userEdit');         // Step 1
    Route::post('/signup/user/{id}', 'Company\CompanySignUpController@userUpdate');
    Route::get('/signup/company/{id}', 'Company\CompanySignUpController@companyEdit');   // Step 2
    Route::post('/signup/company/{id}', 'Company\CompanySignUpController@companyUpdate');
    Route::get('/signup/workers/{id}', 'Company\CompanySignUpController@workersEdit');   // Step 3
    Route::post('/signup/workers/{id}', 'Company\CompanySignUpController@workersUpdate');
    Route::get('/signup/summary/{id}', 'Company\CompanySignUpController@summary');       // Step 4
    Route::get('/signup/documents/{id}', 'Company\CompanySignUpController@documents');   // Step 5
    Route::get('/signup/welcome/{id}', 'Company\CompanySignUpController@welcome');       // Resend welcome email
    Route::get('/signup/cancel/{id}', 'Company\CompanySignUpController@cancel');       // Resend welcome email

    // Site Checkin
    Route::get('checkin', 'Site\SiteCheckinController@checkin');
    Route::post('checkin', 'Site\SiteCheckinController@getQuestions');
    Route::get('checkin/whs/{site_id}', 'Site\SiteCheckinController@showQuestions');
    Route::post('checkin/whs/{site_id}', 'Site\SiteCheckinController@processCheckin');


    // Pages
    //Route::get('/', 'Misc\PagesController@index');
    Route::get('/home', 'Misc\PagesController@index');
    Route::get('/dashboard', 'Misc\PagesController@index');
    Route::get('/manage/quick', 'Misc\PagesController@quick');
    Route::get('/manage/fixplanner', 'Misc\PagesController@fixplanner');
    Route::get('/manage/importcompany', 'Misc\PagesController@importCompany');
    Route::get('/manage/completedqa', 'Misc\PagesController@completedQA');
    Route::get('/manage/create_permission', 'Misc\PagesController@createPermission');
    Route::get('/manage/importmaterials', 'Misc\PagesController@importMaterials');
    Route::get('/manage/disabled_tasks', 'Misc\PagesController@disabledTasks');

    // Reports
    Route::get('/manage/report', 'Misc\ReportController@index');
    Route::get('/manage/report/recent', 'Misc\ReportController@recent');
    Route::get('/manage/report/recent/files', 'Misc\ReportController@recentFiles');
    Route::get('/manage/report/newusers', 'Misc\ReportController@newusers');
    Route::get('/manage/report/newcompanies', 'Misc\ReportController@newcompanies');
    Route::get('/manage/report/users_noemail', 'Misc\ReportController@users_noemail');
    Route::get('/manage/report/users_nowhitecard', 'Misc\ReportController@users_nowhitecard');
    Route::get('/manage/report/users_lastlogin', 'Misc\ReportController@usersLastLogin');
    Route::get('/manage/report/roleusers', 'Misc\ReportController@roleusers');
    Route::get('/manage/report/users_extra_permissions', 'Misc\ReportController@usersExtraPermissions');
    Route::get('/manage/report/missing_company_info', 'Misc\ReportController@missingCompanyInfo');
    Route::get('/manage/report/company_users', 'Misc\ReportController@companyUsers');
    Route::get('/manage/report/company_contactinfo', 'Misc\ReportController@companyContactInfo');
    Route::get('/manage/report/company_privacy', 'Misc\ReportController@companyPrivacy');
    Route::get('/manage/report/company_privacy_send', 'Misc\ReportController@companyPrivacySend');
    Route::get('/manage/report/company_swms', 'Misc\ReportController@companySWMS');
    Route::get('/manage/report/licence_override', 'Misc\ReportController@licenceOverride');
    Route::get('/manage/report/attendance', 'Misc\ReportController@attendance');
    Route::get('/manage/report/attendance/dt/attendance', 'Misc\ReportController@getAttendance');
    Route::get('/manage/report/expired_company_docs', 'Misc\ReportController@expiredCompanyDocs');
    Route::get('/manage/report/expired_company_docs/dt/expired_company_docs', 'Misc\ReportController@getExpiredCompanyDocs');
    Route::get('/manage/report/payroll', 'Misc\ReportController@payroll');
    Route::get('/manage/report/nightly', 'Misc\ReportController@nightly');
    Route::get('/manage/report/equipment', 'Misc\ReportController@equipment');
    Route::get('/manage/report/equipment/report', 'Misc\ReportController@equipmentPDF');
    Route::get('/manage/report/equipment_site', 'Misc\ReportController@equipmentSite');
    Route::get('/manage/report/equipment_site/report', 'Misc\ReportController@equipmentSitePDF');
    Route::get('/manage/report/equipment_transactions', 'Misc\ReportController@equipmentTransactions');
    Route::post('/manage/report/equipment_transactions/report', 'Misc\ReportController@equipmentTransactionsPDF');
    Route::get('/manage/report/equipment/dt/transactions', 'Misc\ReportController@getEquipmentTransactions');
    Route::get('/manage/report/equipment_stocktake', 'Misc\ReportController@equipmentStocktake');
    //Route::post('/manage/report/equipment_stocktake/report', 'Misc\ReportController@equipmentStocktakePDF');
    Route::get('/manage/report/equipment/dt/stocktake', 'Misc\ReportController@getEquipmentStocktake');
    Route::get('/manage/report/equipment/dt/stocktake-not', 'Misc\ReportController@getEquipmentStocktakeNot');
    Route::get('/manage/report/qa/{id}', 'Misc\ReportController@QAdebug');
    Route::get('/manage/report/maintenance_no_action', 'Misc\ReportController@maintenanceNoAction');
    Route::get('/manage/report/maintenance_on_hold', 'Misc\ReportController@maintenanceOnHold');
    Route::get('/manage/report/site_inspections', 'Misc\ReportController@siteInspections');
    Route::get('/manage/report/site_inspections/dt/list', 'Misc\ReportController@getSiteInspections');


    // User Docs
    Route::get('user/{uid}/doc/dt/docs', 'User\UserDocController@getDocs');
    Route::get('user/{uid}/doc/upload', 'User\UserDocController@create');
    Route::post('user/{uid}/doc/reject/{id}', 'User\UserDocController@reject');
    Route::get('user/{uid}/doc/archive/{id}', 'User\UserDocController@archive');
    //Route::delete('user/{uid}/doc/{id}', 'User\UserDocController@destroy');
    //Route::get('user/{uid}/doc/cats/{department}', 'User\UserDocController@getCategories');
    Route::resource('user/{uid}/doc', 'User\UserDocController');

    // User Routes
    Route::get('user/dt/users', 'UserController@getUsers');
    Route::get('user/dt/contractors', 'UserController@getContractors');
    Route::post('user/{id}/login', 'UserController@updateLogin');
    Route::get('user/{id}/security', 'UserController@showSecurity');
    Route::get('user/{id}/resetpassword', 'UserController@showResetPassword');
    Route::post('user/{id}/resetpassword', 'UserController@updatePassword');
    Route::post('user/{id}/security', 'UserController@updateSecurity');
    Route::post('user/{id}/construction', 'UserController@updateConstruction');
    Route::get('user/{id}/resetpermissions', 'UserController@resetPermissions');
    Route::post('user/{id}/compliance', 'UserController@storeCompliance');
    Route::post('user/{id}/compliance/update', 'UserController@updateCompliance');
    Route::get('contractor', 'UserController@contractorList');
    Route::resource('user', 'UserController');

    // Company Leave Routes
    Route::get('/company/leave/dt/leave', 'Company\CompanyLeaveController@getCompanyLeave');
    Route::resource('/company/leave', 'Company\CompanyLeaveController');

    // Company Standard Documents
    Route::get('company/doc/standard', 'Company\CompanyDocController@showStandard');
    Route::get('company/doc/standard/dt/docs', 'Company\CompanyDocController@getStandard');

    // Company Period Trade Contract
    Route::post('company/{cid}/doc/period-trade-contract/reject/{id}', 'Company\CompanyPeriodTradeController@reject');
    Route::resource('company/{cid}/doc/period-trade-contract', 'Company\CompanyPeriodTradeController');

    // Company Subcontractors Statement
    Route::post('company/{cid}/doc/subcontractor-statement/reject/{id}', 'Company\CompanySubcontractorStatementController@reject');
    Route::resource('company/{cid}/doc/subcontractor-statement', 'Company\CompanySubcontractorStatementController');

    // Privacy Policy
    Route::post('company/{cid}/doc/privacy-policy/reject/{id}', 'Company\CompanyPrivacyPolicyController@reject');
    Route::resource('company/{cid}/doc/privacy-policy', 'Company\CompanyPrivacyPolicyController');

    // Company Docs
    Route::any('company/doc/export', 'Company\CompanyExportController@exportDocs');
    Route::post('company/doc/export/pdf', 'Company\CompanyExportController@docsPDF');
    Route::get('company/doc/create/tradecontract/{id}/{version}', 'Company\CompanyExportController@tradecontractPDF');
    Route::get('company/doc/create/subcontractorstatement/{id}/{version}', 'Company\CompanyExportController@subcontractorstatementPDF');
    Route::resource('company/doc', 'Company\CompanyDocController');

    // Company Docs
    Route::get('company/{cid}/doc/dt/docs', 'Company\CompanyDocController@getDocs');
    Route::get('company/{cid}/doc/upload', 'Company\CompanyDocController@create');
    Route::post('company/{cid}/doc/reject/{id}', 'Company\CompanyDocController@reject');
    Route::get('company/{cid}/doc/archive/{id}', 'Company\CompanyDocController@archive');
    Route::get('company/{cid}/doc/cats/{department}', 'Company\CompanyDocController@getCategories');
    Route::resource('company/{cid}/doc', 'Company\CompanyDocController');

    // Company Routes
    Route::get('company/dt/companies', 'Company\CompanyController@getCompanies');
    Route::get('company/dt/users', 'Company\CompanyController@getUsers');
    Route::get('company/{id}/name', 'Company\CompanyController@getCompanyName');
    Route::get('company/{id}/approve/{type}', 'Company\CompanyController@approveCompany');
    Route::post('company/{id}/business', 'Company\CompanyController@updateBusiness');
    Route::post('company/{id}/construction', 'Company\CompanyController@updateConstruction');
    Route::post('company/{id}/whs', 'Company\CompanyController@updateWHS');
    Route::post('company/{id}/leave', 'Company\CompanyController@storeLeave');
    Route::post('company/{id}/leave/update', 'Company\CompanyController@updateLeave');
    Route::get('company/{id}/leave/{lid}', 'Company\CompanyController@destroyLeave');
    Route::post('company/{id}/compliance', 'Company\CompanyController@storeCompliance');
    Route::post('company/{id}/compliance/update', 'Company\CompanyController@updateCompliance');
    Route::get('company/{id}/user', 'Company\CompanyController@users');
    //Route::get('company/{id}/edit/trade', 'Company\CompanyController@editTrade');
    //Route::post('company/{id}/settings/logo', 'Company\CompanyController@updateLogo');
    //Route::post('company/{id}/edit/logo', 'Company\CompanyController@updateLogo');
    Route::resource('company', 'Company\CompanyController');

    // Client Routes
    Route::get('client/dt/clients', 'Misc\ClientController@getClients');
    Route::get('client/{slug}/settings', 'Misc\ClientController@showSettings');
    Route::get('client/{slug}/settings/{tab}', 'Misc\ClientController@showSettings');
    Route::resource('client', 'Misc\ClientController');

    // File Manager
    Route::get('/manage/file', 'Misc\FileController@index');
    Route::get('/manage/file/directory', 'Misc\FileController@fileDirectory');
    Route::get('manage/file/directory/dt/docs', 'Misc\FileController@getDocs');

    // Site Hazards
    Route::get('site/hazard/dt/hazards', 'Site\SiteHazardController@getHazards');
    Route::get('site/hazard/{id}/status/{status}', 'Site\SiteHazardController@updateStatus');
    Route::resource('site/hazard', 'Site\SiteHazardController');
    //Route::resource('site/hazard-action', 'Site\SiteHazardActionController');

    // Site Accidents
    Route::get('site/accident/dt/accidents', 'Site\SiteAccidentController@getAccidents');
    Route::resource('site/accident', 'Site\SiteAccidentController');

    // Site Compliance
    Route::resource('site/compliance', 'Site\Planner\SiteComplianceController');

    // Site Docs
    Route::get('site/doc/type/{type}', 'Site\SiteDocController@listDocs');
    Route::get('site/doc/type/dt/{type}', 'Site\SiteDocController@getDocsType');
    Route::get('site/doc/dt/docs', 'Site\SiteDocController@getDocs');
    Route::any('site/doc/create', 'Site\SiteDocController@create');
    Route::any('site/doc/upload', 'Site\SiteDocController@upload');
    Route::resource('site/doc', 'Site\SiteDocController');

    // Site QA Categories
    Route::get('site/categories/qa/dt/qa_cats', 'Site\SiteQaCategoryController@getQaCategories');
    Route::resource('site/qa/category', 'Site\SiteQaCategoryController');

    // Site Quality Assurance
    Route::get('site/qa/{id}/items', 'Site\SiteQaController@getItems');
    Route::any('site/qa/{id}/update', 'Site\SiteQaController@updateReport');
    Route::any('site/qa/item/{id}', 'Site\SiteQaController@updateItem');
    Route::get('site/qa/company/{task_id}', 'Site\SiteQaController@getCompaniesForTask');
    Route::get('site/qa/dt/qa_reports', 'Site\SiteQaController@getQaReports');
    Route::get('site/qa/dt/qa_templates', 'Site\SiteQaController@getQaTemplates');
    Route::get('site/qa/templates', 'Site\SiteQaController@templates');
    Route::resource('site/qa', 'Site\SiteQaController');

    // Site Maintenance Categories
    Route::get('site/categories/maintenance/dt/main_cats', 'Site\SiteMaintenanceCategoryController@getMainCategories');
    Route::resource('site/maintenance/category', 'Site\SiteMaintenanceCategoryController');

    // Site Maintenance
    Route::get('site/maintenance/{id}/items', 'Site\SiteMaintenanceController@getItems');
    Route::any('site/maintenance/{id}/update', 'Site\SiteMaintenanceController@updateReport');
    Route::any('site/maintenance/item/{id}', 'Site\SiteMaintenanceController@updateItem');
    Route::get('site/maintenance/dt/maintenance', 'Site\SiteMaintenanceController@getMaintenance');
    Route::any('site/maintenance/upload', 'Site\SiteMaintenanceController@uploadAttachment');
    Route::get('site/maintenance/data/prac_completion/{site_id}', 'Site\SiteMaintenanceController@getPracCompletion');
    Route::get('site/maintenance/data/site_super/{site_id}', 'Site\SiteMaintenanceController@getSiteSupervisor');
    Route::any('site/maintenance/{id}/review', 'Site\SiteMaintenanceController@review');
    Route::any('site/maintenance/{id}/photos', 'Site\SiteMaintenanceController@photos');
    Route::resource('site/maintenance', 'Site\SiteMaintenanceController');


    // Site Asbestos Register
    Route::get('site/asbestos/dt/list', 'Site\SiteAsbestosController@getReports');
    Route::get('site/asbestos/{id}/status/{status}', 'Site\SiteAsbestosController@updateStatus');
    Route::resource('site/asbestos', 'Site\SiteAsbestosController');

    // Site Inspection Electrical Register
    Route::get('site/inspection/electrical/dt/list', 'Site\SiteInspectionElectricalController@getInspections');
    Route::any('site/inspection/electrical/upload', 'Site\SiteInspectionElectricalController@uploadAttachment');
    Route::any('site/inspection/electrical/{id}/docs', 'Site\SiteInspectionElectricalController@documents');
    Route::get('site/inspection/electrical/{id}/report', 'Site\SiteInspectionElectricalController@reportPDF');
    Route::resource('site/inspection/electrical', 'Site\SiteInspectionElectricalController');

    // Site Inspection Plumbing Register
    Route::get('site/inspection/plumbing/dt/list', 'Site\SiteInspectionPlumbingController@getInspections');
    Route::any('site/inspection/plumbing/upload', 'Site\SiteInspectionPlumbingController@uploadAttachment');
    Route::any('site/inspection/plumbing/{id}/docs', 'Site\SiteInspectionPlumbingController@documents');
    Route::get('site/inspection/plumbing/{id}/report', 'Site\SiteInspectionPlumbingController@reportPDF');
    Route::resource('site/inspection/plumbing', 'Site\SiteInspectionPlumbingController');

    // Report Actions
    Route::get('report/actions/{type}/{id}', 'Misc\ReportActionController@index');
    Route::post('report/actions/{type}/{id}', 'Misc\ReportActionController@store');
    Route::patch('report/actions/{type}/{id}', 'Misc\ReportActionController@update');

    Route::get('action/{table}/{table_id}', 'Misc\ActionController@index');
    Route::resource('action', 'Misc\ActionController');

    // Site Supervisors
    Route::get('site/supervisor/data/supers', 'Company\CompanySupervisorController@getSupers');
    Route::resource('site/supervisor', 'Company\CompanySupervisorController');

    // Site Attendance
    Route::get('/site/attendance/dt/attendance', 'Site\SiteAttendanceController@getAttendance');
    Route::resource('/site/attendance', 'Site\SiteAttendanceController');


    // Site Exports
    Route::get('site/export', 'Site\Planner\SitePlannerExportController@index');
    Route::get('site/export/plan', 'Site\Planner\SitePlannerExportController@exportPlanner');
    Route::post('site/export/site', 'Site\Planner\SitePlannerExportController@sitePDF');
    Route::get('site/export/start', 'Site\Planner\SitePlannerExportController@exportStart');
    Route::post('site/export/start', 'Site\Planner\SitePlannerExportController@jobstartPDF');
    Route::get('site/export/completion', 'Site\Planner\SitePlannerExportController@exportCompletion');
    Route::post('site/export/completion', 'Site\Planner\SitePlannerExportController@completionPDF');
    Route::get('site/export/attendance', 'Site\Planner\SitePlannerExportController@exportAttendance');
    Route::post('site/export/attendance', 'Site\Planner\SitePlannerExportController@attendancePDF');
    Route::get('site/export/qa', 'Site\SiteQaController@exportQA');
    Route::post('site/export/qa', 'Site\SiteQaController@qaPDF');

    // Site Routes
    Route::get('site/dt/sites', 'Site\SiteController@getSites');
    Route::get('site/dt/sitelist', 'Site\SiteController@getSiteList');
    Route::get('sitelist', 'Site\SiteController@siteList');
    //Route::get('site/{slug}/checkin', 'Site\SiteController@siteCheckin');
    //Route::post('site/{slug}/checkin', 'Site\SiteController@processCheckin');
    Route::get('site/{slug}/settings', 'Site\SiteController@showSettings');
    Route::post('site/{slug}/settings/admin', 'Site\SiteController@updateAdmin');
    Route::post('site/{slug}/settings/logo', 'Site\SiteController@updateLogo');
    Route::get('site/{slug}/settings/{tab}', 'Site\SiteController@showSettings');
    Route::get('site/data/details/{id}', 'Site\SiteController@getSiteDetails');
    //Route::get('site/data/owner/{id}', 'Site\SiteController@getSiteOwner');
    Route::resource('site', 'Site\SiteController');

    // Trade + Task Routes
    Route::resource('trade', 'Site\Planner\TradeController');
    Route::resource('task', 'Site\Planner\TaskController');

    // SDS Safety Docs
    Route::get('safety/doc/dt/sds', 'Safety\SdsController@getSDS');
    Route::any('safety/doc/sds/create', 'Safety\SdsController@create');
    Route::any('safety/doc/sds/upload', 'Safety\SdsController@upload');
    Route::resource('safety/doc/sds', 'Safety\SdsController');

    // Toolbox Talks
    Route::get('safety/doc/toolbox2', 'Safety\ToolboxTalkController@index');
    Route::get('safety/doc/toolbox2/{id}/accept', 'Safety\ToolboxTalkController@accept');
    Route::get('safety/doc/toolbox2/{id}/create', 'Safety\ToolboxTalkController@createFromTemplate');
    Route::get('safety/doc/toolbox2/{id}/reject', 'Safety\ToolboxTalkController@reject');
    Route::get('safety/doc/toolbox2/{id}/signoff', 'Safety\ToolboxTalkController@signoff');
    Route::get('safety/doc/toolbox2/{id}/archive', 'Safety\ToolboxTalkController@archive');
    Route::get('safety/doc/toolbox2/{id}/destroy', 'Safety\ToolboxTalkController@destroy');
    Route::post('safety/doc/toolbox2/{id}/upload', 'Safety\ToolboxTalkController@uploadMedia');
    Route::get('safety/doc/dt/toolbox2', 'Safety\ToolboxTalkController@getToolbox');
    Route::get('safety/doc/dt/toolbox_templates', 'Safety\ToolboxTalkController@getToolboxTemplates');
    Route::resource('safety/doc/toolbox2', 'Safety\ToolboxTalkController');

    // Safety Docs - WMS
    Route::get('safety/doc/wms', 'Safety\WmsController@index');
    Route::get('safety/doc/wms/expired', 'Safety\WmsController@expired');
    Route::get('safety/doc/wms/{id}/create', 'Safety\WmsController@createFromTemplate');
    Route::get('safety/doc/wms/{id}/steps', 'Safety\WmsController@getSteps');
    Route::any('safety/doc/wms/{id}/update', 'Safety\WmsController@update');
    Route::get('safety/doc/wms/{id}/reject', 'Safety\WmsController@reject');
    Route::get('safety/doc/wms/{id}/signoff', 'Safety\WmsController@signoff');
    Route::get('safety/doc/wms/{id}/archive', 'Safety\WmsController@archive');
    Route::any('safety/doc/wms/{id}/pdf', 'Safety\WmsController@pdf');
    Route::post('safety/doc/wms/{id}/email', 'Safety\WmsController@email');
    Route::any('safety/doc/wms/{id}/upload', 'Safety\WmsController@upload');
    Route::get('safety/doc/wms/{id}/replace', 'Safety\WmsController@replace');
    Route::get('safety/doc/wms/{id}/renew', 'Safety\WmsController@createRenew');
    Route::get('safety/doc/dt/wms', 'Safety\WmsController@getWms');
    Route::get('safety/doc/dt/wms_templates', 'Safety\WmsController@getWmsTemplates');
    Route::resource('safety/doc/wms', 'Safety\WmsController');


    // Equipment Transfers
    Route::get('equipment/dt/transfers', 'Misc\EquipmentTransferController@getTransfers');
    Route::get('equipment/{id}/transfer', 'Misc\EquipmentTransferController@transfer');
    Route::post('equipment/{id}/transfer', 'Misc\EquipmentTransferController@transferItem');
    Route::get('equipment/{id}/transfer-bulk', 'Misc\EquipmentTransferController@transferBulk');
    Route::post('equipment/{id}/transfer-bulk', 'Misc\EquipmentTransferController@transferBulkItems');
    Route::get('equipment/{id}/transfer-verify', 'Misc\EquipmentTransferController@verifyTransfer');
    Route::get('equipment/{id}/transfer-cancel', 'Misc\EquipmentTransferController@cancelTransfer');
    Route::post('equipment/{id}/transfer-confirm', 'Misc\EquipmentTransferController@confirmTransfer');

    // Locations Other
    Route::get('equipment/other-location/dt/other', 'Misc\EquipmentLocationOtherController@getOther');
    Route::get('equipment/other-location/{id}/delete', 'Misc\EquipmentLocationOtherController@destroy');
    Route::resource('equipment/other-location', 'Misc\EquipmentLocationOtherController');

    // Equipment
    Route::get('equipment/dt/allocation', 'Misc\EquipmentController@getAllocation');
    Route::get('equipment/dt/inventory', 'Misc\EquipmentController@getInventory');
    Route::get('equipment/dt/missing', 'Misc\EquipmentController@getMissing');
    Route::get('equipment/dt/log', 'Misc\EquipmentController@getLog');
    Route::get('equipment/inventory', 'Misc\EquipmentController@inventory');
    Route::get('equipment/writeoff', 'Misc\EquipmentController@writeoff');
    Route::post('equipment/writeoff', 'Misc\EquipmentController@writeoffItems');
    Route::get('equipment/{id}/delete', 'Misc\EquipmentController@destroy');
    Route::resource('equipment', 'Misc\EquipmentController');
    // Stocktake
    Route::get('equipment/stocktake/dt/stocktake', 'Misc\EquipmentStocktakeController@getStocktake');
    Route::get('equipment/stocktake/view/{id}', 'Misc\EquipmentStocktakeController@showStocktake');
    Route::resource('equipment/stocktake', 'Misc\EquipmentStocktakeController');



    // Configuration
    Route::get('settings', 'Misc\PagesController@settings');
    Route::resource('settings/notifications', 'Misc\SettingsNotificationController');

    // Roles / Permission
    Route::get('settings/role/permissions', 'Misc\RoleController@getPermissions');
    Route::get('settings/role/resetpermissions', 'Misc\PagesController@resetPermissions');
    Route::get('settings/role/child-role/{id}', 'Misc\RoleController@childRole');
    Route::get('settings/role/child-primary/{id}', 'Misc\RoleController@childPrimary');
    Route::get('settings/role/child-default/{id}', 'Misc\RoleController@childDefault');
    Route::get('settings/role/parent', 'Misc\RoleController@parent');
    Route::get('settings/role/child', 'Misc\RoleController@child');
    Route::resource('settings/role', 'Misc\RoleController');


    // Planners
    Route::any('planner/weekly', 'Site\Planner\SitePlannerController@showWeekly');
    Route::any('planner/site', 'Site\Planner\SitePlannerController@showSite');
    Route::any('planner/site/{site_id}', 'Site\Planner\SitePlannerController@showSite');
    Route::any('planner/trade', 'Site\Planner\SitePlannerController@showTrade');
    Route::any('planner/roster', 'Site\Planner\SitePlannerController@showAttendance');
    Route::any('planner/transient', 'Site\Planner\SitePlannerController@showTransient');
    Route::get('planner/data/sites', 'Site\Planner\SitePlannerController@getSites');
    Route::get('planner/data/site/{site_id}', 'Site\Planner\SitePlannerController@getSitePlan');
    Route::get('planner/data/site/{site_id}/attendance/{date}', 'Site\Planner\SitePlannerController@getSiteAttendance');
    Route::get('planner/data/site/{site_id}/allocate/{user_id}', 'Site\Planner\SitePlannerController@allocateSiteSupervisor');
    Route::any('planner/data/roster/user', 'Site\Planner\SitePlannerController@addUserRoster');
    Route::any('planner/data/roster/user/{id}', 'Site\Planner\SitePlannerController@delUserRoster');
    Route::any('planner/data/roster/add-company/{cid}/site/{site_id}/date/{date}', 'Site\Planner\SitePlannerController@addCompanyRoster');
    Route::any('planner/data/roster/del-company/{cid}/site/{site_id}/date/{date}', 'Site\Planner\SitePlannerController@delCompanyRoster');
    Route::any('planner/data/weekly/{date}/{super_id}', 'Site\Planner\SitePlannerController@getWeeklyPlan');
    Route::get('planner/data/company/{company_id}/tasks', 'Site\Planner\SitePlannerController@getCompanyTasks');
    Route::get('planner/data/company/{company_id}/tasks/trade/{trade_id}', 'Site\Planner\SitePlannerController@getCompanyTasks');
    Route::get('planner/data/company/{company_id}/trades', 'Site\Planner\SitePlannerController@getCompanyTrades');
    Route::get('planner/data/company/trade/{trade_id}', 'Site\Planner\SitePlannerController@getCompaniesWithTrade');
    Route::get('planner/data/company/{company_id}/trade/{trade_id}/site/{site_id}', 'Site\Planner\SitePlannerController@getCompanies');
    Route::get('planner/data/company/{company_id}/site/{site_id}/{date}', 'Site\Planner\SitePlannerController@getCompanySitesOnDate');
    Route::get('planner/data/trade', 'Site\Planner\SitePlannerController@getTrades');
    Route::get('planner/data/trade/upcoming/{date}', 'Site\Planner\SitePlannerController@getUpcomingTasks');
    Route::get('planner/data/trade/{trade_id}/tasks', 'Site\Planner\SitePlannerController@getTradeTasks');
    Route::get('planner/data/trade/jobstarts/{exists}', 'Site\Planner\SitePlannerController@getJobStarts');
    Route::get('planner/data/trade/joballocate', 'Site\Planner\SitePlannerController@getSitesWithoutSuper');
    Route::any('planner/data/trade/email-jobstart', 'Site\Planner\SitePlannerController@emailJobstart');
    Route::resource('planner', 'Site\Planner\SitePlannerController');

    // Support Tickets
    Route::get('support/ticket/dt/tickets', 'Support\SupportTicketController@getTickets');
    Route::get('support/ticket/dt/upgrades', 'Support\SupportTicketController@getUpgrades');
    Route::get('support/ticket/create', 'Support\SupportTicketController@create');
    Route::post('support/ticket/action', 'Support\SupportTicketController@addAction');
    Route::get('support/ticket/{id}/eta/{date}', 'Support\SupportTicketController@updateETA');
    Route::get('support/ticket/{id}/status/{status}', 'Support\SupportTicketController@updateStatus');
    Route::get('support/ticket/{id}/hours/{hours}', 'Support\SupportTicketController@updateHours');
    Route::get('support/ticket/{id}/priority/{priority}', 'Support\SupportTicketController@updatePriority');
    Route::resource('support/ticket', 'Support\SupportTicketController');


    // Comms
    Route::get('todo/dt/todo', 'Comms\TodoController@getTodo');
    Route::get('todo/create/{type}/{type_id}', 'Comms\TodoController@createType');
    Route::resource('todo', 'Comms\TodoController');
    Route::get('comms/notify/dt/notify', 'Comms\NotifyController@getNotify');
    Route::resource('comms/notify', 'Comms\NotifyController');
    Route::get('safety/tip/active', 'Comms\TipController@getActive');
    Route::resource('safety/tip', 'Comms\TipController');

    // PDF
    Route::get('pdf/test', 'Misc\PdfController@test');
    Route::get('pdf/workmethod/{id}', 'Misc\PdfController@workmethod');
    Route::get('pdf/planner/site/{site_id}/{date}/{weeks}', 'Misc\PdfController@plannerSite');

    // Fudge
    Route::get('userlog', 'Misc\PagesController@userlog');
    Route::post('userlog', 'Misc\PagesController@userlogAuth');

});

// Cron routes
Route::get('cron/nightly', 'Misc\CronController@nightly');
Route::get('cron/nightly-verify', 'Misc\CronController@verifyNightly');
Route::get('cron/roster', 'Misc\CronController@roster');
Route::get('cron/qa', 'Misc\CronController@qa');
Route::get('cron/overdue-todo', 'Misc\CronController@overdueToDo');
Route::get('cron/expired-companydoc', 'Misc\CronController@expiredCompanyDoc');
Route::get('cron/expired-swms', 'Misc\CronController@expiredSWMS');
Route::get('cron/archive-toolbox', 'Misc\CronController@archiveToolbox');
Route::get('cron/email-jobstart', 'Misc\CronController@emailJobstart');
Route::get('cron/email-fortnight', 'Misc\CronController@emailFortnightlyReports');

Route::get('test/cal', 'Misc\PagesController@testcal');
Route::get('manage/updateroles', 'Misc\PagesController@updateRoles');
Route::get('manage/import-payroll', 'Misc\PagesController@importPayroll');
Route::get('manage/import-maintenance', 'Misc\PagesController@importMaintenance');

// PHP Info
Route::get('php-info', function () {
    phpinfo();
});

Route::get('test/email', function () {
    return view('emails/blank');
});


