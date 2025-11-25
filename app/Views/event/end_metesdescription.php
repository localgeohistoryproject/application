var map = L.map("map", {
layers: [baseMap, governmentOverlayMap, metesdescriptionlayer]
});

map.fitBounds(metesdescriptionlayer.getBounds());

var overlayMaps = {
"<?= lang('Application.approximateCurrentBoundaries') ?>": governmentOverlayMap,
"<?= lang('Application.descriptions') ?>": metesdescriptionlayer
};

Object.keys(stateOverlayMaps).forEach(function (element) {
overlayMaps[element] = stateOverlayMaps[element];
});

L.control.layers(baseMaps, overlayMaps).addTo(map);