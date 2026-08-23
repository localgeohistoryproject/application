#!/bin/bash
set -e
# Create new database
psql --command="CREATE ROLE readonly NOLOGIN;"
psql --command="CREATE ROLE $POSTGRES_OTHER_USER LOGIN PASSWORD '$POSTGRES_OTHER_PASSWORD';"
psql --command="GRANT readonly TO $POSTGRES_OTHER_USER;"
psql --command="GRANT USAGE ON SCHEMA public TO readonly;" $POSTGRES_DB
psql --command="ALTER DATABASE $POSTGRES_DB SET timezone TO '$TZ';" $POSTGRES_DB
psql --command="CREATE EXTENSION postgis;" $POSTGRES_DB
psql --command="CREATE EXTENSION unaccent;" $POSTGRES_DB
# Custom state plane SRID for Pennsylvania
psql --command="DELETE FROM spatial_ref_sys WHERE srid = 100007;  INSERT INTO spatial_ref_sys values ('100007', 'Other', '100007', 'PROJCS[\"NAD_1983_Lambert_Conformal_Conic\",GEOGCS[\"GCS_North_American_1983\",DATUM[\"D_North_American_1983\",SPHEROID[\"GRS_1980\",6378137.0,298.257222101]],PRIMEM[\"Greenwich\",0.0],UNIT[\"Degree\",0.0174532925199433]],PROJECTION[\"Lambert_Conformal_Conic\"],PARAMETER[\"False_Easting\",0.0],PARAMETER[\"False_Northing\",0.0],PARAMETER[\"Central_Meridian\",-78.0],PARAMETER[\"Standard_Parallel_1\",40.25],PARAMETER[\"Standard_Parallel_2\",41.5],PARAMETER[\"Latitude_Of_Origin\",39.0],UNIT[\"Foot_US\",0.3048006096012192]]', '+proj=lcc +lat_0=39 +lon_0=-78 +lat_1=40.25 +lat_2=41.5 +x_0=0 +y_0=0 +datum=NAD83 +units=us-ft +no_defs');" $POSTGRES_DB
# Start database restoration
mkdir /tmp/inpostgis/schema/
echo "DATABASE RESTORATION:"
refreshString=""
tsvSchemas=( $POSTGRES_SCHEMA )
for tsvSchema in "${tsvSchemas[@]}"
do
    if [ -f "/inpostgis/${tsvSchema,,}/_schema.sql" ]; then
        echo "Restoring ${tsvSchema,,} schema:"
        psql --file="/inpostgis/${tsvSchema,,}/_schema.sql" $POSTGRES_DB
        ## Block foreign key checks
        tableString="BEGIN;
        SET CONSTRAINTS ALL DEFERRED;
        "
        if [ "$tsvSchema" == "geohistory" ]; then
            tableString+="ALTER TABLE geohistory.governmentshape DISABLE TRIGGER governmentshape_insert_trigger;
            "
            # Concatenate governmentshape_*.tsv files
            if [ ! -f "/inpostgis/${tsvSchema,,}/governmentshape.tsv" ]; then
                isFirstFile=Y
                for fileName in /inpostgis/${tsvSchema,,}/governmentshape_*.tsv
                do
                    if [ "$isFirstFile" == "Y" ]; then
                        head -n +1 "${fileName}" > "/inpostgis/${tsvSchema,,}/governmentshape.tsv"
                        isFirstFile=N
                    fi
                    tail -n +2 "${fileName}" >> "/inpostgis/${tsvSchema,,}/governmentshape.tsv"
                    rm "${fileName}"
                done
            fi
        fi
        ## Add table imports
        hasTableImport=Y
        for tableName in /inpostgis/${tsvSchema,,}/*.tsv
        do
            tableName=$(basename $tableName .tsv)
            if [ "$tableName" != "*" ]; then
                tsvHeader=$(head -n +1 "/inpostgis/${tsvSchema,,}/${tableName,,}.tsv" | sed "s/\t/,/g")
                tail -n +2 "/inpostgis/${tsvSchema,,}/${tableName,,}.tsv" > "/tmp/inpostgis/schema/${tableName,,}.tsv"
                tableString+="\COPY ${tsvSchema,,}.${tableName,,} ($tsvHeader) FROM '/tmp/inpostgis/schema/${tableName,,}.tsv';
                "
            else
                hasTableImport=N
            fi
        done
        if [ "$hasTableImport" == "Y" ]; then
            tableString+="COMMIT;
            CHECKPOINT;
            "
        else
            tableString=""
        fi
        ## Add refresh sequence/generated columns and analyze
        tableString+="SELECT geohistory.refresh_sequence('${tsvSchema,,}');
        SELECT ${tsvSchema,,}.refresh_generated();
        SELECT geohistory.refresh_analyze('${tsvSchema,,}');
        CHECKPOINT;
        "
        # Add refresh views (run later)
        refreshString+="SELECT ${tsvSchema,,}.refresh_view();
        "
        if [ "$tsvSchema" == "geohistory" ]; then
            tableString+="ALTER TABLE geohistory.governmentshape ENABLE TRIGGER governmentshape_insert_trigger;
            "
        fi
        ## Run table imports, refresh sequence/generated columns, and analyze
        echo "${tableString}" > /tmp/inpostgis/schema/import.sql
        psql --file="/tmp/inpostgis/schema/import.sql" $POSTGRES_DB
        rm /tmp/inpostgis/schema/*
    else
        echo "WARNING: ${tsvSchema,,} SQL file missing"
    fi
done
# Run refresh views
refreshString+="CHECKPOINT;
"
echo "${refreshString}" > /tmp/inpostgis/schema/import.sql
psql --file="/tmp/inpostgis/schema/import.sql" $POSTGRES_DB
rm /tmp/inpostgis/schema/*
