<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\GovernmentModel;

class LawSectionModel extends BaseModel
{
    public function getDetail(int|string $id): array
    {
        if (!is_int($id)) {
            $id = $this->getSlugId($id);
        }

        $query = <<<QUERY
                SELECT DISTINCT lawsection.lawsectionid,
                    lawsection.lawsectionpagefrom,
                    lawsection.lawsectioncitation,
                    CASE
                        WHEN (NOT ?) AND left(law.lawtitle, 1) = '~' THEN ''
                        ELSE law.lawtitle
                    END AS lawtitle,
                    law.lawurl AS url,
                    source.sourcetype,
                    source.sourceabbreviation,
                    source.sourcefullcitation
                FROM geohistory.source
                JOIN geohistory.law
                    ON source.sourceid = law.source
                JOIN geohistory.lawsection
                    ON law.lawid = lawsection.law
                    AND lawsection.lawsectionid = ?
            QUERY;

        $query = $this->db->query($query, [
            \App\Controllers\BaseController::isLive(),
            $id,
        ]);

        return $this->getObject($query);
    }

    public function getByEvent(int $id): array
    {
        $query = <<<QUERY
                SELECT DISTINCT lawsection.lawsectionslug,
                    law.lawapproved,
                    lawsection.lawsectioncitation,
                    eventrelationship.eventrelationshipshort AS lawsectioneventrelationship,
                    lawsection.lawsectionfrom,
                    law.lawnumberchapter,
                    lawgroup.lawgrouplong
                FROM geohistory.law
                JOIN geohistory.lawsection
                    ON law.lawid = lawsection.law
                JOIN geohistory.lawsectionevent
                    ON lawsection.lawsectionid = lawsectionevent.lawsection
                    AND lawsectionevent.event = ?
                JOIN geohistory.eventrelationship
                    ON lawsectionevent.eventrelationship = eventrelationship.eventrelationshipid
                LEFT JOIN geohistory.lawgroup
                    ON lawsectionevent.lawgroup = lawgroup.lawgroupid
                ORDER BY 4, 2, 1
            QUERY;

        $query = $this->db->query($query, [
            $id,
        ]);

        return $this->getObject($query);
    }

    public function getRelated(int $id): array
    {
        $query = <<<QUERY
                SELECT DISTINCT lawsection.lawsectionslug,
                    law.lawapproved,
                    lawsection.lawsectioncitation,
                    'Amends'::text AS lawsectioneventrelationship,
                    lawsection.lawsectionfrom,
                    law.lawnumberchapter
                FROM geohistory.law
                JOIN geohistory.lawsection
                    ON law.lawid = lawsection.law
                JOIN geohistory.lawsection currentlawsection
                    ON lawsection.lawsectionid = currentlawsection.lawsectionamend
                    AND currentlawsection.lawsectionid = ?
                UNION
                SELECT DISTINCT lawsection.lawsectionslug,
                    law.lawapproved,
                    lawsection.lawsectioncitation,
                    'Amended By'::text AS lawsectioneventrelationship,
                    lawsection.lawsectionfrom,
                    law.lawnumberchapter
                FROM geohistory.law
                JOIN geohistory.lawsection
                    ON law.lawid = lawsection.law
                    AND lawsection.lawsectionamend = ?
                UNION
                SELECT DISTINCT lawalternatesection.lawalternatesectionslug AS lawsectionslug,
                    law.lawapproved,
                    lawalternatesection.lawalternatesectioncitation AS lawsectioncitation,
                    'Alternate'::text AS lawsectioneventrelationship,
                    lawsection.lawsectionfrom,
                    law.lawnumberchapter
                FROM geohistory.law
                JOIN geohistory.lawsection
                    ON law.lawid = lawsection.law
                JOIN geohistory.lawalternatesection
                    ON lawsection.lawsectionid = lawalternatesection.lawsection
                    AND lawalternatesection.lawsection = ?
                UNION
                SELECT DISTINCT NULL AS lawsectionslug,
                    law.lawapproved,
                    law.lawcitation AS lawsectioncitation,
                    'Amended To Add ' || lawsection.lawsectionnewsymbol || CASE
                        WHEN lawsection.lawsectionnewfrom <> lawsection.lawsectionnewto THEN lawsection.lawsectionnewsymbol
                        ELSE ''
                    END || ' ' || lawsection.lawsectionnewsection AS lawsectioneventrelationship,
                    lawsection.lawsectionnewfrom AS lawsectionfrom,
                    law.lawnumberchapter
                FROM geohistory.law
                JOIN geohistory.lawsection
                    ON law.lawid = lawsection.lawsectionnewlaw
                    AND lawsection.lawsectionid = ?
                ORDER BY 4, 3
            QUERY;

        $query = $this->db->query($query, [
            $id,
            $id,
            $id,
            $id,
        ]);

        return $this->getObject($query);
    }

