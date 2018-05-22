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
        $this->curl->url($this->mockUrl . '/test/get')->get();
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test get success', $response);
    }

    public function testPost()
    {
        $this->curl->url($this->mockUrl . '/test/post', ['id' => '1'])
            ->set(['data' => 'post'])
            ->post();
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test post success', $response);
    }

    public function testPostWithOptions()
    {
        $this->curl->setOptions([CURLOPT_TIMEOUT => 5])
            ->url($this->mockUrl . '/test/post')
            ->set(['data' => 'post'])
            ->post();
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test post success', $response);
    }

    public function testPut()
    {
        $this->curl->url($this->mockUrl . '/test/put')
            ->set(['data' => 'put'])
            ->put();
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test put success', $response);
    }

    public function testDelete()
    {
        $this->curl->url($this->mockUrl . '/test/delete')->delete();
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test delete success', $response);
    }

    public function testPatch()
    {
        $this->curl->url($this->mockUrl . '/test/patch')
            ->set(['data' => 'patch'])
            ->patch();
        if ($this->curl->error()) {
            var_dump($this->curl->message());
        }
        $response = $this->curl->response();
        $this->curl->close();
        $this->assertEquals('test patch success', $response);
    }

    public function testRequest()
    {
        $this->curl->url($this->mockUrl . '/test/get')->get();
        $response = $this->curl->response();

        $this->curl->url($this->mockUrl . '/test/get')->request('GET');
        $response2 = $this->curl->response();

        $this->curl->close();
        $this->assertEquals($response2, $response);
    }
}
