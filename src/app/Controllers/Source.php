<?php

namespace App\Controllers;

class Source extends BaseController
{

    private $data;

    public function __construct()
    {
        $this->data = [
            'title' => 'Source Detail',
            'isInternetExplorer' => $this->isInternetExplorer(),
            'live' => $this->isLive(),
            'online' => $this->isOnline(),
            'updated' => $this->lastUpdated()->fulldate,
        ];
    }

    public function noRecord($state)
    {
        $this->data['state'] = $state;
        echo view('header', $this->data);
        echo view('norecord');
        echo view('footer');
    }

    public function view($state, $id)
    {
        $this->data['state'] = $state;
        if ($this->data['live'] and preg_match('/^\d{1,9}$/', $id)) {
            $id = intval($id);
        }
        $query = $this->db->query('SELECT * FROM extra.ci_model_source_detail(?, ?)', [$id, $state])->getResult();
        if (count($query) != 1) {
            $this->noRecord($state);
        } else {
            $id = $query[0]->sourcecitationid;
            $this->data['pageTitle'] = $query[0]->sourceabbreviation . (empty($query[0]->sourcecitationpage) ? '' : ' ' . $query[0]->sourcecitationpage);
            echo view('header', $this->data);
            echo view('general_sourcecitation', ['query' => $query, 'state' => $state, 'hasColor' => false, 'hasLink' => false, 'title' => 'Detail']);
            echo view('general_source', ['query' => $query, 'hasLink' => $this->data['live']]);
            if ($query[0]->url != '') {
                echo view('general_url', ['query' => $query, 'title' => 'Actual URL']);
            }
            $query = $this->db->query('SELECT * FROM extra.ci_model_source_note(?)', [$id])->getResult();
            if (count($query) > 0) {
                echo view('source_note', ['query' => $query, 'state' => $state]);
            }
            $query = $this->db->query('SELECT * FROM extra.ci_model_source_url(?)', [$id])->getResult();
            if (count($query) > 0) {
                echo view('general_url', ['query' => $query, 'state' => $state, 'title' => 'Calculated URL']);
            }
            $query = $this->db->query('SELECT * FROM extra.ci_model_source_event(?)', [$id])->getResult();
            if (count($query) > 0) {
                echo view('general_event', ['query' => $query, 'state' => $state, 'title' => 'Event Links']);
            }
            echo view('footer');
        }
    }
}
