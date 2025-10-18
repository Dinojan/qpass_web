<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'app/config/config.php';
require_once DIR_LAYOUTS_LIB . 'Includes.php';
use Layouts\Lib\Route;
use App\Controllers\Auth\LoginController;
use Layouts\Lib\Auth;
use App\Controllers\Admin\LocalizationController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProfileController;
use App\Controllers\Admin\AdminUserController;
use App\Controllers\Admin\RoleController;
use App\Controllers\Admin\DesignationsController;
use App\Controllers\Admin\DepartmentsController;
use App\Controllers\Admin\EmployeeController;
use App\Controllers\Admin\PreRegisterController;
use App\Controllers\Admin\VisitorController;
use App\Controllers\Admin\VisitorReportController;
use App\Controllers\Admin\PreRegistersReportController;
use App\Controllers\Admin\AttendanceReportController;
use App\Controllers\Admin\EmployeeReportController;
use App\Controllers\Admin\LanguageController;
use App\Controllers\Admin\AttendanceController;
use App\Controllers\Admin\SettingController;
use App\Controllers\Admin\FrontendController;
// Add these missing imports
use App\Controllers\Admin\AddonController;
use App\Controllers\Admin\WebNotificationController;
use App\Controllers\Home\CheckInController;
use App\Controllers\Home\CheckoutController;
Auth::routes();
// Route::group(['middleware' => ['installed']], function () {
//     Auth::routes(['verify' => false]);
// });

// Route::group(['middleware' => ['installed', 'not-verified']], function () {
//     Route::get('/license-activate', [PurchaseCodeController::class, 'licenseCodeActivate'])->name('license-activate');
// });

// Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'middleware' => ['web', 'install']], function () {
//     Route::post('environment/saveWizard', [EnvironmentController::class, 'saveWizard'])->name('environmentSaveWizard');
//     Route::get('purchase-code', [PurchaseCodeController::class, 'index'])->name('purchase_code');
//     Route::post('purchase-code', [PurchaseCodeController::class, 'action'])->name('purchase_code.check');
//     Route::get('final', [FinalController::class, 'finish'])->name('final');
// });

Route::redirect('/', '/admin/dashboard')->middleware('backend_permission');
Route::redirect('/admin', '/DashboardControllermin/dashboard')->middleware('backend_permission');

