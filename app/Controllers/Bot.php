<?php

namespace App\Controllers;

use App\Models\DocumentationModel;

class Bot extends BaseController
{
    private string $title = 'Bot';

    public function index(): void
    {
        echo view('core/header', ['title' => $this->title, 'url' => 'bot/']);
        $DocumentationModel = new DocumentationModel();
        $summary = $DocumentationModel->getKey('bot');
        $summary = $summary[0]->keylong ?? '';
        echo view('bot/index', [
            'summary' => $summary,
        ]);
        echo view('core/footer');
    }

    public function robotsTxt(): void
    {
        $this->response->setHeader('Content-Type', 'text/plain');
        echo view('bot/robotstxt');
        echo view('bot/robotstxt_app');
    }
}
