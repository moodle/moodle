<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use PHPUnit\Framework\TestCase;

/**
 * A base SSP test case that takes care of removing global state prior to test runs
 */
class ClearStateTestCase extends TestCase
{
    /**
     * Used for managing and clearing state
     * @var StateClearer
     */
    protected static $stateClearer;


    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        if (!self::$stateClearer) {
            self::$stateClearer = new StateClearer();
            self::$stateClearer->backupGlobals();
        }
    }


    /**
     * @return void
     */
    protected function setUp()
    {
        self::clearState();
    }


    /**
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::clearState();
    }


    /**
     * Clear any SSP global state to reduce spill over between tests.
     * @return void
     */
    public static function clearState(): void
    {
        self::$stateClearer->clearGlobals();
        self::$stateClearer->clearSSPState();
    }
}