Route::group(['prefix' => 'admin', 'middleware' => ['installed'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    Route::get('login', [LoginController::class, 'showLoginForm']);
});

Route::get('admin/lang/{locale}', [LocalizationController::class, 'index'])->middleware(['installed'])->name('admin.lang.index');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'installed', 'backend_permission'], 'as' => 'admin.'], function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('get-total-preregister', [DashboardController::class, 'getTotalPreregister'])->name('get.total.preregister');
    Route::post('get-total-visitor', [DashboardController::class, 'getTotalVisitor'])->name('get.total.visitor');
    Route::post('get-total-visitor-state', [DashboardController::class, 'getTotalVisitorState'])->name('get.total.visitor.state');


    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('profile/edit', [ProfileController::class, 'editProfile'])->name('profile.edit');
    Route::put('profile/update{update}', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.changepassword');
    Route::put('profile/change', [ProfileController::class, 'change'])->name('profile.change');

    Route::resource('adminusers', AdminUserController::class);
    Route::get('get-adminusers', [AdminUserController::class, 'getAdminUsers'])->name('adminusers.get-adminusers');
    Route::resource('role', RoleController::class);
    Route::post('role/save-permission/{id}', [RoleController::class, 'savePermission'])->name('role.save-permission');
    // role module
    Route::get('get-roles', [RoleController::class, 'getroles'])->name('roles.get-roles');

    //designations
    Route::prefix('designations')->name('designations.')->group(function () {
        Route::get('/', [DesignationsController::class, 'index'])->name('index');
        Route::get('list', [DesignationsController::class, 'list'])->name('list');
        Route::get('/create', [DesignationsController::class, 'create'])->name('create');
        Route::post('/', [DesignationsController::class, 'store'])->name('store');
        Route::get('/show/{designation}', [DesignationsController::class, 'show'])->name('show');
        Route::get('/{designation}', [DesignationsController::class, 'edit'])->name('edit');
        Route::put('/{designation}', [DesignationsController::class, 'update'])->name('update');
        Route::delete('{designation}', [DesignationsController::class, 'destroy'])->name('destroy');
    });

    //departments
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentsController::class, 'index'])->name('index');
        Route::get('list', [DepartmentsController::class, 'list'])->name('list');
        Route::get('/create', [DepartmentsController::class, 'create'])->name('create');
        Route::post('/', [DepartmentsController::class, 'store'])->name('store');
        Route::get('/show/{department}', [DepartmentsController::class, 'show'])->name('show');
        Route::get('/{department}', [DepartmentsController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentsController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentsController::class, 'destroy'])->name('destroy');
    });

    //web-token
    Route::post('store-token', [WebNotificationController::class, 'store'])->name('store.token');

    //employee route
    Route::resource('employees', EmployeeController::class);
    Route::get('get-employees', [EmployeeController::class, 'getEmployees'])->name('employees.get-employees');
    Route::get('employees/get-pre-registers/{id}', [EmployeeController::class, 'getPreRegister'])->name('employees.get-pre-registers');
    Route::get('employees/get-visitors/{id}', [EmployeeController::class, 'getVisitor'])->name('employees.get-visitors');
    Route::put('employees/check/{id}', [EmployeeController::class, 'checkEmployee'])->name('employees.check');

    //pre-registers
    Route::resource('pre-registers', PreRegisterController::class);
    Route::get('get-pre-registers', [PreRegisterController::class, 'getPreRegister'])->name('pre-registers.get-pre-registers');

    //visitors
    Route::resource('visitors', VisitorController::class);
    Route::post('visitor/search', [VisitorController::class, 'search'])->name('visitor.search');
    Route::get('visitor/check-out/{visitingDetail}', [VisitorController::class, 'checkout'])->name('visitors.checkout');
    Route::get('visitor/change-status/{id}/{status}/{dashboard}', [VisitorController::class, 'changeStatus'])->name('visitor.change-status');
    Route::get('get-visitors', [VisitorController::class, 'getVisitor'])->name('visitors.get-visitors');
    Route::get('get-visitor-list', [DashboardController::class, 'getVisitor'])->name('get-visitor.list');
    Route::get('visitor/disable/{id}', [VisitorController::class, 'visitorDisable'])->name('visitors.disable');
    Route::get('visitor/unblock/{id}', [VisitorController::class, 'visitorUnblock'])->name('visitors.unblock');

    //report
    //report
    Route::get('admin-visitor-report', [VisitorReportController::class, 'index'])->name('admin.report.visitor.index');
    Route::get('admin-visitor-report/list', [VisitorReportController::class, 'list'])->name('report.visitor.list');

    Route::get('admin-pre-registers-report', [PreRegistersReportController::class, 'index'])->name('admin.report.pre-registers.index');
    Route::get('admin-pre-registers-report/list', [PreRegistersReportController::class, 'list'])->name('report.pre-registers.list');

    Route::get('admin-attendance-report', [AttendanceReportController::class, 'index'])->name('admin.report.attendance.index');
    Route::get('admin-attendance-report/list', [AttendanceReportController::class, 'list'])->name('report.attendance.list');

    Route::get('admin-pre-registers-report', [PreRegistersReportController::class, 'index'])->name('admin-pre-registers-report.index');
    Route::post('admin-pre-registers-report', [PreRegistersReportController::class, 'index'])->name('admin-pre-registers-report.post');

    Route::get('attendance-report', [AttendanceReportController::class, 'index'])->name('attendance-report.index');
    Route::post('attendance-report', [AttendanceReportController::class, 'index'])->name('attendance-report.post');

    Route::get('employee-report', [EmployeeReportController::class, 'index'])->name('employee-report.index');
    Route::post('employee-report', [EmployeeReportController::class, 'index'])->name('employee-report.post');


    Route::post('admin-attendance/clockin', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('admin-attendance/clockout', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');

    Route::resource('attendance', AttendanceController::class);
    Route::get('get-attendance', [AttendanceController::class, 'getAttendance'])->name('attendance.get-attendance');
    //language
    Route::resource('language', LanguageController::class);
    Route::get('get-language', [LanguageController::class, 'getLanguage'])->name('language.get-language');
    Route::get('language/change-status/{id}/{status}', [LanguageController::class, 'changeStatus'])->name('language.change-status');

    //Addons
    Route::resource('addons', AddonController::class);
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'siteSettingUpdate'])->name('site-update');
        Route::get('sms', [SettingController::class, 'smsSetting'])->name('sms');
        Route::post('sms', [SettingController::class, 'smsSettingUpdate'])->name('sms-update');
        Route::get('fcm-notification', [SettingController::class, 'fcmSetting'])->name('fcm');
        Route::post('fcm-notification', [SettingController::class, 'fcmSettingUpdate'])->name('fcm-update');
        Route::get('email', [SettingController::class, 'emailSetting'])->name('email');
        Route::post('email', [SettingController::class, 'emailSettingUpdate'])->name('email-update');
        Route::get('emailtemplate', [SettingController::class, 'emailTemplateSetting'])->name('email-template');
        Route::post('emailtemplate', [SettingController::class, 'mailTemplateSettingUpdate'])->name('email-template-update');
        Route::get('homepage', [SettingController::class, 'homepageSetting'])->name('homepage');
        Route::post('homepage', [SettingController::class, 'homepageSettingUpdate'])->name('homepage-update');
        Route::get('whatsapp', [SettingController::class, 'whatsappSetting'])->name('whatsapp-message');
        Route::post('whatsapp', [SettingController::class, 'whatsappSettingupdate'])->name('whatsapp-message-update');
        Route::get('theme', [SettingController::class, 'themeSetting'])->name('theme');
        Route::post('theme', [SettingController::class, 'themeSettingUpdate'])->name('theme-update');
        Route::get('page', [SettingController::class, 'pageSetting'])->name('page');
        Route::post('page', [SettingController::class, 'pageSettingUpdate'])->name('page-update');
    });


});



