<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiForm;

class TiiFormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiForm
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
        $this->object = new TiiForm;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     *
     */
    public function testSetHasButton()
    {
        $expected = true;
        $this->object->setHasButton($expected);
    }

    /**
     *
     */
    public function testGetHasButton()
    {
        $expected = true;
        $this->object->setHasButton($expected);
        $result = $this->object->getHasButton();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetButtonText()
    {
        $expected = 'Button';
        $this->object->setButtonText($expected);
    }

    /**
     *
     */
    public function testGetButtonText()
    {
        $expected = 'Button';
        $this->object->setButtonText($expected);
        $result = $this->object->getButtonText();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetButtonStyle()
    {
        $expected = 'font-size: 2em';
        $this->object->setButtonStyle($expected);
    }

    /**
     *
     */
    public function testGetButtonStyle()
    {
        $expected = 'font-size: 2em';
        $this->object->setButtonStyle($expected);
        $result = $this->object->getButtonStyle();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetFormTarget()
    {
        $expected = '_self';
        $this->object->setFormTarget($expected);
    }

    /**
     *
     */
    public function testGetFormTarget()
    {
        $expected = '_self';
        $this->object->setFormTarget($expected);
        $result = $this->object->getFormTarget();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetWideMode()
    {
        $expected = true;
        $this->object->setWideMode($expected);
    }

    /**
     *
     */
    public function testGetWideMode()
    {
        $expected = true;
        $this->object->setWideMode($expected);
        $result = $this->object->getWideMode();

        $this->assertEquals($expected,$result);
    }

}
