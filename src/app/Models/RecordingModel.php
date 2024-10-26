<?php

namespace App\Models;

use App\Models\BaseModel;

class RecordingModel extends BaseModel
{
    // extra.ci_model_event_recording(integer, character varying, character varying)

    // FUNCTION: extra.governmentshort
    // FUNCTION: extra.governmentslug
    // FUNCTION: extra.rangefix
    // FUNCTION: extra.shortdate

    public function getByEvent(int $id): array
    {
        $query = <<<QUERY
            SELECT extra.governmentslug(recordingoffice.government) AS government,
                extra.governmentshort(recordingoffice.government) AS governmentshort,
                trim(recordingtype.recordingtypelong || CASE
                    WHEN recordingtype.recordingtypetype = '' THEN ''
                    ELSE ' ' || recordingtype.recordingtypetype
                END) AS recordingtype,
                trim(CASE
                    WHEN recordingtype.recordingtypevolumetype IS NULL OR recordingtype.recordingtypevolumetype = '' OR recording.recordingvolume = '' THEN ''
                    WHEN recordingtype.recordingtypevolumetype = 'Volume' THEN 'v.'
                    ELSE recordingtype.recordingtypevolumetype
                END ||
                CASE
                    WHEN recording.recordingvolumeofficerinitials = '' AND recording.recordingvolume = '' THEN ''
                    ELSE ' ' || trim(recording.recordingvolumeofficerinitials || ' ' || recording.recordingvolume)
                END ||
                CASE
                    WHEN recording.recordingvolume <> '' AND recording.recordingpage IS NOT NULL AND recording.recordingpage <> '0' THEN ', '
                    ELSE ''
                END ||
                CASE
                    WHEN recordingtype.recordingtypepagetype IS NULL OR recordingtype.recordingtypepagetype = '' OR recording.recordingpage IS NULL OR (recording.recordingpage = 0 AND recording.recordingpagetext = '') THEN ''
                    WHEN recordingtype.recordingtypepagetype = 'Page' THEN 'p.'
                    WHEN recordingtype.recordingtypepagetype = 'Number' THEN 'no.'
                    ELSE recordingtype.recordingtypepagetype
                END ||
                CASE
                    WHEN recording.recordingpage = 0 THEN ''::text
                    ELSE ' ' || recording.recordingpage::text
                END || recording.recordingpagetext::text) AS recordinglocation,
                trim(recordingnumbertype.recordingtypelong || CASE
                    WHEN recordingnumbertype.recordingtypetype = '' THEN ''
                    ELSE ' ' || recordingnumbertype.recordingtypetype
                END) AS recordingnumbertype,
                trim(CASE
                    WHEN recording.recordingnumber IS NULL THEN NULL
                    ELSE ' no. ' || recording.recordingnumber || recording.recordingnumbertext
                END) AS recordingnumberlocation,
                recordingtype.recordingtypeid IS NOT NULL AND recordingnumbertype.recordingtypeid IS NOT NULL AS hasbothtype,
                extra.shortdate(recording.recordingdate) AS recordingdate,
                recording.recordingdate AS recordingdatesort,
                eventrelationship.eventrelationshipshort AS recordingeventrelationship,
                recording.recordingrepositoryshort,
                recording.recordingrepositoryitemnumber,
                extra.rangefix(recording.recordingrepositoryitemfrom::text, recording.recordingrepositoryitemto::text) AS recordingrepositoryitemrange,
                recording.recordingrepositoryitemlocation,
                recording.recordingrepositoryseries,
                recording.recordingrepositorycontainer
            FROM geohistory.recording
            JOIN geohistory.recordingevent
                ON recording.recordingid = recordingevent.recording
                AND recordingevent.event = ?
            JOIN geohistory.eventrelationship
                ON recordingevent.eventrelationship = eventrelationship.eventrelationshipid
            JOIN geohistory.recordingoffice
                ON recording.recordingoffice = recordingoffice.recordingofficeid
            LEFT JOIN geohistory.recordingtype
                ON recording.recordingtype = recordingtype.recordingtypeid
            LEFT JOIN geohistory.recordingtype recordingnumbertype
                ON recording.recordingnumbertype = recordingnumbertype.recordingtypeid
            ORDER BY 1, 6
        QUERY;

        $query = $this->db->query($query, [
            $id,
        ]);

        return $this->getObject($query);
    }
}
