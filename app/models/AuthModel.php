<?php
namespace App\Models;
use Pdo;
class AuthModel
{
    protected $table = 'users'; // your users table

    /**
     * Find a user by a condition
     * 
     * @param array $conditions ['email' => 'example@example.com']
     * @return array|false
     */
    public static function where(array $conditions)
    {
        $query = "SELECT * FROM users WHERE ";
        $clauses = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $clauses[] = "`$key` = :$key";
            $params[":$key"] = $value;
        }

        $query .= implode(' AND ', $clauses) . " LIMIT 1";
        $stmt = db()->prepare($query);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Attempt to login a user
     * 
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public static function attempt(string $email, string $password)
    {
        $user = self::where(['email' => $email]);

        if ($user && password_verify($password, $user['password'])) {
            // Store user in session
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user'] = $user;
            $_SESSION['is_logged_in'] = true;
            return $user;
        }

        return false;
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['user']);
        unset($_SESSION['is_logged_in']);
    }

    /**
     * Register a new user
     * 
     * @param array $data ['name'=>'','email'=>'','password'=>'']
     * @return bool
     */
    public static function create(array $data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":$f", $fields);

        $query = "INSERT INTO users (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = db()->prepare($query);

        return $stmt->execute($data);
    }

    /**
     * Get currently logged-in user
     * 
     * @return array|null
     */
    public static function user()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION['user'] ?? null;
    }
}
