<?php

namespace App\Controllers;

use App\Models\AffectedGovernmentGroupModel;
use App\Models\AppModel;
use App\Models\EventModel;
use App\Models\GovernmentIdentifierModel;
use App\Models\GovernmentShapeModel;
use App\Models\GovernmentSourceModel;
use App\Models\NationalArchivesModel;
use App\Models\ResearchLogModel;
use CodeIgniter\HTTP\RedirectResponse;

class Government extends BaseController
{
    private string $title = 'Government';

    public function redirect(int|string $id): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/government/' . $id . '/', 301);
    }

    public function view(int|string $id, bool $isHistory = false): void
    {
        $id = $this->getIdInt($id);
        $GovernmentMapStatusModel = $this->getModelNamespace($this, 'GovernmentMapStatusModel');
        $GovernmentModel = $this->getModelNamespace($this, 'GovernmentModel');
        $GovernmentPopulationModel = $this->getModelNamespace($this, 'GovernmentPopulationModel');
        $MetesDescriptionLineModel = $this->getModelNamespace($this, 'MetesDescriptionLineModel');
        $SourceModel = $this->getModelNamespace($this, 'SourceModel');
        $query = $GovernmentModel->getDetail($id);
        if (count($query) !== 1 || $query[0]->governmentlevel === 'placeholder') {
            $this->noRecord();
        } elseif (!is_null($query[0]->governmentslugsubstitute)) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: /" . $this->request->getLocale() . "/government/" . $query[0]->governmentslugsubstitute . "/");
            exit();
        } else {
            $id = $query[0]->governmentid;
            $isMultiple = ($query[0]->governmentsubstitutemultiple === 't');
            $allId = $GovernmentModel->getIdByGovernment($id);
            echo view('core/header', ['title' => $this->title, 'pageTitle' => $query[0]->governmentlong]);
            $isMunicipalityOrLower = ($query[0]->governmentlevel === 'municipality or lower');
            $isCountyOrLower = ($query[0]->governmentlevel === 'municipality or lower' || $query[0]->governmentlevel === 'county');
            $isCountyOrState = ($query[0]->governmentlevel === 'state' || $query[0]->governmentlevel === 'county');
            $isStateOrHigher = (($query[0]->governmentlevel === 'state' || $query[0]->governmentlevel === 'country'));
            $hasMap = ($isCountyOrLower && $query[0]->hasmap === 't');
            $jurisdictions = [
                strtolower($query[0]->governmentcurrentleadstate),
            ];
            $showTimeline = ($query[0]->governmentmapstatustimelapse === 't');
            $statusQuery = $GovernmentMapStatusModel->getDetails();
            echo view('government/view', ['query' => $query, 'statuses' => $statusQuery]);
            if (!$isHistory) {
                $this->viewPrivateOne($id, $jurisdictions);
            }
            if ($hasMap) {
                echo view('core/map', ['includeBase' => true]);
            }
            if (!$isHistory) {
                $populationQuery = $GovernmentPopulationModel->getByGovernment($id);
                if ($populationQuery !== []) {
                    echo view('core/chart');
                }
                echo view('government/related', ['query' => $GovernmentModel->getRelated($id)]);
                $GovernmentIdentifierModel = new GovernmentIdentifierModel();
                echo view('governmentidentifier/table', ['query' => $GovernmentIdentifierModel->getByGovernment($id), 'title' => 'Identifier', 'isMultiple' => $isMultiple]);
            }
            $AffectedGovernmentGroupModel = new AffectedGovernmentGroupModel();
            $query = $AffectedGovernmentGroupModel->getByGovernmentGovernment($id);
            $events = [];
            echo view('government/affectedgovernment', ['query' => $query, 'isMultiple' => $isMultiple]);
            foreach ($query as $row) {
                $events[] = $row->event;
            }
            $query = $AffectedGovernmentGroupModel->getByGovernmentForm($id);
            echo view('event/table_affectedgovernmentform', ['includeGovernment' => false, 'query' => $query]);
            foreach ($query as $row) {
                $events[] = $row->event;
            }
            $events = array_unique($events);
            $EventModel = new EventModel();
            if (!$isHistory) {
                $GovernmentSourceModel = new GovernmentSourceModel();
                $query = $GovernmentSourceModel->getByGovernment($id);
                echo view('governmentsource/table', ['query' => $query, 'type' => 'government', 'isMultiple' => $isMultiple]);
                foreach ($query as $row) {
                    $row->eventid = explode(',', str_replace(['{', '}'], '', $row->eventid));
                    foreach ($row->eventid as $rowRow) {
                        $events[] = $rowRow;
                    }
                }
                if ($isCountyOrLower) {
                    echo view('event/table', ['query' => $EventModel->getByGovernmentOther($allId, $events), 'title' => 'Other Event Links', 'tableId' => 'eventother']);
                }
                $this->viewPrivateTwo($id, $isMultiple);
                echo view('source/table', ['query' => $SourceModel->getByGovernment($id), 'hasLink' => true]);
                $ResearchLogModel = new ResearchLogModel();
                echo view('government/researchlog', ['query' => $ResearchLogModel->getByGovernment($id), 'isMultiple' => $isMultiple]);
                $NationalArchivesModel = new NationalArchivesModel();
                echo view('government/nationalarchives', ['query' => $NationalArchivesModel->getByGovernment($id), 'isMultiple' => $isMultiple]);
                $this->viewPrivateThree($id, $isMultiple, $isMunicipalityOrLower, $isCountyOrLower, $isCountyOrState, $isStateOrHigher, $jurisdictions);
                echo view('core/chartjs', ['query' => $populationQuery, 'xLabel' => 'Year', 'yLabel' => 'Population']);
            }
            if ($hasMap) {
                echo view('leaflet/start', ['type' => 'government', 'jurisdictions' => $jurisdictions, 'includeBase' => true, 'needRotation' => false]);
                $query = $MetesDescriptionLineModel->getGeometryByGovernment($id);
                $layers = [];
                $primaryLayer = '';
                if ($query !== []) {
                    echo view('core/gis', [
                        'query' => $query,
                        'element' => 'metesdescription',
                        'onEachFeature' => true,
                        'onEachFeature2' => false,
                        'weight' => 1.25,
                        'color' => 'D5103F',
                        'fillOpacity' => 0,
                    ]);
                    $layers['metesdescription'] = 'Descriptions';
                    $primaryLayer = 'metesdescription';
                }
                $GovernmentShapeModel = new GovernmentShapeModel();
                $query = $GovernmentShapeModel->getCurrentByGovernment($id);
                if ($query !== []) {
                    echo view('core/gis', [
                        'query' => $query,
                        'element' => 'current',
                        'onEachFeature' => false,
                        'onEachFeature2' => false,
                        'weight' => 3,
                        'color' => '000000',
                        'fillOpacity' => 0,
                    ]);
                    $layers['current'] = 'Approximate Current Boundary';
                }
                $query = $GovernmentShapeModel->getPartByGovernment($id);
                if ($query !== []) {
                    echo view('core/gis', [
                        'query' => $query,
                        'element' => 'shape',
                        'onEachFeature' => false,
                        'onEachFeature2' => true,
                        'customStyle' => 'dispositionStyle',
                    ]);
                    $layers['shape'] = 'Government Area';
                    $primaryLayer = 'shape';
                }
                date_default_timezone_set('America/New_York');
                $AppModel = new AppModel();
                $updatedParts = $AppModel->getLastUpdated()[0];
                echo view('government/end', ['layers' => $layers, 'primaryLayer' => $primaryLayer, 'updatedParts' => $updatedParts, 'showTimeline' => $showTimeline]);
                echo view('leaflet/end');
            }
            echo view('core/footer');
        }
    }

    protected function viewPrivateOne(int $id, array $jurisdictions): void {}

    protected function viewPrivateTwo(int $id, bool $isMultiple): void {}

    protected function viewPrivateThree(int $id, bool $isMultiple, bool $isMunicipalityOrLower, bool $isCountyOrLower, bool $isCountyOrState, bool $isStateOrHigher, array $jurisdictions): void {}
}
