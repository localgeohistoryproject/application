<?php

namespace App\Models;

use CodeIgniter\Model;

class AdjudicationModel extends Model
{
    // extra.ci_model_adjudication_detail(character varying, character varying)
    // extra.ci_model_adjudication_detail(integer, character varying)

    // FUNCTION: extra.shortdate
    // FUNCTION: extra.tribunalfilingoffice
    // FUNCTION: extra.tribunallong
    // VIEW: extra.adjudicationextracache
    // VIEW: extra.adjudicationgovernmentcache

    public function getDetail(int|string $id, string $state): array
    {
        if (!is_int($id)) {
            $id = $this->getSlugId($id);
        }

        $query = <<<QUERY
            SELECT DISTINCT adjudication.adjudicationid,
                adjudicationtype.adjudicationtypelong,
                extra.tribunallong(tribunal.tribunalid) AS tribunallong,
                adjudication.adjudicationnumber,
                extra.shortdate(adjudication.adjudicationterm || CASE
                    WHEN length(adjudication.adjudicationterm) = 4 THEN '-~07-~28'
                    WHEN length(adjudication.adjudicationterm) = 7 THEN '-~28'
                    ELSE ''
                END) AS adjudicationterm,
                CASE
                    WHEN adjudication.adjudicationlong = '' AND adjudication.adjudicationshort = '' AND adjudication.adjudicationnotes = '' THEN FALSE
                    ELSE TRUE
                END AS textflag,
                adjudication.adjudicationlong,
                adjudication.adjudicationshort,
                adjudication.adjudicationnotes,
                extra.tribunalfilingoffice(tribunal.tribunalid) AS tribunalfilingoffice,
                adjudicationextracache.adjudicationtitle
            FROM geohistory.adjudicationtype
            JOIN geohistory.tribunal
                ON adjudicationtype.tribunal = tribunal.tribunalid
            JOIN geohistory.adjudication
                ON adjudicationtype.adjudicationtypeid = adjudication.adjudicationtype
                AND adjudication.adjudicationid = ?
            JOIN extra.adjudicationextracache
                ON adjudication.adjudicationid = adjudicationextracache.adjudicationid
            LEFT JOIN extra.adjudicationgovernmentcache
                ON adjudication.adjudicationid = adjudicationgovernmentcache.adjudicationid
            WHERE governmentrelationstate = ?
                OR governmentrelationstate IS NULL
        QUERY;

        return $this->db->query($query, [
            $id,
            strtoupper($state),
        ])->getResult();
    }

    // extra.ci_model_reporter_adjudication(integer)

    // FUNCTION: extra.shortdate
    // FUNCTION: extra.tribunallong
    // VIEW: extra.adjudicationextracache

    public function getByAdjudicationSourceCitation(int $id): array
    {
        $query = <<<QUERY
            SELECT adjudicationextracache.adjudicationslug,
                adjudicationtype.adjudicationtypelong,
                extra.tribunallong(adjudicationtype.tribunal) AS tribunallong,
                adjudication.adjudicationnumber,
                extra.shortdate(adjudication.adjudicationterm || CASE
                    WHEN length(adjudication.adjudicationterm) = 4 THEN '-~07-~28'
                    WHEN length(adjudication.adjudicationterm) = 7 THEN '-~28'
                    ELSE ''
                END) AS adjudicationterm,
                adjudication.adjudicationterm AS adjudicationtermsort
            FROM geohistory.adjudication
            JOIN extra.adjudicationextracache
                ON adjudication.adjudicationid = adjudicationextracache.adjudicationid
            JOIN geohistory.adjudicationtype
                ON adjudication.adjudicationtype = adjudicationtype.adjudicationtypeid
            JOIN geohistory.adjudicationsourcecitation
                ON adjudication.adjudicationid = adjudicationsourcecitation.adjudication
                AND adjudicationsourcecitation.adjudicationsourcecitationid = ?
        QUERY;

        return $this->db->query($query, [
            $id,
        ])->getResult();
    }

    // extra.ci_model_event_adjudication(integer)

    // FUNCTION: extra.tribunallong
    // FUNCTION: extra.shortdate
    // VIEW: extra.adjudicationextracache

    public function getByEvent(int $id): array
    {
        $query = <<<QUERY
            SELECT adjudicationextracache.adjudicationslug,
                adjudicationtype.adjudicationtypelong,
                extra.tribunallong(adjudicationtype.tribunal) AS tribunallong,
                adjudication.adjudicationnumber,
                extra.shortdate(adjudication.adjudicationterm || CASE
                    WHEN length(adjudication.adjudicationterm) = 4 THEN '-~07-~28'
                    WHEN length(adjudication.adjudicationterm) = 7 THEN '-~28'
                    ELSE ''
                END) AS adjudicationterm,
                adjudication.adjudicationterm AS adjudicationtermsort,
                eventrelationship.eventrelationshipshort AS eventrelationship
            FROM geohistory.adjudicationevent
            JOIN geohistory.eventrelationship
                ON adjudicationevent.eventrelationship = eventrelationship.eventrelationshipid
            JOIN geohistory.adjudication
                ON adjudicationevent.adjudication = adjudication.adjudicationid
            JOIN extra.adjudicationextracache
                ON adjudication.adjudicationid = adjudicationextracache.adjudicationid
            JOIN geohistory.adjudicationtype
                ON adjudication.adjudicationtype = adjudicationtype.adjudicationtypeid
            WHERE adjudicationevent.event = ?
        QUERY;

        return $this->db->query($query, [
            $id,
        ])->getResult();
    }

    // extra.adjudicationslugid(text)

    // VIEW: extra.adjudicationextracache

    private function getSlugId(string $id): int
    {
        $query = <<<QUERY
            SELECT adjudicationextracache.adjudicationid AS id
                FROM extra.adjudicationextracache
            WHERE adjudicationextracache.adjudicationslug = ?
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ])->getResult();

        $id = -1;

        if (count($query) === 1) {
            $id = $query[0]->id;
        }

        return $id;
    }
}
