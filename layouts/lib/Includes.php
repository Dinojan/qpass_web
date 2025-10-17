<?php 
// db
require_once DIR_CORE_LIB . 'DB.php';
require_once DIR_CORE_LIB . 'Layout.php';
require_once DIR_CORE_LIB . 'Hash.php';
require_once DIR_CORE_LIB . 'Validator.php';
// vendors
require_once DIR_APP . 'vonder/Session.php';
// Layout library
require_once DIR_CORE.'bootstrap.php';
require_once DIR_CORE.'Session.php';


// middleware
require_once DIR_MIDDLEWARE.'auth.php';
require_once DIR_MIDDLEWARE.'installed.php';
require_once DIR_MIDDLEWARE.'backend_permission.php';


// Routes library
require_once DIR_LAYOUTS_LIB.'Route.php';
require_once DIR_LAYOUTS_LIB.'Auth.php';
// controllers
// controllers/admin

require_once DIR_CONTROLLERS.'BackendController.php';
require_once DIR_CONTROLLERS.'Controller.php';
use App\Controllers\BackendController;
require_once DIR_CONTROLLERS_ADMIN.'AddonController.php';
require_once DIR_CONTROLLERS_ADMIN.'AdminUserController.php';
require_once DIR_CONTROLLERS_ADMIN.'AttendanceController.php';
require_once DIR_CONTROLLERS_ADMIN.'AttendanceReportController.php';
require_once DIR_CONTROLLERS_ADMIN.'BookingController.php';
require_once DIR_CONTROLLERS_ADMIN.'DashboardController.php';
require_once DIR_CONTROLLERS_ADMIN.'DepartmentsController.php';
require_once DIR_CONTROLLERS_ADMIN.'DesignationsController.php';
require_once DIR_CONTROLLERS_ADMIN.'EmployeeController.php';
require_once DIR_CONTROLLERS_ADMIN.'EmployeeReportController.php';
require_once DIR_CONTROLLERS_ADMIN.'LanguageController.php';
require_once DIR_CONTROLLERS_ADMIN.'LocalizationController.php';
require_once DIR_CONTROLLERS_ADMIN.'PreRegisterController.php';
require_once DIR_CONTROLLERS_ADMIN.'PreRegistersReportController.php';
require_once DIR_CONTROLLERS_ADMIN.'ProfileController.php';
require_once DIR_CONTROLLERS_ADMIN.'RoleController.php';
require_once DIR_CONTROLLERS_ADMIN.'SettingController.php';
require_once DIR_CONTROLLERS_ADMIN.'VisitorController.php';
require_once DIR_CONTROLLERS_ADMIN.'VisitorDashboardController.php';
require_once DIR_CONTROLLERS_ADMIN.'VisitorReportController.php';
require_once DIR_CONTROLLERS_ADMIN.'WebNotificationController.php';
require_once DIR_CONTROLLERS_ADMIN.'DashboardController.php';


// controllers/auth
require_once DIR_CONTROLLERS_AUTH.'LoginController.php';
// controllers/home
require_once DIR_CONTROLLERS_HOME.'CheckInController.php';
require_once DIR_CONTROLLERS_HOME.'CheckoutController.php';