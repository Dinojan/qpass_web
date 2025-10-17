<?php
namespace App\Vonder;
use App\Vonder\Session;
class Store implements Session
{
    /**
     * The session ID.
     */
    protected $id;

    /**
     * The session name.
     */
    protected $name;

    public function __construct($name = 'PHPSESSID')
    {
        $this->name = $name;
        $this->start();
    }

    /**
     * Start the session.
     */
    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($this->name);
            session_start();
        }

        $this->id = session_id();
        return true;
    }

    /**
     * Get a value from the session.
     */
    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Store a value in the session.
     */
    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Determine if the session has a given key.
     */
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a key from the session.
     */
    public function forget($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Remove all items from the session.
     */
    public function flush()
    {
        $_SESSION = [];
    }

    /**
     * Get the session ID.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Regenerate the session ID.
     */
    public function regenerate()
    {
        session_regenerate_id(true);
        $this->id = session_id();
    }
}
