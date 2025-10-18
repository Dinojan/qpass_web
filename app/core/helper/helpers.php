<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Translate a key using language files
 *
 * @param string $key   The translation key, e.g. 'all.pre_registered_visitor_details'
 * @param string|null $locale Optional locale, defaults to 'en'
 * @return string
 */
function __($key, $locale = null)
{
    static $translations = [];

    $locale = $locale ?? ($_SESSION['locale'] ?? 'en');

    if (!isset($translations[$locale])) {
        $translations[$locale] = [];

        $langPath = DIR_PUBLIC_ASSETS . '/lang/' . $locale;

        if (is_dir($langPath)) {
            foreach (glob($langPath . '/*.php') as $file) {
                $filename = basename($file, '.php'); // e.g., 'all', 'auth'
                $translations[$locale][$filename] = include $file;
            }
        }
    }

    $keys = explode('.', $key);
    $text = $translations[$locale];

    foreach ($keys as $k) {
        if (isset($text[$k])) {
            $text = $text[$k];
        } else {
            return $key; // return key if not found
        }
    }

    return $text;
}
// ✅ Start section (same as @section in Blade)
if (!function_exists('start_section')) {
    function start_section($name)
    {
        App\Core\Lib\Layout::start($name);
    }
}

// ✅ End section (same as @endsection)
if (!function_exists('end_section')) {
    function end_section()
    {
        App\Core\Lib\Layout::end();
    }
}

// ✅ Yield content (same as @yield)
if (!function_exists('yield_content')) {
    function yield_content($name)
    {
        App\Core\Lib\Layout::yield($name);
    }
}

// ✅ Include another partial (same as @include)
if (!function_exists('include_view')) {
    function include_view($view, $data = [])
    {
        $file = DIR_VIEWS . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';
        if (file_exists($file)) {
            extract($data);
            include $file;
        } else {
            echo "❌ Partial not found: {$file}";
        }
    }
}

// ✅ Stack system for pushing custom CSS/JS
if (!function_exists('stack_content')) {
    function stack_content($name)
    {
        App\Core\Lib\Layout::yield($name);
    }
}

if (!function_exists('start_stack')) {
    function start_stack($name)
    {
        App\Core\Lib\Layout::start($name,true); // starts output buffer for the stack
    }
}

if (!function_exists('end_stack')) {
    function end_stack()
    {
        App\Core\Lib\Layout::end(); // ends buffer and saves it to the stack
    }
}

if (!function_exists('stack_content')) {
    function stack_content($name)
    {
        App\Core\Lib\Layout::yield($name); // output all content of the stack
    }
}
if (!function_exists('env')) {
    /**
     * Get the value of an environment variable
     *
     * @param string $key The environment variable key
     * @param mixed $default Default value if key does not exist
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        // Convert common values
        $lower = strtolower($value);
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
        if ($lower === 'null') return null;

        return $value;
    }
}



// assets functions
// if (!function_exists('asset')) {
//     function asset(string $path)
//     {
//         return DIR_PUBLIC_ASSETS . $path;
//     }
// }
if (!function_exists('asset')) {
    function asset(string $path)
    {
        // Get protocol + host (http://localhost)
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];

        // Get base directory of your app (e.g., /THIVA_ANNA/gatepass_v1)
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        // Build full URL
        $url = $protocol . $host . $base . '/public/assets/' . ltrim($path, '/');

        return $url;
    }
}


/**
 * Generate or return CSRF token
 *
 * @return string
 */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        // Generate a random token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Echo a hidden input field for CSRF token (like @csrf)
 */
function csrf_field()
{
    $token = csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Set validation errors in session
 * Call this after validating a form
 */
function set_errors(array $errors)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['errors'] = $errors;
}

/**
 * Get error message for a specific field
 */
function error($field)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!empty($_SESSION['errors'][$field])) {
        $msg = $_SESSION['errors'][$field];

        // Don't unset all errors immediately, only the one being shown
        unset($_SESSION['errors'][$field]);

        return $msg;
    }

    return null;
}


/**
 * Blade-like @error directive replacement
 * Usage: <?= error_message('email') ?>
 */
function error_message($field)
{
    $msg = error($field);

    if ($msg) {
        // If the error is an array, take only the first message
        if (is_array($msg)) {
            $msg = reset($msg); // get first element of array
        }

        echo '<div class="invalid-feedback ">' . htmlspecialchars($msg) . '</div>';
    }
}

/**
 * Retrieve old input value from previous request.
 * Mimics Laravel's old() helper.
 *
 * @param string $field Input field name
 * @param mixed $default Default value if not found
 * @return mixed
 */
