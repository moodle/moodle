<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiLTI;

class TiiLTITest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiLTI
     */
    protected $object;

    public static function setUpBeforeClass()
    {
        // fwrite(STDOUT,"\n" . __METHOD__ . "\n");
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TiiLTI;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testSetSubmissionIds()
    {
        $expected = [ 1234, 5678 ];
        $this->object->setSubmissionIds($expected);
    }

    public function testGetSubmissionIds()
    {
        $expected = [ 1234, 5678 ];
        $this->object->setSubmissionIds($expected);
        $result = $this->object->getSubmissionIds();

        $this->assertEquals($expected,$result);
    }

    public function testSetXmlResponse()
    {
        $expected = true;
        $this->object->setXmlResponse($expected);
    }

    public function testGetXmlResponse()
    {
        $expected = true;
        $this->object->setXmlResponse($expected);
        $result = $this->object->getXmlResponse();

        $this->assertEquals($expected,$result);
    }

    public function testSetCustomCSS()
    {
        $expected = 'http://example.com/custom.css';
        $this->object->setCustomCSS($expected);
    }

    public function testGetCustomCSS()
    {
        $expected = 'http://example.com/custom.css';
        $this->object->setCustomCSS($expected);
        $result = $this->object->getCustomCSS();

        $this->assertEquals($expected,$result);
    }

    public function testSetPeermarkId()
    {
        $expected = 1234;
        $this->object->setPeermarkId($expected);
    }

    public function testGetPeermarkId()
    {
        $expected = 1234;
        $this->object->setPeermarkId($expected);
        $result = $this->object->getPeermarkId();

        $this->assertEquals($expected,$result);
    }

    public function testSetSkipSetup()
    {
        $expected = true;
        $this->object->setSkipSetup($expected);
    }

    public function testGetSkipSetup()
    {
        $expected = true;
        $this->object->setSkipSetup($expected);
        $result = $this->object->getSkipSetup();

        $this->assertEquals($expected,$result);
    }

    public function testSetStudentList()
    {
        $expected = '1234,5678,9123';
        $this->object->setStudentList($expected);
    }

    public function testGetStudentList()
    {
        $expected = '1234,5678,9123';
        $this->object->setStudentList($expected);
        $result = $this->object->getStudentList();

        $this->assertEquals($expected,$result);
    }

    public function testSetGetReturnUrl()
    {
        $expected = "https://returnUrl.consumer.lms.com";
        $this->object->setReturnUrl($expected);
        $result =$this->object->getReturnUrl();

        $this->assertEquals($expected, $result);
    }
}
