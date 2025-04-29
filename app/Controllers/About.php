<?php

namespace App\Controllers;

use App\Models\DocumentationModel;
use CodeIgniter\HTTP\RedirectResponse;

class About extends BaseController
{
    private string $title = 'About';

    public function index(string $jurisdiction = ''): void
    {
        $url = 'about/';
        if ($jurisdiction !== '') {
            $this->title .= ' (' . strtoupper($jurisdiction) . ')';
            $url .= $jurisdiction . '/';
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
            echo view('core/header', ['title' => $this->title, 'url' => $url]);
            echo view('about/index', ['query' => $query, 'jurisdictions' => $jurisdictions]);
            echo view('core/footer');
        }
    }

    public function redirect(int|string $id): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/about/' . $id . '/', 301);
    }
}
