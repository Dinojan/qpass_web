<?php 
namespace App\Models;
use PDO;
use App\Core\Lib\DB;

class Setting
{
    public static function where(array $conditions)
    {
        $db = DB::getInstance()->db(); // PDO instance

        $query = "SELECT * FROM settings WHERE ";
        $clauses = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $clauses[] = "`$key` = :$key";
            $params[":$key"] = $value;
        }

        $query .= implode(' AND ', $clauses) . " LIMIT 1";
        $statement = $db->prepare($query);
        $statement->execute($params);
        $data = $statement->fetch(PDO::FETCH_ASSOC);

        return $data ? $data['value'] : null;
    }
}
