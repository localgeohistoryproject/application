<?php

namespace App\Controllers;

use App\Models\DocumentationModel;

class Key extends BaseController
{
    private string $title = 'Key';

    public function index(): void
    {
        echo view('core/header', ['title' => $this->title]);

        $keyQueries = [];

        $keys = [
            'Adjudication, Government Action, and Recorded Document' => 'adjudicationgovernmentactionrecordeddocument',
            'Date' => 'date',
            'Government Level' => 'governmentlevel',
            'Government Map Status' => 'governmentmapstatus',
            'Government Timelapse Map Color' => 'governmenttimelapsemapcolor',
            'Law' => 'law',
            'Name and Abbreviation' => 'nameabbreviation',
        ];
        $DocumentationModel = new DocumentationModel();
        foreach ($keys as $k => $v) {
            $keyQueries[$k] = $DocumentationModel->getKey($v);
        }

        $modelKeys = [
            'Event Type' => 'EventType',
            'How Affected' => 'AffectedType',
            'Relationship' => 'EventRelationship',
            'Successful?' => 'EventGranted',
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
