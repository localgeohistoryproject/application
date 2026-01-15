<?php

namespace App\Controllers;

use App\Models\DocumentationModel;
use App\Models\EventTypeModel;
use App\Models\GovernmentModel;
use CodeIgniter\HTTP\RedirectResponse;

class Statistics extends BaseController
{
    private readonly string $title;

    private array $byType;

    private array $forType;

    public function __construct()
    {
        $this->title = lang('Application.statistics');

        $this->byType = [
            'current' => lang('Application.modernDayJurisdictions'),
            'historic' => lang('Application.contemporaneousJurisdictions'),
            'incorporated' => lang('Application.incorporatedMunicipalities'),
            'total' => lang('Application.totalMunicipalities'),
        ];

        $this->forType = [
            'eventtype' => lang('Application.eventsByEventType'),
            'created' => lang('Application.createdMunicipalities'),
            'dissolved' => lang('Application.dissolvedMunicipalities'),
            'net' => lang('Application.netCreatedDissolvedMunicipalities'),
            'mapped' => lang('Application.mappedMunicipalities'),
            'mapped_review' => lang('Application.reviewedMunicipalities'),
        ];
    }

    public function index(): void
    {
        echo view('core/header', ['title' => $this->title, 'url' => 'statistics/']);
        echo view('core/ui');
        $EventTypeModel = new EventTypeModel();
        $GovernmentModel = new GovernmentModel();
        echo view('statistics/index', [
            'eventTypeQuery' => $EventTypeModel->getManyByStatistics(),
            'jurisdictions' => $GovernmentModel->getByStatisticsJurisdiction(),
        ]);
        echo view('core/footer');
    }

    public function redirect(): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/statistics/', 301);
    }

    public function view(): void
    {
        $isError = false;
        $by = $this->request->getPost('by');
        $for = $this->request->getPost('for');
        $for = explode('_', $for);
        if (isset($for[1])) {
            $by = $for[1];
        }
        $byExtra = '';
        if (isset($for[2])) {
            $byExtra = '_' . $for[2];
        }
        $for = $for[0];

        $searchParameter = [];
        if (!isset($this->byType[$by])) {
            $isError = true;
        } else {
            $searchParameter['byType'] = $this->byType[$by];
            $by .= $byExtra;
        }

        $from = (int) $this->request->getPost('from', FILTER_SANITIZE_NUMBER_INT);
        $to = (int) $this->request->getPost('to', FILTER_SANITIZE_NUMBER_INT);
        if ($from === 0 && $to === 0) {
            $to = (int) date('Y');
        } elseif ($from === 0) {
            $from = $to;
        } elseif ($to === 0) {
            $to = $from;
        } elseif ($from > $to) {
            $temporary = $to;
            $to = $from;
            $from = $temporary;
        }
        if ($from === 0 || $from === $to) {
            $dateRange = $from === 0 ? '' : (string) $from;
            $dateRangePlural = '';
        } else {
            $dateRange = $from . '&ndash;' . $to;
            $dateRangePlural = 's';
        }

        if (!isset($this->forType[$for])) {
            $isError = true;
        } else {
            $searchParameter = [
                lang('Application.metric') => $this->forType[$for . $byExtra],
                lang('Application.groupedBy') => $searchParameter['byType'],
            ];
        }

        $fields = [$from, $to, $by];
        if ($for === 'eventtype') {
            $eventType = (string) $this->request->getPost('eventtype');
            $EventTypeModel = new EventTypeModel();
            $query = $EventTypeModel->getOneByStatistics($eventType);
            if (count($query) !== 1) {
                $isError = true;
            } else {
                array_unshift($fields, $eventType);
                $searchParameter[lang('Application.eventType')] = $query[0]->eventtypeshort;
            }
        } else {
            $eventType = '';
            array_unshift($fields, $for);
            if ($for !== 'mapped') {
                $for = 'createddissolved';
            }
        }

        if ($isError) {
            $this->isError();
        } else {
            if ($dateRange !== '') {
                $searchParameter['Year' . $dateRangePlural] = $dateRange;
            }

            $jurisdiction = $this->request->getPost('governmentjurisdiction') ?? '';
            $fields[] = $jurisdiction;

            $types = [
                'createddissolved' => 'Government',
                'eventtype' => 'Event',
                'mapped' => 'GovernmentShape',
            ];
            $model = $this->getModelNamespace($this, $types[$for] . 'Model');
            $type = 'getByStatistics' . ($jurisdiction === '' ? 'Nation' : 'State') . 'Whole';

            $wholeQuery = $model->$type($fields);
            if ($wholeQuery[0]->datarow === '["x"]') {
                $wholeQuery = [];
                $query = [];
            } else {
                $type = str_replace('Whole', 'Part', $type);
                $query = $model->$type($fields);
                foreach ($query as $key => $row) {
                    $query[$key] = '"' . $row->series . '":{"xrow":' . $row->xrow . ',"yrow":' . $row->yrow . ',"ysum":' . $row->ysum . '}';
                }
                $query = '{' . implode(',', $query) . '}';
            }
            echo view('core/header', ['title' => $this->title]);
            echo view('core/parameter', ['searchParameter' => $searchParameter]);
            $DocumentationModel = new DocumentationModel();
            $statisticsOriginal = $DocumentationModel->getKey('statistics');
            $statistics = [];
            foreach ($statisticsOriginal as $item) {
                $statistics[$item->keyshort] = $item->keylong;
            }
            unset($statisticsOriginal);
            echo view('statistics/view', [
                'wholeQuery' => $wholeQuery,
                'isContemporaneous' => ($searchParameter[lang('Application.groupedBy')] === lang('Application.contemporaneousJurisdictions')),
                'notEvent' => ($searchParameter[lang('Application.metric')] === lang('Application.eventsByEventType')),
                'query' => $query,
                'jurisdiction' => $jurisdiction,
                'statistics' => $statistics,
            ]);
            echo view('core/chartjs', ['query' => $wholeQuery, 'xLabel' => lang('Application.year'), 'yLabel' => ($for === 'createddissolved' ? lang('Application.governments') : lang('Application.events'))]);
            echo view('core/footer');
        }
    }
}
