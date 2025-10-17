<?php
/**
 * Database configuration
 * -----------------------
 * Centralized PDO connection setup.
 */

return [
    'default' => 'mysql',

    'connections' => [
        'mysql' => [
            'host'      => getenv('DB_HOST') ?: '127.0.0.1',
            'port'      => getenv('DB_PORT') ?: '3306',
            'database'  => getenv('DB_NAME') ?: 'qpass',
            'username'  => getenv('DB_USER') ?: 'root',
            'password'  => getenv('DB_PASS') ?: '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
];
