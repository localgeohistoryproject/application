<?php
$zoom ??= true;
$state ??= '';
?>
// Taken from http://fuzzytolerance.info/blog/2016/07/01/Printing-Mapbox-GL-JS-maps-in-Firefox/
function fixFirefoxPrint() {
return navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
}

var baseMapATT = 'Base: <a href="https://daylightmap.org/attribution.html" target="_blank">Daylight</a> &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>, <a href="https://github.com/microsoft/GlobalMLBuildingFootprints/" target="_blank">Microsoft</a>, <a href="https://communitymaps.arcgis.com/home/" target="_blank">Esri</a>, <a href="https://www.openmaptiles.org/" target="_blank">OpenMapTiles</a>.';
var baseMapUrl = '/<?= \Config\Services::request()->getLocale() ?>/<?= (empty($state) ? '' : $state . "/") ?>map-base/<?= ($zoom ? "zoom/" : '') ?>';

<?php if ($state !== '' || !$zoom) { ?>
baseMapATT = baseMapATT + ' Hillshading: <a href="https://aws.amazon.com/public-datasets/terrain/">AWS</a> &copy; <a href="https://github.com/tilezen/joerd/blob/master/docs/attribution.md">Mapzen</a>.';
<?php } ?>

<?php if ($state !== '') { ?>
var governmentOverlayMap = L.maplibreGL({
style: '/<?= \Config\Services::request()->getLocale() ?>/<?= $state ?>/map-overlay/',
preserveDrawingBuffer: fixFirefoxPrint(), // Taken from http://fuzzytolerance.info/blog/2016/07/01/Printing-Mapbox-GL-JS-maps-in-Firefox/
pane: 'overlayPane'
});
<?php } ?>

var baseMap = L.maplibreGL({
attribution: baseMapATT,
style: baseMapUrl,
preserveDrawingBuffer: fixFirefoxPrint(), // Taken from http://fuzzytolerance.info/blog/2016/07/01/Printing-Mapbox-GL-JS-maps-in-Firefox/
pane: 'tilePane'
});

