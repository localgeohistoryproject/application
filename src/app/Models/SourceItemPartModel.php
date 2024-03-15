<?php

namespace App\Models;

use CodeIgniter\Model;

class SourceItemPartModel extends Model
{
    // extra.ci_model_reporter_url(integer)

    public function getByAdjudicationSourceCitation(int $id): array
    {
        $query = <<<QUERY
            SELECT sourceitem.sourceitemurl ||
            CASE
                WHEN sourceitem.sourceitemurlcomplete THEN ''::text
                ELSE
                CASE
                    WHEN sourceitempart.sourceitempartisbypage THEN sourceitempart.sourceitempartsequencecharacter::text || (sourceitempart.sourceitempartsequence + adjudicationsourcecitation.adjudicationsourcecitationpagefrom)
                    ELSE replace(format('%04s', (sourceitempart.sourceitempartsequence)::text), ' ', '0') || sourceitempart.sourceitempartsequencecharacter::text
                END || sourceitempart.sourceitempartsequencecharacterafter::text
            END AS url
            FROM geohistory.sourceitempart,
                geohistory.sourceitem,
                geohistory.source,
                geohistory.adjudicationsourcecitation
            WHERE sourceitempart.sourceitem = sourceitem.sourceitemid
                AND sourceitem.source = source.sourceurlsubstitute
                AND source.sourceid = adjudicationsourcecitation.source
                AND (
                    (sourceitempartfrom IS NULL AND sourceitempartto IS NULL) OR
                    (sourceitempartisbypage AND sourceitempartfrom <= adjudicationsourcecitationpagefrom AND sourceitempartto >= adjudicationsourcecitationpagefrom)
                ) AND adjudicationsourcecitationvolume::character varying = sourceitemvolume
                AND adjudicationsourcecitation.adjudicationsourcecitationid = ?
        QUERY;

        return $this->db->query($query, [
            $id,
        ])->getResult();
    }

    // extra.ci_model_governmentsource_url(integer)

    // FUNCTION: extra.sourceurlid

    public function getByGovernmentSource(int $id): array
    {
        $query = <<<QUERY
            SELECT sourceitem.sourceitemurl ||
            CASE
                WHEN sourceitem.sourceitemurlcomplete THEN ''::text
                ELSE
                CASE
                    WHEN sourcecitationpagefrom ~ '^\d+$' THEN sourceitempart.sourceitempartsequencecharacter::text || (sourceitempart.sourceitempartsequence + sourcecitationpagefrom::integer)
                    ELSE replace(format('%04s', (sourceitempart.sourceitempartsequence)::text), ' ', '0') || || sourceitempart.sourceitempartsequencecharacter::text
                END || sourceitempart.sourceitempartsequencecharacterafter::text
            END AS url
            FROM geohistory.governmentsource
            JOIN geohistory.sourceitem
                ON sourceitem.source = ANY (extra.sourceurlid(governmentsource.source))
            JOIN geohistory.sourceitempart
                ON sourceitem.sourceitemid = sourceitempart.sourceitem
            WHERE (
                (sourceitempartfrom IS NULL AND sourceitempartto IS NULL) OR
                sourceitem.sourceitemurlcomplete OR
                (sourceitempartisbypage AND sourcecitationpagefrom ~ '^\d+$' AND sourcecitationpageto ~ '^\d+$' AND sourceitempartfrom <= sourcecitationpagefrom::integer AND sourceitempartto >= sourcecitationpagefrom::integer)
                ) AND (
                (sourcecitationvolume = '' AND governmentsourceterm = '' AND sourceitemvolume = '' AND sourceitemyear IS NULL) OR
                (sourcecitationvolume <> '' AND sourcecitationvolume = sourceitemvolume) OR
                (sourcecitationvolume = '' AND governmentsourceterm <> '' AND governmentsourceterm = sourceitemvolume) OR
                (sourcecitationvolume ~ '^\d{4}$' AND sourcecitationvolume::smallint = sourceitemyear) OR
                (sourcecitationvolume = '' AND governmentsourceterm ~ '^\d{4}$' AND governmentsourceterm::smallint = sourceitemyear)
            )
            AND governmentsource.governmentsourceid = ?
        QUERY;

        return $this->db->query($query, [
            $id,
        ])->getResult();
    }

