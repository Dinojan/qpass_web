<?php 
// app/vonder
$vendorFiles = glob(DIR_APP . 'vonder/*.php');

foreach ($vendorFiles as $file) {
    require_once $file;
}
// app/core/libraries
$libFiles = glob(DIR_CORE_LIB . '*.php');

foreach ($libFiles as $file) {
    require_once $file;
}

// app/core/helpers
$helperFiles = glob(DIR_CORE_HELPER . '*.php');

foreach ($helperFiles as $file) {
    require_once $file;
}
// app/models
$modelFiles = glob(DIR_MODELS . '*.php');

foreach ($modelFiles as $file) {
    require_once $file;
}
// app/Enums 
$enumFiles = glob(DIR_ENUMS . '*.php');

foreach ($enumFiles as $file) {
    require_once $file;
}
//app/middleware
$middlewareFiles = glob(DIR_MIDDLEWARE . '*.php');

foreach ($middlewareFiles as $file) {
    require_once $file;
}
// app/controllers
$controllerFiles = glob(DIR_CONTROLLERS . '*.php');

foreach ($controllerFiles as $file) {
    require_once $file;
}
//app/controllers/admin
$adminControllerFiles = glob(DIR_CONTROLLERS_ADMIN . '*.php');

foreach ($adminControllerFiles as $file) {
    require_once $file;
}
//app/controllers/auth
$authControllerFiles = glob(DIR_CONTROLLERS_AUTH . '*.php');

foreach ($authControllerFiles as $file) {
    require_once $file;
}
// app/controllers/home
$homeControllerFiles = glob(DIR_CONTROLLERS_HOME . '*.php');

foreach ($homeControllerFiles as $file) {
    require_once $file;
}