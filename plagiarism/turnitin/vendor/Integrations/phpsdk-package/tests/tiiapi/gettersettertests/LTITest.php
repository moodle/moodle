<?php

require_once(__DIR__ . '/../testconsts.php');
require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\LTI;

class LTITest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LTI
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
        $this->object = new LTI( TII_APIBASEURL );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testSetAccountId()
    {
        $expected = 12345;
        $this->object->setAccountId($expected);
        $result = $this->object->getAccountId();

        $this->assertEquals($expected,$result);
    }

    public function testGetAccountId()
    {
        $expected = 12345;
        $this->object->setAccountId($expected);
        $result = $this->object->getAccountId();

        $this->assertEquals($expected,$result);
    }

    public function testSetSharedKey()
    {
        $expected = 'secret';
        $this->object->setSharedKey($expected);
        $result = $this->object->getSharedKey();

        $this->assertEquals($expected,$result);
    }

    public function testGetSharedKey()
    {
        $expected = 'secret';
        $this->object->setSharedKey($expected);
        $result = $this->object->getSharedKey();

        $this->assertEquals($expected,$result);
    }

    public function testSetProxyType()
    {
        $expected = 'test';
        $this->object->setProxyType($expected);
        $result = $this->object->getProxyType();

        $this->assertEquals($expected,$result);
    }

    public function testGetProxyType()
    {
        $expected = 'test';
        $this->object->setProxyType($expected);
        $result = $this->object->getProxyType();

        $this->assertEquals($expected,$result);
    }

    public function testSetProxyBypass()
    {
        $expected = 'test';
        $this->object->setProxyBypass($expected);
        $result = $this->object->getProxyBypass();

        $this->assertEquals($expected,$result);
    }

    public function testGetProxyBypass()
    {
        $expected = 'test';
        $this->object->setProxyBypass($expected);
        $result = $this->object->getProxyBypass();

        $this->assertEquals($expected,$result);
    }

    public function testGetSSLCertificate()
    {
        $expected = 'test';
        $this->object->setSSLCertificate($expected);
        $result = $this->object->getSSLCertificate();

        $this->assertEquals($expected,$result);
    }

    public function testSetIntegrationVersion()
    {
        $expected = 'test';
        $this->object->setIntegrationVersion($expected);
        $result = $this->object->getIntegrationVersion();

        $this->assertEquals($expected,$result);
    }

    public function testGetIntegrationVersion()
    {
        $expected = 'test';
        $this->object->setIntegrationVersion($expected);
        $result = $this->object->getIntegrationVersion();

        $this->assertEquals($expected,$result);
    }

    public function testSetPluginVersion()
    {
        $expected = 'test';
        $this->object->setPluginVersion($expected);
        $result = $this->object->getPluginVersion();

        $this->assertEquals($expected,$result);
    }

    public function testGetPluginVersion()
    {
        $expected = 'test';
        $this->object->setPluginVersion($expected);
        $result = $this->object->getPluginVersion();

        $this->assertEquals($expected,$result);
    }

    public function testSetLtiParams()
    {
        $pluginversion = 1;
        $integrationversion = 2;
        $params = array();
        $this->object->setPluginVersion($pluginversion);
        $this->object->setIntegrationVersion($integrationversion);

        $this->object->setLtiParams($params);
        $result = $this->object->getLtiParams();

        $this->assertEquals($integrationversion,$result["custom_integration_version"]);
        $this->assertEquals($pluginversion,$result["custom_plugin_version"]);
    }
}
