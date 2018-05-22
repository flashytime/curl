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
     * POST|PUT|PATCH data
     * @var array
     */
    private $data = [];

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
     * set url
     * @param $url
     * @param array $params
     * @return $this
     */
    public function url($url, $params = [])
    {
        $this->setOption(CURLOPT_URL, $this->buildUrl($url, $params));

        return $this;
    }

    /**
     * set data
     * POST|PUT|PATCH
     * @param array $data
     * @return $this
     */
    public function set(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * make a GET request
     * @return Curl
     */
    public function get()
    {
        return $this->request('GET');
    }

    /**
     * make a POST request
     * @return Curl
     */
    public function post()
    {
        return $this->request('POST');
    }

    /**
     * make a PUT request
     * @return Curl
     */
    public function put()
    {
        return $this->request('PUT');
    }

    /**
     * make a DELETE request
     * @return Curl
     */
    public function delete()
    {
        return $this->request('DELETE');
    }

    /**
     * make a PATCH request
     * @return Curl
     */
    public function patch()
    {
        return $this->request('PATCH');
    }

    /**
     * make a HEAD request
     * @return Curl
     */
    public function head()
    {
        return $this->request('HEAD');
    }

    /**
     * make a HTTP request with specified METHOD and optional data
     * @param $method
     * @return Curl
     */
    public function request($method)
    {
        switch ($method) {
            case 'GET':
                $this->setOption(CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                $this->setOption(CURLOPT_POST, true);
                $this->setOption(CURLOPT_POSTFIELDS, $this->data);
                break;
            case 'HEAD':
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'HEAD');
                $this->setOption(CURLOPT_NOBODY, true);
                break;
            case 'DELETE':
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                // PUT and PATCH
                $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
                $this->setOption(CURLOPT_POSTFIELDS, $this->data);
        }

        return $this->exec();
    }

    /**
     * download file
     * @param $file
     * @return $this
     * @throws \Exception
     */
    public function download($file)
    {
        $this->get();
        if ($this->error()) {
            throw new \Exception($this->message(), $this->error());
        }
        $handle = @fopen($file, 'w');
        if (false === $handle) {
            throw new \Exception('Can not open file: ' . $file, 500);
        }
        fwrite($handle, $this->response());
        fclose($handle);

        return $this;
    }

    /**
     * return error code
     * @return mixed
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * return error message
     * @return mixed
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * return the result
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
     * set headers
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
     * build url
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
