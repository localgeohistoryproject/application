<?php

namespace App\Controllers;

class Fourofour extends BaseController
{
    private string $title = '404';

    public function index(): void
    {
        $this->isError();
    }
}
