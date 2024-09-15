<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Product;
use App\Models\ProductType;

class ProductController
{
    public function index()
    {
        $products = Product::getAll();
        return View::render('product/list', ['products' => $products]);
    }

    public function add()
    {
        $types = ProductType::getAll();
        return View::render('product/add', compact('types'));
    }

    public function store()
    {
        try {
            $data = $_POST;

            // Directly create the product with the submitted data
            Product::make($data);

            // Redirect to the homepage after successful creation
            redirect('/');
        } catch (\Throwable $e) {
            // Handle any potential errors and redirect back to the form
            redirect(back());
        }
    }


    public function destroy()
    {
        try {
            // Delete products where SKU is in the submitted 'deleted' list
            Product::deleteIn('sku', $_POST['deleted'] ?? []);

            // Redirect to the homepage after successful deletion
            redirect('/');
        } catch (\Throwable $e) {
            // Handle any potential errors and redirect back to the page
            redirect(back());
        }
    }
}
