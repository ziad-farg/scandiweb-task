<?php

namespace App\Core\Database;

use PDO;
use PDOException;

class Database

{
    private static $connection = null;

    private function __construct()
    {
        // to prevent make instance from this object
    }

    public static function getConnection()
    {
        if (self::$connection === null) {
            try {
                $dsn = 'mysql:host=' . DATABASE_HOST_NAME . ';dbname=' . DATABASE_DB_NAME . ';charset=utf8mb4';
                self::$connection = new PDO($dsn, DATABASE_USERNAME, DATABASE_PASSWORD);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
