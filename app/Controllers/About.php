<?php

namespace App\Controllers;

use App\Models\DocumentationModel;
use CodeIgniter\HTTP\RedirectResponse;

class About extends BaseController
{
    private string $title = 'About';

    public function index(string $jurisdiction = ''): void
    {
        if ($jurisdiction !== '') {
            $this->title .= ' (' . strtoupper($jurisdiction) . ')';
        }
        $DocumentationModel = new DocumentationModel();
        $jurisdictions = [];
        if ($jurisdiction === '') {
            $jurisdictions = $DocumentationModel->getAboutJurisdiction();
        }
        $query = $DocumentationModel->getAboutDetail($jurisdiction);
        if ($query === []) {
            $this->noRecord();
        } else {
            echo view('core/header', ['title' => $this->title]);
            echo view('about/index', ['query' => $query, 'jurisdictions' => $jurisdictions]);
            echo view('core/footer');
        }
    }

    public function redirect(int|string $id): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/about/' . $id . '/', 301);
    }
}
