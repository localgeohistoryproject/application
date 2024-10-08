<?php

namespace App\Controllers;

use App\Models\AdjudicationModel;
use App\Models\AdjudicationSourceCitationModel;
use App\Models\EventModel;
use App\Models\SourceItemPartModel;

class Reporter extends BaseController
{
    private string $title = 'Reporter Details';

    public function noRecord(string $state): void
    {
        echo view('core/header', ['state' => $state, 'title' => $this->title]);
        echo view('core/norecord');
        echo view('core/footer');
    }

    public function view(string $state, int|string $id): void
    {
        $id = $this->getIdInt($id);
        $AdjudicationSourceCitationModel = new AdjudicationSourceCitationModel();
        $query = $AdjudicationSourceCitationModel->getDetail($id, $state);
        if (count($query) !== 1) {
            $this->noRecord($state);
        } else {
            $id = $query[0]->adjudicationsourcecitationid;
            echo view('core/header', ['state' => $state, 'title' => $this->title]);
            echo view('reporter/table', ['query' => $query, 'state' => $state, 'hasLink' => false, 'title' => 'Detail']);
            echo view('source/table', ['query' => $query, 'hasLink' => false]);
            echo view('reporter/authorship', ['query' => $query]);
            if ($query[0]->url !== '') {
                echo view('core/url', ['query' => $query, 'title' => 'Actual URL']);
            }
            $SourceItemPartModel = new SourceItemPartModel();
            echo view('core/url', ['query' => $SourceItemPartModel->getByAdjudicationSourceCitation($id), 'state' => $state, 'title' => 'Calculated URL']);
            $AdjudicationModel = new AdjudicationModel();
            echo view('adjudication/table', ['query' => $AdjudicationModel->getByAdjudicationSourceCitation($id), 'state' => $state]);
            $EventModel = new EventModel();
            echo view('event/table', ['query' => $EventModel->getByAdjudicationSourceCitation($id), 'state' => $state, 'title' => 'Event Links']);
            echo view('core/footer');
        }
    }
}