    // extra.ci_model_lawalternate_url(integer, boolean)

    // FUNCTION: extra.sourceurlid

    public function getByLawAlternateSection(int $id): array
    {
        $query = <<<QUERY
            SELECT sourceitem.sourceitemurl ||
                CASE
                    WHEN sourceitem.sourceitemurlcomplete THEN ''
                    ELSE sourceitempart.sourceitempartsequencecharacter || replace(format('%0' || sourceitempart.sourceitempartzeropad::text || 's', (sourceitempart.sourceitempartsequence +
                    CASE
                        WHEN sourceitempart.sourceitempartisbypage THEN lawalternatesection.lawalternatesectionpagefrom
                        ELSE lawalternate.lawalternatenumberchapter
                    END)::text), ' ', '0') || sourceitempart.sourceitempartsequencecharacterafter
                END AS url
        FROM geohistory.lawalternatesection
        JOIN geohistory.lawalternate
            ON lawalternatesection.lawalternate = lawalternate.lawalternateid
        JOIN geohistory.law
            ON lawalternate.law = law.lawid
        JOIN geohistory.sourceitem
            ON sourceitem.source = ANY (extra.sourceurlid(lawalternate.source))
        JOIN geohistory.sourceitempart
            ON sourceitem.sourceitemid = sourceitempart.sourceitem
        WHERE (
            (
                sourceitem.sourceitemvolume <> ''
                AND sourceitem.sourceitemyear IS NULL
                AND (sourceitem.sourceitemvolume = lawalternate.lawalternatevolume OR (lawalternate.lawalternatevolume = '' AND sourceitem.sourceitemvolume = substring(law.lawapproved FOR 4)))
            ) OR (
                sourceitem.sourceitemvolume <> ''
                AND sourceitem.sourceitemyear IS NOT NULL
                AND sourceitem.sourceitemvolume = lawalternate.lawalternatevolume
                AND sourceitem.sourceitemyear::character varying = substring(law.lawapproved FOR 4)
            ) OR (
                sourceitem.sourceitemvolume = ''
                AND sourceitem.sourceitemyear IS NOT NULL
                AND ((sourceitem.sourceitemyear::character varying = lawalternate.lawalternatevolume) OR (lawalternate.lawalternatevolume = '' AND sourceitem.sourceitemyear::character varying = substring(law.lawapproved FOR 4)))
            ) OR (
                sourceitem.sourceitemvolume = '' AND sourceitem.sourceitemyear IS NULL
            )
        ) AND (
            (sourceitempartfrom IS NULL AND sourceitempartto IS NULL) OR
            (sourceitempartisbypage AND sourceitempartfrom <= lawalternatesectionpagefrom AND sourceitempartto >= lawalternatesectionpagefrom) OR
            (NOT sourceitempartisbypage AND sourceitempartfrom <= lawnumberchapter AND sourceitempartto >= lawnumberchapter)
            ) AND lawalternatesection.lawalternatesectionid = ?
            AND (? OR NOT sourceitem.sourceitemlocal)
        QUERY;

        return $this->db->query($query, [
            $id,
            \App\Controllers\BaseController::isLive(),
        ])->getResult();
    }

    // extra.ci_model_law_url(integer, boolean)

    // FUNCTION: extra.sourceurlid

