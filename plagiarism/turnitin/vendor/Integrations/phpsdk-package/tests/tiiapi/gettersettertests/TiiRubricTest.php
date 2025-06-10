<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiRubric;

class TiiRubricTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiRubric
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
        $this->object = new TiiRubric;
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
    public function testSetRubricId()
    {
        $expected = 12345;
        $this->object->setRubricId($expected);
    }

    /**
     *
     */
    public function testGetRubricId()
    {
        $expected = 12345;
        $this->object->setRubricId($expected);
        $result = $this->object->getRubricId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetRubricName()
    {
        $expected = 'Test Rubric';
        $this->object->setRubricName($expected);
    }

    /**
     *
     */
    public function testGetRubricName()
    {
        $expected = 'Test Rubric';
        $this->object->setRubricName($expected);
        $result = $this->object->getRubricName();

        $this->assertEquals($expected,$result);
    }

	/**
	 *
	 */
	public function testSetRubricGroupName()
	{
		$expected = 'Test Rubric Group';
		$this->object->setRubricGroupName($expected);
	}

	/**
	 *
	 */
	public function testGetRubricGroupName()
	{
		$expected = 'Test Rubric Group';
		$this->object->setRubricGroupName($expected);
		$result = $this->object->getRubricGroupName();

		$this->assertEquals($expected,$result);
	}

}
