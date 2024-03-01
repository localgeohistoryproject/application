<?php

namespace App\Models;

use CodeIgniter\Model;

class MetesDescriptionModel extends Model
{
    // extra.ci_model_metes_detail(integer, character varying)
    // extra.ci_model_metes_detail(text, character varying)

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.metesdescriptionextracache
    // VIEW: extra.eventextracache
    // VIEW: extra.eventgovernmentcache
    // VIEW: extra.governmentrelationcache

    public function getDetail($id, $state)
    {
        if (!is_int($id)) {
            $id = $this->getSlugId($id);
        }
        
        $query = <<<QUERY
            WITH metesdescriptionpart AS (
                SELECT DISTINCT metesdescription.metesdescriptionid,
                metesdescription.metesdescriptiontype,
                metesdescription.metesdescriptionsource,
                metesdescription.metesdescriptionbeginningpoint,
                metesdescriptionextracache.metesdescriptionlong,
                metesdescription.metesdescriptionacres,
                    CASE
                        WHEN metesdescription.metesdescriptionlongitude = 0::numeric AND metesdescription.metesdescriptionlatitude = 0::numeric THEN false
                        ELSE true
                    END AS hasbeginpoint,
                eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
                FROM geohistory.metesdescription
                    LEFT JOIN extra.metesdescriptionextracache
                    ON metesdescription.metesdescriptionid = metesdescriptionextracache.metesdescriptionid
                    JOIN geohistory.event
                    ON metesdescription.event = event.eventid
                    JOIN geohistory.eventgranted
                    ON event.eventgranted = eventgranted.eventgrantedid
                    LEFT JOIN extra.eventextracache
                    ON event.eventid = eventextracache.eventid
                    AND eventextracache.eventslugnew IS NULL
                    JOIN geohistory.eventtype
                    ON event.eventtype = eventtype.eventtypeid     
                    LEFT JOIN extra.eventgovernmentcache
                    ON metesdescription.event = eventgovernmentcache.eventid
                    LEFT JOIN extra.governmentrelationcache
                    ON eventgovernmentcache.government = governmentrelationcache.governmentid
                WHERE metesdescription.metesdescriptionid = ?
                AND (governmentrelationcache.governmentrelationstate = ? OR governmentrelationcache.governmentrelationstate IS NULL)
            )
            SELECT metesdescriptionpart.*,
            public.st_asgeojson(public.st_buffer(public.st_collect(governmentshape.governmentshapegeometry), 0)) AS geometry    
                FROM metesdescriptionpart
                LEFT JOIN gis.metesdescriptiongis
                ON metesdescriptionpart.metesdescriptionid = metesdescriptiongis.metesdescription
                LEFT JOIN gis.governmentshape
                ON metesdescriptiongis.governmentshape = governmentshape.governmentshapeid
            GROUP BY 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
        QUERY;

        $query = $this->db->query($query, [
            $id,
            strtoupper($state),
        ])->getResult();

        return $query ?? [];
    }

    // extra.metesdescriptionslugid(text)

    // VIEW: extra.metesdescriptionextracache

    private function getSlugId($id)
    {
        $query = <<<QUERY
            SELECT metesdescriptionextracache.metesdescriptionid AS id
                FROM extra.metesdescriptionextracache
            WHERE metesdescriptionextracache.metesdescriptionslug = ?
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ])->getResult();

        $id = -1;

        if (count($query) == 1) {
            $id = $query[0]->id;
        }
        
        return $id;
    }
}