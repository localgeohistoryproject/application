FROM postgis/postgis:18-3.6
# Copy scripts and imports to launch database
COPY /docker/postgis/import_common_postgis.sh /docker-entrypoint-initdb.d/
RUN mkdir /tmp/inpostgis/
RUN chmod -R 777 /tmp/inpostgis/
RUN rm -rf /docker-entrypoint-initdb.d/10_postgis.sh
