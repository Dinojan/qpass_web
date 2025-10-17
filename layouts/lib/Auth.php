<?php
namespace Layouts\Lib;

use Layouts\Lib\Route;

class Auth
{
    /**
     * Register all the authentication routes.
     */
    public static function routes()
    {
        // Login routes
        Route::get('login', [\App\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [\App\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
        Route::post('logout', [\App\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

        // Registration routes
        Route::get('register', [\App\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [\App\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');

        // Password reset routes
        Route::get('password/reset', [\App\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [\App\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [\App\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [\App\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

        // Email verification (optional)
        Route::get('email/verify', [\App\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', [\App\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
        Route::post('email/resend', [\App\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');
    }
}
