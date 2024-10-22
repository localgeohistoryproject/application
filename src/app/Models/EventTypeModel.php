<?php

namespace App\Models;

use App\Models\BaseModel;

class EventTypeModel extends BaseModel
{
    // extra.ci_model_key_eventtype()

    public function getKey(): array
    {
        $query = <<<QUERY
            SELECT eventtype.eventtypeshort AS keyshort,
                eventtype.eventtypeshort AS keysort,
                eventtype.eventtypelong AS keylong,
                CASE
                    WHEN eventtype.eventtypeborders ~~ 'yes%' THEN 'yes'
                    WHEN eventtype.eventtypeborders = 'documentation' THEN NULL
                    ELSE ''
                END AS keyincluded
            FROM geohistory.eventtype
            WHERE eventtype.eventtypelong NOT IN ('~', '*')
                AND eventtype.eventtypeborders <> 'ignore'
            ORDER BY 2, 1
        QUERY;

        $query = $this->db->query($query);

        return $this->getObject($query);
    }

    // extra.ci_model_search_form_eventtype(character varying)

    public function getSearch(): array
    {
        $query = <<<QUERY
            SELECT DISTINCT eventtype.eventtypeshort,
                eventtype.eventtypeid,
                eventtype.eventtypeborders = 'documentation' AS isdocumentation
            FROM geohistory.eventtype
            WHERE eventtype.eventtypeborders <> 'ignore'
                OR eventtype.eventtypeborders = 'documentation'
            ORDER BY 3 DESC, 1, 2
        QUERY;

        $query = $this->db->query($query);

        return $this->getArray($query);
    }

    // extra.ci_model_statistics_eventtype_list(boolean)
    // extra.ci_model_statistics_eventtype_list(character varying)

    // VIEW: extra.statistics_eventtype

    public function getManyByStatistics(): array
    {
        $query = <<<QUERY
            SELECT DISTINCT eventtype.eventtypeshort,
                eventtype.eventtypeid
            FROM geohistory.eventtype
            JOIN extra.statistics_eventtype
                ON eventtype.eventtypeid = statistics_eventtype.eventtype
            WHERE eventtype.eventtypeborders NOT IN ('documentation', 'ignore')
            ORDER BY 1, 2
        QUERY;

        $query = $this->db->query($query);

        return $this->getArray($query);
    }

    // extra_removed.ci_model_statistics_eventtype(text)

    public function getOneByStatistics(string $eventType): array
    {
        $query = <<<QUERY
            SELECT DISTINCT eventtype.eventtypeshort,
                eventtype.eventtypeid
            FROM geohistory.eventtype
            WHERE eventtype.eventtypeshort = ?
                AND eventtype.eventtypeborders NOT IN ('documentation', 'ignore')
            ORDER BY 1, 2
        QUERY;

        $query = $this->db->query($query, [
            $eventType,
        ]);

        return $this->getObject($query);
    }
}
