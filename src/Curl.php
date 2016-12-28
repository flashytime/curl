<?php
/**
 * A lightweight CURL wrapper for php
 * @package: curl
 * @author: flashytime <myflashytime@gmail.com>
 * @date: 16/12/22 12:23
 */

namespace flashytime\Curl;

class Curl
{
    private static $instance;
    private $request;
    private $response;
    private $error;
    private $message;
    private $headers = [];
    private $options = [
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 10,
    ];

    /**
     * ensure the cURL extension is available
     * @throws \Exception
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('The cURL extension is required.');
        }
        $this->request = curl_init();
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
     * Make a GET request with optional data
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function get($url, $params = [])
    {
        return $this->request('GET', $url, $params);
    }

    /**
     * Make a POST request with optional data
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function post($url, $params = [])
    {
        return $this->request('POST', $url, $params);
    }

    /**
     * Make a PUT request with optional data
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function put($url, $params = [])
    {
        return $this->request('PUT', $url, $params);
    }

    /**
     * Make a DELETE request with optional data
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function delete($url, $params = [])
    {
        return $this->request('DELETE', $url, $params);
    }

    /**
     * Make a HEAD request with optional data
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function head($url, $params = [])
    {
        return $this->request('HEAD', $url, $params);
    }

    /**
     * Make a HTTP request with specified METHOD and optional data
     * @param $method
     * @param $url
     * @param array $params
     * @return Curl
     */
    public function request($method, $url, $params = [])
    {
        switch ($method) {
            case 'GET':
                $this->setUrl($url, $params);
                $this->setOption(CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                $this->setUrl($url);
                $this->setOption(CURLOPT_POST, true);
                if ($params) {
                    $this->setOption(CURLOPT_POSTFIELDS, $params);
                }
                break;
            case 'HEAD':
                $this->setUrl($url, $params);
                $this->setOption(CURLOPT_CUSTOMREQUEST, 'HEAD');
                $this->setOption(CURLOPT_NOBODY, true);
                break;
            default:
                // PUT and DELETE
                $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
                if ($params) {
                    $this->setOption(CURLOPT_POSTFIELDS, $params);
                }
        }
        return $this->_exec();
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
        if ($this->error) {
            throw new \Exception($this->message, $this->error);
        }
        $handle = @fopen($file, 'w');
        if (false === $handle) {
            throw new \Exception('Can not open file: ' . $file, 500);
        }
        fwrite($handle, $this->response);
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
     * set a CURLOPT option for the current curl request
     * @param $option
     * @param $value
     * @return bool
     */
    public function setOption($option, $value)
    {
        return curl_setopt($this->request, $option, $value);
    }

    /**
     * set CURLOPT options for the current curl request
     * @param $options
     * @return bool
     */
    public function setOptions($options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
        return true;
    }

    /**
     * set url
     * @param $url
     * @param array $params
     */
    public function setUrl($url, $params = [])
    {
        $this->setOption(CURLOPT_URL, $this->_buildUrl($url, $params));
    }

    /**
     * set headers for the current curl request
     * @param $headers
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $val) {
            $this->headers[$key] = $val;
        }
        $headers = [];
        foreach ($this->headers as $key => $val) {
            $headers[] = $key . ':' . $val;
        }
        $this->setOption(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Execute the curl request
     * @return $this
     */
    private function _exec()
    {
        $this->setOptions($this->options);
        $this->response = curl_exec($this->request);
        $this->error = curl_errno($this->request);
        $this->message = $this->error ? curl_error($this->request) : '';
        return $this;
    }

    private function _buildUrl($url, $params = [])
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
    }

    public function __destruct()
    {
        $this->close();
    }
}