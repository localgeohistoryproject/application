# Changelog

## [3.1.1] - 2025-04-15

### Fixed

- Missing 404 status codes when error or no record.

## [3.1.0] - 2025-04-12

### Added

- In database table geohistory.metesdescription, comment to column metesdescriptionquality to indicate removal from Open Data.

### Changed

- In database table geohistory.adjudication, generated columns adjudicationslug and adjudicationtitle to use long date format.
- In database table geohistory.lawsection, generated column lawsectionslug to use law.lawcitation instead of law.lawslug.
- PostGIS import to allow governmentshape table data to be split into multiple files.

### Fixed

- Deprecation issues in PHP 8.4 with fputcsv.
- In database tables geohistory.event, geohistory.government, geohistory.governmentsource, geohistory.lawalternatesection, geohistory.lawsection, geohistory.metesdescription, and gis.governmentshape, irregularities in slug formatting.
- Various code quality, linting, and whitespace issues using PhpStan, PHP CS Fixer, and Rector.

### Removed

- Database function geohistory.lawalternateslug.
- Database function geohistory.lawslug.
- In database table geohistory.law, generated column lawslug.
- In database table geohistory.lawalternate, generated column lawalternateslug.

## [3.0.3] - 2025-04-05

### Fixed

- In database table geohistory.lawalternatesection, slug to add "-alternate" to end.
- Invalid column reference in LawAlternateSectionModel, getSlugId function.

## [3.0.2] - 2025-03-29

### Added

- Database function geohistory.array_to_slug to standardize formatting of slugs.

### Changed

- In database table geohistory.lastrefresh, expand maximum length of lastrefreshversion field to 10 characters.

### Fixed

- In database tables geohistory.adjudication, geohistory.adjudicationsourcecitation, and geohistory.sourcecitation, irregularities in slug formatting.

### Removed

- Readonly user permissions from database functions geohistory.array_combine and geohistory.plssmeridianlong.

## [3.0.1] - 2025-03-23

### Fixed

- Redirects to handle removed jurisdiction (state) segments from URLs.

## [3.0.0] - 2025-03-23

### Added

- Adjudication, Government Action, and Recorded Document entry to Key.
- Database function refresh_view to any schema without it.
- Database schema gis_extra, to store some database functions, materialized views, and views from the removed extra schema.
- DOCKER_COMPOSER variable to .env.
- Generated columns in database, mostly for slug generation and to replace features in database functions, materialized views, and views from the removed extra schema.
- Jurisdiction (state) as a search option where jurisdiction-specific search pages removed.
- Mastodon profile link to .env and as link tag on welcome page.
- Redirects to handle removed jurisdiction (state) segments from URLs.
- Shared content for CSV handling and textual representations relating to tribunals from private repositories.
- Status page.
- Table of Contents label to the about and key pages.

### Changed

- app_jurisdiction in .env to use commas instead of pipe delimiters.
- CodeIgniter from 4.5.5 to 4.6.0.
- Composer docker image from 2.6.5 to 2.8.6.
- Composer.json to add project name, license, and author.
- Controller and model references when they can be overridden by private repositories.
- CSS page width.
- CSS print media to ensure printing in black instead of blue.
- Database import scripts to include column names.
- Database materialized view geohistory.lastrefresh to a table, and added lastrefreshversion column.
- DataTables from 2.1.8 to 2.2.2.
- docker-compose.yaml to remove version and accommodate new folder structure.
- Existing Material Icon PNGs and SVGs to use Material Symbols Outlined font.
- Folders to allow for modularization of private features, by moving all content in app and env to the root folder.
- Footer to bifurcate Code and Data versions and licenses.
- Government page to fold Other Successful Event Links into Other Event Links, and remove events under Government Action from Other Event Links.
- Government record text representations to include the jurisdiction (state) in most forms.
- Html to Image 1.11.11 to 1.11.13.
- License for code from CC-BY-SA-4.0 to Apache-2.0.
- License references to use SPDX Identifiers.
- Model queries to replace features in database functions, materialized views, and views from the removed extra schema.
- Model result handling harmonized.
- PHP coding style to PER-CS.
- PHP CS Fixer from 3.64.0 to 3.73.1.
- PHP dockerfile to also install git.
- PHP docker image from 8.3.11-apache to 8.4.3-apache-bookworm.
- PHPStan CodeIgniter from 1.4.3 to 1.5.3.
- PHPStan from 1.12.6 to 2.1.8.
- phpstan.neon.dist to remove most remaining excludePaths and add additional ignoreErrors and strictRules.
- PHPStan Strict Rules from 1.6.1 to 2.0.4.
- PMTiles from 3.2.0 to 4.3.0.
- Postgis/postgis docker image from 16-3.4 to 17-3.5.
- Rector from 1.2.6 to 2.0.10.
- Rector PHP sets from 8.2 to 8.4.
- Routes to specify get/post methods.
- Search page, Law searches to require Government search term.
- Welcome page to use feature icons.

