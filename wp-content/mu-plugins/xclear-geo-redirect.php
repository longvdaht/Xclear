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
    private const COOKIE_NAME   = 'xclear_geo_redirected';
    private const COOKIE_TTL    = YEAR_IN_SECONDS;
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

        $country = $this->detectCountry($this->getVisitorIp());
        if ($country === null) {
            $this->setCookie(); // don't look it up again on every request
            return;
        }

        $targetLang = $this->countryToLang[$country] ?? $this->defaultLang;

        if (! function_exists('pll_languages_list') || ! in_array($targetLang, pll_languages_list(), true)) {
            $this->setCookie();
            return;
        }

        $currentLang = function_exists('pll_current_language') ? pll_current_language() : null;

        $this->setCookie();

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

        if (isset($_COOKIE[self::COOKIE_NAME])) {
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
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function getVisitorIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }

    private function setCookie(): void
    {
        setcookie(self::COOKIE_NAME, '1', time() + self::COOKIE_TTL, '/');
    }
}

new XclearGeoRedirect();
