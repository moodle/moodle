<?php
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Database driver test case.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Special test case for testing of DML drivers and DDL layer.
 *
 * Note: Use only 'test_table*' names when creating new tables.
 *
 * For DML/DDL developers: you can add following settings to config.php if you want to test different driver than the main one,
 *                         the reason is to allow testing of incomplete drivers that do not allow full PHPUnit environment
 *                         initialisation (the database can be empty).
 * $CFG->phpunit_extra_drivers = array(
 *      1=>array('dbtype'=>'mysqli', 'dbhost'=>'localhost', 'dbname'=>'moodle', 'dbuser'=>'root', 'dbpass'=>'', 'prefix'=>'phpu2_'),
 *      2=>array('dbtype'=>'pgsql', 'dbhost'=>'localhost', 'dbname'=>'moodle', 'dbuser'=>'postgres', 'dbpass'=>'', 'prefix'=>'phpu2_'),
 *      3=>array('dbtype'=>'sqlsrv', 'dbhost'=>'127.0.0.1', 'dbname'=>'moodle', 'dbuser'=>'sa', 'dbpass'=>'', 'prefix'=>'phpu2_'),
 * );
 * define('PHPUNIT_TEST_DRIVER')=1; //number is index in the previous array
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class database_driver_testcase extends base_testcase {
    /** @var moodle_database connection to extra database */
    private static $extradb = null;

    /** @var moodle_database used in these tests*/
    protected $tdb;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     */
    final public function __construct($name = null) {
        parent::__construct($name);

        $this->setBackupGlobals(false);
        $this->setRunTestInSeparateProcess(false);
    }

    public static function setUpBeforeClass(): void {
        global $CFG;
        parent::setUpBeforeClass();

        if (!defined('PHPUNIT_TEST_DRIVER')) {
            // use normal $DB
            return;
        }

        if (!isset($CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER])) {
            throw new exception('Can not find driver configuration options with index: '.PHPUNIT_TEST_DRIVER);
        }

        $dblibrary = empty($CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dblibrary']) ? 'native' : $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dblibrary'];
        $dbtype = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbtype'];
        $dbhost = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbhost'];
        $dbname = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbname'];
        $dbuser = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbuser'];
        $dbpass = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dbpass'];
        $prefix = $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['prefix'];
        $dboptions = empty($CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dboptions']) ? array() : $CFG->phpunit_extra_drivers[PHPUNIT_TEST_DRIVER]['dboptions'];

        $classname = "{$dbtype}_{$dblibrary}_moodle_database";
        require_once("$CFG->libdir/dml/$classname.php");
        $d = new $classname();
        if (!$d->driver_installed()) {
            throw new exception('Database driver for '.$classname.' is not installed');
        }

        $d->connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);

        self::$extradb = $d;
    }

    #[Before]
    protected function setup_extradb(): void {
        global $DB;

        if (self::$extradb) {
            $this->tdb = self::$extradb;
        } else {
            $this->tdb = $DB;
        }
    }

    #[After]
    protected function teardown_extradb(): void {
        // delete all test tables
        $dbman = $this->tdb->get_manager();
        $tables = $this->tdb->get_tables(false);
        foreach($tables as $tablename) {
            if (strpos($tablename, 'test_table') === 0) {
                $table = new xmldb_table($tablename);
                $dbman->drop_table($table);
            }
        }
    }

    public static function tearDownAfterClass(): void {
        if (self::$extradb) {
            self::$extradb->dispose();
            self::$extradb = null;
        }
        phpunit_util::reset_all_data(null);
        parent::tearDownAfterClass();
    }

    #[After]
    public function check_debugging(): void {
        // Deal with any debugging messages.
        $debugerror = phpunit_util::display_debugging_messages(true);
        $this->resetDebugging();
        if (!empty($debugerror)) {
            trigger_error('Unexpected debugging() call detected.' . "\n" . $debugerror, E_USER_NOTICE);
        }
    }

    /**
     * Return debugging messages from the current test.
     * @return array with instances having 'message', 'level' and 'stacktrace' property.
     */
    public function getDebuggingMessages() {
        return phpunit_util::get_debugging_messages();
    }

    /**
     * Clear all previous debugging messages in current test.
     */
    public function resetDebugging() {
        phpunit_util::reset_debugging();
    }

    /**
     * Assert that exactly debugging was just called once.
     *
     * Discards the debugging message if successful.
     *
     * @param null|string $debugmessage null means any
     * @param null|string $debuglevel null means any
     * @param string $message
     */
    public function assertDebuggingCalled($debugmessage = null, $debuglevel = null, $message = '') {
        $debugging = $this->getDebuggingMessages();
        $count = count($debugging);

        if ($count == 0) {
            if ($message === '') {
                $message = 'Expectation failed, debugging() not triggered.';
            }
            $this->fail($message);
        }
        if ($count > 1) {
            if ($message === '') {
                $message = 'Expectation failed, debugging() triggered '.$count.' times.';
            }
            $this->fail($message);
        }
        $this->assertEquals(1, $count);

        $debug = reset($debugging);
        if ($debugmessage !== null) {
            $this->assertSame($debugmessage, $debug->message, $message);
        }
        if ($debuglevel !== null) {
            $this->assertSame($debuglevel, $debug->level, $message);
        }

        $this->resetDebugging();
    }

    /**
     * Call when no debugging() messages expected.
     * @param string $message
     */
    public function assertDebuggingNotCalled($message = '') {
        $debugging = $this->getDebuggingMessages();
        $count = count($debugging);

        if ($message === '') {
            $message = 'Expectation failed, debugging() was triggered.';
        }
        $this->assertEquals(0, $count, $message);
    }
}