/*Multi step form*/

Route::group(['middleware' => ['installed']], function () {
    Route::group(['middleware' => ['frontend']], function () {
        Route::get('/home', [CheckInController::class, 'index'])->name('home');
        Route::get('/', [CheckInController::class, 'index'])->name('/');
        Route::get('/scanqr', [CheckInController::class, 'scanQr'])->name('check-in.scan-qr');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

        Route::post('/checkout', [CheckoutController::class, 'getVisitor'])->name('checkout.index');

        Route::get('/checkout/update/{visitingDetails}', [CheckoutController::class, 'update'])->name('checkout.update');

        Route::get('/check-in', [CheckInController::class, 'index'])->name('check-in');
        Route::get('/check-in/create-step-one', [CheckInController::class, 'createStepOne'])->name('check-in.step-one');
        Route::post('/check-in/create-step-one', [CheckInController::class, 'postCreateStepOne'])->name('check-in.step-one.next');
        Route::get('/check-in/create-step-two', [CheckInController::class, 'createStepTwo'])->name('check-in.step-two');
        Route::post('/check-in/create-step-two', [CheckInController::class, 'store'])->name('check-in.step-two.next');

        Route::get('/check-in/show/{id}', [CheckInController::class, 'show'])->name('check-in.show');
        Route::get('/check-in/return', [CheckInController::class, 'visitor_return'])->name('check-in.return');
        Route::post('/check-in/return', [CheckInController::class, 'find_visitor'])->name('check-in.find.visitor');

        Route::get('/check-in/pre-registered', [CheckInController::class, 'pre_registered'])->name('check-in.pre.registered');
        Route::post('/check-in/pre-registered', [CheckInController::class, 'find_pre_visitor'])->name('check-in.find.pre.visitor');

        /**
         * Scan Qr Code
         */
        Route::get('check-in/visitor-details/{visitorPhone}', [CheckInController::class, 'visitorDetails'])->name('checkin.visitor-details');
        Route::get('check-in/pre-registered/visitor-details/{visitorPhone}', [CheckInController::class, 'preVisitorDetails'])->name('checkin.pre-visitor-details');
    });
});

Route::get('visitor/change-status/{status}/{token}', [FrontendController::class, 'changeStatus']);

Route::get('qrcode/{number}', [FrontendController::class, 'qrcode'])->name('qrcode');
Route::get('terms_and_conditions', [FrontendController::class, 'termsConditions'])->name('terms_and_conditions.view');

// Add this at the very end of index.php
Route::dispatch();
