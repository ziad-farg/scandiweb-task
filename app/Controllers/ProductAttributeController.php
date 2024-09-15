<?php

namespace App\Controllers;

use PDO;
use App\Core\Database\Database;

class ProductAttributeController
{
    public function productAttribute()
    {

        $sql = "SELECT a.name, a.label FROM types t JOIN attribute_type att ON t.id = att.type_id JOIN attributes a ON a.id = att.attribute_id WHERE t.name = :name";

        $stmt = Database::getConnection()->prepare($sql);

        $stmt->execute([':name' => $_GET['type']]);

        $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($attributes);
    }
}
