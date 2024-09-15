<?php

namespace App\Controllers;

class NotFoundController

{
    public function __invoke()
    {
        die("Not Found Controller");
    }
}
