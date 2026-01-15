<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\SourceCitationModel;
use App\Models\SourceCitationNoteModel;
use App\Models\SourceItemPartModel;
use CodeIgniter\HTTP\RedirectResponse;

class Source extends BaseController
{
    private readonly string $title;

    public function __construct()
    {
        $this->title = lang('Application.source');
    }

    public function redirect(int|string $id): RedirectResponse
    {
        return redirect()->to('/' . $this->request->getLocale() . '/source/' . $id . '/', 301);
    }

    public function view(int|string $id): void
    {
        $url = 'source/' . $id . '/';
        $id = $this->getIdInt($id);
        $SourceCitationModel = new SourceCitationModel();
        $query = $SourceCitationModel->getDetail($id);
        if (count($query) !== 1) {
            $this->noRecord();
        } else {
            $id = $query[0]->sourcecitationid;
            $pageTitle = $query[0]->sourceabbreviation . ($query[0]->sourcecitationpage === '' ? '' : ' ' . $query[0]->sourcecitationpage);
            echo view('core/header', ['title' => $this->title, 'pageTitle' => $pageTitle, 'url' => $url]);
            echo view('source/table_citation', ['query' => $query, 'hasColor' => false, 'hasLink' => false, 'title' => lang('Application.detail')]);
            echo view('source/table', ['query' => $query, 'hasLink' => $this->isLive()]);
            if ($query[0]->url !== '') {
                echo view('core/url', ['query' => $query, 'title' => lang('Application.actualUrl')]);
            }
            $SourceCitationNoteModel = new SourceCitationNoteModel();
            echo view('source/note', ['query' => $SourceCitationNoteModel->getBySourceCitation($id)]);
            $SourceItemPartModel = new SourceItemPartModel();
            echo view('core/url', ['query' => $SourceItemPartModel->getBySourceCitation($id), 'title' => lang('Application.calculatedUrl')]);
            $EventModel = new EventModel();
            echo view('event/table', ['query' => $EventModel->getBySourceCitation($id), 'title' => lang('Application.eventLinks')]);
            echo view('core/footer');
        }
    }
}
