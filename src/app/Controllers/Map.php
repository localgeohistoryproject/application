<?php

namespace App\Controllers;

class Map extends BaseController
{

    private $data;

    public function __construct()
    {
        $this->data = [
            'title' => 'Map',
            'isInternetExplorer' => $this->isInternetExplorer(),
            'live' => $this->isLive(),
            'online' => $this->isOnline(),
            'updated' => $this->lastUpdated()->fulldate,
        ];
    }

    public function baseStyle($maxZoom = 14, $response = NULL)
    {
        if (empty($response)) {
            $response = $this->response;
        }
        $response->removeHeader('Cache-Control');
        $response->setHeader('Cache-Control', 'max-age=86400');
        $response->setHeader('Content-Type', 'application/json');
        $json = json_decode(file_get_contents(__DIR__ . '/../../html/asset/map/map_style_base.json'), true);
        if (strpos(getenv('map_tile'), '.json') !== FALSE OR strpos(getenv('map_tile'), '.pmtiles') !== FALSE) {
            $json['sources']['street-tile']['url'] = getenv('map_tile');
            unset($json['sources']['street-tile']['tiles']);
        } else {
            $json['sources']['street-tile']['tiles'][] = getenv('map_tile');
            unset($json['sources']['street-tile']['url']);
        }
        $json['glyphs'] = getenv('map_glyph');
        if (!empty(getenv('map_elevation')) AND $maxZoom == 14) {
            $json['sources']['elevation-tile']['tiles'][] = getenv('map_elevation');
        } else {
            unset($json['sources']['elevation-tile']);
            for ($i = count($json['layers']) - 1; $i >= 0; $i--) {
                if ($json['layers'][$i]['id'] == 'hillshading') {
                    unset($json['layers'][$i]);
                    break;
                }
            }
        }
        foreach ($json['layers'] AS $layerNumber => $layerContent) {
            if (!empty($layerContent['layout']['text-field']) AND $layerContent['layout']['text-field'] == '{name}') {
                $json['layers'][$layerNumber]['layout']['text-field'] = [
                    'coalesce',
                    ['get', 'name_' . \Config\Services::request()->getLocale()],
                    ['get', 'name:' . \Config\Services::request()->getLocale()],
                    ['get', 'name']
                ];
                if (!in_array(\Config\Services::request()->getLocale(), ['de', 'en'])) {
                    $json['layers'][$layerNumber]['layout']['text-field'][] = ['get', 'name_en'];
                }
            }
        }
        if ($maxZoom < 14) {
            $json['sources']['street-tile']['maxzoom'] = $maxZoom;
        }
        echo json_encode($json);
    }

    public function leaflet($state = '')
    {
        $this->data['state'] = ($state == 'zoom' ? '' : $state);
        $this->data['zoom'] = ($state == 'zoom');
        $this->response->removeHeader('Cache-Control');
        $this->response->setHeader('Cache-Control', 'max-age=86400');
        $this->response->setHeader('Content-Type', 'application/javascript');
        echo view('leaflet_state_base', $this->data);
        try {
            echo view('leaflet_state_' . $state, $this->data);
        } catch (\Throwable $t) {
            try {
                echo view('development/leaflet_state_' . $state, $this->data);
            } catch (\Throwable $t) {
                echo view('leaflet_state', $this->data);
            }
        }
    }

    public function overlayStyle($state = '')
    {
        $this->response->removeHeader('Cache-Control');
        $this->response->setHeader('Cache-Control', 'max-age=86400');
        $this->response->setHeader('Content-Type', 'application/json');
        $json = json_decode(file_get_contents(__DIR__ . '/../../html/asset/map/map_style_overlay.json'), true);
        $json['sources']['localgeohistoryproject']['tiles'][0] = getenv('app_baseLocalGeohistoryProjectUrl') . '/' . \Config\Services::request()->getLocale() . '/' . $state . $json['sources']['localgeohistoryproject']['tiles'][0];
        echo json_encode($json);
    }

    public function tile($z, $x, $y, $state = '')
    {
        $this->response->setHeader('Content-Type', 'application/x-protobuf');
        $query = $this->db->query('SELECT * FROM extra.ci_model_map_tile(?, ?, ?, ?)', [$state, $z, $x, $y])->getResult();
        foreach ($query as $row) {
            echo pg_unescape_bytea($row->mvt);
        }
    }
}
