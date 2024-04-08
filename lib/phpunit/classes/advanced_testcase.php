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

use core\di;
use core\hook;

/**
 * Advanced PHPUnit test case customised for Moodle.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class advanced_testcase extends base_testcase {
    /** @var bool automatically reset everything? null means log changes */
    // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore
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
    final public function __construct($name = null, array $data = [], $dataname = '') {
        parent::__construct($name, $data, $dataname);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setPreserveGlobalState(false);
    }

    /**
     * Runs the bare test sequence.
     */
    final public function runBare(): void {
        global $DB;

        if (phpunit_util::$lastdbwrites != $DB->perf_get_writes()) {
            // This happens when previous test does not reset, we can not use transactions.
            $this->testdbtransaction = null;
        } else if ($DB->get_dbfamily() === 'postgres' || $DB->get_dbfamily() === 'mssql') {
            // Database must allow rollback of DDL, so no mysql here.
            $this->testdbtransaction = $DB->start_delegated_transaction();
        }

        try {
            $this->setCurrentTimeStart();
            parent::runBare();
            // Set DB reference in case somebody mocked it in test.
            $DB = phpunit_util::get_global_backup('DB');

            // Deal with any debugging messages.
            $debugerror = phpunit_util::display_debugging_messages(true);
            $this->resetDebugging();
            if (!empty($debugerror)) {
                trigger_error('Unexpected debugging() call detected.' . "\n" . $debugerror, E_USER_NOTICE);
            }
        } catch (Exception $ex) {
            $e = $ex;
        } catch (Throwable $ex) {
            // Engine errors in PHP7 throw exceptions of type Throwable (this "catch" will be ignored in PHP5).
            $e = $ex;
        }

        if (isset($e)) {
            // Cleanup after failed expectation.
            self::resetAllData();
            throw $e;
        }

        if (!$this->testdbtransaction || $this->testdbtransaction->is_disposed()) {
            $this->testdbtransaction = null;
        }

        if ($this->resetAfterTest === true) {
            if ($this->testdbtransaction) {
                $DB->force_transaction_rollback();
                phpunit_util::reset_all_database_sequences();
                phpunit_util::$lastdbwrites = $DB->perf_get_writes(); // No db reset necessary.
            }
            self::resetAllData(null);
        } else if ($this->resetAfterTest === false) {
            if ($this->testdbtransaction) {
                $this->testdbtransaction->allow_commit();
            }
            // Keep all data untouched for other tests.
        } else {
            // Reset but log what changed.
            if ($this->testdbtransaction) {
                try {
                    $this->testdbtransaction->allow_commit();
                } catch (dml_transaction_exception $e) {
                    self::resetAllData();
                    throw new coding_exception('Invalid transaction state detected in test ' . $this->getName());
                }
            }
            self::resetAllData(true);
        }

        // Reset context cache.
        context_helper::reset_caches();

        // Make sure test did not forget to close transaction.
        if ($DB->is_transaction_started()) {
            self::resetAllData();
            if (
                $this->getStatus() == PHPUnit\Runner\BaseTestRunner::STATUS_PASSED
                || $this->getStatus() == PHPUnit\Runner\BaseTestRunner::STATUS_SKIPPED
                || $this->getStatus() == PHPUnit\Runner\BaseTestRunner::STATUS_INCOMPLETE
            ) {
                throw new coding_exception('Test ' . $this->getName() . ' did not close database transaction');
            }
        }
    }

    /**
     * @deprecated since Moodle 3.10 - See MDL-67673 and MDL-64600 for more info.
     */
    protected function createXMLDataSet() {
        throw new coding_exception(__FUNCTION__ . '() is deprecated. Please use dataset_from_files() instead.');
    }

    /**
     * @deprecated since Moodle 3.10 - See MDL-67673 and MDL-64600 for more info.
     */
    protected function createCsvDataSet() {
        throw new coding_exception(__FUNCTION__ . '() is deprecated. Please use dataset_from_files() instead.');
    }

    /**
     * @deprecated since Moodle 3.10 - See MDL-67673 and MDL-64600 for more info.
     */
    protected function createArrayDataSet() {
        throw new coding_exception(__FUNCTION__ . '() is deprecated. Please use dataset_from_array() instead.');
    }

    /**
     * @deprecated since Moodle 3.10 - See MDL-67673 and MDL-64600 for more info.
     */
    protected function loadDataSet() {
        throw new coding_exception(__FUNCTION__ . '() is deprecated. Please use dataset->to_database() instead.');
    }

    /**
     * Creates a new dataset from CVS/XML files.
     *
     * This method accepts an array of full paths to CSV or XML files to be loaded
     * into the dataset. For CSV files, the name of the table which the file belongs
     * to needs to be specified. Example:
     *
     *   $fullpaths = [
     *       '/path/to/users.xml',
     *       'course' => '/path/to/courses.csv',
     *   ];
     *
     * @since Moodle 3.10
     *
     * @param array $files full paths to CSV or XML files to load.
     * @return phpunit_dataset
     */
    protected function dataset_from_files(array $files) {
        // We ignore $delimiter, $enclosure and $escape, use the default ones in your fixtures.
        $dataset = new phpunit_dataset();
        $dataset->from_files($files);
        return $dataset;
    }

    /**
     * Creates a new dataset from string (CSV or XML).
     *
     * @since Moodle 3.10
     *
     * @param string $content contents (CSV or XML) to load.
     * @param string $type format of the content to be loaded (csv or xml).
     * @param string $table name of the table which the file belongs to (only for CSV files).
     * @return phpunit_dataset
     */
    protected function dataset_from_string(string $content, string $type, ?string $table = null) {
        $dataset = new phpunit_dataset();
        $dataset->from_string($content, $type, $table);
        return $dataset;
    }

    /**
     * Creates a new dataset from PHP array.
     *
     * @since Moodle 3.10
     *
     * @param array $data array of tables, see {@see phpunit_dataset::from_array()} for supported formats.
     * @return phpunit_dataset
     */
    protected function dataset_from_array(array $data) {
        $dataset = new phpunit_dataset();
        $dataset->from_array($data);
        return $dataset;
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
        if ($this->testdbtransaction && !$this->testdbtransaction->is_disposed()) {
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
        $debugging = $this->getDebuggingMessages();
        $debugdisplaymessage = "\n" . phpunit_util::display_debugging_messages(true);
        $this->resetDebugging();

        $count = count($debugging);

        if ($count == 0) {
            if ($message === '') {
                $message = 'Expectation failed, debugging() not triggered.';
            }
            $this->fail($message);
        }
        if ($count > 1) {
            if ($message === '') {
                $message = 'Expectation failed, debugging() triggered ' . $count . ' times.' . $debugdisplaymessage;
            }
            $this->fail($message);
        }
        $this->assertEquals(1, $count);

        $message .= $debugdisplaymessage;
        $debug = reset($debugging);
        if ($debugmessage !== null) {
            $this->assertSame($debugmessage, $debug->message, $message);
        }
        if ($debuglevel !== null) {
            $this->assertSame($debuglevel, $debug->level, $message);
        }
    }

    /**
     * Asserts how many times debugging has been called.
     *
     * @param int $expectedcount The expected number of times
     * @param array $debugmessages Expected debugging messages, one for each expected message.
     * @param array $debuglevels Expected debugging levels, one for each expected message.
     * @param string $message
     * @return void
     */
    public function assertdebuggingcalledcount($expectedcount, $debugmessages = [], $debuglevels = [], $message = '') {
        if (!is_int($expectedcount)) {
            throw new coding_exception('assertDebuggingCalledCount $expectedcount argument should be an integer.');
        }

        $debugging = $this->getDebuggingMessages();
        $message .= "\n" . phpunit_util::display_debugging_messages(true);
        $this->resetDebugging();

        $this->assertEquals($expectedcount, count($debugging), $message);

        if ($debugmessages) {
            if (!is_array($debugmessages) || count($debugmessages) != $expectedcount) {
                throw new coding_exception(
                    'assertDebuggingCalledCount $debugmessages should contain ' . $expectedcount . ' messages',
                );
            }
            foreach ($debugmessages as $key => $debugmessage) {
                $this->assertSame($debugmessage, $debugging[$key]->message, $message);
            }
        }

        if ($debuglevels) {
            if (!is_array($debuglevels) || count($debuglevels) != $expectedcount) {
                throw new coding_exception(
                    'assertDebuggingCalledCount $debuglevels should contain ' . $expectedcount . ' messages',
                );
            }
            foreach ($debuglevels as $key => $debuglevel) {
                $this->assertSame($debuglevel, $debugging[$key]->level, $message);
            }
        }
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
        $message .= "\n".phpunit_util::display_debugging_messages(true);
        $this->resetDebugging();
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
     * Assert that various event methods are not using event->context
     *
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

        // Restore event->context (note that this is unreachable when the event uses context). But ok for correct events.
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
        $msg = ($message === '') ? 'Time is lower that allowed start value' : $message;
        $this->assertGreaterThanOrEqual($this->currenttimestart, $time, $msg);
        $msg = ($message === '') ? 'Time is in the future' : $message;
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
     * Override hook callbacks.
     *
     * @param string $hookname
     * @param callable $callback
     * @return void
     */
    public function redirectHook(string $hookname, callable $callback): void {
        di::get(hook\manager::class)->phpunit_redirect_hook($hookname, $callback);
    }

    /**
     * Remove all hook overrides.
     *
     * @return void
     */
    public function stopHookRedirections(): void {
        di::get(hook\manager::class)->phpunit_stop_redirections();
    }

    /**
     * Reset all database tables, restore global state and clear caches and optionally purge dataroot dir.
     *
     * @param bool $detectchanges
     *      true  - changes in global state and database are reported as errors
     *      false - no errors reported
     *      null  - only critical problems are reported as errors
     * @return void
     */
    public static function resetAllData($detectchanges = false) {
        phpunit_util::reset_all_data($detectchanges);
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
            $user = $DB->get_record('user', ['id' => $user]);
        }
        unset($user->description);
        unset($user->access);
        unset($user->preference);

        // Enusre session is empty, as it may contain caches and user specific info.
        \core\session\manager::init_empty_session();

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
            $path = '/' . $path;
        }
        if ($https) {
            if (defined('TEST_EXTERNAL_FILES_HTTPS_URL')) {
                if (!TEST_EXTERNAL_FILES_HTTPS_URL) {
                    $this->markTestSkipped('Tests using external https test files are disabled');
                }
                return TEST_EXTERNAL_FILES_HTTPS_URL . $path;
            }
            return 'https://download.moodle.org/unittest' . $path;
        }

        if (defined('TEST_EXTERNAL_FILES_HTTP_URL')) {
            if (!TEST_EXTERNAL_FILES_HTTP_URL) {
                $this->markTestSkipped('Tests using external http test files are disabled');
            }
            return TEST_EXTERNAL_FILES_HTTP_URL . $path;
        }
        return 'http://download.moodle.org/unittest' . $path;
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
            $filepath = $path . '/' . $file;
            if (strpos($file, '.') === 0) {
                // Don't check hidden files.
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

    /**
     * Wait for a second to roll over, ensures future calls to time() return a different result.
     *
     * This is implemented instead of sleep() as we do not need to wait a full second. In some cases
     * due to calls we may wait more than sleep() would have, on average it will be less.
     */
    public function waitForSecond() {
        $starttime = time();
        while (time() == $starttime) {
            usleep(50000);
        }
    }

    /**
     * Run adhoc tasks, optionally matching the specified classname.
     *
     * @param   string  $matchclass The name of the class to match on.
     * @param   int     $matchuserid The userid to match.
     */
    protected function runAdhocTasks($matchclass = '', $matchuserid = null) {
        global $DB;

        $params = [];
        if (!empty($matchclass)) {
            if (strpos($matchclass, '\\') !== 0) {
                $matchclass = '\\' . $matchclass;
            }
            $params['classname'] = $matchclass;
        }

        if (!empty($matchuserid)) {
            $params['userid'] = $matchuserid;
        }

        $lock = $this->createMock(\core\lock\lock::class);
        $cronlock = $this->createMock(\core\lock\lock::class);

        $tasks = $DB->get_recordset('task_adhoc', $params);
        foreach ($tasks as $record) {
            // Note: This is for cron only.
            // We do not lock the tasks.
            $task = \core\task\manager::adhoc_task_from_record($record);

            $user = null;
            if ($userid = $task->get_userid()) {
                // This task has a userid specified.
                $user = \core_user::get_user($userid);

                // User found. Check that they are suitable.
                \core_user::require_active_user($user, true, true);
            }

            $task->set_lock($lock);
            $cronlock->release();

            \core\cron::prepare_core_renderer();
            \core\cron::setup_user($user);

            $task->execute();
            \core\task\manager::adhoc_task_complete($task);

            unset($task);
        }
        $tasks->close();
    }

    /**
     * Run adhoc tasks.
     */
    protected function run_all_adhoc_tasks(): void {
        // Run the adhoc task.
        while ($task = \core\task\manager::get_next_adhoc_task(time())) {
            $task->execute();
            \core\task\manager::adhoc_task_complete($task);
        }
    }

    /**
     * Mock the clock with an incrementing clock.
     *
     * @param null|int $starttime
     * @return \incrementing_clock
     */
    public function mock_clock_with_incrementing(
        ?int $starttime = null,
    ): \incrementing_clock {
        require_once(dirname(__DIR__, 2) . '/testing/classes/incrementing_clock.php');
        $clock = new \incrementing_clock($starttime);

        \core\di::set(\core\clock::class, $clock);

        return $clock;
    }

    /**
     * Mock the clock with a frozen clock.
     *
     * @param null|int $time
     * @return \frozen_clock
     */
    public function mock_clock_with_frozen(
        ?int $time = null,
    ): \frozen_clock {
        require_once(dirname(__DIR__, 2) . '/testing/classes/frozen_clock.php');
        $clock = new \frozen_clock($time);

        \core\di::set(\core\clock::class, $clock);

        return $clock;
    }

    /**
     * Add a mocked plugintype to Moodle.
     *
     * A new plugintype name must be provided with a path to the plugintype's root.
     *
     * Please note that tests calling this method must be run in separate isolation mode.
     * Please avoid using this if at all possible.
     *
     * @param string $plugintype The name of the plugintype
     * @param string $path The path to the plugintype's root
     */
    protected function add_mocked_plugintype(
        string $plugintype,
        string $path,
    ): void {
        require_phpunit_isolation();

        $mockedcomponent = new \ReflectionClass(\core_component::class);
        $plugintypes = $mockedcomponent->getStaticPropertyValue('plugintypes');

        if (array_key_exists($plugintype, $plugintypes)) {
            throw new \coding_exception("The plugintype '{$plugintype}' already exists.");
        }

        $plugintypes[$plugintype] = $path;
        $mockedcomponent->setStaticPropertyValue('plugintypes', $plugintypes);

        $this->resetDebugging();
    }

    /**
     * Add a mocked plugin to Moodle.
     *
     * A new plugin name must be provided with a path to the plugin's root.
     * The plugin type must already exist (or have been mocked separately).
     *
     * Please note that tests calling this method must be run in separate isolation mode.
     * Please avoid using this if at all possible.
     *
     * @param string $plugintype The name of the plugintype
     * @param string $pluginname The name of the plugin
     * @param string $path The path to the plugin's root
     */
    protected function add_mocked_plugin(
        string $plugintype,
        string $pluginname,
        string $path,
    ): void {
        require_phpunit_isolation();

        $mockedcomponent = new \ReflectionClass(\core_component::class);
        $plugins = $mockedcomponent->getStaticPropertyValue('plugins');

        if (!array_key_exists($plugintype, $plugins)) {
            $plugins[$plugintype] = [];
        }

        $plugins[$plugintype][$pluginname] = $path;
        $mockedcomponent->setStaticPropertyValue('plugins', $plugins);
        $this->resetDebugging();
    }
}
