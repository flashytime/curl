<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/5/16 21:11
 */

namespace Flashytime\Curl\Tests;

use Flashytime\Curl\Curl;

class CurlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Curl
     */
    protected $curl;
    protected $mockUrl = 'http://demo7968461.mockable.io';

    public function setUp()
    {
        $this->curl = new Curl();
    }

    public function testSetOptions()
    {
        $this->curl->setOptions([
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 11
        ]);

        $options = $this->curl->getOptions();

        $this->assertEquals(12, $options[CURLOPT_TIMEOUT]);
        $this->assertEquals(11, $options[CURLOPT_CONNECTTIMEOUT]);
    }

    public function testGet()
    {
        $this->curl->get($this->mockUrl . '/test/get');
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test get success', $response);
    }

    public function testPost()
    {
        $this->curl->post($this->mockUrl . '/test/post', ['data' => 'post']);
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test post success', $response);
    }

    public function testPostWithOptions()
    {
        $this->curl->setOptions([CURLOPT_TIMEOUT => 5])->post($this->mockUrl . '/test/post', ['data' => 'post']);
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test post success', $response);
    }

    public function testPut()
    {
        $this->curl->put($this->mockUrl . '/test/put', ['data' => 'put']);
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test put success', $response);
    }

    public function testDelete()
    {
        $this->curl->delete($this->mockUrl . '/test/delete');
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test delete success', $response);
    }

    public function testPatch()
    {
        $this->curl->patch($this->mockUrl . '/test/patch', ['data' => 'patch']);
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test patch success', $response);
    }

    public function testRequest()
    {
        $this->curl->get($this->mockUrl . '/test/get');
        $response = $this->curl->response();

        $this->curl->request('GET',$this->mockUrl . '/test/get');
        $response2 = $this->curl->response();

        $this->curl->close();
        $this->assertEquals($response2, $response);
    }
}
