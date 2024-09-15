<?php

namespace App\Models;

use App\Core\Model;

class ProductType extends Model
{
    public ?int $id;
    public ?string $name;

    protected static array $tableSchema = [
        'name',
    ];

    public static string $tableName = 'types';
    public static string $primaryKey = 'id';
}
