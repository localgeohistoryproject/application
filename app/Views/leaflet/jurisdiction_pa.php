<?php /* Verified May 11, 2023 */ ?>

var stateImageryPaUrl = 'https://imagery.pasda.psu.edu/arcgis/services/pasda/PEMAImagery2018_2020/MapServer/WMSServer';
var stateImageryPaAtt = '<?= lang('Application.baseMap') ?>: <a href="https://www.pasda.psu.edu/uci/DataSummary.aspx?dataset=5158">PASDA</a>.';

var stateImageryPa = L.tileLayer.wms(stateImageryPaUrl, {
    layers: "1",
    attribution: stateImageryPaAtt,
    maxZoom: 20
});

stateBaseMaps["<?= lang('Application.imagery') ?> (PA)"] = stateImageryPa;

var stateParcelPaUrl = 'https://apps.pasda.psu.edu/arcgis/services/PA_Parcels_Vector/MapServer/WMSServer';
var stateParcelPaAtt = '<?= lang('Application.parcels') ?>: <a href="https://www.pasda.psu.edu/uci/DataSummary.aspx?dataset=1696">PASDA</a>.';

var stateParcelPa = L.tileLayer.wms(stateParcelPaUrl, {
    layers: "1",
    transparent: true,
    format: "image/png",
    attribution: stateParcelPaAtt,
    minZoom: 12
});

stateOverlayMaps["<?= lang('Application.parcels') ?> (PA)"] = stateParcelPa;