### Fixed

- Government page, Related table, to accurately mark parent governments that are more than one level above as having a current status where appropriate, instead of always showing former.
- Various code quality and linting issues using PhpStan, PHP CS Fixer, and Rector.

### Removed

- app/Config files that match CodeIgniter defaults from repository.
- "Detail" word from most page titles.
- Extra schema in database, along with the majority of remaining database functions, materialized views, and views.
- Jurisdiction (state) as a segment in most URLs, including removing state icons and separate search pages.
- "Modifications for other operating systems" section from README.
- Statistics page content not used in production.
- Symbolic links to .env and private features from repository.
- Text content on the key and welcome pages to the Open Data repository.

## [2.1.0] - 2024-10-09

### Added

- Database column hassource to views extra.governmentsourceextra and extra.governmentsourceextracache.
- Database column plsstownshipname to geohistory.plsstownship.
- Database views extra.recordingextra and extra.recordingextracache.
- Functionality to accommodate changes to related private repositories.
- Google Analytics support.

### Changed

- CodeIgniter from 4.5.2 to 4.5.5.
- Controller properties and view calls to remove $this->data array; pass model results directly in more situations.
- CSS and HTML tag id references.
- Database column documentationshort in geohistory.documentation to lengthen field.
- Database extract pg_dump and OS versions.
- Database function extra.governmentindigobook(integer) to cover additional scenarios.
- DataTables from 2.0.8 to 2.1.8.
- Dockerfile to accommodate changes to related private repositories.
- Dockerfiles to remove globs and reconfigure folder structure for environment-specific files.
- MapLibre GL JS from 4.4.1 to 4.7.1.
- MapLibre GL Leaflet from 0.0.21 to 0.0.22.
- PHP CS Fixer from 3.59.3 to 3.64.0.
- PHP docker image from 8.2.11-apache to php:8.3.11-apache.
- PHPStan Extension Installer from 1.4.1 to 1.4.3.
- PHPStan from 1.11.5 to 1.12.6.
- PHPStan Strict Rules from 1.6.0 to 1.6.1.
- PMTiles from 3.0.6 to 3.2.0.
- README to update installation instructions to accommodate Ubuntu 24.04 LTS, and remove other OS warning.
- Rector from 1.1.0 to 1.2.6.

### Fixed

- App\Filters\LanguageRedirect to accommodate classes without a getLocale method.
- Functionality to accommodate changes to related private repositories.
- GovernmentModel->getAbbreviationid(string) to force uppercase comparison and output proper response column.

### Removed

- Database extract exclusion warnings from comments for columns recordingrepositoryitemfrom and recordingrepositoryitemto in geohistory.recording.
- Disclaimer text and venue environmental variables from repository (to be stored in Open Data repository).
- PHPStan review of certain development directories.

## [2.0.2] - 2024-06-20

### Changed

