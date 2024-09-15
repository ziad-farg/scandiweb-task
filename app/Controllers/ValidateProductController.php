<?php

namespace App\Controllers;

use App\Core\Validator;

class ValidateProductController
{
    public function validate(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $roles = [
            'sku' => 'req|alphanum|unique:products,sku',
            'name' => 'req',
            'price' => 'req|num',
            'type' => 'req|exists:types,name',
        ];

        $validator = Validator::validate($roles, $data);
        if ($validator->failed()) {
            echo json(['errors' => $validator->errors()], 419);
        }
    }
}
