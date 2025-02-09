<?php

namespace App\Models;

use App\Models\BaseModel;
use stdClass;

class AppModel extends BaseModel
{
    public function getLastUpdated(): array
    {
        $query = <<<QUERY
                SELECT calendar.historicdatetextformat(lastrefresh.lastrefreshdate::calendar.historicdatetext::calendar.historicdate, 'short', ?) AS fulldate,
                    to_char(lastrefresh.lastrefreshdate, 'J') AS sortdate,
                    to_char(lastrefresh.lastrefreshdate, 'Mon FMDD, YYYY') AS sortdatetext,
                    lastrefresh.lastrefreshversion
                FROM geohistory.lastrefresh
            QUERY;

        $query = $this->db->query($query, [
            \Config\Services::request()->getLocale(),
        ]);

        $query = $this->getObject($query);

        if (count($query) > 1) {
            return [];
        }

        return $query;
    }
}
