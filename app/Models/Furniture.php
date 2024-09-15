<?php

namespace App\Models;

use App\Models\Product;

class Furniture extends Product
{
    public function __construct($data = [])
    {
        parent::__construct($data['sku'], $data['name'], $data['price']);
        $this->setAttrName('dimension');
        $this->setAttrValue("{$data['height']}x{$data['width']}x{$data['length']}");
    }
}
