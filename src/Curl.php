<?php
/**
 * A lightweight CURL wrapper for php
 * @package: Curl
 * @author: flashytime <myflashytime@gmail.com>
 * @date: 16/12/22 12:23
 */

namespace Flashytime\Curl;

/**
 * Class Curl
 * @package Flashytime\Curl
 */
class Curl
{
    private static $instance;
    private $request;
    private $response;
    private $error;
    private $message;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    private $defaultOptions = [
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false
    ];

    /**
     * Curl constructor.
     * ensure the cURL extension is available
     * @throws \Exception
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('The cURL extension is required.');
        }
    }

    /**
     * @return Curl
     */
    public static function init()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * make a GET request
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function get($url, $params = [])
    {
        return $this->request('GET', $url, [], $params);
    }

    /**
     * make a POST request with optional data
     * @param $url
     * @param $data
     * @param array $params
     * @return Curl
     */
    public function post($url, $data, $params = [])
    {
        return $this->request('POST', $url, $data, $params);
    }

    /**
     * make a PUT request with optional data
     * @param $url
     * @param array $data
     * @param array $params
     * @return Curl
     */
    public function put($url, $data = [], $params = [])
    {
        return $this->request('PUT', $url, $data, $params);
    }

    /**
     * make a DELETE request
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function delete($url, $params = [])
    {
        return $this->request('DELETE', $url, [], $params);
    }

    /**
     * make a PATCH request with optional data
     * @param $url
     * @param array $data
     * @param array $params
     * @return Curl
     */
    public function patch($url, $data = [], $params = [])
    {
        return $this->request('PATCH', $url, $data, $params);
    }

    /**
     * make a HEAD request
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function head($url, $params = [])
    {
        return $this->request('HEAD', $url, [], $params);
    }

    /**
     * make a HTTP request with specified METHOD and optional data
     * @param $method
     * @param $url
     * @param array $data
     * @param array $params
     * @return Curl
     */
    public function request($method, $url, $data = [], $params = [])
    {
        switch ($method) {
            case 'GET':
                $this->setUrl($url, $params);
                $this->setOption(CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                $this->setUrl($url, $params);
                $this->setOption(CURLOPT_POST, true);
                $this->setOption(CURLOPT_POSTFIELDS, $data);
                break;
            case 'HEAD':
                $this->setUrl($url, $params);
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'HEAD');
                $this->setOption(CURLOPT_NOBODY, true);
                break;
            case 'DELETE':
                $this->setUrl($url, $params);
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                // PUT and PATCH
                $this->setUrl($url, $params);
                $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
                $this->setOption(CURLOPT_POSTFIELDS, $data);
        }

        return $this->exec();
    }

    /**
     * download file
     * @param $url
     * @param $file
     * @throws \Exception
     */
    public function download($url, $file)
    {
        $this->get($url);
        if ($this->error()) {
            throw new \Exception($this->message(), $this->error());
        }
        $handle = @fopen($file, 'w');
        if (false === $handle) {
            throw new \Exception('Can not open file: ' . $file, 500);
        }
        fwrite($handle, $this->response());
        fclose($handle);
    }

    /**
     * return error code of the current curl request
     * @return mixed
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * return error message of the current curl request
     * @return mixed
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * return the result of the current curl request
     * @return mixed
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * set single option
     * @param $option
     * @param $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * set options
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * get options
     * @return array
     */
    public function getOptions()
    {
        return array_merge_keep_keys($this->defaultOptions, $this->options);
    }

    /**
     * reset options
     * @return $this
     */
    public function resetOptions()
    {
        $this->options = [];

        return $this;
    }

    /**
     * set url
     * @param $url
     * @param array $params
     */
    public function setUrl($url, $params = [])
    {
        $this->setOption(CURLOPT_URL, $this->buildUrl($url, $params));
    }

    /**
     * set headers for the current curl request
     * @param $headers
     */
    public function setHeaders($headers)
    {
        $httpHeaders = [];
        foreach ($headers as $key => $val) {
            $httpHeaders[] = $key . ':' . $val;
        }
        $this->setOption(CURLOPT_HTTPHEADER, $httpHeaders);
    }

    /**
     * execute the curl request
     * @return $this
     */
    private function exec()
    {
        $this->request = curl_init();
        curl_setopt_array($this->request, $this->getOptions());
        $this->response = curl_exec($this->request);
        $this->error = curl_errno($this->request);
        $this->message = $this->error ? curl_error($this->request) : '';
        $this->resetOptions();

        return $this;
    }

    /**
     * @param $url
     * @param array $params
     * @return string
     */
    private function buildUrl($url, $params = [])
    {
        if (!empty($params)) {
            $url .= '?' . http_build_query($params, '', '&');
        }

        return $url;
    }

    /**
     * close the current curl resource
     */
    public function close()
    {
        if (is_resource($this->request)) {
            curl_close($this->request);
        }

        $this->resetOptions();
    }

    public function __destruct()
    {
        $this->close();
    }
}
