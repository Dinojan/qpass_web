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
     * Get a single input value by key
     */
    public function input($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->sanitize($this->data[$key]) : $default;
    }

    /**
     * Get only specific fields from the request
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->input($key);
        }

        return $results;
    }

    /**
     * Get all input except specified fields
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = $this->all();

        foreach ($keys as $key) {
            unset($results[$key]);
        }

        return $results;
    }

    /**
     * Check if a key exists and is not empty
     */
    public function filled($key)
    {
        return $this->has($key) && !empty($this->data[$key]);
    }

    /**
     * Get a GET parameter only
     */
    public function get($key, $default = null)
    {
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $default;
    }

    /**
     * Get a POST parameter only
     */
    public function post($key, $default = null)
    {
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $default;
    }

    /**
     * Check if a key exists
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Check if any of the keys exist
     */
    public function hasAny($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if all keys exist
     */
    public function hasAll($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if the request contains a non-empty value for any of the given inputs
     */
    public function anyFilled($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        
        foreach ($keys as $key) {
            if ($this->filled($key)) {
                return true;
            }
        }
        
        return false;
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
     * Get the request method
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Check if the request method matches
     */
    public function isMethod($method)
    {
        return strtoupper($method) === $this->method();
    }

    /**
     * Check if request is AJAX
     */
    public function ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request expects JSON
     */
    public function expectsJson()
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($accept, '/json') !== false || $this->ajax();
    }

    /**
     * Get current full URL
     */
    public function url()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the current path
     */
    public function path()
    {
        return parse_url($this->url(), PHP_URL_PATH);
    }

    /**
     * Get the current route
     */
    public function route()
    {
        return str_replace('/', '', $this->path());
    }

    /**
     * Get the IP address
     */
    public function ip()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    /**
     * Get user agent
     */
    public function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Validate request data with rules
     */
    public function validate(array $rules, array $messages = [])
    {
        $validator = Validator::make($this->all(), $rules, $messages);
        $valid = $validator->validate();

        if (!$valid) {
            throw new Exception(json_encode($validator->errors()));
        }

        return true;
    }

    /**
     * Access session helper
     */
    public function session()
    {
        return Session();
    }

    /**
     * Get cookie
     */
    public function cookie($key, $default = null)
    {
        return $_COOKIE[$key] ?? $default;
    }

    /**
     * Get file upload
     */
    public function file($key)
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Check if file was uploaded
     */
    public function hasFile($key)
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Sanitize input
     */
    protected function sanitize($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }

    /**
     * Magic getter for property-style access
     */
    public function __get($key)
    {
        return $this->input($key);
    }

    /**
     * Check if property exists
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
}