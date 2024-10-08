<?php

namespace App\Models;

use CodeIgniter\Model;

class GovernmentSourceModel extends Model
{
    // extra.ci_model_governmentsource_detail(integer, character varying, boolean, character varying)
    // extra.ci_model_governmentsource_detail(text, character varying, boolean, character varying)

    // FUNCTION: extra.governmentlong
    // FUNCTION: extra.governmentstatelink
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.sourceextra

    public function getDetail(int|string $id, string $state): array
    {
        if (!is_int($id)) {
            $id = $this->getSlugId($id);
        }

        $query = <<<QUERY
            SELECT DISTINCT governmentsource.governmentsourceid,
                extra.governmentstatelink(governmentsource.government, ?, ?) AS government,
                extra.governmentlong(governmentsource.government, ?) AS governmentlong,
                governmentsource.governmentsourcetype,
                governmentsource.governmentsourcenumber,
                    CASE
                        WHEN (NOT ?) AND (governmentsource.governmentsourcetitle ~* '^[~#]' OR governmentsource.governmentsourcetitle ~~ '[Type %') THEN ''
                        ELSE governmentsource.governmentsourcetitle
                    END AS governmentsourcetitle,
                extra.shortdate(governmentsource.governmentsourcedate) AS governmentsourcedate,
                governmentsource.governmentsourcedate AS governmentsourcedatesort,
                extra.shortdate(governmentsource.governmentsourceapproveddate) AS governmentsourceapproveddate,
                governmentsource.governmentsourceapproveddate AS governmentsourceapproveddatesort,
                extra.shortdate(governmentsource.governmentsourceeffectivedate) AS governmentsourceeffectivedate,
                governmentsource.governmentsourceeffectivedate AS governmentsourceeffectivedatesort,
                    CASE
                        WHEN governmentsource.governmentsourceapproveddate <> '' THEN governmentsource.governmentsourceapproveddate
                        WHEN governmentsource.governmentsourcedate <> '' THEN governmentsource.governmentsourcedate
                        WHEN governmentsource.governmentsourceeffectivedate <> '' THEN governmentsource.governmentsourceeffectivedate
                        ELSE ''
                    END || governmentsource.governmentsourcetype || lpad(governmentsource.governmentsourcenumber, 5, '0') AS governmentsourcesort,
                governmentsource.governmentsourcebody,
                governmentsource.governmentsourceterm,
                governmentsource.governmentsourceapproved,
                trim(CASE
                    WHEN governmentsource.governmentsourcevolumetype = '' OR governmentsource.governmentsourcevolume = '' THEN ''
                    ELSE governmentsource.governmentsourcevolumetype
                END ||
                CASE
                    WHEN governmentsource.governmentsourcevolume = '' THEN ''
                    ELSE ' v. ' || governmentsource.governmentsourcevolume
                END ||
                CASE
                    WHEN governmentsource.governmentsourcevolume <> '' AND governmentsource.governmentsourcepagefrom <> '' AND governmentsource.governmentsourcepagefrom <> '0' THEN ', '
                    ELSE ''
                END ||
                CASE
                    WHEN governmentsource.governmentsourcepagefrom = '' OR governmentsource.governmentsourcepagefrom = '0' THEN ''
                    ELSE ' p. ' || extra.rangefix(governmentsource.governmentsourcepagefrom, governmentsource.governmentsourcepageto)
                END) AS governmentsourcelocation,
                trim(CASE
                    WHEN governmentsource.sourcecitationvolumetype = '' OR governmentsource.sourcecitationvolume = '' THEN ''
                    ELSE governmentsource.sourcecitationvolumetype
                END ||
                CASE
                    WHEN governmentsource.sourcecitationvolume = '' THEN ''
                    ELSE ' v. ' || governmentsource.sourcecitationvolume
                END ||
                CASE
                    WHEN governmentsource.sourcecitationvolume <> '' AND governmentsource.sourcecitationpagefrom <> '' AND governmentsource.sourcecitationpagefrom <> '0' THEN ', '
                    ELSE ''
                END ||
                CASE
                    WHEN governmentsource.sourcecitationpagefrom = '' OR governmentsource.sourcecitationpagefrom = '0' THEN ''
                    ELSE ' p. ' || extra.rangefix(governmentsource.sourcecitationpagefrom, governmentsource.sourcecitationpageto)
                END) AS sourcecitationlocation,
                sourceextra.sourceabbreviation,
                source.sourcetype,
                sourceextra.sourcefullcitation,
                source.sourceid,
                'sourceitem' AS linktype
            FROM geohistory.governmentsource
            JOIN geohistory.source
                ON governmentsource.source = source.sourceid
            JOIN extra.sourceextra
                ON source.sourceid = sourceextra.sourceid
            WHERE governmentsource.governmentsourceid = ?
            -- Add state filter
        QUERY;

        return $this->db->query($query, [
            $state,
            \Config\Services::request()->getLocale(),
            strtoupper($state),
            \App\Controllers\BaseController::isLive(),
            $id
        ])->getResult();
    }

