<?php
namespace App\Controllers;

use App\Core\Lib\Request;

class Controller
{
    protected $middleware = [];

    /**
     * Constructor - apply middleware
     */
    public function __construct()
    {
        $this->applyMiddleware();
    }

    /**
     * Apply middleware to controller methods
     */
    protected function applyMiddleware()
    {
        foreach ($this->middleware as $middleware => $options) {
            if (is_numeric($middleware)) {
                $middleware = $options;
                $options = [];
            }

            $this->applySingleMiddleware($middleware, $options);
        }
    }

    /**
     * Apply a single middleware
     */
    protected function applySingleMiddleware($middleware, $options = [])
    {
        $except = $options['except'] ?? [];
        $only = $options['only'] ?? [];

        // Get the current method being called
        $currentMethod = $this->getCurrentMethod();

        // Skip if current method is in except list
        if (!empty($except) && in_array($currentMethod, $except)) {
            return;
        }

        // Apply only if current method is in only list (if specified)
        if (!empty($only) && !in_array($currentMethod, $only)) {
            return;
        }

        // Execute the middleware
        $this->executeMiddleware($middleware);
    }

    /**
     * Get the current method being called
     */
    protected function getCurrentMethod()
    {
        // This is a simplified approach - you might need to adjust based on your routing
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // You can enhance this to better detect the method
        return strtolower($method);
    }

    /**
     * Execute middleware function
     */
    protected function executeMiddleware($middleware)
    {
        if (function_exists($middleware)) {
            $middleware();
        } elseif (class_exists($middleware)) {
            $middlewareInstance = new $middleware();
            if (method_exists($middlewareInstance, 'handle')) {
                $middlewareInstance->handle();
            }
        }
    }

    /**
     * Register middleware for the controller
     */
    protected function middleware($middleware, array $options = [])
    {
        $this->middleware[$middleware] = $options;
    }

    /**
     * Render a view
     */
    protected function view(string $view, array $data = [])
    {
        return view($view, $data);
    }

    /**
     * Validate request data
     */
    protected function validate(Request $request, array $rules)
    {
        // Simple validation implementation
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $request->input($field);
            $rulesArray = explode('|', $rule);

            foreach ($rulesArray as $singleRule) {
                if ($singleRule === 'required' && empty($value)) {
                    $errors[$field][] = "The {$field} field is required.";
                } elseif ($singleRule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The {$field} must be a valid email address.";
                } elseif (strpos($singleRule, 'min:') === 0) {
                    $min = (int) str_replace('min:', '', $singleRule);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters.";
                    }
                }
            }
        }

        if (!empty($errors)) {
            flash_old($request->all());
            set_errors($errors);
            redirect()->back();
            exit;
        }

        return true;
    }

    /**
     * Get authenticated user
     */
    protected function user()
    {
        if (function_exists('authHelper')) {
            return authHelper()->user();
        }
        return null;
    }

    /**
     * Check if user is authenticated
     */
    protected function check()
    {
        return $this->user() !== null;
    }

    /**
     * Get guard instance
     */
    protected function guard()
    {
        if (function_exists('authHelper')) {
            return authHelper();
        }
        return null;
    }
     protected function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }
}
