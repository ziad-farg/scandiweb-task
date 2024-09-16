<?php

namespace App\Core;

use PDO;
use App\Core\Database\Database;

abstract class Model
{
    protected string $sql;

    // Remove the $db property from the class

    private function prepareValues(\PDOStatement &$stmt): void
    {
        foreach (static::$tableSchema as $columnName) {
            $stmt->bindValue(":$columnName", $this->$columnName);
        }
    }

    private function buildNameParametersSQL(): string
    {
        $namedParams = '';
        foreach (static::$tableSchema as $columnName) {
            $namedParams .= $columnName . ' = :' . $columnName . ', ';
        }
        return trim($namedParams, ', ');
    }

    protected function save(): bool
    {
        $db = Database::getConnection(); // Get the database connection here
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . $this->buildNameParametersSQL();

        $stmt = $db->prepare($sql);

        $this->prepareValues($stmt);
        if ($stmt->execute()) {
            if (property_exists($this, static::$primaryKey ?? '')) {
                $this->{static::$primaryKey} = $db->lastInsertId();
            }
            return true;
        }
        return false;
    }

    public static function getAll()
    {
        $db = Database::getConnection(); // Get the database connection here
        $sql = "SELECT * FROM " . static::$tableName;
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function deleteIn($colName, $items = [])
    {
        $db = Database::getConnection(); // Get the database connection here
        $placeholders = rtrim(str_repeat('?,', count($items)), ',');
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE ' . $colName . ' IN ' . "($placeholders)";
        $stmt = $db->prepare($sql);
        return $stmt->execute($items);
    }
}