function old($field, $default = null)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if old inputs exist in session
    if (!empty($_SESSION['old'][$field])) {
        $value = $_SESSION['old'][$field];
        unset($_SESSION['old'][$field]); // remove after fetching
        return htmlspecialchars($value);  // escape HTML
    }

    return $default;
}

/**
 * Save old input values to session
 * Call this after validation fails
 *
 * @param array $data Associative array of field => value
 */
function flash_old(array $data)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['old'] = $data;
}
// Redirect to a URL
function redirect_to($url)
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    } else {
        echo "<script>window.location.href='" . addslashes($url) . "';</script>";
        exit;
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [])
    {
        $viewPath = str_replace('.', '/', $view);
        $file = DIR_VIEWS . $viewPath . '.php';
       
        if (!file_exists($file)) {
            echo "❌ View not found: <b>{$file}</b><br>";
            die();
        }

        extract($data);

        // Initialize languages as empty array
        $languages = [];
        
        try {
            if (class_exists('App\Models\LanguageModel')) {
                $langModel = new \App\Models\LanguageModel();
                $languagesResult = $langModel->getAll();
                
                // Ensure $languages is always an array
                $languages = is_array($languagesResult) ? $languagesResult : [];
            }
        } catch (\Exception $e) {
            error_log("View helper - LanguageModel error: " . $e->getMessage());
            $languages = []; // Ensure it's always an array
        }

        // Clear previous sections
        if (method_exists('App\Core\Lib\Layout', 'clear')) {
            App\Core\Lib\Layout::clear();
        }

        ob_start();
        include $file;
        $content = ob_get_clean();

        // Determine layout
        if (strpos($view, 'admin.') === 0) {
            $layout = DIR_VIEWS . 'admin/app.php';
        } else {
            $layout = DIR_VIEWS . 'frontend/frontend.php';
        }

        if (file_exists($layout)) {
            include $layout;
        } else {
            echo $content;
        }
    }
}
if (!function_exists('themeSetting')) {
    function themeSetting($key)
    {
        return App\Models\ThemeSetting::where(['key' => $key]);//->first();
    }
}
// db()
if (!function_exists('db')) {
    function db()
    {
        return App\Core\Lib\DB::getInstance();
    }
}
// authHelper()
if (!function_exists('authHelper')) {
    function authHelper()
    {
        return new class {
            public function user()
            {
                return App\Models\AuthModel::user();
            }

            public function check()
            {
                return $this->user() !== null;
            }

            public function logout()
            {
                App\Models\AuthModel::logout();
            }
        };
    }
}

// setting()
if (!function_exists('setting')) {
    function setting($key)
    {
        return App\Models\Setting::where(['key' => $key]);
    }
}
if (!function_exists('Session')) {
    /**
     * Get the session instance
     */
    function Session()
    {
        static $session = null;
        
        if ($session === null) {
            if (class_exists('App\Core\Lib\Session')) {
                $session = new App\Core\Lib\Session();
                $session->start();
            } else {
                // Fallback if Session class doesn't exist
                return null;
            }
        }
        
        return $session;
    }
}

if (!function_exists('trans')) {
    /**
     * Simple translation loader with locale support
     *
     * Usage:
     *   trans('genders') — loads lang/{locale}/genders.php
     *   trans('genders.5') — returns a specific value
     */
    function trans(string $key)
    {
        // Default locale (if not set in session)
        $defaultLocale = 'en';
        
        // Detect current locale
        $locale = isset($_SESSION['applocale']) ? $_SESSION['applocale'] : $defaultLocale;

        // Split key (e.g. "genders.5")
        $parts = explode('.', $key);
        $file = array_shift($parts);

        // Build file path (e.g. lang/en/genders.php)
        $path = DIR_PUBLIC_ASSETS . "lang/{$locale}/{$file}.php";

        // Fallback path (lang/en/genders.php)
        $fallbackPath = DIR_PUBLIC_ASSETS. "lang/{$defaultLocale}/{$file}.php";

        // Load translations
        if (file_exists($path)) {
            $translations = include $path;
        } elseif (file_exists($fallbackPath)) {
            $translations = include $fallbackPath;
        } else {
            return $key; // If not found, return key as-is
        }

        // Return specific value or the whole array
        foreach ($parts as $part) {
            if (isset($translations[$part])) {
                $translations = $translations[$part];
            } else {
                return $key; // Key not found
            }
        }

        return $translations;
    }
}

