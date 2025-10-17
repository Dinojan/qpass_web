<?php
namespace App\Vonder;
use Exception;
use ReflectionClass;
class Container
{
    /**
     * The current globally available container instance.
     *
     * @var static|null
     */
    protected static $instance;

    /**
     * The container's bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The container's shared instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Create (or get) the container instance.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set the shared instance.
    
     */
    public static function setInstance($container = null)
    {
        static::$instance = $container;
    }

    /**
     * Bind an abstract type to a concrete implementation.
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Register a shared binding (singleton).
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Determine if the container has a binding.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Resolve a binding.
     *
     * @param  string  $abstract
     * @param  array   $parameters
     * @return mixed
     */
    public function make($abstract, array $parameters = [])
    {
        // Return shared instance if it exists
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Get binding
        $binding = $this->bindings[$abstract] ?? null;

        $concrete = $binding['concrete'] ?? $abstract;
        $shared = $binding['shared'] ?? false;

        // Build instance
        $object = $this->build($concrete, $parameters);

        // If shared, store instance
        if ($shared) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Build a concrete instance.
     *
     * @param  \Closure|string  $concrete
     * @param  array  $parameters
     * @return mixed
     */
    protected function build($concrete, array $parameters = [])
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }

        if (!class_exists($concrete)) {
            throw new Exception("Class {$concrete} does not exist.");
        }

        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if ($type && !$type->isBuiltin()) {
                $depClass = $type->getName();
                $dependencies[] = $this->make($depClass);
            } elseif (array_key_exists($param->getName(), $parameters)) {
                $dependencies[] = $parameters[$param->getName()];
            } elseif ($param->isDefaultValueAvailable()) {
                $dependencies[] = $param->getDefaultValue();
            } else {
                throw new Exception("Unresolvable dependency [{$param->getName()}] in class {$concrete}");
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Register an existing instance as shared.
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return mixed
     */
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }
}