    // extra.ci_model_event_governmentsource(integer, character varying, boolean, character varying)

    // FUNCTION: extra.governmentlong
    // FUNCTION: extra.governmentstatelink
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.governmentsourceextracache

    public function getByEvent(int $id, string $state): array
    {
        $query = <<<QUERY
            SELECT DISTINCT extra.governmentstatelink(governmentsource.government, ?, ?) AS government,
                extra.governmentlong(governmentsource.government, ?) AS governmentlong,
                governmentsource.governmentsourcetype,
                governmentsource.governmentsourcenumber,
                    CASE
                        WHEN (NOT ?) AND (governmentsource.governmentsourcetitle ~* '^[~#]' OR governmentsource.governmentsourcetitle ~~ '[Type %') THEN ''
                        ELSE governmentsource.governmentsourcetitle
                    END AS governmentsourcetitle,
                extra.shortdate(governmentsource.governmentsourcedate) AS governmentsourcedate,
                governmentsource.governmentsourcedate AS governmentsourcedatesort,
                extra.shortdate(governmentsource.governmentsourceapproveddate) AS governmentsourceapproveddate,
                governmentsource.governmentsourceapproveddate AS governmentsourceapproveddatesort,
                extra.shortdate(governmentsource.governmentsourceeffectivedate) AS governmentsourceeffectivedate,
                governmentsource.governmentsourceeffectivedate AS governmentsourceeffectivedatesort,
                    CASE
                        WHEN governmentsource.governmentsourceapproveddate <> '' THEN governmentsource.governmentsourceapproveddate
                        WHEN governmentsource.governmentsourcedate <> '' THEN governmentsource.governmentsourcedate
                        WHEN governmentsource.governmentsourceeffectivedate <> '' THEN governmentsource.governmentsourceeffectivedate
                        ELSE ''
                    END || governmentsource.governmentsourcetype || lpad(governmentsource.governmentsourcenumber, 5, '0') AS governmentsourcesort,
                governmentsource.governmentsourcebody,
                governmentsource.governmentsourceterm,
                governmentsource.governmentsourceapproved,
                governmentsourceextracache.governmentsourceslug,
                trim(CASE
                    WHEN governmentsource.governmentsourcevolumetype = '' OR governmentsource.governmentsourcevolume = '' THEN ''
                    ELSE governmentsource.governmentsourcevolumetype
                END ||
                CASE
                    WHEN governmentsource.governmentsourcevolume = '' THEN ''
                    ELSE ' v. ' || governmentsource.governmentsourcevolume
                END ||
                CASE
                    WHEN governmentsource.governmentsourcevolume <> '' AND governmentsource.governmentsourcepagefrom <> '' AND governmentsource.governmentsourcepagefrom <> '0' THEN ', '
                    ELSE ''
                END ||
                CASE
                    WHEN governmentsource.governmentsourcepagefrom = '' OR governmentsource.governmentsourcepagefrom = '0' THEN ''
                    ELSE ' p. ' || extra.rangefix(governmentsource.governmentsourcepagefrom, governmentsource.governmentsourcepageto)
                END) AS governmentsourcelocation,
                trim(CASE
                    WHEN governmentsource.sourcecitationvolumetype = '' OR governmentsource.sourcecitationvolume = '' THEN ''
                    ELSE governmentsource.sourcecitationvolumetype
                END ||
                CASE
                    WHEN governmentsource.sourcecitationvolume = '' THEN ''
                    ELSE ' v. ' || governmentsource.sourcecitationvolume
                END ||
                CASE
                    WHEN governmentsource.sourcecitationvolume <> '' AND governmentsource.sourcecitationpagefrom <> '' AND governmentsource.sourcecitationpagefrom <> '0' THEN ', '
                    ELSE ''
                END ||
                CASE
                    WHEN governmentsource.sourcecitationpagefrom = '' OR governmentsource.sourcecitationpagefrom = '0' THEN ''
                    ELSE ' p. ' || extra.rangefix(governmentsource.sourcecitationpagefrom, governmentsource.sourcecitationpageto)
                END) AS sourcecitationlocation
            FROM geohistory.governmentsource
            JOIN geohistory.governmentsourceevent
                ON governmentsource.governmentsourceid = governmentsourceevent.governmentsource
                AND governmentsourceevent.event = ?
            LEFT JOIN extra.governmentsourceextracache
                ON governmentsource.governmentsourceid = governmentsourceextracache.governmentsourceid
                AND governmentsourceextracache.hassource
            ORDER BY 12
        QUERY;

        return $this->db->query($query, [
            $state,
            \Config\Services::request()->getLocale(),
            strtoupper($state),
            \App\Controllers\BaseController::isLive(),
            $id,
        ])->getResult();
    }

    // extra.ci_model_government_governmentsource(integer, character varying, boolean)

