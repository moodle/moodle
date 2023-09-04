<?php

namespace Basho\Tests\Riak\Search;

use Basho\Riak\Search\Doc;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Search result document test
 *
 * @author Michael Mayer <michael@lastzero.net>
 */
class DocTest extends TestCase
{
    /**
     * @var Doc
     */
    protected $doc;

    public function setUp()
    {
        $data = new \stdClass();
        $data->_yz_id = '1*tests*test*5*39';
        $data->_yz_rk = '5';
        $data->_yz_rt = 'tests';
        $data->_yz_rb = 'test';
        $data->foo = 'bar';
        $data->_status = 200;
        $this->doc = new Doc($data);
    }

    public function testGetLocation()
    {
        $result = $this->doc->getLocation();
        $this->assertInstanceOf('\Basho\Riak\Location', $result);
        $this->assertInstanceOf('\Basho\Riak\Bucket', $result->getBucket());
        $this->assertEquals('tests', $result->getBucket()->getType());
        $this->assertEquals('test', $result->getBucket()->getName());
        $this->assertEquals('5', $result->getKey());
    }

    public function testGetData()
    {
        $result = $this->doc->getData();
        $this->assertInternalType('array', $result);
        $this->assertEquals('bar', $result['foo']);
        $this->assertEquals(200, $result['_status']);
    }

    public function testMagicGetter()
    {
        $this->assertEquals('bar', $this->doc->foo);
        $this->assertEquals(200, $this->doc->_status);
    }
}
