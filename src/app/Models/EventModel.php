<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    // extra.ci_model_event_detail(integer, character varying)
    // extra.ci_model_event_detail(text, character varying)

    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // FUNCTION: extra.governmentstatelink
    // FUNCTION: extra.eventgovernmentcache
    // FUNCTION: extra.governmentrelationcache

    public function getDetail($id, $state)
    {
        if (!is_int($id)) {
            $id = $this->getSlugId($id, $state);
        }

        $query = <<<QUERY
            SELECT DISTINCT 
                event.eventid,
                eventtype.eventtypeshort,
                eventmethod.eventmethodlong,
                event.eventlong,
                eventgranted.eventgrantedshort AS eventgranted,
                    CASE
                        WHEN eventgranted.eventgrantedshort = 'government' OR (event.eventfrom = 0 AND event.eventto = 0 AND event.eventeffective::text = ''::text AND event.eventeffectivetypepresumedsource IS NULL AND other.otherdatetype IS NULL) THEN false
                        ELSE true
                    END AS textflag,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                extra.shortdate(event.eventeffective) AS eventeffective,
                eventeffectivetype.eventeffectivetypegroup::text ||
                    CASE
                        WHEN eventeffectivetype.eventeffectivetypequalifier IS NOT NULL AND eventeffectivetype.eventeffectivetypequalifier::text = ''::text THEN ''::text
                        ELSE ': '::text || eventeffectivetype.eventeffectivetypequalifier::text
                    END AS eventeffectivetype,
                other.otherdate,
                other.otherdatetype,
                event.eventismapped,
                extra.governmentstatelink(event.government, ?, 'en') AS government
            FROM geohistory.event
                JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
                JOIN geohistory.eventmethod
                ON event.eventmethod = eventmethod.eventmethodid 
                JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
                LEFT JOIN ( SELECT other_1.otherdate,
                        other_1.otherdatetype
                    FROM ( SELECT DISTINCT extra.shortdate(filing.filingdate) AS otherdate,
                                'Final Decree'::text AS otherdatetype
                            FROM geohistory.adjudicationevent,
                                geohistory.filing,
                                geohistory.filingtype
                            WHERE adjudicationevent.adjudication = filing.adjudication AND filing.filingtype = filingtype.filingtypeid AND filingtype.filingtypefinalrecording AND adjudicationevent.event = ?
                            UNION
                            SELECT DISTINCT extra.shortdate(governmentsource.governmentsourcedate) AS otherdate,
                                'Letters Patent'::text AS otherdatetype
                            FROM geohistory.governmentsource,
                                geohistory.governmentsourceevent
                            WHERE governmentsource.governmentsourceid = governmentsourceevent.governmentsource AND governmentsource.governmentsourcetype::text = 'Letters Patent'::text AND governmentsourceevent.event = ?) other_1
                    WHERE 1 = (( SELECT count(*) AS rowct
                            FROM ( SELECT DISTINCT extra.shortdate(filing.filingdate) AS otherdate,
                                        'Final Decree'::text AS otherdatetype,
                                        true AS isother
                                    FROM geohistory.adjudicationevent,
                                        geohistory.filing,
                                        geohistory.filingtype
                                    WHERE adjudicationevent.adjudication = filing.adjudication AND filing.filingtype = filingtype.filingtypeid AND filingtype.filingtypefinalrecording AND adjudicationevent.event = ?
                                    UNION
                                    SELECT DISTINCT extra.shortdate(governmentsource.governmentsourcedate) AS otherdate,
                                        'Letters Patent'::text AS otherdatetype,
                                        true AS isother
                                    FROM geohistory.governmentsource,
                                        geohistory.governmentsourceevent
                                    WHERE governmentsource.governmentsourceid = governmentsourceevent.governmentsource AND governmentsource.governmentsourcetype::text = 'Letters Patent'::text AND governmentsourceevent.event = ?) other_2))) other ON 0 = 0
                LEFT JOIN geohistory.eventeffectivetype ON event.eventeffectivetypepresumedsource = eventeffectivetype.eventeffectivetypeid
                LEFT JOIN extra.eventgovernmentcache ON event.eventid = eventgovernmentcache.eventid
                LEFT JOIN extra.governmentrelationcache ON eventgovernmentcache.government = governmentrelationcache.governmentid
            WHERE event.eventid = ?
                AND (
                    governmentrelationcache.governmentrelationstate = ? OR 
                    governmentrelationcache.governmentrelationstate IS NULL
                )    
        QUERY;

        $query = $this->db->query($query, [
            $state,
            $id,
            $id,
            $id,
            $id,
            $id,
            strtoupper($state),
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_adjudication_event(integer)

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache

    public function getByAdjudication($id)
    {
        $query = <<<QUERY
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate,
                eventrelationship.eventrelationshipshort AS eventrelationship
            FROM geohistory.event
            JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
            JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
            JOIN extra.eventextracache
                ON event.eventid = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
            JOIN geohistory.adjudicationevent
                ON event.eventid = adjudicationevent.event 
                AND adjudicationevent.adjudication = ?
            JOIN geohistory.eventrelationship
                ON adjudicationevent.eventrelationship = eventrelationship.eventrelationshipid
            ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_reporter_event(integer)

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache

    public function getByAdjudicationSourceCitation($id)
    {
        $query = <<<QUERY
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
            FROM geohistory.event
            JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
            JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
            JOIN extra.eventextracache
                ON event.eventid = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
            JOIN geohistory.adjudicationevent
                ON event.eventid = adjudicationevent.event
            JOIN geohistory.adjudicationsourcecitation
                ON adjudicationevent.adjudication = adjudicationsourcecitation.adjudication
                AND adjudicationsourcecitation.adjudicationsourcecitationid = ?
            ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong;
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_government_event_failure(integer, integer[])

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache
    // VIEW: extra.eventgovernmentcache
    // VIEW: extra.governmentsubstitutecache

    public function getByGovernmentFailure($id, $events)
    {
        $query = <<<QUERY
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
            FROM geohistory.event
            JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
                AND NOT eventgranted.eventgrantedsuccess
                AND NOT eventgranted.eventgrantedplaceholder
            JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
            JOIN extra.eventextracache
                ON event.eventid = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
            JOIN extra.eventgovernmentcache
                ON event.eventid = eventgovernmentcache.eventid
            JOIN extra.governmentsubstitutecache
                ON eventgovernmentcache.government = governmentsubstitutecache.governmentid
            WHERE event.eventid <> ALL (?)
                AND governmentsubstitutecache.governmentsubstitute = ?
            ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong
        QUERY;

        $query = $this->db->query($query, [
            $events,
            $id,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_area_event_failure(integer, integer[])

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache

    public function getByGovernmentShapeFailure($id, $events)
    {
        $query = <<<QUERY
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
            FROM geohistory.event
            JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
            JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
            JOIN extra.eventextracache
                ON event.eventid = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
                AND event.eventid <> ALL (?)
            WHERE (event.eventid IN ( SELECT event_1.eventid
                    FROM geohistory.event event_1,
                        geohistory.affectedgovernmentgroup,
                        gis.affectedgovernmentgis
                    WHERE event_1.eventid = affectedgovernmentgroup.event
                        AND affectedgovernmentgroup.affectedgovernmentgroupid = affectedgovernmentgis.affectedgovernment
                        AND affectedgovernmentgis.governmentshape = ?
                    UNION
                    SELECT event_1.eventid
                    FROM geohistory.event event_1,
                        geohistory.metesdescription,
                        gis.metesdescriptiongis
                    WHERE event_1.eventid = metesdescription.event 
                        AND metesdescription.metesdescriptionid = metesdescriptiongis.metesdescription
                        AND metesdescriptiongis.governmentshape = ?))
            ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong
        QUERY;

        $query = $this->db->query($query, [
            $events,
            $id,
            $id,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_governmentsource_event(integer)

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache

    public function getByGovernmentSource($id)
    {
        $query = <<<QUERY
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
            FROM geohistory.event
            JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
            JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
            JOIN extra.eventextracache
                ON event.eventid = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
            JOIN geohistory.governmentsourceevent
                ON event.eventid = governmentsourceevent.event 
                AND governmentsourceevent.governmentsource = ?
            ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_government_event_success(integer, integer[])

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache
    // VIEW: extra.eventgovernmentcache
    // VIEW: extra.governmentsubstitutecache

    public function getByGovernmentSuccess($id, $events)
    {
        $query = <<<QUERY
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
            FROM geohistory.event
            JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
                AND eventgranted.eventgrantedsuccess   
            JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
            JOIN extra.eventextracache
                ON event.eventid = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
            JOIN extra.eventgovernmentcache
                ON event.eventid = eventgovernmentcache.eventid
            JOIN extra.governmentsubstitutecache
                ON eventgovernmentcache.government = governmentsubstitutecache.governmentid
            WHERE event.eventid <> ALL (?)
                AND governmentsubstitutecache.governmentsubstitute = ?
            ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong;
        QUERY;

        $query = $this->db->query($query, [
            $events,
            $id,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_source_event(integer)

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache

    public function getBySourceCitation($id)
    {
        $query = <<<QUERY
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
            FROM geohistory.event
            JOIN geohistory.eventgranted
                ON event.eventgranted = eventgranted.eventgrantedid
            JOIN geohistory.eventtype
                ON event.eventtype = eventtype.eventtypeid
            JOIN extra.eventextracache
                ON event.eventid = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
            JOIN geohistory.sourcecitationevent
                ON event.eventid = sourcecitationevent.event 
                AND sourcecitationevent.sourcecitation = ?
            ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_statistics_eventtype_nation_part(text, integer, integer, character varying, boolean)

    // FUNCTION: extra.governmentabbreviation
    // VIEW: extra.statistics_eventtype

    public function getByStatisticsNationPart($fields)
    {
        $for = $fields[0];
        $from = $fields[1];
        $to = $fields[2];
        $by = $fields[3];
        $state = $fields[4];
        if (empty($state)) {
            $state = implode(',', \App\Controllers\BaseController::getJurisdictions());
        }
        $state = '{' . strtoupper($state) . '}';

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT min(governmentidentifier.governmentidentifier) AS series,
                statistics_eventtype.governmentstate AS actualseries,
                statistics_eventtype.eventyear AS x,
                statistics_eventtype.eventcount::integer AS y
                FROM extra.statistics_eventtype
                JOIN geohistory.eventtype
                    ON statistics_eventtype.eventtype = eventtype.eventtypeid
                    AND eventtype.eventtypeshort = ?
                JOIN geohistory.governmentidentifier
                    ON statistics_eventtype.governmentstate = extra.governmentabbreviation(governmentidentifier.government)
                    AND governmentidentifier.governmentidentifiertype = 1
                WHERE statistics_eventtype.governmenttype = 'state'
                AND statistics_eventtype.grouptype = ?
                AND statistics_eventtype.governmentstate = ANY (?)
                AND statistics_eventtype.eventyear >= ?
                AND statistics_eventtype.eventyear <= ?
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
            $for,
            $by,
            $state,
            $from,
            $to,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_statistics_eventtype_nation_whole(text, integer, integer, character varying, boolean)

    // VIEW: extra.statistics_eventtype

    public function getByStatisticsNationWhole($fields)
    {
        $for = $fields[0];
        $from = $fields[1];
        $to = $fields[2];
        $by = $fields[3];

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT statistics_eventtype.eventyear AS x,
                statistics_eventtype.eventcount::text AS y
                FROM extra.statistics_eventtype
                JOIN geohistory.eventtype
                    ON statistics_eventtype.eventtype = eventtype.eventtypeid
                    AND eventtype.eventtypeshort = ?
                WHERE statistics_eventtype.governmenttype = 'nation'
                AND statistics_eventtype.grouptype = ?
                AND statistics_eventtype.governmentstate = ?
                AND statistics_eventtype.eventyear >= ?
                AND statistics_eventtype.eventyear <= ?
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
            $for,
            $by,
            ENVIRONMENT,
            $from,
            $to,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_statistics_eventtype_state_part(text, integer, integer, character varying, character varying)

    // VIEW: extra.statistics_eventtype

    public function getByStatisticsStatePart($fields)
    {
        $for = $fields[0];
        $from = $fields[1];
        $to = $fields[2];
        $by = $fields[3];
        $state = strtoupper($fields[4]);

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT statistics_eventtype.governmentcounty AS series,
                statistics_eventtype.eventyear AS x,
                statistics_eventtype.eventcount::integer AS y
                FROM extra.statistics_eventtype
                JOIN geohistory.eventtype
                    ON statistics_eventtype.eventtype = eventtype.eventtypeid
                    AND eventtype.eventtypeshort = ?
                WHERE statistics_eventtype.governmenttype = 'county'
                AND statistics_eventtype.grouptype = ?
                AND statistics_eventtype.governmentstate = ?
                AND statistics_eventtype.eventyear >= ?
                AND statistics_eventtype.eventyear <= ?
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
            $for,
            $by,
            $state,
            $from,
            $to,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_statistics_eventtype_state_whole(text, integer, integer, character varying, character varying)

    // VIEW: extra.statistics_eventtype

    public function getByStatisticsStateWhole($fields)
    {
        $for = $fields[0];
        $from = $fields[1];
        $to = $fields[2];
        $by = $fields[3];
        $state = strtoupper($fields[4]);

        $query = <<<QUERY
            WITH eventdata AS (
                SELECT DISTINCT statistics_eventtype.eventyear AS x,
                statistics_eventtype.eventcount::text AS y
                FROM extra.statistics_eventtype
                JOIN geohistory.eventtype
                    ON statistics_eventtype.eventtype = eventtype.eventtypeid
                    AND eventtype.eventtypeshort = ?
                WHERE statistics_eventtype.governmenttype = 'state'
                AND statistics_eventtype.grouptype = ?
                AND statistics_eventtype.governmentstate = ?
                AND statistics_eventtype.eventyear >= ?
                AND statistics_eventtype.eventyear <= ?
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
            $for,
            $by,
            $state,
            $from,
            $to,
            $state,
        ])->getResult();

        return $query ?? [];
    }

    // extra.ci_model_search_event_government(text, text, text, text, integer, integer)

    // FUNCTION: extra.eventsortdate
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.eventextracache
    // VIEW: extra.eventgovernmentcache
    // VIEW: extra.governmentextracache
    // VIEW: extra.governmentrelationcache

    public function getSearchByGovernment($parameters)
    {
        $state = $parameters[0];
        $government = $parameters[1];
        $parent = $parameters[2];
        $eventType = $parameters[3];
        $year = $parameters[4];
        $plusMinus = $parameters[5];

        $query = <<<QUERY
            WITH alternategovernment AS (
                SELECT DISTINCT alternategovernment.governmentrelation AS governmentid
                FROM extra.governmentrelationcache
                JOIN extra.governmentrelationcache alternategovernment
                    ON governmentrelationcache.governmentid = alternategovernment.governmentid
                    AND alternategovernment.governmentlevel = alternategovernment.governmentrelationlevel
                    AND governmentrelationcache.governmentlevel > 2
                    AND (
                    governmentrelationcache.governmentrelationstate = ?
                    OR governmentrelationcache.governmentrelationstate IS NULL
                    )
                    AND governmentrelationcache.governmentshort ILIKE ?
            )
            SELECT DISTINCT eventextracache.eventslug,
                eventtype.eventtypeshort,
                event.eventlong,
                extra.rangefix(event.eventfrom::text, event.eventto::text) AS eventrange,
                eventgranted.eventgrantedshort AS eventgranted,
                extra.shortdate(event.eventeffective) AS eventeffective,
                extra.eventsortdate(event.eventid) AS eventsortdate
                FROM alternategovernment
                JOIN extra.eventgovernmentcache      
                    ON alternategovernment.governmentid = eventgovernmentcache.government
                JOIN geohistory.event
                    ON eventgovernmentcache.eventid = event.eventid
                JOIN geohistory.eventgranted
                    ON event.eventgranted = eventgranted.eventgrantedid
                    AND NOT eventgranted.eventgrantedplaceholder
                JOIN geohistory.eventtype
                    ON event.eventtype = eventtype.eventtypeid  
                JOIN extra.governmentextracache
                    ON alternategovernment.governmentid = governmentextracache.governmentid
                    AND NOT governmentextracache.governmentisplaceholder
                JOIN extra.governmentrelationcache lookupgovernment
                    ON alternategovernment.governmentid = lookupgovernment.governmentid
                    AND lookupgovernment.governmentid <> lookupgovernment.governmentrelation
                JOIN extra.governmentextracache governmentparentextracache
                    ON lookupgovernment.governmentrelation = governmentparentextracache.governmentid
                    AND (? = ''::text OR governmentparentextracache.governmentshort ILIKE ?)
                JOIN extra.eventextracache
                    ON event.eventid = eventextracache.eventid
                    AND eventextracache.eventslugnew IS NULL
                WHERE (? = ''::text 
                    OR ? = 'Any Type'::text
                    OR (? = 'Only Border Changes'::text AND eventtype.eventtypeborders ~~ 'yes%')
                    OR eventtype.eventtypeshort = ?)
                AND (0 = ?
                    OR (event.eventfrom <= (? + ?) AND event.eventfrom >= (? - ?))
                    OR (event.eventto <= (? + ?) AND event.eventto >= (? - ?))
                    OR (floor(extra.eventsortdate(event.eventid)) <= (? + ?) AND floor(extra.eventsortdate(event.eventid)) >= (? - ?)))
                ORDER BY (extra.eventsortdate(event.eventid)), event.eventlong
        QUERY;

        $query = $this->db->query($query, [
            strtoupper($state),
            $government,
            $parent,
            $parent,
            $eventType,
            $eventType,
            $eventType,
            $eventType,
            $year,
            $year,
            $plusMinus,
            $year,
            $plusMinus,
            $year,
            $plusMinus,
            $year,
            $plusMinus,
            $year,
            $plusMinus,
            $year,
            $plusMinus,
        ])->getResult();

        return $query ?? [];
    }

    // extra.eventslugidreplacement(text)

    // VIEW: extra.eventextracache

    private function getSlugId($id, $state)
    {
        $query = <<<QUERY
            SELECT eventextracache.eventid AS id,
                eventextracache.eventslugnew
            FROM extra.eventextracache
            WHERE eventextracache.eventslug = ?
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ])->getResult();

        $id = -1;

        if (count($query) == 1) {
            if (!empty($query[0]->eventslugnew)) {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: /" . \Config\Services::request()->getLocale() . "/" . $state . "/event/" . $query[0]->eventslugnew . "/");
                exit();
            }
            $id = $query[0]->id;
        }
        
        return $id;
    }
}