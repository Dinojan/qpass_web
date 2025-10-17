<?php
namespace App\Core\Lib;
class Validator
{
    protected $data = [];
    protected $rules = [];
    protected $messages = [];
    protected $errors = [];
    protected $afterCallbacks = [];

    /**
     * Create a new validator instance
     */
    public static function make(array $data, array $rules, array $messages = [])
    {
        $instance = new static();
        $instance->data = $data;
        $instance->rules = $rules;
        $instance->messages = $messages;
        return $instance;
    }

    /**
     * Run the validator
     */
    public function validate()
    {
        foreach ($this->rules as $field => $rules) {
            $rulesArray = is_array($rules) ? $rules : explode('|', $rules);
            $value = $this->data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $this->$method($field, $value);
                }
            }
        }

        // Run all after callbacks
        foreach ($this->afterCallbacks as $callback) {
            call_user_func($callback, $this);
        }

        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails()
    {
        return !$this->validate();
    }

    /**
     * Return all validation errors
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Add custom error manually
     */
    public function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Register an after-validation callback
     *
     * @param callable $callback
     * @return void
     */
    public function after(callable $callback)
    {
        $this->afterCallbacks[] = $callback;
    }

    /**
     * Rule: required
     */
    protected function validateRequired($field, $value)
    {
        if (is_null($value) || $value === '') {
            $this->addError(
                $field,
                $this->messages["$field.required"] ?? ucfirst($field) . ' is required.'
            );
        }
    }

    /**
     * Rule: email
     */
    protected function validateEmail($field, $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError(
                $field,
                $this->messages["$field.email"] ?? ucfirst($field) . ' must be a valid email.'
            );
        }
    }

    /**
     * Rule: min
     */
protected function validateMin($field, $value, $min)
{
    // If value is numeric
    if (is_numeric($value)) {
        if ($value < $min) {
            $this->addError($field, "The {$field} must be at least {$min}.");
        }
    } 
    // If value is a string (check length)
    elseif (is_string($value)) {
        if (mb_strlen($value) < $min) {
            $this->addError($field, "The {$field} must be at least {$min} characters.");
        }
    } 
    // If array, check count
    elseif (is_array($value)) {
        if (count($value) < $min) {
            $this->addError($field, "The {$field} must have at least {$min} items.");
        }
    }
}

}
