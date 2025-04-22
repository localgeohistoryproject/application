<?php

namespace App\Controllers;

class Sitemap extends BaseController
{
    private array $model = [
        'adjudication' => 'Adjudication',
        'area' => 'GovernmentShape',
        'event' => 'Event',
        'government' => 'Government',
        'governmentidentifier' => 'GovernmentIdentifier',
        'governmentsource' => 'GovernmentSource',
        'law' => 'LawSection',
        'metes' => 'MetesDescription',
        'other' => '',
        'reporter' => 'AdjudicationSourceCitation',
        'source' => 'SourceCitation',
    ];

    private string $title = 'Sitemap';

    public function index(): void
    {
        $query = array_keys($this->model);
        $this->response->setHeader('Content-Type', 'application/xml; charset=UTF-8');
        echo view('sitemap/index', [
            'query' => $query,
        ]);
    }

    public function view(string $id = ''): void
    {
        $model = $this->model[$id] ?? '';
        if ($model === '') {
            $this->isError();
        } else {
            $model = "App\\Models\\" . $model . 'Model';
            $model = new $model();
            $query = $model->getSitemap();
            $this->response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
            echo view('sitemap/view', [
                'id' => $id,
                'query' => $query,
            ]);
        }
    }

    public function viewOther(): void
    {
        $query = [
            '',
            'about/',
            'bot/',
            'disclaimer/',
            'key/',
            'search/',
            'statistics/',
            'status/',
        ];

        foreach($this->getProductionJurisdictions() as $jurisdiction) {
            $query[] = 'about/' . $jurisdiction . '/';
        }

        sort($query);

        $this->response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
        echo view('sitemap/view_other', [
            'query' => $query,
        ]);
    }
}
