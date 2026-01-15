<?php

namespace App\Controllers;

use App\Models\EventTypeModel;
use App\Models\GovernmentIdentifierTypeModel;
use App\Models\GovernmentModel;
use App\Models\SourceModel;
use CodeIgniter\HTTP\RedirectResponse;

class Search extends BaseController
{
    private readonly string $title;

    private array $categoryType;

    private array $parameterType;

    private array $typeType;

    public function __construct()
    {
        $this->title = lang('Application.search');

        $this->categoryType = [
            'event' => lang('Application.event'),
            'government' => lang('Application.government'),
            'governmentidentifier' => lang('Application.government'),
            'law' => lang('Application.law'),
        ];

        $this->parameterType = [
            'date' => lang('Application.date'),
            'eventtype' => lang('Application.eventType'),
            'government' => lang('Application.government'),
            'governmentjurisdiction' => lang('Application.government'),
            'governmentidentifiertype' => lang('Application.identifierSource'),
            'governmentlevel' => lang('Application.level'),
            'governmentparent' => lang('Application.parent'),
            'identifier' => lang('Application.identifier'),
            'numberchapter' => lang('Application.number') . '/' . lang('Application.chapter'),
            'page' => lang('Application.page'),
            'plusminus' => '±',
            'year' => lang('Application.year'),
            'yearvolume' => lang('Application.year') . '/' . lang('Application.volume'),
        ];

        $this->typeType = [
            'dateEvent' => lang('Application.dateAndEventType'),
            'government' => lang('Application.government'),
            'identifier' => lang('Application.identifier'),
            'reference' => lang('Application.reference'),
            'statewide' => lang('Application.statewide'),
        ];
    }

    private function governmentLevel(string $a): int
    {
        return match ($a) {
            lang('Application.state') => 2,
            lang('Application.county') => 3,
            lang('Application.municipality') => 4,
            default => 0,
        };
    }

    public function governmentLookup(string $government = '', string $type = ''): void
    {
        $GovernmentModel = new GovernmentModel();
        $type = 'getLookupByGovernment' . ucwords($type);
        $this->response->setHeader('Content-Type', 'application/json');
        echo json_encode($GovernmentModel->$type($government));
    }

    public function index(): void
    {
        echo view('core/header', ['title' => $this->title, 'url' => 'search/']);
        echo view('core/ui');
        $EventTypeModel = new EventTypeModel();
        $GovernmentIdentifierTypeModel = new GovernmentIdentifierTypeModel();
        echo view('search/index', [
            'eventTypeQuery' => $EventTypeModel->getSearch(),
            'governmentIdentifierTypeQuery' => $GovernmentIdentifierTypeModel->getSearch(),
        ]);
        echo view('core/footer');
    }

    public function redirect(): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/search/', 301);
    }

    public function view(string $category): void
    {
        $type = $this->request->getPost('type');
        $fields = [];
        $model = '';

        switch ($category) {
            case 'event':
                $fields = [
                    $this->request->getPost('government'),
                    $this->request->getPost('governmentparent'),
                    $this->request->getPost('eventtype'),
                    (int) $this->request->getPost('year', FILTER_SANITIZE_NUMBER_INT),
                    (int) $this->request->getPost('plusminus', FILTER_SANITIZE_NUMBER_INT),
                ];
                $model = 'EventModel';
                break;
            case 'government':
                switch ($type) {
                    case 'statewide':
                    case 'government':
                        $fields = [
                            $this->request->getPost('government'),
                            ($type === 'statewide' ? $this->request->getPost('governmentjurisdiction') : $this->request->getPost('governmentparent')),
                            '{' . $this->governmentLevel($this->request->getPost('governmentlevel')) . '}',
                            $type,
                        ];
                        $model = 'GovernmentModel';
                        $type = 'government';
                        break;
                    case 'identifier':
                        $fields = [
                            $this->request->getPost('governmentidentifiertype'),
                            $this->request->getPost('identifier'),
                        ];
                        $model = 'GovernmentIdentifierModel';
                        $category = 'governmentidentifier';
                        break;
                    default:
                        break;
                }
                break;
            case 'law':
                $model = 'LawSectionModel';
                switch ($type) {
                    case 'reference':
                        $fields = [
                            $this->request->getPost('governmentjurisdiction'),
                            $this->request->getPost('yearvolume'),
                            (int) $this->request->getPost('page'),
                            (int) $this->request->getPost('numberchapter'),
                        ];
                        break;
                    case 'dateEvent':
                        $fields = [
                            $this->request->getPost('governmentjurisdiction'),
                            $this->request->getPost('date'),
                            $this->request->getPost('eventtype'),
                        ];
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }

        if ($fields !== [] && $model !== '') {
            echo view('core/header', ['title' => $this->title]);
            $model = "App\\Models\\" . $model;
            $model = new $model();
            $modelType = 'getSearchBy' . ucwords($type);
            $searchParameter = [
                lang('Application.searchFor') => $this->categoryType[$category],
                lang('Application.searchBy') => $this->typeType[$this->request->getPost('type')],
            ];
            foreach ($this->request->getPost() as $key => $value) {
                if ($value !== '' && $key !== 'type') {
                    $searchParameter[$this->parameterType[$key]] = $value;
                }
            }
            echo view('core/parameter', ['searchParameter' => $searchParameter]);
            echo view($category . '/table', ['query' => $model->$modelType($fields), 'title' => lang('Application.results') . ':', 'type' => $type]);
            echo view('core/footer');
        } else {
            $this->response->setHeader('Content-Type', 'application/json');
            echo json_encode($this->request->getPost());
        }
    }
}
