<?php

namespace App\Controllers;

use App\Models\GovernmentModel;
use CodeIgniter\HTTP\RedirectResponse;

class Governmentidentifier extends BaseController
{
    private readonly array $data;

    public function __construct()
    {
        $this->data = [
            'title' => 'Government Identifier Detail',
        ];
    }

    public function noRecord(): void
    {
        echo view('header', $this->data);
        echo view('norecord');
        echo view('footer');
    }

    public function view(string $type, string $id): null|RedirectResponse
    {
        if ($id !== strtolower($id)) {
            $this->response->setStatusCode(301);
            return redirect()->to("/" . $this->request->getLocale() . '/governmentidentifier/' . $type . '/' . strtolower($id) . '/');
        }
        if ($this->isLive()) {
            $GovernmentIdentifierModel = new \App\Models\Development\GovernmentIdentifierModel();
        } else {
            $GovernmentIdentifierModel = new \App\Models\GovernmentIdentifierModel();
        }
        $query = $GovernmentIdentifierModel->getDetail($type, $id);
        if (count($query) !== 1) {
            $this->noRecord();
        } else {
            $governmentidentifierids = $query[0]->governmentidentifierids;
            $governments = $query[0]->governments;
            echo view('header', $this->data);
            echo view('general_governmentidentifier', ['query' => $query, 'title' => 'Detail']);
            $GovernmentModel = new GovernmentModel();
            $query = $GovernmentModel->getByGovernmentIdentifier($governmentidentifierids);
            echo view('general_government', ['query' => $query, 'title' => 'Government', 'type' => 'identifier']);
            $query = $GovernmentIdentifierModel->getRelated($governments, $governmentidentifierids);
            echo view('general_governmentidentifier', ['query' => $query, 'title' => 'Related']);
            if ($type === 'us-census' || $type === 'usgs') {
                if ($type === 'us-census') {
                    $query = $GovernmentIdentifierModel->getCensus($governmentidentifierids);
                } else {
                    $query = $GovernmentIdentifierModel->getUsgs($governmentidentifierids);
                }
                echo view('governmentidentifier_census', ['query' => $query, 'type' => $type]);
            }
            echo view('footer');
        }
        return null;
    }
}
