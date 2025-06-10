<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiMembership;

class TiiMembershipTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiMembership
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
        $this->object = new TiiMembership;
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
    public function testSetMembershipId()
    {
        $expected = 12345;
        $this->object->setMembershipId($expected);
    }

    /**
     *
     */
    public function testGetMembershipId()
    {
        $expected = 12345;
        $this->object->setMembershipId($expected);
        $result = $this->object->getMembershipId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetMembershipIds()
    {
        $expected = array(12345,67890);
        $this->object->setMembershipIds($expected);
    }

    /**
     *
     */
    public function testGetMembershipIds()
    {
        $expected = array(12345,67890);
        $this->object->setMembershipIds($expected);
        $result = $this->object->getMembershipIds();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetUserId()
    {
        $expected = 12345;
        $this->object->setUserId($expected);
    }

    /**
     *
     */
    public function testGetUserId()
    {
        $expected = 12345;
        $this->object->setUserId($expected);
        $result = $this->object->getUserId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetClassId()
    {
        $expected = 12345;
        $this->object->setClassId($expected);
    }

    /**
     *
     */
    public function testGetClassId()
    {
        $expected = 12345;
        $this->object->setClassId($expected);
        $result = $this->object->getClassId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetRole()
    {
        $expected = "Learner";
        $this->object->setRole($expected);
    }

    /**
     *
     */
    public function testGetRole()
    {
        $input    = "Student";
        $expected = "Learner";
        $this->object->setRole($input);
        $result = $this->object->getRole();

        $this->assertEquals($expected,$result);
    }
}
