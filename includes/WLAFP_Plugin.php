<?php

/**
 * Require settings
 */
require_once 'WLAFP_Settings.php';
require_once 'WLAFP_Asset.php';

/**
 * Wordpress Local Assets Fallback Plugin
 */
class WLAFP_Plugin {

    public $version = '1.0.0';

    protected $settings;

    protected $plugin_file_name;

    protected $client;

    /**
     * Constructor
     */
    public function __construct($plugin_file_name, WLAFP_Settings $settings)
    {
        define('WLAFP_VERSION', $this->version);

        $this->plugin_file_name = $plugin_file_name;
        $this->settings = $settings->get();
        $this->client = new GuzzleHttp\Client();

        $this->actions();
    }

    /**
     * Create a new instance
     */
    static function create($plugin_file_name, $settings = null)
    {
        $settings = $settings ?: WLAFP_Settings::create($plugin_file_name);

        return new self($plugin_file_name, $settings);
    }

    /**
     * Register actions
     */
    public function actions()
    {
        add_action('init', [$this, 'addRewriteRule']);
        add_action('wp', [$this, 'fallback']);
    }

    /**
     * Add rewrite rule to return 404 for non existing assets
     */
    public function addRewriteRule()
    {
        $uploads_url = wp_upload_dir()['baseurl'];
        $uploads_url = str_replace($this->getHost(), '', $uploads_url);
        $uploads_url = trim($uploads_url, '/');

        add_rewrite_rule($uploads_url . '/(.*)', 'index.php?attachment=$matches[1]');
    }

    /**
     * Stream fallback asset when necessary
     */
    public function fallback()
    {
        /**
         * Ignore if request is not 404
         */
        if (!is_404()) {
            return;
        }

        /**
         * Ignore html requests
         */
        if (strpos(apache_request_headers()['Accept'], 'text/html') !== false) {
            return;
        }

        $url = $this->getUrl();
        $local_host = $this->getHost();
        $production_host = $this->settings->production_host;

        /**
         * Ignore if $production_host is not set
         */
        if (empty($production_host)) {
            return;
        }

        $fallback = str_replace($local_host, $production_host, $url);

        /**
         * Ignore if fallback has not changed
         */
        if ($fallback === $url) {
            return;
        }

        $asset = WLAFP_Asset::create($url, $fallback);

        $asset->download();

        $asset->stream();

        if ($this->settings->download !== true) {
            $asset->delete();
        }

        exit;
    }

    protected function getUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    protected function getHost()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }
}
