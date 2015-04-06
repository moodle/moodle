<?php
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
 * Advanced test case.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Advanced PHPUnit test case customised for Moodle.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class advanced_testcase extends PHPUnit_Framework_TestCase {
    /** @var bool automatically reset everything? null means log changes */
    private $resetAfterTest;

    /** @var moodle_transaction */
    private $testdbtransaction;

    /** @var int timestamp used for current time asserts */
    private $currenttimestart;

    /**
     * Constructs a test case with the given name.
     *
     * Note: use setUp() or setUpBeforeClass() in your test cases.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    final public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setRunTestInSeparateProcess(false);
    }

    /**
     * Runs the bare test sequence.
     * @return void
     */
    final public function runBare() {
        global $DB;

        if (phpunit_util::$lastdbwrites != $DB->perf_get_writes()) {
            // this happens when previous test does not reset, we can not use transactions
            $this->testdbtransaction = null;

        } else if ($DB->get_dbfamily() === 'postgres' or $DB->get_dbfamily() === 'mssql') {
            // database must allow rollback of DDL, so no mysql here
            $this->testdbtransaction = $DB->start_delegated_transaction();
        }

        try {
            $this->setCurrentTimeStart();
            parent::runBare();
            // set DB reference in case somebody mocked it in test
            $DB = phpunit_util::get_global_backup('DB');

            // Deal with any debugging messages.
            $debugerror = phpunit_util::display_debugging_messages();
            phpunit_util::reset_debugging();
            if ($debugerror) {
                trigger_error('Unenxpected debugging() call detected.', E_USER_NOTICE);
            }

        } catch (Exception $e) {
            // cleanup after failed expectation
            phpunit_util::reset_all_data();
            throw $e;
        }

        if (!$this->testdbtransaction or $this->testdbtransaction->is_disposed()) {
            $this->testdbtransaction = null;
        }

        if ($this->resetAfterTest === true) {
            if ($this->testdbtransaction) {
                $DB->force_transaction_rollback();
                phpunit_util::reset_all_database_sequences();
                phpunit_util::$lastdbwrites = $DB->perf_get_writes(); // no db reset necessary
            }
            phpunit_util::reset_all_data(null);

        } else if ($this->resetAfterTest === false) {
            if ($this->testdbtransaction) {
                $this->testdbtransaction->allow_commit();
            }
            // keep all data untouched for other tests

        } else {
            // reset but log what changed
            if ($this->testdbtransaction) {
                try {
                    $this->testdbtransaction->allow_commit();
                } catch (dml_transaction_exception $e) {
                    phpunit_util::reset_all_data();
                    throw new coding_exception('Invalid transaction state detected in test '.$this->getName());
                }
            }
            phpunit_util::reset_all_data(true);
        }

        // make sure test did not forget to close transaction
        if ($DB->is_transaction_started()) {
            phpunit_util::reset_all_data();
            if ($this->getStatus() == PHPUnit_Runner_BaseTestRunner::STATUS_PASSED
                or $this->getStatus() == PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED
                or $this->getStatus() == PHPUnit_Runner_BaseTestRunner::STATUS_INCOMPLETE) {
                throw new coding_exception('Test '.$this->getName().' did not close database transaction');
            }
        }
    }

    /**
     * Creates a new FlatXmlDataSet with the given $xmlFile. (absolute path.)
     *
     * @param string $xmlFile
     * @return PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet
     */
    protected function createFlatXMLDataSet($xmlFile) {
        return new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet($xmlFile);
    }

    /**
     * Creates a new XMLDataSet with the given $xmlFile. (absolute path.)
     *
     * @param string $xmlFile
     * @return PHPUnit_Extensions_Database_DataSet_XmlDataSet
     */
    protected function createXMLDataSet($xmlFile) {
        return new PHPUnit_Extensions_Database_DataSet_XmlDataSet($xmlFile);
    }

    /**
     * Creates a new CsvDataSet from the given array of csv files. (absolute paths.)
     *
     * @param array $files array tablename=>cvsfile
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return PHPUnit_Extensions_Database_DataSet_CsvDataSet
     */
    protected function createCsvDataSet($files, $delimiter = ',', $enclosure = '"', $escape = '"') {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet($delimiter, $enclosure, $escape);
        foreach($files as $table=>$file) {
            $dataSet->addTable($table, $file);
        }
        return $dataSet;
    }

    /**
     * Creates new ArrayDataSet from given array
     *
     * @param array $data array of tables, first row in each table is columns
     * @return phpunit_ArrayDataSet
     */
    protected function createArrayDataSet(array $data) {
        return new phpunit_ArrayDataSet($data);
    }

    /**
     * Load date into moodle database tables from standard PHPUnit data set.
     *
     * Note: it is usually better to use data generators
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     * @return void
     */
    protected function loadDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset) {
        global $DB;

        $structure = phpunit_util::get_tablestructure();

        foreach($dataset->getTableNames() as $tablename) {
            $table = $dataset->getTable($tablename);
            $metadata = $dataset->getTableMetaData($tablename);
            $columns = $metadata->getColumns();

            $doimport = false;
            if (isset($structure[$tablename]['id']) and $structure[$tablename]['id']->auto_increment) {
                $doimport = in_array('id', $columns);
            }

            for($r=0; $r<$table->getRowCount(); $r++) {
                $record = $table->getRow($r);
                if ($doimport) {
                    $DB->import_record($tablename, $record);
                } else {
                    $DB->insert_record($tablename, $record);
                }
            }
            if ($doimport) {
                $DB->get_manager()->reset_sequence(new xmldb_table($tablename));
            }
        }
    }

    /**
     * Call this method from test if you want to make sure that
     * the resetting of database is done the slow way without transaction
     * rollback.
     *
     * This is useful especially when testing stuff that is not compatible with transactions.
     *
     * @return void
     */
    public function preventResetByRollback() {
        if ($this->testdbtransaction and !$this->testdbtransaction->is_disposed()) {
            $this->testdbtransaction->allow_commit();
            $this->testdbtransaction = null;
        }
    }

    /**
     * Reset everything after current test.
     * @param bool $reset true means reset state back, false means keep all data for the next test,
     *      null means reset state and show warnings if anything changed
     * @return void
     */
    public function resetAfterTest($reset = true) {
        $this->resetAfterTest = $reset;
    }

    /**
     * Return debugging messages from the current test.
     * @return array with instances having 'message', 'level' and 'stacktrace' property.
     */
    public function getDebuggingMessages() {
        return phpunit_util::get_debugging_messages();
    }

    /**
     * Clear all previous debugging messages in current test
     * and revert to default DEVELOPER_DEBUG level.
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
        $debugging = phpunit_util::get_debugging_messages();
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

        phpunit_util::reset_debugging();
    }

    /**
     * Call when no debugging() messages expected.
     * @param string $message
     */
    public function assertDebuggingNotCalled($message = '') {
        $debugging = phpunit_util::get_debugging_messages();
        $count = count($debugging);

        if ($message === '') {
            $message = 'Expectation failed, debugging() was triggered.';
        }
        $this->assertEquals(0, $count, $message);
    }

    /**
     * Assert that an event legacy data is equal to the expected value.
     *
     * @param mixed $expected expected data.
     * @param \core\event\base $event the event object.
     * @param string $message
     * @return void
     */
    public function assertEventLegacyData($expected, \core\event\base $event, $message = '') {
        $legacydata = phpunit_event_mock::testable_get_legacy_eventdata($event);
        if ($message === '') {
            $message = 'Event legacy data does not match expected value.';
        }
        $this->assertEquals($expected, $legacydata, $message);
    }

    /**
     * Assert that an event legacy log data is equal to the expected value.
     *
     * @param mixed $expected expected data.
     * @param \core\event\base $event the event object.
     * @param string $message
     * @return void
     */
    public function assertEventLegacyLogData($expected, \core\event\base $event, $message = '') {
        $legacydata = phpunit_event_mock::testable_get_legacy_logdata($event);
        if ($message === '') {
            $message = 'Event legacy log data does not match expected value.';
        }
        $this->assertEquals($expected, $legacydata, $message);
    }

    /**
     * Assert that an event is not using event->contxet.
     * While restoring context might not be valid and it should not be used by event url
     * or description methods.
     *
     * @param \core\event\base $event the event object.
     * @param string $message
     * @return void
     */
    public function assertEventContextNotUsed(\core\event\base $event, $message = '') {
        // Save current event->context and set it to false.
        $eventcontext = phpunit_event_mock::testable_get_event_context($event);
        phpunit_event_mock::testable_set_event_context($event, false);
        if ($message === '') {
            $message = 'Event should not use context property of event in any method.';
        }

        // Test event methods should not use event->context.
        $event->get_url();
        $event->get_description();
        $event->get_legacy_eventname();
        phpunit_event_mock::testable_get_legacy_eventdata($event);
        phpunit_event_mock::testable_get_legacy_logdata($event);

        // Restore event->context.
        phpunit_event_mock::testable_set_event_context($event, $eventcontext);
    }

    /**
     * Stores current time as the base for assertTimeCurrent().
     *
     * Note: this is called automatically before calling individual test methods.
     * @return int current time
     */
    public function setCurrentTimeStart() {
        $this->currenttimestart = time();
        return $this->currenttimestart;
    }

    /**
     * Assert that: start < $time < time()
     * @param int $time
     * @param string $message
     * @return void
     */
    public function assertTimeCurrent($time, $message = '') {
        $msg =  ($message === '') ? 'Time is lower that allowed start value' : $message;
        $this->assertGreaterThanOrEqual($this->currenttimestart, $time, $msg);
        $msg =  ($message === '') ? 'Time is in the future' : $message;
        $this->assertLessThanOrEqual(time(), $time, $msg);
    }

    /**
     * Starts message redirection.
     *
     * You can verify if messages were sent or not by inspecting the messages
     * array in the returned messaging sink instance. The redirection
     * can be stopped by calling $sink->close();
     *
     * @return phpunit_message_sink
     */
    public function redirectMessages() {
        return phpunit_util::start_message_redirection();
    }

    /**
     * Starts email redirection.
     *
     * You can verify if email were sent or not by inspecting the email
     * array in the returned phpmailer sink instance. The redirection
     * can be stopped by calling $sink->close();
     *
     * @return phpunit_message_sink
     */
    public function redirectEmails() {
        return phpunit_util::start_phpmailer_redirection();
    }

    /**
     * Starts event redirection.
     *
     * You can verify if events were triggered or not by inspecting the events
     * array in the returned event sink instance. The redirection
     * can be stopped by calling $sink->close();
     *
     * @return phpunit_event_sink
     */
    public function redirectEvents() {
        return phpunit_util::start_event_redirection();
    }

    /**
     * Cleanup after all tests are executed.
     *
     * Note: do not forget to call this if overridden...
     *
     * @static
     * @return void
     */
    public static function tearDownAfterClass() {
        phpunit_util::reset_all_data();
    }

    /**
     * Reset all database tables, restore global state and clear caches and optionally purge dataroot dir.
     * @static
     * @return void
     */
    public static function resetAllData() {
        phpunit_util::reset_all_data();
    }

    /**
     * Set current $USER, reset access cache.
     * @static
     * @param null|int|stdClass $user user record, null or 0 means non-logged-in, positive integer means userid
     * @return void
     */
    public static function setUser($user = null) {
        global $CFG, $DB;

        if (is_object($user)) {
            $user = clone($user);
        } else if (!$user) {
            $user = new stdClass();
            $user->id = 0;
            $user->mnethostid = $CFG->mnet_localhost_id;
        } else {
            $user = $DB->get_record('user', array('id'=>$user));
        }
        unset($user->description);
        unset($user->access);
        unset($user->preference);

        \core\session\manager::set_user($user);
    }

    /**
     * Set current $USER to admin account, reset access cache.
     * @static
     * @return void
     */
    public static function setAdminUser() {
        self::setUser(2);
    }

    /**
     * Set current $USER to guest account, reset access cache.
     * @static
     * @return void
     */
    public static function setGuestUser() {
        self::setUser(1);
    }

    /**
     * Change server and default php timezones.
     *
     * @param string $servertimezone timezone to set in $CFG->timezone (not validated)
     * @param string $defaultphptimezone timezone to fake default php timezone (must be valid)
     */
    public static function setTimezone($servertimezone = 'Australia/Perth', $defaultphptimezone = 'Australia/Perth') {
        global $CFG;
        $CFG->timezone = $servertimezone;
        core_date::phpunit_override_default_php_timezone($defaultphptimezone);
        core_date::set_default_server_timezone();
    }

    /**
     * Get data generator
     * @static
     * @return testing_data_generator
     */
    public static function getDataGenerator() {
        return phpunit_util::get_data_generator();
    }

    /**
     * Returns UTL of the external test file.
     *
     * The result depends on the value of following constants:
     *  - TEST_EXTERNAL_FILES_HTTP_URL
     *  - TEST_EXTERNAL_FILES_HTTPS_URL
     *
     * They should point to standard external test files repository,
     * it defaults to 'http://download.moodle.org/unittest'.
     *
     * False value means skip tests that require external files.
     *
     * @param string $path
     * @param bool $https true if https required
     * @return string url
     */
    public function getExternalTestFileUrl($path, $https = false) {
        $path = ltrim($path, '/');
        if ($path) {
            $path = '/'.$path;
        }
        if ($https) {
            if (defined('TEST_EXTERNAL_FILES_HTTPS_URL')) {
                if (!TEST_EXTERNAL_FILES_HTTPS_URL) {
                    $this->markTestSkipped('Tests using external https test files are disabled');
                }
                return TEST_EXTERNAL_FILES_HTTPS_URL.$path;
            }
            return 'https://download.moodle.org/unittest'.$path;
        }

        if (defined('TEST_EXTERNAL_FILES_HTTP_URL')) {
            if (!TEST_EXTERNAL_FILES_HTTP_URL) {
                $this->markTestSkipped('Tests using external http test files are disabled');
            }
            return TEST_EXTERNAL_FILES_HTTP_URL.$path;
        }
        return 'http://download.moodle.org/unittest'.$path;
    }

    /**
     * Recursively visit all the files in the source tree. Calls the callback
     * function with the pathname of each file found.
     *
     * @param string $path the folder to start searching from.
     * @param string $callback the method of this class to call with the name of each file found.
     * @param string $fileregexp a regexp used to filter the search (optional).
     * @param bool $exclude If true, pathnames that match the regexp will be ignored. If false,
     *     only files that match the regexp will be included. (default false).
     * @param array $ignorefolders will not go into any of these folders (optional).
     * @return void
     */
    public function recurseFolders($path, $callback, $fileregexp = '/.*/', $exclude = false, $ignorefolders = array()) {
        $files = scandir($path);

        foreach ($files as $file) {
            $filepath = $path .'/'. $file;
            if (strpos($file, '.') === 0) {
                /// Don't check hidden files.
                continue;
            } else if (is_dir($filepath)) {
                if (!in_array($filepath, $ignorefolders)) {
                    $this->recurseFolders($filepath, $callback, $fileregexp, $exclude, $ignorefolders);
                }
            } else if ($exclude xor preg_match($fileregexp, $filepath)) {
                $this->$callback($filepath);
            }
        }
    }
}
