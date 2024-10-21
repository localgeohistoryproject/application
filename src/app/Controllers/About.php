<?php

namespace App\Controllers;

use App\Models\DocumentationModel;

class About extends BaseController
{
    private string $title = 'About';

    public function index(string $jurisdiction = ''): void
    {
        echo view('core/header', ['title' => $this->title]);
        $DocumentationModel = new DocumentationModel();
        $jurisdictions = [];
        if ($jurisdiction === '') {
            $jurisdictions = $DocumentationModel->getAboutJurisdiction();
        }
        $query = $DocumentationModel->getAboutDetail($jurisdiction);
        if (count($query) === 0) {
            echo view('core/norecord');
        } else {
            echo view('about/index', ['query' => $query, 'jurisdictions' => $jurisdictions]);
        }
        echo view('core/footer');
    }
}
