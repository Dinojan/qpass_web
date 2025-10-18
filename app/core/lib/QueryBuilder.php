<?php
namespace App\Core\Lib;

class QueryBuilder
{
    protected $table;
    protected $conditions = [];
    protected $bindings = [];

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function where($column, $operator, $value = null)
    {
        // If only two parameters are passed, assume operator is '='
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->conditions[] = "$column $operator :where_$column";
        $this->bindings["where_$column"] = $value;
        
        return $this;
    }

    public function first()
    {
        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($this->conditions)) {
            $query .= " WHERE " . implode(' AND ', $this->conditions);
        }
        
        $query .= " LIMIT 1";
        
        $db = DB::getInstance();
        $stmt = $db->connection()->prepare($query);
        
        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function get()
    {
        $query = "SELECT * FROM {$this->table}";
        
        if (!empty($this->conditions)) {
            $query .= " WHERE " . implode(' AND ', $this->conditions);
        }
        
        $db = DB::getInstance();
        $stmt = $db->connection()->prepare($query);
        
        foreach ($this->bindings as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        $stmt->execute();
        $results = [];
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }
        
        return $results;
    }
}