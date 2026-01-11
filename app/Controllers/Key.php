<?php

namespace App\Controllers;

use App\Models\DocumentationModel;

class Key extends BaseController
{
    private string $title;

    public function __construct() {
        $this->title = lang('Application.key');
    }

    public function index(): void
    {
        echo view('core/header', ['title' => $this->title, 'url' => 'key/']);

        $keyQueries = [];

        $keys = [
            lang('Application.adjudicationGovernmentActionAndRecordedDocument') => 'adjudicationgovernmentactionrecordeddocument',
            lang('Application.date') => 'date',
            lang('Application.governmentLevel') => 'governmentlevel',
            lang('Application.governmentMapStatus') => 'governmentmapstatus',
            lang('Application.governmentTerritorialEvolutionMapColor') => 'governmentterritorialevolutionmapcolor',
            lang('Application.law') => 'law',
            lang('Application.nameAndAbbreviation') => 'nameabbreviation',
        ];
        $DocumentationModel = new DocumentationModel();
        foreach ($keys as $k => $v) {
            $keyQueries[$k] = $DocumentationModel->getKey($v);
        }

        $modelKeys = [
            lang('Application.eventType') => 'EventType',
            lang('Application.howAffected') => 'AffectedType',
            lang('Application.relationship') => 'EventRelationship',
            lang('Application.successful') => 'EventGranted',
        ];
        $keys = array_merge($keys, $modelKeys);
        foreach ($modelKeys as $k => $v) {
            $model = "App\\Models\\" . $v . 'Model';
            $model = new $model();
            $keyQueries[$k] = $model->getKey();
        }

        ksort($keys);
        ksort($keyQueries);
        echo view('key/start', ['keys' => $keys]);
        foreach ($keys as $k => $v) {
            echo view('key/table', ['query' => $keyQueries[$k], 'type' => strtolower($v), 'title' => $k]);
        }
        echo view('core/footer');
    }
}
