<?php

namespace App\Controllers;

use App\Models\DocumentationModel;

class Disclaimer extends BaseController
{
    private string $title = 'Disclaimers';

    public function index(string $state = ''): void
    {
        echo view('core/header', ['state' => $state, 'title' => $this->title]);
        $DocumentationModel = new DocumentationModel();
        echo view('disclaimer/index', ['query' => $DocumentationModel->getDisclaimer()]);
        echo view('core/footer');
    }
}