- CodeIgniter from 4.5.1 to 4.5.2.
- DataTables from 2.0.6 to 2.0.8.
- MapLibre GL JS from 4.2.0 to 4.4.1.
- MapLibre GL Leaflet from 0.0.20 to 0.0.21.
- PHP CS Fixer from 3.54.0 to 3.59.3.
- PHPStan from 1.10.67 to 1.11.5.
- PHPStan Extension Installer from 1.3.1 to 1.4.1.
- PHPStan Strict Rules from 1.5.5 to 1.6.0.
- PMTiles from 3.0.5 to 3.0.6.
- Rector from 1.0.4 to 1.1.0.

### Fixed

- View for Government Action table to re-add Government column in some scenarios.

## [2.0.1] - 2024-05-05

### Changed

- MapLibre GL JS from 4.1.3 to 4.2.0.

### Fixed

- Database permissions issues for readonly group, including allowing use of schemas, direct assignment of permissions to functions and tables in some schemas, and adding SECURITY DEFINER to calendar extension (extension version 1.6 -> 1.7).

## [2.0.0] - 2024-05-04

### Added

- Additional database comments.
- Gd extension to PHP.
- PHP CS Fixer.
- PHPStan.
- Rector.
- Table geohistory.locale.

### Changed

- CodeIgniter from 4.4.3 to 4.5.1.
- Database extract pg_dump and OS versions.
- Database to harmonize foreign key constraint properties.
- DataTables from 1.13.6 to 2.0.6.
- Default database character set in ENV.
- Dependencies to consolidate path references in ENV, remove dependency files except licenses from repository, but auto-populate most current versions on docker-compose up.
- Dockerfile to accommodate changes to related private repositories.
- Elevation tiles to be retrieved in development environment when not online.
- MapLibre GL JS from 3.3.1 to 4.1.3.
- Open Data links in ENV to split into 2 fields.
- PMTiles from 2.11.0 to 3.0.5.
- Queries in controllers moved to models, and removed as database functions.
- Standardize some queries to use standard functions instead of custom functions.
- Stylistic and minor changes to changelog and README.
- Table geohistory.event, to add generated columns and remove additional database functions related to the table.
- Table geohistory.event, to change data type for eventgranted column and replace data with foreign key reference.
- Table geohistory.governmentidentifiertype, to replace column governmentidentifiertypeprefixlength with governmentidentifiertypeprefixlengthfrom and governmentidentifiertypeprefixlengthto, and to replace column governmentidentifiertypelength with governmentidentifiertypelengthfrom and governmentidentifiertypelengthto.
- Table geohistory.lawgroup, to remove columns eventtype and lawgroupgovernmenttype and replace with new tables geohistory.lawgroupeventtype and geohistory.lawgroupgovernmenttype.
- Table geohistory.plss, to change data type for plssfirstdivisionpart column.
- Table geohistory.source, to remove sourcegovernment column and replace with new table geohistory.sourcegovernment.
- Table geohistory.sourcecitation, to remove sourcecitationdetail column and replace with new tables geohistory.sourcecitationnote and geohistory.sourcecitationnotetype.
- Tables geohistory.adjudicationevent, geohistory.governmentsourceevent, geohistory.lawgroupsection, geohistory.lawsectionevent, and geohistory.recordingevent, to replace columns adjudicationeventrelationship, governmentsourceeventrelationship, lawsectionrelationship, lawsectioneventrelationship, and recordingeventrelationship, respectively, with eventrelationship column with foreign key reference.
- Views moved into subfolders.

### Fixed

- Changelog link.
- DataTables color striping issues.
- DataTables CORS issues.
- Linting using PHP CS Fixer.
- Refactoring using PHPStan and Rector.

### Removed

- Database references in controllers.
- References to additional database functions in models.
- In table geohistory.researchlog, internetarchivehandle column.
- In table geohistory.sourcecitation, sourcecitationnotes column.
- Twitterbot from robots.txt whitelist.

## [1.2.2] - 2023-11-12

### Added

- BrowserStack note to changelog.
- Composer.

### Changed

