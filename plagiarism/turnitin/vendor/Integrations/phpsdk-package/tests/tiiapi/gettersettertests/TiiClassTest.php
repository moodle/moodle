<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiRubric;

class TiiClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiClass
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
        $this->object = new TiiClass;
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
    public function testSetClassIds()
    {
        $expected = array(12345,67890);
        $this->object->setClassIds($expected);
    }

    /**
     *
     */
    public function testGetClassIds()
    {
        $expected = array(12345,67890);
        $this->object->setClassIds($expected);
        $result = $this->object->getClassIds();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTitle()
    {
        $expected = "Test Title";
        $this->object->setTitle($expected);
    }

    /**
     *
     */
    public function testGetTitle()
    {
        $expected = "Test Title";
        $this->object->setTitle($expected);
        $result = $this->object->getTitle();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEndDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setEndDate($expected);
    }

    /**
     *
     */
    public function testGetEndDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setEndDate($expected);
        $result = $this->object->getEndDate();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetDateFrom()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDateFrom($expected);
    }

    /**
     *
     */
    public function testGetDateFrom()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDateFrom($expected);
        $result = $this->object->getDateFrom();

        $this->assertEquals($expected,$result);
    }

    /**
     * Test shared rubrics on a class
     */
    public function testGetSetSharedRubrics()
    {
        $expectedRubric1 = new TiiRubric();
        $expectedRubric1->setRubricId(1234);
        $expectedRubric1->setRubricName("Test Rubric");
        $expectedRubric2 = new TiiRubric();
        $expectedRubric2->setRubricId(4321);
        $expectedRubric2->setRubricName("Test Rubric 2");

        $this->object->setSharedRubrics('[{ "RubricId": 1234, "RubricName": "Test Rubric" } , { "RubricId": 4321, "RubricName": "Test Rubric 2" }]');
        $rubric = $this->object->getSharedRubrics();

        $this->assertEquals($expectedRubric1->getRubricId(), $rubric[0]->getRubricId());
        $this->assertEquals($expectedRubric1->getRubricName(), $rubric[0]->getRubricName());
        $this->assertEquals($expectedRubric2->getRubricId(), $rubric[1]->getRubricId());
        $this->assertEquals($expectedRubric2->getRubricName(), $rubric[1]->getRubricName());
    }

}
