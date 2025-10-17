<?php
namespace App\Core\Lib;
class Hash
{
    /**
     * Generate a bcrypt hash from a plain string
     *
     * @param string $value The plain text password
     * @param int $cost Optional cost parameter (default 10)
     * @return string
     */
    public static function make(string $value, int $cost = 10): string
    {
        return password_hash($value, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    /**
     * Verify a plain text value against a hash
     *
     * @param string $value The plain text password
     * @param string $hash The hashed password
     * @return bool
     */
    public static function check(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }
}
