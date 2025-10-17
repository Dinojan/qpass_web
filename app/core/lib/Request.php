<?php
namespace App\Core\Lib;
use App\Core\Lib\Validator;
use Exception;
class Request
{
    protected $data = [];

    public function __construct()
    {
        // Merge GET and POST data
        $this->data = array_merge($_GET, $_POST);
    }

    /**
     * Get a single input value by key (POST preferred)
     */
    public function input($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->sanitize($this->data[$key]) : $default;
    }

    /**
     * Get a GET parameter only
     */
    public function get($key, $default = null)
    {
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    /**
     * Check if a key exists
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Return all input data
     */
    public function all()
    {
        $cleanData = [];
        foreach ($this->data as $key => $value) {
            $cleanData[$key] = $this->sanitize($value);
        }
        return $cleanData;
    }

    /**
     * Validate request data with rules
     */
    public function validate(array $rules, array $messages = [])
    {
        $validator = Validator::make($this->all(), $rules, $messages);
        $valid = $validator->validate();

        if (!$valid) {
            // You can throw an exception, return errors, or handle as needed
            throw new Exception(json_encode($validator->errors()));
        }

        return true;
    }

    /**
     * Request method
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Current full URL
     */
    public function url()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Access session helper
     */
    public function session()
    {
        return Session(); // uses your global Session() helper
    }

    /**
     * Sanitize input
     */
    protected function sanitize($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
