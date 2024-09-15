<?php

namespace App\Models;

use App\Models\Product;

class DVD extends Product
{
    public function __construct($data = [])
    {
        parent::__construct($data['sku'], $data['name'], $data['price']);
        $this->setAttrName('size');
        $this->setAttrValue($data['size']);
    }
}
