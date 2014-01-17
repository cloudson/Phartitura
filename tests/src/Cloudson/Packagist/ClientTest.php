<?php

namespace Cloudson\Phartitura\Packagist; 


class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @test
    */ 
    public function should_returns_ack()
    {
        $response = new MockResponseOKAdapter; 
        $curlClient = $this->getMock('Cloudson\Phartitura\Curl\ClientAdapter');
        $curlClient->expects($this->once())
            ->method('head')
            ->will($this->returnValue($response));

        $c = new Client($curlClient);
        $c->ping();
    }

    /**
    * @test
    * @expectedException \UnexpectedValueException
    */ 
    public function should_returns_miss()
    {
        $response = new MockResponseNotOKAdapter; 
        $curlClient = $this->getMock('Cloudson\Phartitura\Curl\ClientAdapter');
        $curlClient->expects($this->once())
            ->method('head')
            ->will($this->returnValue($response));

        $c = new Client($curlClient);
        $c->ping();   
    }

    public function should_ping_a_repository()
    {
        $response = new MockResponseOKAdapter; 
        $curlClient = $this->getMock('Cloudson\Phartitura\Curl\ClientAdapter');
        $curlClient->expects($this->once())
            ->method('head')
            ->will($this->returnValue($response));

        $c = new Client($curlClient);
        $c->ping('cloudson/oliveira');
    }

    /**
    * @test
    * @dataProvider returnInvalidPackageNames
    * @expectedException \InvalidArgumentException
    */ 
    public function should_throw_error_when_ping_with_invalid_package_names($packageName)
    {
        $response = new MockResponseOKAdapter; 
        $curlClient = $this->getMock('Cloudson\Phartitura\Curl\ClientAdapter');
        
        $c = new Client($curlClient);
        $c->ping($packageName);
    }

    public function returnInvalidPackageNames()
    {
        return [
            ['cloudson'],
            [[]],
            [true],
            [42],
            ['juca/'],
        ];
    }
}

// Help, please! 
class MockResponseOKAdapter implements \Cloudson\Phartitura\Curl\ResponseAdapter
{
    public function getStatusCode()
    {
        return 200;
    }
    public function getBody(){}
    public function getHeader(){}
    public function getHeaderInfo($info){}
}

class MockResponseNotOKAdapter implements \Cloudson\Phartitura\Curl\ResponseAdapter
{
    public function getStatusCode()
    {
        return 500;
    }
    public function getBody(){}
    public function getHeader(){}
    public function getHeaderInfo($info){}
}