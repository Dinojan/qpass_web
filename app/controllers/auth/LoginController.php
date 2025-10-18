<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Core\Lib\Request;
use App\Models\User;
use App\Core\Lib\Auth as Authenticator;
use App\Core\Lib\Session;

class LoginController extends Controller
{
    protected $auth;
    protected $session;

    public function __construct()
    {
        $this->auth = new Authenticator();
        $this->session = new Session(); // Add this line
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        return $this->view('auth.login');
    }

    public function login(Request $request = null)
    {
        if (!$request) {
            $request = new Request();
        }

        // Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if ($this->auth->attempt($credentials, $remember)) {
            // Authentication passed...
            $this->session->regenerate(); // Use instance method

            return $this->sendLoginResponse($request);
        }

        // Authentication failed...
        return $this->sendFailedLoginResponse($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->session->regenerate(); // Use instance method

        // Check if intended URL exists, otherwise redirect to dashboard
        $intended = $this->session->get('url.intended'); // Use instance method

        if ($intended) {
            $this->session->forget('url.intended'); // Use instance method
            return redirect()->to($intended);
        }

        return redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        flash_old($request->all());
        set_errors(['email' => trans('auth.failed')]);

        return redirect()->back()->withInput();
    }

    public function logout(Request $request)
    {
        $this->auth->logout();

        $this->session->flush(); // Use instance method
        $this->session->regenerate(); // Use instance method

        return redirect('/');
    }

    protected function redirectPath()
    {
        return '/admin/dashboard';
    }

    protected function username()
    {
        return 'email';
    }
}