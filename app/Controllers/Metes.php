<?php

namespace App\Controllers;

use App\Models\AffectedGovernmentGroupModel;
use App\Models\DocumentationModel;
use App\Models\MetesDescriptionModel;
use CodeIgniter\HTTP\RedirectResponse;

class Metes extends BaseController
{
    private string $title;

    public function __construct() {
        $this->title = lang('Application.metesAndBoundsDescription');
    }

    public function redirect(int|string $id): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/metes/' . $id . '/', 301);
    }

    public function view(int|string $id): void
    {
        $url = 'metes/' . $id . '/';
        $id = $this->getIdInt($id);
        $MetesDescriptionModel = new MetesDescriptionModel();
        $areaQuery = $MetesDescriptionModel->getDetail($id);
        if (count($areaQuery) !== 1) {
            $this->noRecord();
        } else {
            $id = $areaQuery[0]->metesdescriptionid;
            $jurisdictions = explode(',', $areaQuery[0]->jurisdictions);
            if ($jurisdictions === ['']) {
                $AffectedGovernmentGroupModel = new AffectedGovernmentGroupModel();
                $jurisdictions = $AffectedGovernmentGroupModel->getByEventGovernment($areaQuery[0]->eventid);
                $jurisdictions = $jurisdictions['jurisdictions'];
            }
            echo view('core/header', ['title' => $this->title, 'pageTitle' => $areaQuery[0]->metesdescriptionlong, 'url' => $url]);
            echo view('metes/table', ['query' => $areaQuery, 'hasLink' => false, 'title' => 'Detail']);
            $hasMap = false;
            $hasMetes = false;
            $hasArea = (!is_null($areaQuery[0]->geometry));
            $hasBegin = ($areaQuery[0]->hasbeginpoint === 't' || $hasArea);
            $MetesDescriptionLineModel = $this->getModelNamespace($this, 'MetesDescriptionLineModel');
            $geometryQuery = $MetesDescriptionLineModel->getGeometryByMetesDescription($id);
            $hasMetes = (count($geometryQuery) > 1);
            if ($hasArea || $hasMetes) {
                $hasMap = true;
                echo view('core/map', ['includeBase' => $hasBegin, 'includeDisclaimer' => true]);
            }
            $DocumentationModel = new DocumentationModel();
            $summary = $DocumentationModel->getKey('metes');
            $summary = $summary[0]->keylong ?? '';
            echo view('metes/row', [
                'query' => $MetesDescriptionLineModel->getByMetesDescription($id),
                'summary' => $summary,
            ]);
            echo view('event/table', ['query' => $areaQuery, 'title' => 'Event Links']);
            if ($hasMap) {
                echo view('leaflet/start', ['type' => 'metes', 'jurisdictions' => $jurisdictions, 'includeBase' => $hasBegin, 'needRotation' => false]);
                if ($hasArea) {
                    echo view('core/gis', [
                        'query' => $areaQuery,
                        'element' => 'area',
                        'onEachFeature' => false,
                        'onEachFeature2' => false,
                        'weight' => 0,
                        'color' => '07517D',
                        'fillOpacity' => 0.5,
                    ]);
                }
                if ($hasMetes) {
                    echo view('core/gis', [
                        'query' => $geometryQuery,
                        'element' => 'line',
                        'onEachFeature' => true,
                        'onEachFeature2' => false,
                        'weight' => 3,
                        'color' => 'D5103F',
                        'fillOpacity' => 0,
                    ]);
                    echo view('core/gis', [
                        'query' => $geometryQuery,
                        'element' => 'point',
                        'onEachFeature' => true,
                        'onEachFeature2' => false,
                        'weight' => 3,
                        'color' => 'D5103F',
                        'fillOpacity' => 0,
                        'radius' => 6,
                    ]);
                }
                echo view('metes/end', ['includeBase' => $hasBegin, 'includeArea' => $hasArea, 'includeMetes' => $hasMetes]);
                echo view('leaflet/end', ['includeBase' => $hasBegin]);
            }
            echo view('core/footer');
        }
    }
}
