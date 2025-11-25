<?php

namespace App\Controllers;

use App\Models\DocumentationModel;

class Disclaimer extends BaseController
{
    private string $title;

    public function __construct() {
        $this->title = lang('Application.disclaimers');
    }

    public function index(): void
    {
        echo view('core/header', ['title' => $this->title, 'url' => 'disclaimer/']);
        $DocumentationModel = new DocumentationModel();
        echo view('disclaimer/index', ['query' => $DocumentationModel->getDisclaimer()]);
        echo view('core/footer');
    }
}
