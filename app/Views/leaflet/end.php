<?php
$includeBase ??= true;
?>
// Add DownloadButton control to map
var downloadButtonControl = new L.Control.DownloadButton();
map.addControl(downloadButtonControl);
var downloadBox = L.control();
downloadBox.onAdd = function(map) {
    this._div = L.DomUtil.create('div', 'downloadbox');
    this.update();
    return this._div;
};
downloadBox.update = function() {
<?php if (\App\Controllers\BaseController::isInternetExplorer()) { ?>
    var textToAdd = '<?= lang('Application.outdatedBrowser') ?> <a href="https://browsehappy.com/?locale=<?= \Config\Services::request()->getLocale() ?>" target="_blank" rel="noopener noreferrer"><?= lang('Application.moreInformation') ?> »</a>';
<?php } else { ?>
    var textToAdd = '<span class="b"><?= lang('Application.selectALayerToDownload') ?>:</span>';
    Object.keys(overlayMaps).forEach(function (element) {
        if (element.indexOf("<?= lang('Application.parcels') ?>") == -1 && element !== "<?= lang('Application.approximateCurrentBoundaries') ?>") {
            textToAdd += '<br><a class="datadownload" id="' + overlayMaps[element].options.title + '" href="#">' + element + '</a>';
        }
    });
    textToAdd += '<br><a class="imagedownload" href="#"><?= lang('Application.image') ?></a>';
<?php } ?>
    this._div.innerHTML = textToAdd;
};
downloadBox.addTo(map);
map.addControl(new L.Control.Fullscreen());
<?php if (\App\Controllers\BaseController::isLive() && $includeBase) { ?>
map.on("click", function(e) {
    document.getElementById("coord").innerHTML = +e.latlng.lng.toFixed(7) + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + +e.latlng.lat.toFixed(7);
});
<?php } ?>
map.attributionControl.setPrefix(Object.getPrototypeOf(map.attributionControl).options.prefix + ' | <?= lang('Application.notASurveyProduct') ?>.');
</script>