    // FUNCTION: extra.governmentlong
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // VIEW: extra.governmentsubstitutecache

    public function getByGovernment(int $id, string $state): array
    {
        $query = <<<QUERY
            SELECT DISTINCT array_agg(event.eventslug) AS eventslug,
                governmentsource.governmentsourcetype,
                governmentsource.governmentsourcenumber,
                    CASE
                        WHEN (NOT ?) AND (governmentsource.governmentsourcetitle ~* '^[~#]' OR governmentsource.governmentsourcetitle ~~ '[Type %') THEN ''
                        ELSE governmentsource.governmentsourcetitle
                    END AS governmentsourcetitle,
                extra.shortdate(governmentsource.governmentsourcedate) AS governmentsourcedate,
                governmentsource.governmentsourcedate AS governmentsourcedatesort,
                extra.shortdate(governmentsource.governmentsourceapproveddate) AS governmentsourceapproveddate,
                governmentsource.governmentsourceapproveddate AS governmentsourceapproveddatesort,
                extra.shortdate(governmentsource.governmentsourceeffectivedate) AS governmentsourceeffectivedate,
                governmentsource.governmentsourceeffectivedate AS governmentsourceeffectivedatesort,
                    CASE
                        WHEN governmentsource.governmentsourceapproveddate <> '' THEN governmentsource.governmentsourceapproveddate
                        WHEN governmentsource.governmentsourcedate <> '' THEN governmentsource.governmentsourcedate
                        WHEN governmentsource.governmentsourceeffectivedate <> '' THEN governmentsource.governmentsourceeffectivedate
                        ELSE ''
                    END || governmentsource.governmentsourcetype || lpad(governmentsource.governmentsourcenumber, 5, '0') AS governmentsourcesort,
                governmentsource.governmentsourcebody,
                governmentsource.governmentsourceterm,
                governmentsource.governmentsourceapproved,
                trim(CASE
                    WHEN governmentsource.governmentsourcevolumetype = '' OR governmentsource.governmentsourcevolume = '' THEN ''
                    ELSE governmentsource.governmentsourcevolumetype
                END ||
                CASE
                    WHEN governmentsource.governmentsourcevolume = '' THEN ''
                    ELSE ' v. ' || governmentsource.governmentsourcevolume
                END ||
                CASE
                    WHEN governmentsource.governmentsourcevolume <> '' AND governmentsource.governmentsourcepagefrom <> '' AND governmentsource.governmentsourcepagefrom <> '0' THEN ', '
                    ELSE ''
                END ||
                CASE
                    WHEN governmentsource.governmentsourcepagefrom = '' OR governmentsource.governmentsourcepagefrom = '0' THEN ''
                    ELSE ' p. ' || extra.rangefix(governmentsource.governmentsourcepagefrom, governmentsource.governmentsourcepageto)
                END) AS governmentsourcelocation,
                trim(CASE
                    WHEN governmentsource.sourcecitationvolumetype = '' OR governmentsource.sourcecitationvolume = '' THEN ''
                    ELSE governmentsource.sourcecitationvolumetype
                END ||
                CASE
                    WHEN governmentsource.sourcecitationvolume = '' THEN ''
                    ELSE ' v. ' || governmentsource.sourcecitationvolume
                END ||
                CASE
                    WHEN governmentsource.sourcecitationvolume <> '' AND governmentsource.sourcecitationpagefrom <> '' AND governmentsource.sourcecitationpagefrom <> '0' THEN ', '
                    ELSE ''
                END ||
                CASE
                    WHEN governmentsource.sourcecitationpagefrom = '' OR governmentsource.sourcecitationpagefrom = '0' THEN ''
                    ELSE ' p. ' || extra.rangefix(governmentsource.sourcecitationpagefrom, governmentsource.sourcecitationpageto)
                END) AS sourcecitationlocation,
                extra.governmentlong(governmentsource.government, ?) AS governmentlong,
                '' AS government
            FROM geohistory.governmentsource
            JOIN extra.governmentsubstitutecache
                ON governmentsource.government = governmentsubstitutecache.governmentid
                AND governmentsubstitutecache.governmentsubstitute = ?
            LEFT JOIN geohistory.governmentsourceevent
                ON governmentsource.governmentsourceid = governmentsourceevent.governmentsource
            LEFT JOIN geohistory.event
                ON governmentsourceevent.event = event.eventid
            GROUP BY 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18
            ORDER BY 11
        QUERY;

        return $this->db->query($query, [
            \App\Controllers\BaseController::isLive(),
            strtoupper($state),
            $id,
        ])->getResult();
    }

    // extra.governmentsourceslugid(text)

    // VIEW: extra.governmentsourceextracache

    private function getSlugId(string $id): int
    {
        $query = <<<QUERY
            SELECT governmentsourceextracache.governmentsourceid AS id
                FROM extra.governmentsourceextracache
            WHERE governmentsourceextracache.governmentsourceslug = ?
                AND governmentsourceextracache.hassource
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
