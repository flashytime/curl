# Curl
A lightweight CURL wrapper for php

### Installation

```bash
composer require flashytime/curl
```

### Usage

##### Instantiation
```php
$curl = new \Flashytime\Curl\Curl();
```
or
```php
$curl = \Flashytime\Curl\Curl::init();
```
##### GET
```php
$curl->url($url)->get();
```
or
```php
//$params is a query parameter array, like ['id' => 1, 'uid' => 2]
$curl->url($url, $params)->get();
```

##### POST
```php
//$data is a multi array
$curl->url($url)->set($data)->post();
```

##### PUT
```php
$curl->url($url)->set($data)->put();
```

##### PATCH
```php
$curl->url($url)->set($data)->patch();
```

##### DELETE
```php
$curl->url($url)->delete();
```

##### DOWNLOAD
```php
$curl->url($url)->download($file);
```

##### Set Options
```php
//$option is the CURLOPT_XXX option
$curl->setOption($option, $value)
->url($url)
->set($data)
->post();
```
or
```php
$curl->setOptions([$option1 => $value1, $option2 => $value2])
->url($url)
->set($data)
->post();
```

##### Result
```php
if ($curl->error()) {
    var_dump($curl->message());
}
//the response data
$response = $curl->response();
$curl->close();
```

### License
MIT