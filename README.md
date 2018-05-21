# curl
A lightweight CURL wrapper for php

### Installation

```bash
composer require flashytime/curl
```

### Usage

```php
$curl = new \Flashytime\Curl\Curl();
$curl->get('http://demo7968461.mockable.io/test/get');
if ($curl->error()) {
    var_dump($curl->message());
}
$response = $curl->response();
$curl->close();
```

```php
$curl = new \Flashytime\Curl\Curl();
$curl->setOptions([CURLOPT_TIMEOUT => 5])->post('http://demo7968461.mockable.io/test/post');
if ($curl->error()) {
    var_dump($curl->message());
}
$response = $curl->response();
$curl->close();
```

### License
MIT