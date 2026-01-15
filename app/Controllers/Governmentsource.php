<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\GovernmentSourceModel;
use App\Models\SourceItemPartModel;
use CodeIgniter\HTTP\RedirectResponse;

class Governmentsource extends BaseController
{
    private readonly string $title;

    public function __construct()
    {
        $this->title = lang('Application.governmentSource');
    }

    public function redirect(int|string $id): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/governmentsource/' . $id . '/', 301);
    }

    public function view(int|string $id): void
    {
        $url = 'governmentsource/' . $id . '/';
        $id = $this->getIdInt($id);
        $GovernmentSourceModel = new GovernmentSourceModel();
        $query = $GovernmentSourceModel->getDetail($id);
        if (count($query) !== 1) {
            $this->noRecord();
        } else {
            $id = $query[0]->governmentsourceid;
            echo view('core/header', ['title' => $this->title, 'url' => $url]);
            echo view('governmentsource/table', ['query' => $query, 'type' => 'source']);
            echo view('source/table', ['query' => $query, 'hasLink' => $this->isLive()]);
            $SourceItemPartModel = new SourceItemPartModel();
            echo view('core/url', ['query' => $SourceItemPartModel->getByGovernmentSource($id), 'title' => lang('Application.calculatedUrl')]);
            $EventModel = new EventModel();
            echo view('event/table', ['query' => $EventModel->getByGovernmentSource($id), 'title' => lang('Application.eventLinks')]);
            echo view('core/footer');
        }
    }
}
