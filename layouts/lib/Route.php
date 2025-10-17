<?php
namespace Layouts\Lib;

class Route
{
    protected static $routes = [];
    protected static $groupStack = [];

    /* -----------------------------------------
     * Basic HTTP verbs
     * --------------------------------------- */
    public static function get(string $uri, $action)
    {
        return self::addRoute('GET', $uri, $action);
    }
    public static function post(string $uri, $action)
    {
        return self::addRoute('POST', $uri, $action);
    }
    public static function put(string $uri, $action)
    {
        return self::addRoute('PUT', $uri, $action);
    }
    public static function patch(string $uri, $action)
    {
        return self::addRoute('PATCH', $uri, $action);
    }
    public static function delete(string $uri, $action)
    {
        return self::addRoute('DELETE', $uri, $action);
    }
    public static function options(string $uri, $action)
    {
        return self::addRoute('OPTIONS', $uri, $action);
    }

    public static function any(string $uri, $action)
    {
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'] as $verb) {
            self::addRoute($verb, $uri, $action);
        }
        return new RouteFluent(end(self::$routes));
    }

    /* -----------------------------------------
     * Redirect helper
     * --------------------------------------- */
    /* -----------------------------------------
     * Redirect helper - UPDATED WITH BASE PATH
     * --------------------------------------- */
    public static function redirect(string $from, string $to, int $status = 302)
    {
        return self::addRoute('GET', $from, function () use ($to, $status) {
            $basePath = self::getBasePath();
            $redirectTo = $basePath . $to;

            header("Location: {$redirectTo}", true, $status);
            exit;
        });
    }

    /* -----------------------------------------
     * Resource routes (Laravel-style)
     * --------------------------------------- */
    public static function resource(string $name, string $controller)
    {
        $base = trim($name, '/');
        $as = self::currentGroupNamePrefix();

        self::get($base, [$controller, 'index'])->name($as . $base . '.index');
        self::get("$base/create", [$controller, 'create'])->name($as . $base . '.create');
        self::post($base, [$controller, 'store'])->name($as . $base . '.store');
        self::get("$base/{id}", [$controller, 'show'])->name($as . $base . '.show');
        self::get("$base/{id}/edit", [$controller, 'edit'])->name($as . $base . '.edit');
        self::put("$base/{id}", [$controller, 'update'])->name($as . $base . '.update');
        self::delete("$base/{id}", [$controller, 'destroy'])->name($as . $base . '.destroy');
    }

    /* -----------------------------------------
     * Grouping
     * --------------------------------------- */
    public static function group(array $attributes, \Closure $callback)
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    /* Laravel-style fluent group starters */
    public static function prefix(string $prefix)
    {
        return new RouteGroupFluent(['prefix' => $prefix]);
    }

    public static function name(string $as)
    {
        return new RouteGroupFluent(['as' => $as]);
    }

    public static function middleware($middleware)
    {
        return new RouteGroupFluent(['middleware' => (array) $middleware]);
    }

    public static function namespace(string $namespace)
    {
        return new RouteGroupFluent(['namespace' => $namespace]);
    }

    /* -----------------------------------------
     * Route registration
     * --------------------------------------- */
    protected static function addRoute(string $method, string $uri, $action)
    {
        $prefix = self::currentGroupPrefix();
        $middleware = self::currentGroupMiddleware();
        $namespace = self::currentGroupNamespace();
        $as = self::currentGroupNamePrefix();

        if ($namespace && is_array($action) && isset($action[0])) {
            $action[0] = trim($namespace, '\\') . '\\' . $action[0];
        }

        $uri = '/' . trim($prefix . '/' . trim($uri, '/'), '/');
        if ($uri === '//')
            $uri = '/';

        $route = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'action' => $action,
            'middleware' => $middleware,
            'name' => $as,
        ];

        self::$routes[] = &$route;
        return new RouteFluent($route);
    }

    /* -----------------------------------------
     * Base Path Detection
     * --------------------------------------- */
    public static function getBasePath()
    {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        return ($scriptDir !== '/' && $scriptDir !== '\\') ? $scriptDir : '';
    }

    /* -----------------------------------------
     * Dispatcher - UPDATED WITH MIDDLEWARE FIX
     * --------------------------------------- */
    public static function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Remove base path
        $basePath = self::getBasePath();
        if ($basePath && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }

        if (empty($requestUri) || $requestUri === '') {
            $requestUri = '/';
        }

        foreach (self::$routes as $route) {
            $pattern = preg_replace('/\{[^\/]+\}/', '([^/]+)', $route['uri']);
            if (preg_match("#^{$pattern}$#", $requestUri, $matches) && $route['method'] === $method) {
                array_shift($matches);

                // Handle middleware with parameter support
                $middlewareResult = self::executeMiddleware($route['middleware'], $requestUri);
                if ($middlewareResult !== true) {
                    return $middlewareResult; // Middleware blocked or returned response
                }

                // Execute the route action
                return self::executeAction($route['action'], $matches);
            }
        }

        http_response_code(404);
        echo "404 Not Found - No route found for: $method $requestUri";
    }

    /* -----------------------------------------
     * Middleware Execution - NEW METHOD
     * --------------------------------------- */
    protected static function executeMiddleware($middlewares, $requestUri)
    {
        foreach ($middlewares as $mw) {
            if (is_callable($mw)) {
                // Check middleware function signature
                $reflection = new \ReflectionFunction($mw);
                $paramCount = $reflection->getNumberOfParameters();

                if ($paramCount === 2) {
                    // New style: expects ($request, $next) parameters
                    $result = $mw($requestUri, function () {
                        return true; // Continue to next middleware/route
                    });
                } else {
                    // Old style: no parameters
                    $result = $mw();
                }

                // Check middleware result
                if ($result === false) {
                    http_response_code(403);
                    return "Middleware blocked access";
                }

                // If middleware returns a response, stop execution
                if ($result !== true && $result !== null) {
                    return $result;
                }
            }
        }
        return true; // All middleware passed
    }

    /* -----------------------------------------
     * Action Execution - CORRECTED VERSION
     * --------------------------------------- */
    protected static function executeAction($action, $matches)
    {
        // Handle closures and named functions
        if ($action instanceof \Closure || (is_string($action) && function_exists($action))) {
            return $action(...$matches);
        }

        // Handle controller@method arrays
        if (is_array($action) && count($action) === 2) {
            [$controllerClass, $methodName] = $action;

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller class not found: {$controllerClass}");
            }

            $controllerInstance = new $controllerClass;

            if (!method_exists($controllerInstance, $methodName)) {
                throw new \Exception("Method not found: {$controllerClass}::{$methodName}");
            }

            return $controllerInstance->{$methodName}(...$matches);
        }

        throw new \Exception("Invalid action format. Expected closure, function, or [Controller::class, 'method']");
    }
    /* -----------------------------------------
     * Debug Methods
     * --------------------------------------- */
    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function debugRoutes()
    {
        echo "<h3>Debug: Registered Routes</h3>";
        echo "<pre>";
        foreach (self::$routes as $index => $route) {
            echo "[$index] {$route['method']} {$route['uri']} -> ";
            if (is_array($route['action'])) {
                echo is_object($route['action'][0]) ? get_class($route['action'][0]) : $route['action'][0];
                echo "@{$route['action'][1]}";
            } else {
                echo 'Closure';
            }
            echo " | Middleware: " . (count($route['middleware']) ? implode(', ', $route['middleware']) : 'none');
            echo "\n";
        }
        echo "</pre>";
    }

    public static function debugCurrentRequest()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $basePath = self::getBasePath();

        echo "<h3>Debug: Current Request</h3>";
        echo "Method: $method<br>";
        echo "Original URI: $requestUri<br>";
        echo "Base Path: '$basePath'<br>";
        echo "Processed URI: " . (strpos($requestUri, $basePath) === 0 ?
            substr($requestUri, strlen($basePath)) : $requestUri) . "<br>";
    }

    /* -----------------------------------------
     * Helpers for group context
     * --------------------------------------- */
    protected static function currentGroupPrefix()
    {
        $prefix = '';
        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= trim($group['prefix'], '/') . '/';
            }
        }
        return trim($prefix, '/');
    }

    protected static function currentGroupNamespace()
    {
        $namespace = '';
        foreach (self::$groupStack as $group) {
            if (isset($group['namespace'])) {
                $namespace .= trim($group['namespace'], '\\') . '\\';
            }
        }
        return trim($namespace, '\\');
    }

    protected static function currentGroupNamePrefix()
    {
        $as = '';
        foreach (self::$groupStack as $group) {
            if (isset($group['as'])) {
                $as .= $group['as'];
            }
        }
        return $as;
    }

    protected static function currentGroupMiddleware()
    {
        $middleware = [];
        foreach (self::$groupStack as $group) {
            if (isset($group['middleware'])) {
                $middleware = array_merge($middleware, (array) $group['middleware']);
            }
        }
        return $middleware;
    }

    public static function getRouteByName($name)
{
    foreach (self::$routes as $route) {
        if (isset($route['name']) && $route['name'] === $name) {
            return $route;
        }
    }
    return null;
}
}

