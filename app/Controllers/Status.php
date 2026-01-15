<?php

namespace App\Controllers;

use App\Models\DocumentationModel;

class Status extends BaseController
{
    private readonly string $title;

    public function __construct()
    {
        $this->title = lang('Application.status');
    }

    public function index(): void
    {
        echo view('core/header', ['title' => $this->title, 'url' => 'status/']);
        echo view('core/ui');
        $DocumentationModel = new DocumentationModel();
        echo view('status/index', [
            'jurisdictions' => $DocumentationModel->getStatus(),
        ]);
        echo view('core/footer');
    }
}