    public function getByLawSection(int $id): array
    {
        $query = <<<QUERY
            SELECT sourceitem.sourceitemurl ||
                CASE
                    WHEN sourceitem.sourceitemurlcomplete THEN ''
                    ELSE sourceitempart.sourceitempartsequencecharacter || replace(format('%0' || sourceitempart.sourceitempartzeropad::text || 's', (sourceitempart.sourceitempartsequence +
                    CASE
                        WHEN sourceitempart.sourceitempartisbypage THEN lawsection.lawsectionpagefrom
                        ELSE law.lawnumberchapter
                    END)::text), ' ', '0') || sourceitempart.sourceitempartsequencecharacterafter
                END AS url
        FROM geohistory.lawsection
        JOIN geohistory.law
            ON lawsection.law = law.lawid
        JOIN geohistory.sourceitem
            ON sourceitem.source = ANY (extra.sourceurlid(law.source))
        JOIN geohistory.sourceitempart
            ON sourceitem.sourceitemid = sourceitempart.sourceitem
        WHERE (
            (
                sourceitem.sourceitemvolume <> ''
                AND sourceitem.sourceitemyear IS NULL
                AND (sourceitem.sourceitemvolume = law.lawvolume OR (law.lawvolume = '' AND sourceitem.sourceitemvolume = substring(law.lawapproved FOR 4)))
            ) OR (
                sourceitem.sourceitemvolume <> ''
                AND sourceitem.sourceitemyear IS NOT NULL
                AND sourceitem.sourceitemvolume = law.lawvolume
                AND sourceitem.sourceitemyear::character varying = substring(law.lawapproved FOR 4)
            ) OR (
                sourceitem.sourceitemvolume = ''
                AND sourceitem.sourceitemyear IS NOT NULL
                AND ((sourceitem.sourceitemyear::character varying = law.lawvolume) OR (law.lawvolume = '' AND sourceitem.sourceitemyear::character varying = substring(law.lawapproved FOR 4)))
            ) OR (
                sourceitem.sourceitemvolume = '' AND sourceitem.sourceitemyear IS NULL
            )
        ) AND (
            (sourceitempartfrom IS NULL AND sourceitempartto IS NULL) OR
            (sourceitempartisbypage AND sourceitempartfrom <= lawsectionpagefrom AND sourceitempartto >= lawsectionpagefrom) OR
            (NOT sourceitempartisbypage AND sourceitempartfrom <= lawnumberchapter AND sourceitempartto >= lawnumberchapter)
            ) AND lawsection.lawsectionid = ?
            AND (? OR NOT sourceitem.sourceitemlocal)
        QUERY;

        return $this->db->query($query, [
            $id,
            \App\Controllers\BaseController::isLive(),
        ])->getResult();
    }

    // extra.ci_model_source_url(integer)

    // FUNCTION: extra.sourceurlid

    public function getBySourceCitation(int $id): array
    {
        $query = <<<QUERY
            SELECT sourceitem.sourceitemurl ||
                CASE
                    WHEN sourceitem.sourceitemurlcomplete THEN ''::text
                    ELSE
                    CASE
                        WHEN sourceitempart.sourceitempartisbypage AND sourcecitationpagefrom ~ '^\d+$' THEN sourceitempart.sourceitempartsequencecharacter::text || (sourceitempart.sourceitempartsequence + sourcecitation.sourcecitationpagefrom::integer)
                        ELSE replace(format('%04s', (sourceitempart.sourceitempartsequence)::text), ' ', '0') || sourceitempart.sourceitempartsequencecharacter::text
                    END || sourceitempart.sourceitempartsequencecharacterafter::text
                END AS url
            FROM geohistory.sourcecitation
            JOIN geohistory.sourceitem
                ON sourceitem.source = ANY (extra.sourceurlid(sourcecitation.source))
            JOIN geohistory.sourceitempart
                ON sourceitem.sourceitemid = sourceitempart.sourceitem
            WHERE (
                (sourceitempartfrom IS NULL AND sourceitempartto IS NULL) OR
                sourceitem.sourceitemurlcomplete OR
                (sourceitempartisbypage AND sourcecitationpagefrom ~ '^\d+$' AND sourcecitationpageto ~ '^\d+$' AND sourceitempartfrom <= sourcecitationpagefrom::integer AND sourceitempartto >= sourcecitationpagefrom::integer)
            ) AND (
                (sourcecitationvolume = '' AND sourceitemvolume = '' AND sourceitemyear IS NULL) OR
                sourcecitationvolume = sourceitemvolume OR
                (sourcecitationvolume ~ '^\d{4}$' AND sourcecitationvolume::smallint = sourceitemyear)
            )
            AND sourcecitation.sourcecitationid = ?
        QUERY;

        return $this->db->query($query, [
            $id,
        ])->getResult();
    }
}