- CodeIgniter from 4.4.1 to 4.4.3.
- CodeIgniter Paths settings to accommodate Composer.
- PHP Dockerfile to accommodate Composer install at entrypoint.
- PHP Dockerfile to change server paths for shell scripts.
- Repository paths to accommodate moving repository to organization.

### Removed

- CodeIgniter system folder from repository.

## [1.2.1] - 2023-10-01

### Fixed

- Base map style to always specify max zoom.

## [1.2.0] - 2023-10-01

### Added

- Additional points of interest to base map style.
- Attribution disclaimer in ENV for commercial tile services.
- License files for additional dependencies.
- Locale-specific map label support.
- PMTiles 2.11.0.
- Self-hosted map tile and glyph support.
- Zip extension to PHP dockerfile.

### Changed

- Attribution in maps (self-hosted tile transition).
- Base map style to simplify identifiers, generalize some attributes for re-use in other places, and to specify original style from which customized.
- CodeIgniter from 4.3.6 to 4.4.1.
- CSS to make background color white and make other stylistic and minor changes.
- Database column default for sourceitemurlcompletepart in geohistory.sourceitem to use true instead of false.
- Database extract pg_dump and OS versions.
- Database views extra.governmentchangecount and extra.governmentchangecountpart to use calendar.historicdate.precision value to determine precision.
- DataTables from 1.11.3 to 1.13.6.
- Dom To Image 2.6.0 to Html to Image 1.11.11.
- Fonts to standardize Lora as default.
- Gitignore to exclude anything in /env except Sample.env.
- jQuery from 3.6.0 to 3.7.1.
- Jurisdictions covered moved to ENV.
- Leaflet from 1.9.3 to 1.9.4.
- Leaflet Fullscreen from 1.0.1 to 1.02.
- MapLibre GL JS from 3.0.0 to 3.3.1.
- MapLibre GL Leaflet from 0.0.19 to 0.0.20.
- Pg_tle clone command in dockerfile to specify v1.1.1 tag instead of default.
- PHP docker image from 8.2.4-apache to 8.2.11-apache.
- Postgis/postgis docker image from 15-3.3 to 16.3.4.
- Stylistic and minor changes to changelog and README.
- Tile URLs moved to ENV.

### Fixed

- Line break character inconsistencies (use standard Unix LF endings).
- Permissions on files and folders.

### Removed

- Empty test view.
- jQuery UI (not used in production).
- Language list in database constraint government_check in geohistory.government.
- Leaflet GeometryUtil (not used in production).
- MapTiler required logo and limitations (self-hosted tile transition).
- Maritime administrative boundaries from base map style.
- State and province administrative boundaries from base map style at or below zoom level 2.
- Trailing slashes on void tags that were required in XHTML but not in HTML5.

## [1.1.2] - 2023-09-03

### Added

- Map labels for water on MapTiler layers.

### Changed

- Layer labels for KlokanTech layers to reference MapTiler.
- Map style to declutter smaller scale maps on MapTiler layers.
- Reorganized references to libraries and styles used to create base maps.

### Fixed

- CORS issue with CSS stylesheet preventing full screen icon from appearing on map.

### Removed

- Stamen and other obsolete map layer content.

## [1.1.1] - 2023-08-26

### Fixed

- Missing Government name on Government Detail timelapse maps for some pages that group related, sequential governments.

## [1.1.0] - 2023-07-01

### Added

- Database column comment indicating partial or full omission from open data in law.lawdescriptiondone, lawgroup.eventeffectivetype, lawgroup.lawgroupcourtname, lawgroup.lawgroupgroup, lawgroup.lawgroupplanningagency, lawgroup.lawgroupprocedure, lawgroup.lawgrouprecording, lawgroup.lawgroupsecretaryofstate, lawgroup.lawgroupsectionlead, recording.recordingrepositoryitemfrom, recording.recordingrepositoryitemto, and researchlog.event.
- Database columns geohistory.lawsectionevent.lawgroup and geohistory.researchlog.event.
- Database function changes to accommodate future mutilingual support for Government Identifier Detail.
- Database tables geohistory.lawgroup and geohistory.lawgroupsection.
- Database triggers to ensure database integrity when changes made in lawgroupsection, lawsectionevent.
- Database trigger when source inserted.
- Group to Law table under Event Detail and Event Links under Law Detail.
- Support for alternate government name information in Event Detail, and links to pages from Government Detail.
- Various accommodations for use in development version.

