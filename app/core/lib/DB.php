<?php
namespace App\Core\Lib;

use PDO;
use PDOException;

class DB
{
    private static $instance = null;
    private $connection;
    private $statement;

    private function __construct()
    {
        $config = require DIR_CONFIG . 'database.php';
        $db = $config['connections'][$config['default']];

        try {
            $dsn = "mysql:host={$db['host']};dbname={$db['database']};charset={$db['charset']}";
            $this->connection = new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    /** Singleton */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Return PDO connection */
    public function connection(): PDO
    {
        return $this->connection;
    }

    /** Prepare statement */
    public function prepare(string $query): self
    {
        $this->statement = $this->connection->prepare($query);
        return $this;
    }

    /** Execute prepared statement */
    public function execute(array $params = []): bool
    {
        return $this->statement->execute($params);
    }

    /** Fetch one row */
    public function fetch($mode = PDO::FETCH_ASSOC)
    {
        return $this->statement->fetch($mode);
    }

    /** Fetch all rows */
    public function fetchAll($mode = PDO::FETCH_ASSOC)
    {
        return $this->statement->fetchAll($mode);
    }

    /** Row count */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    /** Last insert id */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

        /** Return PDO object */
    public function db()
    {
        return $this->connection;
    }
}