if (!function_exists('optional')) {
    /**
     * Safely access properties or methods on nullable objects.
     *
     * @param mixed $value
     * @return object
     */
    function optional($value)
    {
        return new class($value)
        {
            protected $value;

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function __get($key)
            {
                if (is_object($this->value) && isset($this->value->$key)) {
                    return $this->value->$key;
                }
                return null;
            }

            public function __call($method, $args)
            {
                if (is_object($this->value) && method_exists($this->value, $method)) {
                    return call_user_func_array([$this->value, $method], $args);
                }
                return null;
            }
        };
    }
}

if (!function_exists('blank')) {
    /**
     * Determine if a value is "blank".
     * Blank means: null, empty string, string with only whitespace, empty array
     *
     * @param mixed $value
     * @return bool
     */
    function blank($value)
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_array($value)) {
            return empty($value);
        }

        return empty($value); // fallback for other types (int, bool, etc.)
    }
}


function base_url($path = '') {
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
    $path = ltrim($path, '/');
    return $basePath . '/' . $path;
}

/** Global helper */
if (!function_exists('db')) {
    function db()
    {
        return App\Core\Lib\DB::getInstance()->db();
    }
}

// Remove or comment out the Laravel-style functions and replace with:

if (!function_exists('route')) {
    /**
     * Generate URL for named routes with comprehensive error handling
     */
    function route($name, $parameters = [], $absolute = true)
    {
        $basePath = \Layouts\Lib\Route::getBasePath();
        
        try {
            // Try to get route by name from Route class
            if (method_exists('Layouts\Lib\Route', 'getRouteByName')) {
                $route = \Layouts\Lib\Route::getRouteByName($name);
                if ($route) {
                    $uri = $route['uri'];
                    
                    // Replace parameters in route URI
                    if (!empty($parameters)) {
                        foreach ($parameters as $key => $value) {
                            $uri = str_replace(['{' . $key . '}', '{' . $key . '?}'], $value, $uri);
                        }
                    }
                    
                    // Clean up any remaining optional parameters
                    $uri = preg_replace('/\{[^}]+\?\}/', '', $uri);
                    
                    return $basePath . $uri;
                }
            }
            
            // Fallback: check if it's a direct URL
            if (strpos($name, '/') === 0) {
                return $basePath . $name;
            }
            
            // Final fallback: use name as path
            return $basePath . '/' . ltrim($name, '/');
            
        } catch (Exception $e) {
            // Log error and return safe fallback
            error_log("Route helper error: " . $e->getMessage());
            return $basePath . '/';
        }
    }
}

if (!function_exists('app')) {
    /**
     * Simple app helper
     */
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return (object) [
                'path' => function($path = '') {
                    return dirname(__DIR__) . '/' . $path;
                }
            ];
        }
        
        // Handle specific cases
        switch ($abstract) {
            case 'url':
                return new class {
                    public function route($name, $parameters = [], $absolute = true) {
                        return route($name, $parameters, $absolute);
                    }
                };
            case 'redirect':
                return new class {
                    public function to($to = null, $status = 302, $headers = [], $secure = null) {
                        header('Location: ' . $to, true, $status);
                        exit;
                    }
                };
            default:
                return null;
        }
    }
}

if (!function_exists('redirect')) {
    /**
     * Comprehensive redirect helper
     */
    function redirect($to = null, $status = 302, $headers = [])
    {
        if (is_null($to)) {
            return new class {
                public function to($to, $status = 302, $headers = []) {
                    $this->sendRedirect($to, $status, $headers);
                }
                
                public function back() {
                    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
                    $this->sendRedirect($referer);
                }
                
                public function intended($default = '/') {
                    $intended = session()->get('url.intended', $default);
                    session()->forget('url.intended');
                    $this->sendRedirect($intended);
                }
                
                public function route($name, $parameters = []) {
                    $url = route($name, $parameters);
                    $this->sendRedirect($url);
                }
                
                public function with($key, $value = null) {
                    if (is_array($key)) {
                        foreach ($key as $k => $v) {
                            session()->flash($k, $v);
                        }
                    } else {
                        session()->flash($key, $value);
                    }
                    return $this;
                }
                
                public function withInput($input = null) {
                    $input = $input ?: $_POST;
                    flash_old($input);
                    return $this;
                }
                
                public function withErrors($errors) {
                    if (is_string($errors)) {
                        $errors = ['message' => $errors];
                    }
                    set_errors($errors);
                    return $this;
                }
                
                private function sendRedirect($to, $status = 302, $headers = []) {
                    header('Location: ' . $to, true, $status);
                    foreach ($headers as $header) {
                        header($header);
                    }
                    exit;
                }
            };
        }
        
        header('Location: ' . $to, true, $status);
        foreach ($headers as $header) {
            header($header);
        }
        exit;
    }
}