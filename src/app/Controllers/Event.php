<?php

namespace App\Controllers;

use App\Models\AdjudicationModel;
use App\Models\AffectedGovernmentGroupModel;
use App\Models\CurrentGovernmentModel;
use App\Models\EventModel;
use App\Models\GovernmentSourceModel;
use App\Models\LawSectionModel;
use App\Models\MetesDescriptionModel;
use App\Models\PlssModel;
use App\Models\RecordingModel;
use App\Models\SourceCitationModel;

class Event extends BaseController
{
    private array $data = [
        'title' => 'Event Detail',
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
        $EventModel = new EventModel();
        $query = $EventModel->getDetail($id, $state);
        if (count($query) !== 1 || $query[0]->eventgranted === 'placeholder' && !$this->isLive()) {
            $this->noRecord($state);
        } else {
            $id = $query[0]->eventid;
            $eventIsMapped = ($query[0]->eventismapped === 't');
            $this->data['pageTitle'] = $query[0]->eventlong;
            $this->data['pageTitleType'] = $query[0]->eventtypeshort;
            echo view('core/header', $this->data);
            echo view('event/view', ['query' => $query]);
            $AffectedGovernmentGroupModel = new AffectedGovernmentGroupModel();
            $affectedGovernment = $AffectedGovernmentGroupModel->getByEventGovernment($id, $state);
            $hasMap = $affectedGovernment['hasMap'];
            $hasAffectedGovernmentMap = $hasMap;
            $affectedGovernment = $affectedGovernment['affectedGovernment'];
            if ($this->isLive()) {
                $MetesDescriptionLineModel = new \App\Models\Development\MetesDescriptionLineModel();
            } else {
                $MetesDescriptionLineModel = new \App\Models\MetesDescriptionLineModel();
            }
            $metesdescriptiongisquery = $MetesDescriptionLineModel->getGeometryByEvent($id, $state);
            if ($metesdescriptiongisquery !== []) {
                $hasMap = true;
            }
            if (!$this->isLive() && !$eventIsMapped) {
                $hasMap = false;
            }
            if ($hasMap) {
                echo view('core/map', ['includeBase' => true, 'eventIsMapped' => $eventIsMapped]);
            }
            if (isset($affectedGovernment['rows']) && count($affectedGovernment['rows']) > 0) {
                echo view('event/table_affectedgovernment', ['affectedGovernment' => $affectedGovernment, 'state' => $state, 'includeDate' => false, 'isComplete' => true]);
            }
            $query = $AffectedGovernmentGroupModel->getByEventForm($id, $state);
            echo view('event/table_affectedgovernmentform', ['includeGovernment' => true, 'query' => $query]);
            $CurrentGovernmentModel = new CurrentGovernmentModel();
            $query = $CurrentGovernmentModel->getByEvent($id, $state);
            echo view('event/table_currentgovernment', ['query' => $query, 'state' => $state]);
            $MetesDescriptionModel = new MetesDescriptionModel();
            $query = $MetesDescriptionModel->getByEvent($id);
            echo view('metes/table', ['query' => $query, 'hasLink' => true, 'state' => $state, 'title' => 'Metes and Bounds Description']);
            $PlssModel = new PlssModel();
            $query = $PlssModel->getByEvent($id);
            echo view('event/plss', ['query' => $query]);
            $AdjudicationModel = new AdjudicationModel();
            $query = $AdjudicationModel->getByEvent($id);
            echo view('adjudication/table', ['query' => $query, 'state' => $state, 'eventRelationship' => true]);
            $LawSectionModel = new LawSectionModel();
            $query = $LawSectionModel->getByEvent($id);
            echo view('law/table', ['query' => $query, 'state' => $state, 'title' => 'Law', 'type' => 'relationship', 'includeLawGroup' => true]);
            $RecordingModel = new RecordingModel();
            $query = $RecordingModel->getByEvent($id, $state);
            echo view('event/recording', ['query' => $query]);
            $GovernmentSourceModel = new GovernmentSourceModel();
            $query = $GovernmentSourceModel->getByEvent($id, $state);
            echo view('governmentsource/table', ['query' => $query, 'state' => $state, 'type' => 'event']);
            $SourceCitationModel = new SourceCitationModel();
            $query = $SourceCitationModel->getByEvent($id);
            echo view('source/table_citation', ['query' => $query, 'state' => $state, 'hasColor' => false, 'hasLink' => true, 'title' => 'Source']);
            if ($hasMap) {
                $i = 0;
                echo view('leaflet/start', ['type' => 'event', 'includeBase' => true, 'needRotation' => false]);
                echo view('event/affectedgovernmenttype', ['query' => $affectedGovernment['types']]);
                if ($hasAffectedGovernmentMap) {
                    echo view('core/gis', [
                        'query' => $affectedGovernment['rows'],
                        'element' => 'affectedgovernment',
                        'onEachFeature' => true,
                        'onEachFeature2' => false,
                        'weight' => 1.25,
                        'color' => 'D5103F',
                        'fillOpacity' => 0.1
                    ]);
                }
                $layers = [];
                if ($this->isLive() && $metesdescriptiongisquery !== []) {
                    echo view('core/gis', [
                        'query' => $metesdescriptiongisquery,
                        'element' => 'metesdescription',
                        'onEachFeature' => false,
                        'onEachFeature2' => false,
                        'weight' => 1.25,
                        'color' => 'D5103F',
                        'fillOpacity' => 0.1
                    ]);
                    $layers['metesdescription'] = 'Descriptions';
                }
                if ($hasAffectedGovernmentMap) {
                    echo view('event/end', ['layers' => $layers]);
                } else {
                    echo view('event/end_metesdescription');
                }
                echo view('leaflet/end');
            }
            echo view('core/footer');
        }
    }
}
