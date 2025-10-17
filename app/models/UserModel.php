<?php 
namespace App\Models;
use PDO;
class UserModal {
    // exists
    public function exists($email) {
        $statement = db()->prepare("SELECT * FROM users WHERE email = :email");
        $statement->execute(['email' => $email]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    
}