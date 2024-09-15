<?php

namespace App\Core;

class View
{
    public static function render($view, $data = [])
    {
        $fileView = dirname(__DIR__) . '../../app/Views/' . $view . '.php';

        if (file_exists($fileView)) {
            extract($data);
            require_once $fileView;
        } else {
            echo "View {$view} not found!";
        }
    }
}