/* --------------------------------------------------
 * Fluent builder for ->name() and ->middleware()
 * -------------------------------------------------- */
class RouteFluent
{
    protected $route;

    public function __construct(&$route)
    {
        $this->route = &$route;
    }

    public function name(string $name)
    {
        $this->route['name'] .= $name;
        return $this;
    }

    public function middleware($middleware)
    {
        $this->route['middleware'] = array_merge($this->route['middleware'], (array) $middleware);
        return $this;
    }
}

/* --------------------------------------------------
 * Fluent group builder (Laravel-style)
 * -------------------------------------------------- */
class RouteGroupFluent
{
    protected $attributes = [];

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function prefix(string $prefix)
    {
        $this->attributes['prefix'] = trim(($this->attributes['prefix'] ?? '') . '/' . $prefix, '/');
        return $this;
    }

    public function name(string $as)
    {
        $this->attributes['as'] = ($this->attributes['as'] ?? '') . $as;
        return $this;
    }

    public function middleware($middleware)
    {
        $this->attributes['middleware'] = array_merge($this->attributes['middleware'] ?? [], (array) $middleware);
        return $this;
    }

    public function namespace(string $namespace)
    {
        $this->attributes['namespace'] = trim(($this->attributes['namespace'] ?? '') . '\\' . $namespace, '\\');
        return $this;
    }

    public function group(\Closure $callback)
    {
        Route::group($this->attributes, $callback);
    }
}