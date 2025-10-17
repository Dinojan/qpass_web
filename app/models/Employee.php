<?php
namespace App\Models;
use App\Database;
use PDO;
class Employee
{
    protected $table = 'employees';

    /**
     * Get all employees
     */
    public function getAll()
    {
        $stmt = db()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find one employee by condition
     * Example: Employee::where(['id' => 5])
     */
    public static function where(array $conditions)
    {
        $instance = new self();
        $clauses = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $clauses[] = "`$key` = :$key";
            $params[":$key"] = $value;
        }

        $query = "SELECT * FROM {$instance->table} WHERE " . implode(' AND ', $clauses) . " LIMIT 1";
        $stmt = db()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new employee
     * Example: Employee::create(['name' => 'John', 'email' => 'john@doe.com'])
     */
    public static function create(array $data)
    {
        $instance = new self();

        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);

        $query = "INSERT INTO {$instance->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = db()->prepare($query);

        return $stmt->execute($data);
    }

    /**
     * Update an employee record by condition
     * Example: Employee::update(['id' => 1], ['name' => 'New Name'])
     */
    public static function update(array $conditions, array $data)
    {
        $instance = new self();

        $set = [];
        $params = [];

        foreach ($data as $key => $value) {
            $set[] = "`$key` = :set_$key";
            $params[":set_$key"] = $value;
        }

        $where = [];
        foreach ($conditions as $key => $value) {
            $where[] = "`$key` = :where_$key";
            $params[":where_$key"] = $value;
        }

        $query = "UPDATE {$instance->table} SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where);
        $stmt = db()->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Delete employee(s)
     * Example: Employee::delete(['id' => 10])
     */
    public static function delete(array $conditions)
    {
        $instance = new self();

        $clauses = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $clauses[] = "`$key` = :$key";
            $params[":$key"] = $value;
        }

        $query = "DELETE FROM {$instance->table} WHERE " . implode(' AND ', $clauses);
        $stmt = db()->prepare($query);
        return $stmt->execute($params);
    }
}
