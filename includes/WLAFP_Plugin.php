<?php

/**
 * Require settings
 */
require_once 'WLAFP_Settings.php';

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
        add_action('wp', [$this, 'wpAction']);
    }

    /**
     * On wp action
     */
    public function wpAction()
    {
        /**
         * Ignore if request is not 404
         */
        if (!is_404()) {
            return;
        }

        $headers = apache_request_headers();

        /**
         * Ignore html requests
         */
        if (strpos($headers['Accept'], 'text/html') !== false) {
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

        return $this->fetch($fallback, [
            'Accept' => $headers['Accept'] ?? '',
            'Accept-Encoding' => $headers['Accept-Encoding'] ?? '',
        ]);
    }

    protected function getUrl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    protected function getHost()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }

    protected function fetch($url, $headers)
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
            ]);
        } catch (\Exception $exception) {
            return;
        }

        if ($this->settings->download === true && $response->getStatusCode() === 200) {
            $this->store($url, $response->getBody());
        }

        http_response_code($response->getStatusCode());
        header('x-origin: ' . $url);

        foreach ($response->getHeaders() as $name => $values) {
            header($name . ': ' . implode(', ', $values));
        }

        echo $response->getBody();

        exit;
    }

    protected function store($url, $contents)
    {
        $path = parse_url($url)['path'];

        $parts = explode('/', $path);
        $file = array_pop($parts);
        $dir = realpath(WP_CONTENT_DIR);

        foreach($parts as $part) {
            $dir .= "$part/";

            if(!is_dir($dir)) {
                mkdir($dir);
            }
        }

        file_put_contents(WP_CONTENT_DIR. $path, (string)$contents);
    }
}
