<?php
namespace App\Core\Lib;

use App\Models\User;

class Auth
{
    protected $user;
    protected $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function attempt($credentials, $remember = false)
    {
        // Debug
        error_log("Auth attempt: " . $credentials['email']);
        // Find user by email
        $user = User::where('email', $credentials['email']);
        
        // Check if we got a result
        if (is_array($user)) {
            $user = !empty($user) ? $user[0] : null;
        }
        
        error_log("User found: " . ($user ? 'YES' : 'NO'));

        if (!$user) {
            error_log("User not found");
            return false;
        }

        // Check password
        if ($user->verifyPassword($credentials['password'])) {
            error_log("Password verified");
            $this->login($user, $remember);
            return true;
        }

        error_log("Password verification failed");
        return false;
    }

    public function login(User $user, $remember = false)
    {
        // Store user in session using instance method
        $this->session->put('user', [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);

        if ($remember) {
            // Set remember token (simplified)
            $this->setRememberToken($user);
        }

        $this->user = $user;
    }

    public function logout()
    {
        $this->session->forget('user');
        $this->user = null;
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $userData = $this->session->get('user');
        if ($userData) {
            $this->user = User::find($userData['id']);
            return $this->user;
        }

        return null;
    }

    public function check()
    {
        return $this->user() !== null;
    }

    public function guest()
    {
        return !$this->check();
    }

    protected function setRememberToken(User $user)
    {
        // Implement remember token logic here
    }
}