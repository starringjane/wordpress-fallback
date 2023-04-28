<?php

/**
 * Wordpress Local Assets Fallback Asset
 */
class WLAFP_Asset
{
    protected $local_url;

    protected $production_url;

    public function __construct($local_url, $production_url)
    {
        $this->local_url = $local_url;
        $this->production_url = $production_url;
        $this->local_path = $this->getLocalPath();
    }

    public static function create($local_url, $production_url)
    {
        return new self($local_url, $production_url);
    }

    public function download()
    {
        $client = new GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $this->production_url);
        } catch (\Exception $exception) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if ($this->local_path) {
            file_put_contents($this->local_path, $response->getBody());
        }

        return true;
    }

    public function stream()
    {
        if (!file_exists($this->local_path)) {
            return false;
        }

        http_response_code(200);
        header('X-Fallback-Origin: ' . $this->production_url);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($this->local_path).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->local_path));

        readfile($this->local_path);
    }

    public function delete()
    {
        unlink($this->local_path);
    }

    protected function getLocalPath()
    {
        $wp_upload_dir = wp_upload_dir();
        $upload_dir = $wp_upload_dir['basedir'];
        $upload_url = $wp_upload_dir['baseurl'];

        if (strpos($this->local_url, $upload_url) === false) {
            return null;
        }

        $path = str_replace($upload_url, '', $this->local_url);
        $parts = explode('/', $path);
        $file = array_pop($parts);
        $dir = realpath($upload_dir);

        foreach ($parts as $part) {
            $dir .= "$part/";

            if(!is_dir($dir)) {
                mkdir($dir);
            }
        }

        return $upload_dir . $path;
    }
}