    public function getSearchByDateEvent(array $parameters): array
    {
        $government = $parameters[0];
        $GovernmentModel = new GovernmentModel();
        $government = $GovernmentModel->getIdByGovernmentShort($government);
        $date = $parameters[1];
        $eventType = $parameters[2];

        $query = <<<QUERY
                SELECT DISTINCT lawsection.lawsectionslug,
                   lawsection.lawsectioncitation,
                   lawapproved,
                   eventtypeshort
                  FROM geohistory.lawsection
                    JOIN geohistory.eventtype
                      ON lawsection.eventtype = eventtype.eventtypeid
                      AND (? = ''::text
                     OR ? = 'Any Type'::text
                     OR (? = 'Only Border Changes'::text AND eventtype.eventtypeborders ~~ 'yes%')
                     OR eventtype.eventtypeshort = ?)
                    JOIN geohistory.law
                      ON lawsection.law = law.lawid
                      AND law.lawapproved = ?
                    JOIN geohistory.source
                      ON law.source = source.sourceid
                      AND source.sourcetype = 'session laws'
                    JOIN geohistory.sourcegovernment
                      ON source.sourceid = sourcegovernment.source
                      AND sourcegovernment.government = ANY (?)
            QUERY;

        $query = $this->db->query($query, [
            $eventType,
            $eventType,
            $eventType,
            $eventType,
            $date,
            $government,
        ]);

        return $this->getObject($query);
    }

    public function getSearchByReference(array $parameters): array
    {
        $government = $parameters[0];
        $GovernmentModel = new GovernmentModel();
        $government = $GovernmentModel->getIdByGovernmentShort($government);
        $yearVolume = $parameters[1];
        $page = $parameters[2];
        $numberChapter = $parameters[3];

        $query = <<<QUERY
                SELECT DISTINCT lawsection.lawsectionslug,
                   lawsection.lawsectioncitation,
                   lawapproved,
                   eventtypeshort
                  FROM geohistory.lawsection
                    JOIN geohistory.eventtype
                      ON lawsection.eventtype = eventtype.eventtypeid
                    JOIN geohistory.law
                      ON lawsection.law = law.lawid
                      AND (law.lawvolume = ? OR left(law.lawapproved, 4) = ?)
                    JOIN geohistory.source
                      ON law.source = source.sourceid
                      AND source.sourcetype = 'session laws'
                    JOIN geohistory.sourcegovernment
                      ON source.sourceid = sourcegovernment.source
                      AND sourcegovernment.government = ANY (?)
                 WHERE (0 = ? OR law.lawpage = ? OR (lawsection.lawsectionpagefrom >= ? AND lawsection.lawsectionpageto <= ?))
                   AND (0 = ? OR law.lawnumberchapter = ?)
            QUERY;

        $query = $this->db->query($query, [
            $yearVolume,
            $yearVolume,
            $government,
            $page,
            $page,
            $page,
            $page,
            $numberChapter,
            $numberChapter,
        ]);

        return $this->getObject($query);
    }

    private function getSlugId(string $id): int
    {
        $query = <<<QUERY
                SELECT lawsection.lawsectionid AS id
                    FROM geohistory.lawsection
                WHERE lawsection.lawsectionslug = ?
            QUERY;

        $query = $this->db->query($query, [
            $id,
        ]);

        $query = $this->getObject($query);

        $id = -1;

        if (count($query) === 1) {
            $id = $query[0]->id;
        }

        return $id;
    }
}
