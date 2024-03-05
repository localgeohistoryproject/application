<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\GovernmentSourceModel;
use App\Models\SourceItemPartModel;

class Governmentsource extends BaseController
{
    private array $data = [
        'title' => 'Government Source Detail',
    ];

    public function __construct()
    {
    }

    public function noRecord(string $state): void
    {
        $this->data['state'] = $state;
        echo view('header', $this->data);
        echo view('norecord');
        echo view('footer');
    }

    public function view(string $state, int|string $id): void
    {
        $this->data['state'] = $state;
        $id = $this->getIdInt($id);
        $GovernmentSourceModel = new GovernmentSourceModel();
        $query = $GovernmentSourceModel->getDetail($id, $state);
        if (count($query) != 1) {
            $this->noRecord($state);
        } else {
            $id = $query[0]->governmentsourceid;
            echo view('header', $this->data);
            echo view('general_governmentsource', ['query' => $query, 'state' => $state, 'type' => 'source']);
            echo view('general_source', ['query' => $query, 'hasLink' => $this->isLive()]);
            $SourceItemPartModel = new SourceItemPartModel();
            $query = $SourceItemPartModel->getByGovernmentSource($id);
            if ($query !== []) {
                echo view('general_url', ['query' => $query, 'state' => $state, 'title' => 'Calculated URL']);
            }
            $EventModel = new EventModel();
            $query = $EventModel->getByGovernmentSource($id);
            if ($query !== []) {
                echo view('general_event', ['query' => $query, 'state' => $state, 'title' => 'Event Links']);
            }
            echo view('footer');
        }
    }
}
