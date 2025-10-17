<?php
namespace App\Vonder;
interface Session
{
    /**
     * Start the session.
     */
    public function start();

    /**
     * Get a value from the session.
     */
    public function get($key, $default = null);

    /**
     * Put a value into the session.
     */
    public function put($key, $value);

    /**
     * Check if a key exists in the session.
     */
    public function has($key);

    /**
     * Remove a key from the session.
     */
    public function forget($key);

    /**
     * Remove all session data.
     */
    public function flush();

    /**
     * Get the session ID.
     */
    public function getId();

    /**
     * Regenerate the session ID.
     */
    public function regenerate();
}
