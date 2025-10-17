<?php
namespace App\Models;
use PDO;
class VisitingDetails
{
    protected $table = 'visiting_details';
    protected $query;
    protected $params = [];

    public function __construct()
    {
        $this->query = "SELECT * FROM {$this->table}";
    }

    /**
     * Begin where clause
     */
    public static function where(array $conditions)
    {
        $instance = new static();
        $clauses = [];

        foreach ($conditions as $key => $value) {
            $clauses[] = "$key = :$key";
            $instance->params[":$key"] = $value;
        }

        $instance->query .= " WHERE " . implode(' AND ', $clauses);
        return $instance;
    }

    /**
     * Add ORDER BY ... DESC (default column: id)
     */
    public function latest($column = 'id')
    {
        $this->query .= " ORDER BY {$column} DESC";
        return $this;
    }

    /**
     * Fetch the first result
     */
    public function first()
    {
        $stmt = db()->prepare($this->query . " LIMIT 1");
        $stmt->execute($this->params);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetch all results
     */
    public function get()
    {
        $stmt = db()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Find by ID
     */
    public static function find($id)
    {
        $instance = new static();
        $stmt = db()->prepare("SELECT * FROM {$instance->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get all records
     */
    public static function all()
    {
        $instance = new static();
        $stmt = db()->query("SELECT * FROM {$instance->table}");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Create new record
     */
    public static function create(array $data)
    {
        $instance = new static();
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = db()->prepare("INSERT INTO {$instance->table} ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
        return db()->lastInsertId();
    }

    /**
     * Update record by ID
     */
    public static function update($id, array $data)
    {
        $instance = new static();
        $fields = implode(',', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $data['id'] = $id;

        $stmt = db()->prepare("UPDATE {$instance->table} SET $fields WHERE id = :id");
        return $stmt->execute($data);
    }

    /**
     * Delete record by ID
     */
    public static function delete($id)
    {
        $instance = new static();
        $stmt = db()->prepare("DELETE FROM {$instance->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
