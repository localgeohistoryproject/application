<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->add('robots.txt', 'Bot::robotsTxt');

if (mb_strpos(base_url(), $_ENV['app_baseLocalGeohistoryProjectUrl']) !== false) {
    if ($_ENV['app_jurisdiction'] !== '') {
        $stateProvinceRegex = '('. $_ENV['app_jurisdiction'] . ')';
    } else {
        $stateProvinceRegex = '(:segment)';
    }

    $controllerRegex = ['adjudication', 'area', 'event', 'government', 'governmentsource', 'law', 'metes', 'reporter', 'source'];
    $mainSearchRegex = '(event|government|adjudication|law)';

    foreach ($controllerRegex as $c) {
        $routes->add('{locale}/' . $stateProvinceRegex . '/' . $c . '/(:segment)', ucwords($c) . '::view/$1/$2');
        $routes->add('{locale}/' . $stateProvinceRegex . '/' . $c, ucwords($c) . '::noRecord/$1');
    }

    $routes->add('{locale}/' . $stateProvinceRegex . '/search/' . $mainSearchRegex, 'Search::view/$1/$2');
    $routes->add('{locale}/' . $stateProvinceRegex . '/search/(:segment)', 'Search::noRecord/$1');

    $routes->add('{locale}/' . $stateProvinceRegex . '/address', 'Area::address/$1');
    $routes->add('{locale}/' . $stateProvinceRegex . '/point/(:segment)/(:segment)', 'Area::point/$1/$2/$3');
    $routes->add('{locale}/' . $stateProvinceRegex . '/point', 'Area::point/$1');

    $routes->add('{locale}/governmentidentifier/(:segment)/(:segment)', 'Governmentidentifier::view/$1/$2');

    $routes->add('{locale}/' . $stateProvinceRegex . '/leaflet', 'Map::leaflet/$1');

    $routes->add('{locale}/' . $stateProvinceRegex . '/about', 'About::index/$1');
    $routes->add('{locale}/' . $stateProvinceRegex . '/map-base', 'Map::baseStyle');
    $routes->add('{locale}/' . $stateProvinceRegex . '/map-overlay', 'Map::overlayStyle/$1');
    $routes->add('{locale}/' . $stateProvinceRegex . '/map-tile/(:num)/(:num)/(:num)', 'Map::tile/$2/$3/$4/$1');
    $routes->add('{locale}/about', 'About::index');
    $routes->add('{locale}/bot', 'Bot::index');
    $routes->add('{locale}/disclaimer', 'Disclaimer');
    $routes->add('{locale}/key', 'Key::index');

    $routes->add('{locale}/' . $stateProvinceRegex . '/statistics/report/', 'Statistics::view/$1');
    $routes->add('{locale}/' . $stateProvinceRegex . '/statistics/', 'Statistics::index/$1');
    $routes->add('{locale}/statistics/report/', 'Statistics::view');
    $routes->add('{locale}/statistics/', 'Statistics::index');

    $routes->add('{locale}/' . $stateProvinceRegex . '/lookup/government/(:segment)', 'Search::governmentlookup/$1/$2/government');
    $routes->add('{locale}/' . $stateProvinceRegex . '/lookup/governmentparent/(:segment)', 'Search::governmentlookup/$1/$2/governmentparent');
    $routes->add('{locale}/' . $stateProvinceRegex . '/lookup/tribunal/(:num)', 'Search::tribunallookup/$1');

    $routes->add('{locale}/' . $stateProvinceRegex, 'Search::index/$1');
}

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

if (mb_strpos(base_url(), $_ENV['app_baseLocalGeohistoryProjectUrl']) !== false) {
    $routes->add('{locale}', 'Welcome');
    $routes->add('/', 'Welcome::language');
    $routes->set404Override(\App\Controllers\Fourofour::class);
    $routes->add('(:any)', 'Fourofour');
}
