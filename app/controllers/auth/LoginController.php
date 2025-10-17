<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use Layouts\Lib\Route;

class LoginController extends Controller
{
    public function showLoginForm()
    {   
        return $this->view('auth.login');
    }

    
}