<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$defaultLocale = 'en';
$routes->useSupportedLocalesOnly(true);

if (mb_strpos(base_url(), $_ENV['app_baseRouteProjectUrl']) !== false) {
    $routes->get('robots.txt', 'Bot::robotsTxt');
    $routes->get($defaultLocale . '/sitemap.txt', 'Sitemap::viewOther');
    $routes->get($defaultLocale . '/sitemap.xml', 'Sitemap::index');
    $routes->get($defaultLocale . '/(adjudication|area|event|government|governmentidentifier|governmentsource|law|metes|reporter|source)/sitemap.txt', 'Sitemap::view/$1');

    $controllerRegex = ['adjudication', 'area', 'event', 'government', 'governmentsource', 'law', 'metes', 'reporter', 'source'];
    $controllerRegexOverride = ['event', 'government', 'law', 'metes'];
    $mainSearchRegex = '(event|government|adjudication|law)';
    $jurisdictionRedirectRegex = str_replace(',', '|', $_ENV['app_jurisdiction'] ?? '');

    foreach ($controllerRegex as $c) {
        if (!(in_array($c, $controllerRegexOverride) && class_exists('Localgeohistoryproject\\Development\\Controllers\\' . ucwords($c)))) {
            $routes->get($defaultLocale . '/' . $c . '/(:segment)', ucwords($c) . '::view/$1', ['priority' => 100]);
            if ($jurisdictionRedirectRegex !== '') {
                $routes->get($defaultLocale . '/(' . $jurisdictionRedirectRegex . ')/' . $c . '/(:segment)', ucwords($c) . '::redirect/$2', ['priority' => 100]);
            }
            $routes->get($defaultLocale . '/' . $c, ucwords($c) . '::noRecord', ['priority' => 100]);
        }
    }

    if ($jurisdictionRedirectRegex !== '') {
        $routes->get($defaultLocale . '/(' . $jurisdictionRedirectRegex . ')/about', 'About::redirect/$1');
        $routes->get($defaultLocale . '/(' . $jurisdictionRedirectRegex . ')/statistics', 'Statistics::redirect');
        $routes->get($defaultLocale . '/(' . $jurisdictionRedirectRegex . ')', 'Search::redirect');
    }

    $routes->get($defaultLocale . '/lookup/government/(:segment)', 'Search::governmentLookup/$1/');
    $routes->get($defaultLocale . '/lookup/government-jurisdiction/(:segment)', 'Search::governmentLookup/$1/jurisdiction');
    $routes->get($defaultLocale . '/lookup/government-parent/(:segment)', 'Search::governmentLookup/$1/parent');

    $routes->get($defaultLocale . '/search', 'Search::index');
    $routes->post($defaultLocale . '/search/' . $mainSearchRegex, 'Search::view/$1');
    $routes->get($defaultLocale . '/search/(:segment)', 'Search::noRecord');

    $routes->post($defaultLocale . '/address', 'Area::address');
    $routes->get($defaultLocale . '/point/(:segment)/(:segment)', 'Area::point/$1/$2');
    $routes->post($defaultLocale . '/point', 'Area::point');

    $routes->get($defaultLocale . '/about/(:segment)', 'About::index/$1');
    $routes->get($defaultLocale . '/about', 'About::index');
    $routes->get($defaultLocale . '/bot', 'Bot::index');
    $routes->get($defaultLocale . '/disclaimer', 'Disclaimer');
    $routes->get($defaultLocale . '/key', 'Key::index');

    if (!class_exists(\Localgeohistoryproject\Development\Controllers\Governmentidentifier::class, true)) {
        $routes->get($defaultLocale . '/governmentidentifier/(:segment)/(:segment)', 'Governmentidentifier::view/$1/$2');
    }

    $routes->get($defaultLocale . '/leaflet', 'Map::leaflet');
    $routes->get($defaultLocale . '/map-base', 'Map::baseStyle');
    $routes->get($defaultLocale . '/map-overlay', 'Map::overlayStyle');
    $routes->get($defaultLocale . '/map-tile/(:num)/(:num)/(:num)', 'Map::tile/$1/$2/$3');

    if (!class_exists(\Localgeohistoryproject\Development\Controllers\Statistics::class, true)) {
        $routes->post($defaultLocale . '/statistics/report/', 'Statistics::view');
    }
    $routes->get($defaultLocale . '/statistics/', 'Statistics::index');

    $routes->get($defaultLocale . '/status/', 'Status::index');
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

if (mb_strpos(base_url(), $_ENV['app_baseRouteProjectUrl']) !== false) {
    $routes->get($defaultLocale, 'Welcome');
    $routes->get('/', 'Welcome::language');
    $routes->set404Override(\App\Controllers\Fourofour::class);
}
