<?php
/**
 * Plugin Name: Xclear 301 Redirects (old site -> new site)
 * Description: Old-path -> new-path 301 map, same domain. Regenerate with
 *              build-301-mu-plugin.php after updating redirect-map.csv.
 * Version:     1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

add_action('template_redirect', function () {
    static $map = [
        '/fr/desinfection-de-leau/xclear-xpert-series/module-uv-c-1/' => '/fr/product/xclear-module-uv-c/',
        '/nl/waterdesinfectie/xclear-series/jumbo-tech-uv-c/' => '/nl/product/xclear-jumbo-tech-uv-c/',
        '/fr/maison/' => '/fr/',
        '/es/desinfeccion-del-agua/xclear-series/module-uv-c/' => '/es/product/xclear-module-uv-c/',
        '/fr/equipements-et-fournitures-pour-bassin/static-mixer/' => '/fr/product/xclear-static-mixer/',
        '/fr/desinfection-de-leau/xclear-series/immersion-uv-c/' => '/fr/product/xclear-immersion-uv-c/',
        '/de/wasserdesinfektion/xclear-xpert-series/xclear-xpert-inox-uv-c/' => '/de/product/xclear-xpert-inox-uv-c/',
        '/water-disinfection/xclear-xpert-series/xclear-xpert-inox-immersion-uv-c/' => '/product/xclear-xpert-inox-immersion-uv-c/',
        '/nl/waterdesinfectie/xclear-series/module-uv-c/' => '/nl/product/xclear-module-uv-c/',
        '/es/desinfeccion-del-agua/xclear-series/immersion-uv-c/' => '/es/product/xclear-immersion-uv-c/',
        '/de/datenschutzrichtlinie/' => '/de/privacy-policy/',
        '/es/desinfeccion-del-agua/xclear-series/jumbo-tech-uv-c/' => '/es/product/xclear-jumbo-tech-uv-c/',
        '/de/teichausruestung-und-zubehoer/flow-switch-plus/' => '/de/product/xclear-flow-switch-plus/',
        '/pond-equipment-and-supplies/anti-algae-copper-electrolyzer/' => '/product/xclear-copper-electrolyser/',
        '/nl/home/' => '/nl/',
        '/nl/waterdesinfectie/xclear-xpert-series/xclear-xpert-buster-uv-c/' => '/nl/product/xclear-xpert-buster-uv-c/',
        '/nl/vijveruitrusting-en-benodigdheden/flow-switch-plus/' => '/nl/product/xclear-flow-switch-plus/',
        '/de/home/' => '/de/',
        '/pond-equipment-and-supplies/profiheater/' => '/product/xclear-profiheater/',
        '/fr/desinfection-de-leau/xclear-xpert-series/immersion-uv-c-1/' => '/fr/product/xclear-xpert-inox-immersion-uv-c/',
        '/fr/politique-de-cookies/' => '/fr/cookie-policy/',
        '/water-disinfection/xclear-xpert-series/xclear-xpert-buster-uv-c/' => '/product/xclear-xpert-buster-uv-c/',
        '/pond-equipment-and-supplies/static-mixer/' => '/product/xclear-static-mixer/',
        '/de/wasserdesinfektion/xclear-series/immersion-uv-c/' => '/de/product/xclear-immersion-uv-c/',
        '/es/home/' => '/es/',
        '/pond-equipment-and-supplies/flow-switch-plus/' => '/product/xclear-flow-switch-plus/',
        '/de/teichausruestung-und-zubehoer/anti-algae-copper-electrolyzer/' => '/de/product/xclear-copper-electrolyser/',
        '/de/wasserdesinfektion/xclear-series/jumbo-tech-uv-c/' => '/de/product/xclear-jumbo-tech-uv-c/',
        '/water-disinfection/xclear-series/flex-inox-uv-c/' => '/product/xclear-flex-inox-uv-c/',
        '/fr/desinfection-de-leau/xclear-series/module-uv-c/' => '/fr/product/xclear-module-uv-c/',
        '/de/wasserdesinfektion/xclear-xpert-series/xclear-xpert-hdpe-uv-c/' => '/de/product/xclear-xpert-hdpe-uv-c/',
        '/de/teichausruestung-und-zubehoer/profiheater/' => '/de/product/xclear-profiheater/',
        '/nl/vijveruitrusting-en-benodigdheden/profiheater/' => '/nl/product/xclear-profiheater/',
        '/nl/waterdesinfectie/xclear-series/xclear-pond-uv-c-42w/' => '/nl/product/xclear-pond-uv-c/',
        '/es/descargas/' => '/es/downloads/',
        '/nl/vijveruitrusting-en-benodigdheden/anti-algae-copper-electrolyzer/' => '/nl/product/xclear-copper-electrolyser/',
        '/contact/' => '/contact-us/',
        '/de/wasserdesinfektion/xclear-xpert-series/xclear-xpert-buster-uv-c/' => '/de/product/xclear-xpert-buster-uv-c/',
        '/es/equipos-y-suministros-para-estanques/profiheater/' => '/es/product/xclear-profiheater/',
        '/fr/equipements-et-fournitures-pour-bassin/flow-switch-plus/' => '/fr/product/xclear-flow-switch-plus/',
        '/nl/waterdesinfectie/xclear-xpert-series/xclear-xpert-inox-uv-c/' => '/nl/product/xclear-xpert-inox-uv-c/',
        '/de/teichausruestung-und-zubehoer/static-mixer/' => '/de/product/xclear-static-mixer/',
        '/water-disinfection/xclear-xpert-series/xclear-xpert-inox-uv-c/' => '/product/xclear-xpert-inox-uv-c/',
        '/water-disinfection/xclear-xpert-series/xclear-xpert-hdpe-uv-c/' => '/product/xclear-xpert-hdpe-uv-c/',
        '/es/equipos-y-suministros-para-estanques/static-mixer/' => '/es/product/xclear-static-mixer/',
        '/es/equipos-y-suministros-para-estanques/flow-switch-plus/' => '/es/product/xclear-flow-switch-plus/',
        '/nl/waterdesinfectie/xclear-xpert-series/xclear-xpert-inox-immersion-uv-c/' => '/nl/product/xclear-xpert-inox-immersion-uv-c/',
        '/nl/waterdesinfectie/xclear-xpert-series/' => '/nl/product_category/xclear-xpert-series/',
        '/es/politica-de-cookies/' => '/es/cookie-policy/',
        '/es/equipos-y-suministros-para-estanques/anti-algea-copper-electrolyzer/' => '/es/product/xclear-copper-electrolyser/',
        '/nl/contact/' => '/nl/contact-us/',
        '/de/haftungsausschluss/' => '/de/disclaimer/',
        '/es/descargo-de-responsabilidad/' => '/es/disclaimer/',
        '/fr/equipements-et-fournitures-pour-bassin/anti-algea-copper-electrolyzer/' => '/fr/product/xclear-copper-electrolyser/',
        '/es/desinfeccion-del-agua/xclear-xpert-series/immersion-uv-c-1/' => '/es/product/xclear-xpert-inox-immersion-uv-c/',
        '/de/wasserdesinfektion/xclear-series/flex-inox-uv-c/' => '/de/product/xclear-flex-inox-uv-c/',
        '/nl/waterdesinfectie/xclear-series/' => '/nl/product_category/xclear-series-nl/',
        '/es/contacto/' => '/es/contact-us/',
        '/nl/waterdesinfectie/xclear-xpert-series/xclear-xpert-hdpe-uv-c/' => '/nl/product/xclear-xpert-hdpe-uv-c/',
        '/fr/telechargements/' => '/fr/downloads/',
        '/nl/vijveruitrusting-en-benodigdheden/static-mixer/' => '/nl/product/xclear-static-mixer/',
        '/de/kontakt/' => '/de/contact-us/',
        '/nl/waterdesinfectie/xclear-series/immersion-uv-c/' => '/nl/product/xclear-immersion-uv-c/',
        '/nl/cookiebeleid/' => '/nl/cookie-policy/',
        '/de/cookie-richtlinie/' => '/de/cookie-policy/',
        '/de/wasserdesinfektion/xclear-series/' => '/de/product_category/xclear-series/',
        '/es/desinfeccion-del-agua/xclear-xpert-series/module-uv-c-1/' => '/es/product/xclear-module-uv-c/',
        '/fr/desinfection-de-leau/xclear-series/xclear-pond-uv-c-42w/' => '/fr/product/xclear-pond-uv-c/',
        '/fr/desinfection-de-leau/xclear-xpert-series/inox-uv-c/' => '/fr/product/xclear-xpert-inox-uv-c/',
        '/fr/desinfection-de-leau/xclear-series/jumbo-tech-uv-c/' => '/fr/product/xclear-jumbo-tech-uv-c-4/',
        '/fr/desinfection-de-leau/xclear-series/flex-inox-uv-c/' => '/fr/product/xclear-flex-inox-uv-c/',
        '/de/wasserdesinfektion/xclear-series/xclear-pond-uv-c-42w/' => '/de/product/xclear-pond-uv-c/',
        '/nl/waterdesinfectie/xclear-series/flex-inox-uv-c/' => '/nl/product/xclear-flex-inox-uv-c/',
        '/water-disinfection/xclear-series/module-uv-c/' => '/product/xclear-module-uv-c/',
        '/water-disinfection/xclear-series/xclear-pond-uv-c-42w/' => '/product/xclear-pond-uv-c/',
        '/es/desinfeccion-del-agua/xclear-series/' => '/es/product_category/xclear-series/',
        '/es/desinfeccion-del-agua/xclear-series/flex-inox-uv-c/' => '/es/product/xclear-flex-inox-uv-c/',
        '/water-disinfection/xclear-series/immersion-uv-c/' => '/product/xclear-immersion-uv-c/',
        '/water-disinfection/xclear-series/jumbo-tech-uv-c/' => '/product/xclear-jumbo-tech-uv-c/',
        '/water-disinfection/xclear-xpert-series/' => '/product_category/xclear-xpert-series/',
        '/es/desinfeccion-del-agua/xclear-series/xclear-pond-uv-c-42w/' => '/es/product/xclear-pond-uv-c/',
        '/water-disinfection/xclear-series/' => '/product_category/xclear-series/',
        '/es/desinfeccion-del-agua/xclear-xpert-series/inox-uv-c/' => '/es/product/xclear-xpert-inox-uv-c/',
        '/de/wasserdesinfektion/xclear-xpert-series/xclear-xpert-inox-immersion-uv-c/' => '/de/product/xclear-xpert-inox-immersion-uv-c/',
        '/fr/clause-de-non-responsabilite/' => '/fr/disclaimer/',
        '/fr/desinfection-de-leau/xclear-series/' => '/fr/product_category/xclear-series/',
        '/fr/desinfection-de-leau/xclear-xpert-series/' => '/fr/product_category/xclear-xpert-series-fr/',
        '/fr/equipements-et-fournitures-pour-bassin/profiheater/' => '/fr/product/xclear-profiheater/',
        '/de/wasserdesinfektion/xclear-xpert-series/' => '/de/product_category/xclear-xpert-series-de/',
        '/es/desinfeccion-del-agua/xclear-xpert-series/' => '/es/product_category/xclear-xpert-series-es/',
        '/fr/contact/' => '/fr/contact-us/',
        '/de/wasserdesinfektion/xclear-series/module-uv-c/' => '/de/product/xclear-module-uv-c/',
    ];

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = rtrim((string) $path, '/') . '/';

    if (isset($map[$path])) {
        wp_safe_redirect(home_url($map[$path]), 301);
        exit;
    }
}, 1);
