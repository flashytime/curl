# curl
A lightweight CURL wrapper for php

### Installation

```bash
composer require flashytime/curl
```

### Usage
##### to GET a page with default params
```php
$curl = new \Flashytime\Curl\Curl();
$curl->get('http://demo7968461.mockable.io/test/get');
if ($curl->error()) {
    var_dump($curl->message());
}
$response = $curl->response();
$curl->close();
```

##### to POST data to a page with custom options
```php
$curl = new \Flashytime\Curl\Curl();
$curl->setOptions([CURLOPT_TIMEOUT => 5])->post('http://demo7968461.mockable.io/test/post', ['data' => 'post']);
if ($curl->error()) {
    var_dump($curl->message());
}
$response = $curl->response();
$curl->close();
```

### License
MIT