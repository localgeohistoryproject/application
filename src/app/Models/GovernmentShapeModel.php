<?php

namespace App\Models;

use App\Models\BaseModel;

class GovernmentShapeModel extends BaseModel
{
    public function getDetail(int|string $id): array
    {
        if (!is_int($id)) {
            $id = $this->getSlugId($id);
        }

        $query = <<<QUERY
            SELECT DISTINCT governmentshape.governmentshapeid,
                COALESCE(governmentsubmunicipality.governmentslugsubstitute, '') AS governmentsubmunicipality,
                COALESCE(governmentsubmunicipality.governmentlong, '') AS governmentsubmunicipalitylong,
                governmentmunicipality.governmentslugsubstitute AS governmentmunicipality,
                governmentmunicipality.governmentlong AS governmentmunicipalitylong,
                governmentcounty.governmentslugsubstitute AS governmentcounty,
                governmentcounty.governmentshort AS governmentcountyshort,
                governmentstate.governmentslugsubstitute AS governmentstate,
                governmentstate.governmentabbreviation AS governmentstateabbreviation,
                governmentshape.governmentshapeid AS id,
                public.st_asgeojson(governmentshape.governmentshapegeometry) AS geometry
            FROM gis.governmentshape
            JOIN geohistory.government governmentmunicipality
                ON governmentshape.governmentmunicipality = governmentmunicipality.governmentid
            JOIN geohistory.government governmentcounty
                ON governmentshape.governmentcounty = governmentcounty.governmentid
            JOIN geohistory.government governmentstate
                ON governmentshape.governmentstate = governmentstate.governmentid
            LEFT JOIN geohistory.government governmentsubmunicipality
                ON governmentshape.governmentsubmunicipality = governmentsubmunicipality.governmentid
            WHERE governmentshape.governmentshapeid = ?
            ORDER BY 8, 6, 4, 2
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ]);

        return $this->getObject($query);
    }

    public function getPointId(float $y, float $x): array
    {
        $query = <<<QUERY
            SELECT DISTINCT governmentshape.governmentshapeid
                FROM gis.governmentshape
            WHERE ST_Contains(governmentshape.governmentshapegeometry, ST_SetSRID(ST_Point(?,?),4326))
            ORDER BY 1
        QUERY;

        $query = $this->db->query($query, [
            $x,
            $y
        ]);

        return $this->getObject($query);
    }

    // VIEW: extra.statistics_mapped

    public function getByStatisticsNationPart(array $parameters): array
    {
        $by = $parameters[3];
        $jurisdiction = $parameters[4];
        if ($jurisdiction === '') {
            $jurisdiction = implode(',', \App\Controllers\BaseController::getJurisdictions());
        }
        $jurisdiction = '{' . strtoupper($jurisdiction) . '}';

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT min(governmentidentifier.governmentidentifier) AS series,
                    statistics_mapped.governmentstate AS actualseries,
                    0 AS x,
                    statistics_mapped.percentmapped::numeric AS y
                FROM extra.statistics_mapped
                JOIN geohistory.government
                    ON statistics_mapped.governmentstate = government.governmentabbreviation
                JOIN geohistory.governmentidentifier
                    ON government.governmentid = governmentidentifier.government
                    AND governmentidentifier.governmentidentifiertype = 1
                WHERE statistics_mapped.governmenttype = 'state'
                    AND statistics_mapped.grouptype = ?
                    AND statistics_mapped.governmentstate = ANY (?)
                GROUP BY 2, 3, 4
            ), xvalue AS (
                SELECT DISTINCT eventdata.series,
                generate_series(min(eventdata.x),max(eventdata.x)) AS x
                FROM eventdata
                GROUP BY 1
            )
            SELECT xvalue.series,
            array_to_json(array_agg(DISTINCT xvalue.x::text ORDER BY xvalue.x::text)) AS xrow,
            array_to_json(array_agg(
                CASE
                    WHEN eventdata.y IS NULL THEN 0
                    ELSE eventdata.y
                END ORDER BY xvalue.x)) AS yrow,
            sum(eventdata.y) AS ysum
            FROM xvalue
            LEFT JOIN eventdata
                ON xvalue.x = eventdata.x
                AND xvalue.series = eventdata.series
            GROUP BY 1
            ORDER BY 1
        QUERY;

        $query = $this->db->query($query, [
            $by,
            $jurisdiction,
        ]);

        return $this->getObject($query);
    }

    // VIEW: extra.statistics_mapped

    public function getByStatisticsNationWhole(array $parameters): array
    {
        $by = $parameters[3];

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT 0 AS x,
                statistics_mapped.percentmapped::text AS y
                FROM extra.statistics_mapped
                WHERE statistics_mapped.governmenttype = 'nation'
                AND statistics_mapped.grouptype = ?
                AND statistics_mapped.governmentstate = ?
            ), xvalue AS (
                SELECT DISTINCT generate_series(min(eventdata.x),max(eventdata.x)) AS x
                FROM eventdata
            )
            SELECT array_to_json(ARRAY['x'::text] || array_agg(DISTINCT xvalue.x::text ORDER BY xvalue.x::text)) AS datarow
            FROM xvalue
            UNION ALL
            SELECT array_to_json(ARRAY['Whole'] || array_agg(
                CASE
                    WHEN eventdata.y IS NULL THEN '0'::text
                    ELSE eventdata.y
                END ORDER BY xvalue.x)) AS datarow
            FROM xvalue
            LEFT JOIN eventdata
                ON xvalue.x = eventdata.x
        QUERY;

        $query = $this->db->query($query, [
            $by,
            ENVIRONMENT,
        ]);

        return $this->getObject($query);
    }

    // VIEW: extra.statistics_mapped

    public function getByStatisticsStatePart(array $parameters): array
    {
        $by = $parameters[3];
        $jurisdiction = strtoupper($parameters[4]);

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT statistics_mapped.governmentcounty AS series,
                0 AS x,
                statistics_mapped.percentmapped::numeric AS y
                FROM extra.statistics_mapped
                WHERE statistics_mapped.governmenttype = 'county'
                AND statistics_mapped.grouptype = ?
                AND statistics_mapped.governmentstate = ?
            ), xvalue AS (
                SELECT DISTINCT eventdata.series,
                generate_series(min(eventdata.x),max(eventdata.x)) AS x
                FROM eventdata
                GROUP BY 1
            )
            SELECT xvalue.series,
            array_to_json(array_agg(DISTINCT xvalue.x::text ORDER BY xvalue.x::text)) AS xrow,
            array_to_json(array_agg(
                CASE
                    WHEN eventdata.y IS NULL THEN 0
                    ELSE eventdata.y
                END ORDER BY xvalue.x)) AS yrow,
            sum(eventdata.y) AS ysum
            FROM xvalue
            LEFT JOIN eventdata
                ON xvalue.x = eventdata.x
                AND xvalue.series = eventdata.series
            GROUP BY 1
            ORDER BY 1
        QUERY;

        $query = $this->db->query($query, [
            $by,
            $jurisdiction,
        ]);

        return $this->getObject($query);
    }

    // VIEW: extra.statistics_mapped

    public function getByStatisticsStateWhole(array $parameters): array
    {
        $by = $parameters[3];
        $jurisdiction = strtoupper($parameters[4]);

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT 0 AS x,
                statistics_mapped.percentmapped::text AS y
                FROM extra.statistics_mapped
                WHERE statistics_mapped.governmenttype = 'state'
                AND statistics_mapped.grouptype = ?
                AND statistics_mapped.governmentstate = ?
            ), xvalue AS (
                SELECT DISTINCT generate_series(min(eventdata.x),max(eventdata.x)) AS x
                FROM eventdata
            )
            SELECT array_to_json(ARRAY['x'::text] || array_agg(DISTINCT xvalue.x::text ORDER BY xvalue.x::text)) AS datarow
            FROM xvalue
            UNION ALL
            SELECT array_to_json(ARRAY[?] || array_agg(
                CASE
                    WHEN eventdata.y IS NULL THEN '0'::text
                    ELSE eventdata.y
                END ORDER BY xvalue.x)) AS datarow
            FROM xvalue
            LEFT JOIN eventdata
                ON xvalue.x = eventdata.x
        QUERY;

        $query = $this->db->query($query, [
            $by,
            $jurisdiction,
            $jurisdiction,
        ]);

        return $this->getObject($query);
    }

    public function getCurrentByGovernment(int $id): array
    {
        $query = <<<QUERY
            SELECT governmentshapecache.government AS id,
                public.st_asgeojson(public.st_boundary(public.st_union(governmentshapecache.geometry)), 5) AS geometry
            FROM gis.governmentshapecache
            WHERE governmentshapecache.government = ?
            GROUP BY 1
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ]);

        return $this->getObject($query);
    }

    // FUNCTION: extra.emptytonull
    // FUNCTION: extra.plsstownshipshort
    // VIEW: extra.governmentsubstitutecache

    public function getPartByGovernment(int $id): array
    {
        $query = <<<QUERY
            WITH affectedgovernmentsummary AS (
                SELECT DISTINCT affectedgovernmentgroup.affectedgovernmentgroupid AS affectedgovernmentid,
                affectedgovernmentgroup.event AS eventid,
                affectedgovernmentpart.affectedtypefrom AS affectedtypeid,
                'from' AS fromto,
                NULL::integer AS governmentto
                FROM geohistory.affectedgovernmentgroup
                JOIN geohistory.affectedgovernmentgrouppart
                    ON affectedgovernmentgroup.affectedgovernmentgroupid = affectedgovernmentgrouppart.affectedgovernmentgroup
                JOIN geohistory.affectedgovernmentpart
                    ON affectedgovernmentgrouppart.affectedgovernmentpart = affectedgovernmentpart.affectedgovernmentpartid
                JOIN extra.governmentsubstitutecache
                    ON affectedgovernmentpart.governmentfrom = governmentsubstitutecache.governmentid
                    AND governmentsubstitutecache.governmentsubstitute = ?
                UNION
                    SELECT DISTINCT affectedgovernmentgroup.affectedgovernmentgroupid AS affectedgovernmentid,
                    affectedgovernmentgroup.event AS eventid,
                    affectedgovernmentpart.affectedtypeto AS affectedtypeid,
                    'to' AS fromto,
                    affectedgovernmentpart.governmentto
                    FROM geohistory.affectedgovernmentgroup
                    JOIN geohistory.affectedgovernmentgrouppart
                        ON affectedgovernmentgroup.affectedgovernmentgroupid = affectedgovernmentgrouppart.affectedgovernmentgroup
                    JOIN geohistory.affectedgovernmentpart
                        ON affectedgovernmentgrouppart.affectedgovernmentpart = affectedgovernmentpart.affectedgovernmentpartid
                    JOIN extra.governmentsubstitutecache
                        ON affectedgovernmentpart.governmentto = governmentsubstitutecache.governmentid
                        AND governmentsubstitutecache.governmentsubstitute = ?
            ), governmentshapeeventpartparts AS (
                    SELECT affectedgovernmentgis.governmentshape AS governmentshapeid,
                    event.eventdatetext,
                    event.eventsort,
                    to_char(event.eventsortdate, 'Mon FMDD, YYYY') || CASE
                        WHEN event.eventeffective <> '' AND event.eventeffective NOT LIKE '%~%' THEN ''
                        ELSE ' (About)'
                    END AS eventtextsortdate,
                    event.eventslug,
                    array_agg(DISTINCT CASE
                        WHEN NOT eventgranted.eventgrantedsuccess THEN 'proposed'
                        WHEN affectedtype.affectedtypecreationdissolution IN ('end', 'alter', 'ascertain') AND affectedgovernmentsummary.fromto = 'from' THEN 'remove'
                        WHEN affectedtype.affectedtypecreationdissolution IN ('begin', 'alter', 'ascertain') AND affectedgovernmentsummary.fromto = 'to' THEN 'add'
                        WHEN affectedtype.affectedtypecreationdissolution IN ('subordinate', 'separate') THEN affectedtype.affectedtypecreationdissolution
                        ELSE 'other'
                    END ORDER BY CASE
                        WHEN NOT eventgranted.eventgrantedsuccess THEN 'proposed'
                        WHEN affectedtype.affectedtypecreationdissolution IN ('end', 'alter', 'ascertain') AND affectedgovernmentsummary.fromto = 'from' THEN 'remove'
                        WHEN affectedtype.affectedtypecreationdissolution IN ('begin', 'alter', 'ascertain') AND affectedgovernmentsummary.fromto = 'to' THEN 'add'
                        WHEN affectedtype.affectedtypecreationdissolution IN ('subordinate', 'separate') THEN affectedtype.affectedtypecreationdissolution
                        ELSE 'other'
                    END) AS eventstatus,
                    array_agg(DISTINCT affectedgovernmentsummary.governmentto ORDER BY affectedgovernmentsummary.governmentto) AS governmentto
                    FROM affectedgovernmentsummary
                    JOIN geohistory.affectedtype
                        ON affectedgovernmentsummary.affectedtypeid = affectedtype.affectedtypeid
                    JOIN gis.affectedgovernmentgis
                        ON affectedgovernmentsummary.affectedgovernmentid = affectedgovernmentgis.affectedgovernment
                    JOIN geohistory.event
                        ON affectedgovernmentsummary.eventid = event.eventid
                    JOIN geohistory.eventgranted
                        ON event.eventgranted = eventgranted.eventgrantedid
                    GROUP BY 1, 2, 3, 4, 5
            ), governmentshapeeventparts AS (
                    SELECT governmentshapeeventpartparts.governmentshapeid,
                    governmentshapeeventpartparts.eventdatetext,
                    governmentshapeeventpartparts.eventsort,
                    governmentshapeeventpartparts.eventtextsortdate,
                    governmentshapeeventpartparts.eventslug,
                    CASE
                        WHEN governmentshapeeventpartparts.eventstatus = '{add,remove}' THEN 'name' 
                        ELSE governmentshapeeventpartparts.eventstatus[1] 
                    END AS eventstatus,
                    extra.emptytonull(array_to_string(governmentshapeeventpartparts.governmentto, ','))::integer AS governmentto
                    FROM governmentshapeeventpartparts
            ), lasteventstatus AS (
                    SELECT governmentshapeeventparts.governmentshapeid,
                    (array_agg(governmentshapeeventparts.eventstatus ORDER BY CASE
                        WHEN governmentshapeeventparts.eventstatus IN ('add', 'remove') THEN 0
                        ELSE 1
                    END ASC, governmentshapeeventparts.eventsort DESC))[1] AS eventstatus
                    FROM governmentshapeeventparts
                    GROUP BY 1
            ), governmentshapeeventearliest AS (
                    SELECT min(governmentshapeeventparts.eventsort) AS mineventsort
                    FROM governmentshapeeventparts
            ), governmentshapeeventjsonparts AS (
                    SELECT governmentshapeeventparts.governmentshapeid,
                    governmentshapeeventparts.eventsort,
                    json_strip_nulls(json_build_object('eventsort', governmentshapeeventparts.eventsort,
                        'eventslug', governmentshapeeventparts.eventslug,
                        'eventstatus', governmentshapeeventparts.eventstatus,
                        'eventdatetext', governmentshapeeventparts.eventdatetext,
                        'eventtextsortdate', governmentshapeeventparts.eventtextsortdate,
                        'eventgovernmentlong', CASE
                            WHEN governmentshapeeventearliest.mineventsort = governmentshapeeventparts.eventsort OR governmentshapeeventparts.eventstatus = 'name' THEN government.governmentlong
                            ELSE NULL
                        END)) AS eventjson
                    FROM governmentshapeeventearliest,
                        governmentshapeeventparts
                    LEFT JOIN geohistory.government
                        ON governmentshapeeventparts.governmentto = government.governmentid
            ), governmentshapeevent AS (
                    SELECT governmentshapeeventjsonparts.governmentshapeid,
                    lasteventstatus.eventstatus,
                    json_agg(governmentshapeeventjsonparts.eventjson ORDER BY governmentshapeeventjsonparts.eventsort) AS eventjson
                    FROM governmentshapeeventjsonparts
                    JOIN lasteventstatus
                        ON governmentshapeeventjsonparts.governmentshapeid = lasteventstatus.governmentshapeid
                    GROUP BY 1, 2
            )
            SELECT
            CASE
                WHEN governmentshape.governmentshapeslug IS NOT NULL THEN governmentshape.governmentshapeslug
                ELSE governmentshape.governmentshapeid::text
            END AS governmentshapeslug,
            '' AS plsstownship,
            COALESCE(extra.plsstownshipshort(governmentshape.governmentshapeplsstownship), '') AS plsstownshipshort,
            COALESCE(governmentsubmunicipality.governmentslugsubstitute, '') AS submunicipality,
            COALESCE(governmentsubmunicipality.governmentlong, '') AS submunicipalitylong,
            governmentmunicipality.governmentslugsubstitute AS municipality,
            governmentmunicipality.governmentlong AS municipalitylong,
            governmentcounty.governmentslugsubstitute AS county,
            governmentcounty.governmentshort AS countyshort,
            st_asgeojson(governmentshape.governmentshapegeometry) AS geometry,
            CASE
                WHEN NOT (ARRAY[governmentshape.governmentcounty, governmentshape.governmentmunicipality, governmentshape.governmentshapeplsstownship, governmentshape.governmentschooldistrict, governmentshape.governmentsubmunicipality, governmentshape.governmentward] && ARRAY[?::integer]) AND governmentshapeevent.governmentshapeid IS NOT NULL AND governmentshapeevent.eventstatus = 'proposed' THEN 1
                WHEN NOT (ARRAY[governmentshape.governmentcounty, governmentshape.governmentmunicipality, governmentshape.governmentshapeplsstownship, governmentshape.governmentschooldistrict, governmentshape.governmentsubmunicipality, governmentshape.governmentward] && ARRAY[?::integer]) THEN 2
                WHEN (ARRAY[governmentshape.governmentcounty, governmentshape.governmentmunicipality, governmentshape.governmentshapeplsstownship, governmentshape.governmentschooldistrict, governmentshape.governmentsubmunicipality, governmentshape.governmentward] && ARRAY[?::integer]) AND governmentshapeevent.governmentshapeid IS NOT NULL AND governmentshapeevent.eventstatus = 'add' THEN 4
                ELSE 3
            END AS disposition,
            CASE
                WHEN governmentshapeevent.eventjson IS NULL THEN '[]'::json
                ELSE governmentshapeevent.eventjson
            END AS eventjson
            FROM gis.governmentshape
            JOIN geohistory.government governmentmunicipality
                ON governmentshape.governmentmunicipality = governmentmunicipality.governmentid
            JOIN geohistory.government governmentcounty
                ON governmentshape.governmentcounty = governmentcounty.governmentid
            LEFT JOIN geohistory.government governmentsubmunicipality
                ON governmentshape.governmentsubmunicipality = governmentsubmunicipality.governmentid
            LEFT JOIN governmentshapeevent
            ON governmentshape.governmentshapeid = governmentshapeevent.governmentshapeid
            WHERE governmentshape.governmentmunicipality = ? OR
            governmentshape.governmentcounty = ? OR
            (governmentshape.governmentschooldistrict IS NOT NULL AND governmentshape.governmentschooldistrict = ?) OR
            (governmentshape.governmentshapeplsstownship IS NOT NULL AND governmentshape.governmentshapeplsstownship = ?) OR
            (governmentshape.governmentsubmunicipality IS NOT NULL AND governmentshape.governmentsubmunicipality = ?) OR
            (governmentshape.governmentward IS NOT NULL AND governmentshape.governmentward = ?) OR
            governmentshape.governmentshapeid IN (
                SELECT governmentshapeevent.governmentshapeid
                FROM governmentshapeevent
            )
            ORDER BY 1
        QUERY;

        $query = $this->db->query($query, [
            $id,
            $id,
            $id,
            $id,
            $id,
            $id,
            $id,
            $id,
            $id,
            $id,
            $id,
        ]);

        return $this->getObject($query);
    }

    private function getSlugId(string $id): int
    {
        $query = <<<QUERY
            SELECT governmentshape.governmentshapeid AS id
                FROM gis.governmentshape
            WHERE governmentshape.governmentshapeslug = ?
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

    public function getTile(float $z, float $x, float $y): array
    {
        $query = <<<QUERY
            WITH mvtgeometry AS (
                SELECT ST_AsMVTGeom(ST_Transform(governmentshapecache.geometry, 3857), ST_TileEnvelope(?, ?, ?), extent => 4096, buffer => 64) AS geometry,
                    government.governmentid AS name,
                    government.governmentshort AS description,
                    governmentshapecache.governmentlayer
                FROM gis.governmentshapecache
                JOIN geohistory.government
                    ON governmentshapecache.government = government.governmentid
                WHERE (
                    governmentshapecache.governmentlayer = 'county'
                    OR (
                        governmentshapecache.governmentlayer IN ('municipality', 'submunicipality')
                        AND (? >= 6)
                    )
                )
                AND governmentshapecache.geometry && ST_Transform(ST_TileEnvelope(?, ?, ?, margin => (64.0 / 4096)), 4326)
            )
            SELECT ST_AsMVT(mvtgeometry.*, mvtgeometry.governmentlayer) AS mvt
            FROM mvtgeometry
            WHERE mvtgeometry.governmentlayer = 'county'
            UNION
            SELECT ST_AsMVT(mvtgeometry.*, mvtgeometry.governmentlayer) AS mvt
            FROM mvtgeometry
            WHERE mvtgeometry.governmentlayer = 'municipality'
            UNION
            SELECT ST_AsMVT(mvtgeometry.*, mvtgeometry.governmentlayer) AS mvt
            FROM mvtgeometry
            WHERE mvtgeometry.governmentlayer = 'submunicipality';
        QUERY;

        $query = $this->db->query($query, [
            $z,
            $x,
            $y,
            $z,
            $z,
            $x,
            $y,
        ]);

        return $this->getObject($query);
    }
}