### Changed

- CodeIgniter from 4.3.1 to 4.3.6.
- Database constraint on geohistory.metesdescription to ensure metesdescriptionacres cannot be negative.
- Database extract pg_dump and OS versions.
- Database functions to remove placeholder affected government part references when other materialized views refreshed.
- Database table geohistory.government to remove governmentclass and governmentcurrenthomerule fields and substitute with governmentcurrentform field.
- Delimiter in How Effective Date Determined in Event Detail.
- Esri Leaflet from 2.5.3 (manually modified) to 3.0.10 (forked with modifications).
- MapLibre GL JS from 1.15.3 to 3.0.0.
- MapLibre GL Leaflet from 0.0.18 to 0.0.19.
- Parcel map style in New Jersey.
- Statistics for mapped governments to omit Independent School Districts.
- Stylistic and minor changes to changelog and README.

### Fixed

- HTML validation issues in several views.
- Leaflet attribution for maps.
- Linting errors in changelog and README.
- Obsolete state GIS links.
- Parameter order for map tile display to put optional parameter last.

### Removed

- Survey Township page.
- Various database functions only used in development version.

## [1.0.2] - 2023-04-10

### Added

- Bandwidth and Data Extraction section to Disclaimers.
- Changelog.
- Zenodo metadata file.

### Changed

- Host machine port set by .env instead of hardcoded in docker-compose.yaml.

### Removed

- Detail in Research Log under Government Detail.

## [1.0.1] - 2023-04-07

### Added

- Additional TSV import at initial data load to support production redirects for Event Detail.
- Database column comment for Source Detail to reflect masking of status field in Open Data.
- Folder named outpostgis to facilitate PostGIS exports.
- Windows-related and other editorial revisions to README and Dockerfiles.
- Zenodo DOI badge to README.

### Changed

- Database functions for Adjudication Detail to recognize ? in Location instead of prior abbreviation (ver).
- Reduce Child entries in Related for state and country Government Detail.
- Support application individualization through the .env file instead of hard-coded contacts and links.

### Fixed

- Database function permissions (security definer and readonly role grant) for Adjudication Detail.
- Database slug definition for Law Detail to substitute hyphens for forward slashes.
- Define database auto-incrementing sequences automatically after initial data load.
- Use text datatype instead of domain for regnal years in calendar extension (extension version 1.5 -> 1.6).

## [1.0.0] - 2023-04-02

### Added

- Public release of the Local Geohistory Project: Application repository.

[3.1.1]: https://github.com/localgeohistoryproject/application/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/localgeohistoryproject/application/compare/v3.0.3...v3.1.0
[3.0.3]: https://github.com/localgeohistoryproject/application/compare/v3.0.2...v3.0.3
[3.0.2]: https://github.com/localgeohistoryproject/application/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/localgeohistoryproject/application/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/localgeohistoryproject/application/compare/v2.1.0...v3.0.0
[2.1.0]: https://github.com/localgeohistoryproject/application/compare/v2.0.2...v2.1.0
[2.0.2]: https://github.com/localgeohistoryproject/application/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/localgeohistoryproject/application/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/localgeohistoryproject/application/compare/v1.2.2...v2.0.0
[1.2.2]: https://github.com/localgeohistoryproject/application/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/localgeohistoryproject/application/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/localgeohistoryproject/application/compare/v1.1.2...v1.2.0
[1.1.2]: https://github.com/localgeohistoryproject/application/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/localgeohistoryproject/application/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/localgeohistoryproject/application/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/localgeohistoryproject/application/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/localgeohistoryproject/application/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/localgeohistoryproject/application/releases/tag/v1.0.0
