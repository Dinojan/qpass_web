<?php 
namespace App\Models;
use PDO;
class ThemeSetting {
    public static function where($conditions)
    {
        $query = "SELECT * FROM settings WHERE ";
        $clauses = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $clauses[] = "`$key` = :$key";
            $params[":$key"] = $value;
        }

        $query .= implode(' AND ', $clauses) . " LIMIT 1";
        $statement = db()->prepare($query);
        $statement->execute($params);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        return $data? $data['value']: null;
    }
}