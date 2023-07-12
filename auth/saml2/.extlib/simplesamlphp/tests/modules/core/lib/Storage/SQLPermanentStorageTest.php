<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Storage;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Module\core\Storage\SQLPermanentStorage;

/**
 * Test for the SQLPermanentStorage class.
 */
class SQLPermanentStorageTest extends TestCase
{
    /** @var \SimpleSAML\Module\core\Storage\SQLPermanentStorage */
    private static $sql;


    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        // Create instance
        $config = Configuration::loadFromArray([
            'datadir' => sys_get_temp_dir(),
        ]);
        self::$sql = new SQLPermanentStorage('test', $config);
    }


    /**
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::$sql = null;
        unlink(sys_get_temp_dir() . '/sqllite/test.sqlite');
    }


    /**
     * @return void
     */
    public function testSet(): void
    {
        // Set a new value
        self::$sql->set('testtype', 'testkey1', 'testkey2', 'testvalue', 2);

        // Test getCondition
        /** @var array $result */
        $result = self::$sql->get();
        $this->assertEquals('testvalue', $result['value']);
    }


    /**
     * @return void
     */
    public function testSetOverwrite(): void
    {
        // Overwrite existing value
        self::$sql->set('testtype', 'testkey1', 'testkey2', 'testvaluemodified', 2);

        // Test that the value was actually overwriten
        $result = self::$sql->getValue('testtype', 'testkey1', 'testkey2');
        $this->assertEquals('testvaluemodified', $result);

        /** @var array $result */
        $result = self::$sql->getList('testtype', 'testkey1', 'testkey2');
        $this->assertEquals('testvaluemodified', $result[0]['value']);
    }


    /**
     * @return void
     */
    public function testNonexistentKey(): void
    {
        // Test that getting some non-existing key will return null / empty array
        $result = self::$sql->getValue('testtype_nonexistent', 'testkey1_nonexistent', 'testkey2_nonexistent');
        $this->assertNull($result);
        $result = self::$sql->getList('testtype_nonexistent', 'testkey1_nonexistent', 'testkey2_nonexistent');
        $this->assertEmpty($result);
        $result = self::$sql->get('testtype_nonexistent', 'testkey1_nonexistent', 'testkey2_nonexistent');
        $this->assertNull($result);
    }


    /**
     * @return void
     */
    public function testExpiration(): void
    {
        // Make sure the earlier created entry has expired now
        sleep(3);

        // Make sure we can't get the expired entry anymore
        $result = self::$sql->getValue('testtype', 'testkey1', 'testkey2');
        $this->assertNull($result);

        // Now add a second entry that never expires
        self::$sql->set('testtype', 'testkey1_nonexpiring', 'testkey2_nonexpiring', 'testvalue_nonexpiring', null);

        // Expire entries and verify that only the second one is still there
        self::$sql->removeExpired();
        $result = self::$sql->getValue('testtype', 'testkey1_nonexpiring', 'testkey2_nonexpiring');
        $this->assertEquals('testvalue_nonexpiring', $result);
    }


    /**
     * @return void
     */
    public function testRemove(): void
    {
        // Now remove the nonexpiring entry and make sure it's gone
        self::$sql->remove('testtype', 'testkey1_nonexpiring', 'testkey2_nonexpiring');
        $result = self::$sql->getValue('testtype', 'testkey1_nonexpiring', 'testkey2_nonexpiring');
        $this->assertNull($result);
    }
}
