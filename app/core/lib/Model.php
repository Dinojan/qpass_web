<?php
namespace App\Core\Lib;

use PDO;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $casts = [];
    protected $appends = [];

    /**
     * Get the table name for the model
     */
    public function getTable()
    {
        if (isset($this->table)) {
            return $this->table;
        }

        // Auto-generate table name from class name
        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower($className) . 's';
    }

    /**
     * Get the primary key for the model
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the value of the primary key
     */
    public function getKey()
    {
        return $this->{$this->primaryKey};
    }

    /**
     * Handle dynamic method calls
     */
    public function __call($method, $parameters)
    {
        // Handle scope methods
        if (method_exists($this, 'scope' . ucfirst($method))) {
            return $this->{'scope' . ucfirst($method)}(...$parameters);
        }

        // Handle query builder methods
        $query = new QueryBuilder($this->getTable());
        return call_user_func_array([$query, $method], $parameters);
    }

    /**
     * Handle static method calls
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;
        
        // Handle query builder methods
        $query = new QueryBuilder($instance->getTable());
        return call_user_func_array([$query, $method], $parameters);
    }

    /**
     * Convert the model instance to an array
     */
    public function toArray()
    {
        $data = [];
        
        // Get all object properties
        $properties = get_object_vars($this);
        
        foreach ($properties as $key => $value) {
            // Skip hidden attributes
            if (in_array($key, $this->hidden)) {
                continue;
            }
            
            // Skip non-fillable attributes (except primary key)
            if (!in_array($key, $this->fillable) && $key !== $this->primaryKey) {
                continue;
            }
            
            $data[$key] = $value;
        }
        
        // Add appended attributes
        foreach ($this->appends as $attribute) {
            $accessor = 'get' . str_replace('_', '', ucwords($attribute, '_')) . 'Attribute';
            if (method_exists($this, $accessor)) {
                $data[$attribute] = $this->$accessor();
            }
        }
        
        return $data;
    }

    /**
     * Convert the model instance to JSON
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Magic method for JSON serialization
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * String representation of the model
     */
    public function __toString()
    {
        return $this->toJson();
    }
}