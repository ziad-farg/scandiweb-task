<?php


use App\Core\Router;
use App\Controllers\ProductController;
use App\Controllers\ValidateProductController;
use App\Controllers\ProductAttributeController;


require_once dirname(__DIR__) . '/app/Config/Config.php';
require_once ROOT . '/vendor/autoload.php';
require_once ROOT . '/app/Helper/helper.php';


$router = new Router;


$router->get('/', [ProductController::class, 'index']);
$router->get('/product/add', [ProductController::class, 'add']);
$router->get('/product-attributes', [ProductAttributeController::class, 'productAttribute']);
$router->post('/product', [ProductController::class, 'store']);
$router->delete('/product', [ProductController::class, 'destroy']);
$router->post('/product-validate', [ValidateProductController::class, 'validate']);



$router->dispatch();
