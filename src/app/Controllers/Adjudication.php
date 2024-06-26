<?php

namespace App\Controllers;

use App\Models\AdjudicationLocationModel;
use App\Models\AdjudicationModel;
use App\Models\AdjudicationSourceCitationModel;
use App\Models\EventModel;
use App\Models\FilingModel;

class Adjudication extends BaseController
{
    private array $data = [
        'title' => 'Adjudication Detail',
    ];

    public function __construct()
    {
    }

    public function noRecord(string $state): void
    {
        $this->data['state'] = $state;
        echo view('core/header', $this->data);
        echo view('core/norecord');
        echo view('core/footer');
    }

    public function view(string $state, int|string $id): void
    {
        $this->data['state'] = $state;
        $id = $this->getIdInt($id);
        $AdjudicationModel = new AdjudicationModel();
        $query = $AdjudicationModel->getDetail($id, $state);
        if (count($query) !== 1) {
            $this->noRecord($state);
        } else {
            $id = $query[0]->adjudicationid;
            $this->data['pageTitle'] = $query[0]->adjudicationtitle;
            echo view('core/header', $this->data);
            echo view('adjudication/view', ['query' => $query]);
            $AdjudicationLocationModel = new AdjudicationLocationModel();
            $query = $AdjudicationLocationModel->getByAdjudication($id);
            echo view('adjudication/location', ['query' => $query]);
            $FilingModel = new FilingModel();
            $query = $FilingModel->getByAdjudication($id);
            echo view('adjudication/filing', ['query' => $query]);
            $AdjudicationSourceCitationModel = new AdjudicationSourceCitationModel();
            $query = $AdjudicationSourceCitationModel->getByAdjudication($id);
            echo view('reporter/table', ['query' => $query, 'state' => $state, 'hasLink' => true, 'title' => 'Reporter Links']);
            $EventModel = new EventModel();
            $query = $EventModel->getByAdjudication($id);
            echo view('event/table', ['query' => $query, 'state' => $state, 'title' => 'Event Links', 'eventRelationship' => true]);
            echo view('core/footer');
        }

    }
}
