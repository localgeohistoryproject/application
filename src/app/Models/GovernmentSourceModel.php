<?php

namespace App\Models;

use CodeIgniter\Model;

class GovernmentSourceModel extends Model
{
    // extra.ci_model_government_governmentsource(integer, character varying, boolean)

    // FUNCTION: extra.governmentlong
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate
    // FUNCTION: extra.zeropad
    // VIEW: extra.eventextracache
    // VIEW: extra.governmentsubstitutecache

    public function getByGovernment($id, $state, $locale)
    {
        $query = <<<QUERY
            SELECT DISTINCT array_agg(eventextracache.eventslug) AS eventslug,
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
                    END || governmentsource.governmentsourcetype || extra.zeropad(governmentsource.governmentsourcenumber, 5) AS governmentsourcesort,
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
            LEFT JOIN extra.eventextracache
                ON governmentsourceevent.event = eventextracache.eventid
                AND eventextracache.eventslugnew IS NULL
            GROUP BY 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18
            ORDER BY 11
        QUERY;

        $query = $this->db->query($query, [
            $locale,
            strtoupper($state),
            $id,
        ])->getResult();

        return $query ?? [];
    }
}