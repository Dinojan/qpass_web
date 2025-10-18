<?php
namespace App\Core\Lib;

use App\Vonder\Session as SessionInterface;

class Session implements SessionInterface
{
    private static $instance;

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Start the session.
     */
    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $this;
    }

    /**
     * Get a value from the session.
     */
    public function get($key, $default = null)
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Put a value into the session.
     */
    public function put($key, $value)
    {
        $this->start();
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * Check if a key exists in the session.
     */
    public function has($key)
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a key from the session.
     */
    public function forget($key)
    {
        $this->start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return $this;
    }

    /**
     * Remove all session data.
     */
    public function flush()
    {
        $this->start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return $this;
    }

    /**
     * Get the session ID.
     */
    public function getId()
    {
        $this->start();
        return session_id();
    }

    /**
     * Regenerate the session ID.
     */
    public function regenerate()
    {
        $this->start();
        session_regenerate_id(true);
        return $this;
    }

    /**
     * Static methods for convenience
     */
    public static function set($key, $value)
    {
        return self::getInstance()->put($key, $value);
    }

   

    public static function remove($key)
    {
        return self::getInstance()->forget($key);
    }

    public static function destroy()
    {
        return self::getInstance()->flush();
    }

    public static function id()
    {
        return self::getInstance()->getId();
    }

    public static function regenerateToken()
    {
        return self::getInstance()->regenerate();
    }
}