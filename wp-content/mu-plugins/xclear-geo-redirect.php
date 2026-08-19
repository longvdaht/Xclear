<?php
/**
 * Plugin Name: Xclear GEO Redirect
 * Description: Detects visitor country (MaxMind GeoLite2) and redirects first-time visitors to the matching Polylang language.
 * Version:     1.0.0
 */

if (! defined('ABSPATH')) {
    exit;
}

class XclearGeoRedirect
{
    // Stores the language this plugin last auto-assigned (not a plain "already ran" flag),
    // so a genuine manual language switch is distinguishable from our own redirect and stays sticky.
    private const COOKIE_NAME   = 'xclear_geo_lang';
    private const COOKIE_TTL    = DAY_IN_SECONDS;
    private const DB_PATH       = __DIR__ . '/geoip/GeoLite2-Country.mmdb';

    /** Country ISO code => Polylang language slug. Anything not listed falls back to $defaultLang. */
    private array $countryToLang = [
        'NL' => 'nl',
        'BE' => 'nl',
        'DE' => 'de',
        'AT' => 'de',
        'CH' => 'de',
        'FR' => 'fr',
        'ES' => 'es',
    ];

    private string $defaultLang = 'en';

    public function __construct()
    {
        add_action('template_redirect', [$this, 'maybeRedirect'], 1);
    }

    public function maybeRedirect(): void
    {
        if (! $this->shouldRun()) {
            return;
        }

        $currentLang  = function_exists('pll_current_language') ? pll_current_language() : null;
        $lastAutoLang = $_COOKIE[self::COOKIE_NAME] ?? null;

        // A cookie was set by us before, but the visitor is no longer on that language:
        // they switched manually, so stop overriding their choice.
        if ($lastAutoLang !== null && $lastAutoLang !== $currentLang) {
            return;
        }

        $country = $this->detectCountry($this->getVisitorIp());
        if ($country === null) {
            return;
        }

        $targetLang = $this->countryToLang[$country] ?? $this->defaultLang;

        if (! function_exists('pll_languages_list') || ! in_array($targetLang, pll_languages_list(), true)) {
            return;
        }

        $this->setCookie($targetLang);

        if ($targetLang === $currentLang) {
            return;
        }

        $url = function_exists('pll_home_url') ? pll_home_url($targetLang) : null;
        if (! $url) {
            return;
        }

        wp_safe_redirect($url, 302);
        exit;
    }

    private function shouldRun(): bool
    {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return false;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return false;
        }

        if (! is_front_page()) {
            return false;
        }

        if (! file_exists(self::DB_PATH)) {
            return false;
        }

        return true;
    }

    private function detectCountry(?string $ip): ?string
    {
        if ($ip === null) {
            return null;
        }

        $autoload = __DIR__ . '/geoip/vendor/autoload.php';
        if (! file_exists($autoload)) {
            return null;
        }
        require_once $autoload;

        try {
            $reader = new \GeoIp2\Database\Reader(self::DB_PATH);
            $record = $reader->country($ip);

            return $record->country->isoCode ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function getVisitorIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }

    private function setCookie(string $lang): void
    {
        setcookie(self::COOKIE_NAME, $lang, time() + self::COOKIE_TTL, '/');
    }
}

new XclearGeoRedirect();
