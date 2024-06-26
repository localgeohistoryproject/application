<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\SourceCitationModel;
use App\Models\SourceCitationNoteModel;
use App\Models\SourceItemPartModel;

class Source extends BaseController
{
    private array $data = [
        'title' => 'Source Detail',
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
        $SourceCitationModel = new SourceCitationModel();
        $query = $SourceCitationModel->getDetail($id, $state);
        if (count($query) !== 1) {
            $this->noRecord($state);
        } else {
            $id = $query[0]->sourcecitationid;
            $this->data['pageTitle'] = $query[0]->sourceabbreviation . ($query[0]->sourcecitationpage === '' ? '' : ' ' . $query[0]->sourcecitationpage);
            echo view('core/header', $this->data);
            echo view('source/table_citation', ['query' => $query, 'state' => $state, 'hasColor' => false, 'hasLink' => false, 'title' => 'Detail']);
            echo view('source/table', ['query' => $query, 'hasLink' => $this->isLive()]);
            if ($query[0]->url !== '') {
                echo view('core/url', ['query' => $query, 'title' => 'Actual URL']);
            }
            $SourceCitationNoteModel = new SourceCitationNoteModel();
            $query = $SourceCitationNoteModel->getBySourceCitation($id);
            echo view('source/note', ['query' => $query, 'state' => $state]);
            $SourceItemPartModel = new SourceItemPartModel();
            $query = $SourceItemPartModel->getBySourceCitation($id);
            echo view('core/url', ['query' => $query, 'state' => $state, 'title' => 'Calculated URL']);
            $EventModel = new EventModel();
            $query = $EventModel->getBySourceCitation($id);
            echo view('event/table', ['query' => $query, 'state' => $state, 'title' => 'Event Links']);
            echo view('core/footer');
        }
    }
}
