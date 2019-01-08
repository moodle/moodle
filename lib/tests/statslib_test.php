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
 * Tests for ../statslib.php
 *
 * @package    core_stats
 * @category   phpunit
 * @copyright  2012 Tyler Bannister
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/statslib.php');
require_once($CFG->libdir . '/cronlib.php');
require_once(__DIR__ . '/fixtures/stats_events.php');

/**
 * Test functions that affect daily stats.
 */
class core_statslib_testcase extends advanced_testcase {
    /** The day to use for testing **/
    const DAY = 1272672000;

    /** The timezone to use for testing **/
    const TIMEZONE = 0;

    /** @var array The list of temporary tables created for the statistic calculations **/
    protected $tables = array('temp_log1', 'temp_log2', 'temp_stats_daily', 'temp_stats_user_daily');

    /** @var array The replacements to be used when loading XML files **/
    protected $replacements = null;


    /**
     * Setup function
     *   - Allow changes to CFG->debug for testing purposes.
     */
    protected function setUp() {
        global $CFG, $DB;
        parent::setUp();

        // Settings to force statistic to run during testing.
        $this->setTimezone(self::TIMEZONE);
        core_date::set_default_server_timezone();
        $CFG->statsfirstrun           = 'all';
        $CFG->statslastdaily          = 0;

        // Figure out the broken day start so I can figure out when to the start time should be.
        $time   = time();
        // This nonsense needs to be rewritten.
        $date = new DateTime('now', core_date::get_server_timezone_object());
        $offset = $date->getOffset();
        $stime  = $time + $offset;
        $stime  = intval($stime / (60*60*24)) * 60*60*24;
        $stime -= $offset;

        $shour  = intval(($time - $stime) / (60*60));

        if ($DB->record_exists('user', array('username' => 'user1'))) {
            return;
        }

        // Set up the database.
        $datagen = self::getDataGenerator();

        $user1   = $datagen->create_user(array('username'=>'user1'));
        $user2   = $datagen->create_user(array('username'=>'user2'));

        $course1 = $datagen->create_course(array('shortname'=>'course1'));
        $datagen->enrol_user($user1->id, $course1->id);

        $this->generate_replacement_list();

        // Reset between tests.
        $this->resetAfterTest();
    }

    /**
     * Function to setup database.
     *
     * @param array $dataset An array of tables including the log table.
     * @param array $tables
     */
    protected function prepare_db($dataset, $tables) {
        global $DB;

        foreach ($tables as $tablename) {
            $DB->delete_records($tablename);

            foreach ($dataset as $name => $table) {

                if ($tablename == $name) {

                    $rows = $table->getRowCount();

                    for ($i = 0; $i < $rows; $i++) {
                        $row = $table->getRow($i);

                        $DB->insert_record($tablename, $row, false, true);
                    }
                }
            }
        }
    }

    /**
     * Load dataset from XML file.
     */
    protected function generate_replacement_list() {
        global $CFG, $DB;

        if ($this->replacements !== null) {
            return;
        }

        $this->setTimezone(self::TIMEZONE);

        $guest = $DB->get_record('user', array('id' => $CFG->siteguest));
        $user1 = $DB->get_record('user', array('username' => 'user1'));
        $user2 = $DB->get_record('user', array('username' => 'user2'));

        if (($guest === false) || ($user1 === false) || ($user2 === false)) {
            trigger_error('User setup incomplete', E_USER_ERROR);
        }

        $site    = $DB->get_record('course', array('id' => SITEID));
        $course1 = $DB->get_record('course', array('shortname' => 'course1'));

        if (($site === false) || ($course1 === false)) {
            trigger_error('Course setup incomplete', E_USER_ERROR);
        }

        $start      = stats_get_base_daily(self::DAY + 3600);
        $startnolog = stats_get_base_daily(stats_get_start_from('daily'));
        $gr         = get_guest_role();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->replacements = array(
            // Start and end times.
            '[start_0]'          => $start -  14410,  // 4 hours before.
            '[start_1]'          => $start +  14410,  // 4 hours after.
            '[start_2]'          => $start +  14420,
            '[start_3]'          => $start +  14430,
            '[start_4]'          => $start + 100800, // 28 hours after.
            '[end]'              => stats_get_next_day_start($start),
            '[end_no_logs]'      => stats_get_next_day_start($startnolog),

            // User ids.
            '[guest_id]'         => $guest->id,
            '[user1_id]'         => $user1->id,
            '[user2_id]'         => $user2->id,

            // Course ids.
            '[course1_id]'       => $course1->id,
            '[site_id]'          => SITEID,

            // Role ids.
            '[frontpage_roleid]' => (int) $CFG->defaultfrontpageroleid,
            '[guest_roleid]'     => $gr->id,
            '[student_roleid]'   => $studentrole->id,
        );
    }

    /**
     * Load dataset from XML file.
     *
     * @param string $file The name of the file to load
     * @return array
     */
    protected function load_xml_data_file($file) {
        static $replacements = null;

        $raw   = $this->createXMLDataSet($file);
        $clean = new PHPUnit\DbUnit\DataSet\ReplacementDataSet($raw);

        foreach ($this->replacements as $placeholder => $value) {
            $clean->addFullReplacement($placeholder, $value);
        }

        $logs = new PHPUnit\DbUnit\DataSet\Filter($clean);
        $logs->addIncludeTables(array('log'));

        $stats = new PHPUnit\DbUnit\DataSet\Filter($clean);
        $stats->addIncludeTables(array('stats_daily', 'stats_user_daily'));

        return array($logs, $stats);
    }

    /**
     * Provides the log data for test_statslib_cron_daily.
     *
     * @return array of fixture XML log file names.
     */
    public function daily_log_provider() {
        $logfiles = array();
        $fileno = array('00', '01', '02', '03', '04', '05', '06', '07', '08');

        foreach ($fileno as $no) {
            $logfiles[] = array("statslib-test{$no}.xml");
        }

        return $logfiles;
    }

    /**
     * Set of data for test_statlibs_get_base_weekly
     *
     * @return array Dates and timezones for which the first day of the week will be calculated
     */
    public function get_base_weekly_provider() {
        return [
            [
                "startwday" => 0,
                "timezone" => 'America/Chicago',
                "timestart" => '18-03-2017 22:00',
                "expected" => '12-03-2017 00:00:00'
            ],
            [
                "startwday" => 0,
                "timezone" => 'America/Chicago',
                "date" => '25-03-2017 22:00',
                "expected" => '19-03-2017 00:00:00'
            ],
            [
                "startwday" => 1,
                "timezone" => 'Atlantic/Canary',
                "date" => '06-08-2018 22:00',
                "expected" => '06-08-2018 00:00:00'
            ],
        ];
    }

    /**
     * Compare the expected stats to those in the database.
     *
     * @param array $expected
     * @param string $output
     */
    protected function verify_stats($expected, $output = '') {
        global $DB;

        // Note: We can not use $this->assertDataSetEqual($expected, $actual) because there's no
        //       $this->getConnection() in advanced_testcase.

        foreach ($expected as $type => $table) {
            $records = $DB->get_records($type);

            $rows = $table->getRowCount();

            $message = 'Incorrect number of results returned for '. $type;

            if ($output != '') {
                $message .= "\nCron output:\n$output";
            }

            $this->assertCount($rows, $records, $message);

            for ($i = 0; $i < $rows; $i++) {
                $row   = $table->getRow($i);
                $found = 0;

                foreach ($records as $key => $record) {
                    $record = (array) $record;
                    unset($record['id']);
                    $diff = array_merge(array_diff_assoc($row, $record),
                            array_diff_assoc($record, $row));

                    if (empty($diff)) {
                        $found = $key;
                        break;
                    }
                }

                $this->assertGreaterThan(0, $found, 'Expected log '. var_export($row, true)
                                        ." was not found in $type ". var_export($records, true));
                unset($records[$found]);
            }
        }
    }

    /**
     * Test progress output when debug is on.
     */
    public function test_statslib_progress_debug() {
        set_debugging(DEBUG_ALL);
        $this->expectOutputString('1:0 ');
        stats_progress('init');
        stats_progress('1');
        $this->resetDebugging();
    }

    /**
     * Test progress output when debug is off.
     */
    public function test_statslib_progress_no_debug() {
        set_debugging(DEBUG_NONE);
        $this->expectOutputString('.');
        stats_progress('init');
        stats_progress('1');
        $this->resetDebugging();
    }

    /**
     * Test the function that gets the start date from the config.
     */
    public function test_statslib_get_start_from() {
        global $CFG, $DB;

        $dataset = $this->load_xml_data_file(__DIR__."/fixtures/statslib-test01.xml");
        $DB->delete_records('log');

        $date = new DateTime('now', core_date::get_server_timezone_object());
        $day = self::DAY - $date->getOffset();

        $CFG->statsfirstrun = 'all';
        // Allow 1 second difference in case we cross a second boundary.
        // Note: within 3 days of a DST change - -3 days != 3 * 24 hours (it may be more or less).
        $this->assertLessThanOrEqual(1, stats_get_start_from('daily') - strtotime('-3 days', time()), 'All start time');

        $this->prepare_db($dataset[0], array('log'));
        $records = $DB->get_records('log');

        $this->assertEquals($day + 14410, stats_get_start_from('daily'), 'Log entry start');

        $CFG->statsfirstrun = 'none';
        $this->assertLessThanOrEqual(1, stats_get_start_from('daily') - strtotime('-3 days', time()), 'None start time');

        $CFG->statsfirstrun = 14515200;
        $this->assertLessThanOrEqual(1, stats_get_start_from('daily') - (time() - (14515200)), 'Specified start time');

        $this->prepare_db($dataset[1], array('stats_daily'));
        $this->assertEquals($day + DAYSECS, stats_get_start_from('daily'), 'Daily stats start time');

        // New log stores.
        $this->preventResetByRollback();

        $this->assertFileExists("$CFG->dirroot/$CFG->admin/tool/log/store/standard/version.php");
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');
        get_log_manager(true);

        $this->assertEquals(0, $DB->count_records('logstore_standard_log'));

        $DB->delete_records('stats_daily');
        $CFG->statsfirstrun = 'all';
        $firstoldtime = $DB->get_field_sql('SELECT MIN(time) FROM {log}');

        $this->assertEquals($firstoldtime, stats_get_start_from('daily'));

        $time = time() - 5;
        \core_tests\event\create_executed::create(array('context' => context_system::instance()))->trigger();
        $DB->set_field('logstore_standard_log', 'timecreated', $time++, [
                'eventname' => '\\core_tests\\event\\create_executed',
            ]);

        \core_tests\event\read_executed::create(array('context' => context_system::instance()))->trigger();
        $DB->set_field('logstore_standard_log', 'timecreated', $time++, [
                'eventname' => '\\core_tests\\event\\read_executed',
            ]);

        \core_tests\event\update_executed::create(array('context' => context_system::instance()))->trigger();
        $DB->set_field('logstore_standard_log', 'timecreated', $time++, [
                'eventname' => '\\core_tests\\event\\update_executed',
            ]);

        \core_tests\event\delete_executed::create(array('context' => context_system::instance()))->trigger();
        $DB->set_field('logstore_standard_log', 'timecreated', $time++, [
                'eventname' => '\\core_tests\\event\\delete_executed',
            ]);

        $DB->set_field('logstore_standard_log', 'origin', 'web', array());
        $logs = $DB->get_records('logstore_standard_log', null, 'timecreated ASC');
        $this->assertCount(4, $logs);

        $firstnew = reset($logs);
        $this->assertGreaterThan($firstoldtime, $firstnew->timecreated);
        $DB->set_field('logstore_standard_log', 'timecreated', 10, array('id' => $firstnew->id));
        $this->assertEquals(10, stats_get_start_from('daily'));

        $DB->set_field('logstore_standard_log', 'timecreated', $firstnew->timecreated, array('id' => $firstnew->id));
        $DB->delete_records('log');
        $this->assertEquals($firstnew->timecreated, stats_get_start_from('daily'));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * Test the function that calculates the start of the day.
     *
     * NOTE: I don't think this is the way this function should work.
     *       This test documents the current functionality.
     */
    public function test_statslib_get_base_daily() {
        global $CFG;

        for ($x = 0; $x < 13; $x += 1) {
            $this->setTimezone($x);

            $start = 1272672000 - ($x * 3600);
            if ($x >= 20) {
                $start += (24 * 3600);
            }

            $this->assertEquals($start, stats_get_base_daily(1272686410), "Timezone $x check");
        }
    }

    /**
     * Test the function that gets the start of the next day.
     */
    public function test_statslib_get_next_day_start() {
        $this->setTimezone(0);
        $this->assertEquals(1272758400, stats_get_next_day_start(1272686410));

        // Try setting timezone to some place in the US.
        $this->setTimezone('America/New_York', 'America/New_York');
        // Then set the time for midnight before daylight savings.
        // 1425790800 is midnight in New York (2015-03-08) Daylight saving will occur in 2 hours time.
        // 1425873600 is midnight the next day.
        $this->assertEquals(1425873600, stats_get_next_day_start(1425790800));
        $this->assertEquals(23, ((1425873600 - 1425790800) / 60 ) / 60);
        // Then set the time for midnight before daylight savings ends.
        // 1446350400 is midnight in New York (2015-11-01) Daylight saving will finish in 2 hours time.
        // 1446440400 is midnight the next day.
        $this->assertEquals(1446440400, stats_get_next_day_start(1446350400));
        $this->assertEquals(25, ((1446440400 - 1446350400) / 60 ) / 60);
        // The next day should be normal.
        $this->assertEquals(1446526800, stats_get_next_day_start(1446440400));
        $this->assertEquals(24, ((1446526800 - 1446440400) / 60 ) / 60);
    }

    /**
     * Test the function that calculates the start of the week.
     *
     * @dataProvider get_base_weekly_provider
     * @param int $startwday Day in which the week starts (Sunday = 0)
     * @param string $timezone Default timezone
     * @param string $timestart Date and time for which the first day of the week will be obtained
     * @param string $expected Expected date of the first day of the week
     */
    public function test_statslib_get_base_weekly($startwday, $timezone, $timestart, $expected) {
        $this->setTimezone($timezone);
        $time = strtotime($timestart);
        $expected = strtotime($expected);
        set_config('calendar_startwday', $startwday);
        set_config('statslastweekly', $time);

        $weekstarttime = stats_get_base_weekly($time);

        $this->assertEquals($expected, $weekstarttime);
    }

    /**
     * Test the function that gets the action names.
     *
     * Note: The function results depend on installed modules.  The hard coded lists are the
     *       defaults for a new Moodle 2.3 install.
     */
    public function test_statslib_get_action_names() {
        $basepostactions = array (
            0 => 'add',
            1 => 'delete',
            2 => 'edit',
            3 => 'add mod',
            4 => 'delete mod',
            5 => 'edit sectionenrol',
            6 => 'loginas',
            7 => 'new',
            8 => 'unenrol',
            9 => 'update',
            10 => 'update mod',
            11 => 'upload',
            12 => 'submit',
            13 => 'submit for grading',
            14 => 'talk',
            15 => 'choose',
            16 => 'choose again',
            17 => 'record delete',
            18 => 'add discussion',
            19 => 'add post',
            20 => 'delete discussion',
            21 => 'delete post',
            22 => 'move discussion',
            23 => 'prune post',
            24 => 'update post',
            25 => 'add category',
            26 => 'add entry',
            27 => 'approve entry',
            28 => 'delete category',
            29 => 'delete entry',
            30 => 'edit category',
            31 => 'update entry',
            32 => 'end',
            33 => 'start',
            34 => 'attempt',
            35 => 'close attempt',
            36 => 'preview',
            37 => 'editquestions',
            38 => 'delete attempt',
            39 => 'manualgrade',
        );

         $baseviewactions = array (
            0 => 'view',
            1 => 'view all',
            2 => 'history',
            3 => 'view submission',
            4 => 'view feedback',
            5 => 'print',
            6 => 'report',
            7 => 'view discussion',
            8 => 'search',
            9 => 'forum',
            10 => 'forums',
            11 => 'subscribers',
            12 => 'view forum',
            13 => 'view entry',
            14 => 'review',
            15 => 'pre-view',
            16 => 'download',
            17 => 'view form',
            18 => 'view graph',
            19 => 'view report',
        );

        $postactions = stats_get_action_names('post');

        foreach ($basepostactions as $action) {
            $this->assertContains($action, $postactions);
        }

        $viewactions = stats_get_action_names('view');

        foreach ($baseviewactions as $action) {
            $this->assertContains($action, $viewactions);
        }
    }

    /**
     * Test the temporary table creation and deletion.
     */
    public function test_statslib_temp_table_create_and_drop() {
        global $DB;

        foreach ($this->tables as $table) {
            $this->assertFalse($DB->get_manager()->table_exists($table));
        }

        stats_temp_table_create();

        foreach ($this->tables as $table) {
            $this->assertTrue($DB->get_manager()->table_exists($table));
        }

        stats_temp_table_drop();

        foreach ($this->tables as $table) {
            $this->assertFalse($DB->get_manager()->table_exists($table));
        }
    }

    /**
     * Test the temporary table creation and deletion.
     *
     * @depends test_statslib_temp_table_create_and_drop
     */
    public function test_statslib_temp_table_fill() {
        global $CFG, $DB, $USER;

        $dataset = $this->load_xml_data_file(__DIR__."/fixtures/statslib-test09.xml");

        $this->prepare_db($dataset[0], array('log'));

        // This nonsense needs to be rewritten.
        $date = new DateTime('now', core_date::get_server_timezone_object());
        $start = self::DAY - $date->getOffset();
        $end   = $start + (24 * 3600);

        stats_temp_table_create();
        stats_temp_table_fill($start, $end);

        $this->assertEquals(1, $DB->count_records('temp_log1'));
        $this->assertEquals(1, $DB->count_records('temp_log2'));

        stats_temp_table_drop();

        // New log stores.
        $this->preventResetByRollback();
        stats_temp_table_create();

        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $fcontext = context_course::instance(SITEID);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->assertFileExists("$CFG->dirroot/$CFG->admin/tool/log/store/standard/version.php");
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');
        get_log_manager(true);

        $DB->delete_records('logstore_standard_log');

        \core_tests\event\create_executed::create(array('context' => $fcontext, 'courseid' => SITEID))->trigger();
        \core_tests\event\read_executed::create(array('context' => $context, 'courseid' => $course->id))->trigger();
        \core_tests\event\update_executed::create(array('context' => context_system::instance()))->trigger();
        \core_tests\event\delete_executed::create(array('context' => context_system::instance()))->trigger();

        \core\event\user_loggedin::create(
            array(
                'userid' => $USER->id,
                'objectid' => $USER->id,
                'other' => array('username' => $USER->username),
            )
        )->trigger();

        $DB->set_field('logstore_standard_log', 'timecreated', 10);

        $this->assertEquals(5, $DB->count_records('logstore_standard_log'));

        \core_tests\event\delete_executed::create(array('context' => context_system::instance()))->trigger();
        \core_tests\event\delete_executed::create(array('context' => context_system::instance()))->trigger();

        // Fake the origin of events.
        $DB->set_field('logstore_standard_log', 'origin', 'web', array());

        $this->assertEquals(7, $DB->count_records('logstore_standard_log'));

        stats_temp_table_fill(9, 11);

        $logs1 = $DB->get_records('temp_log1');
        $logs2 = $DB->get_records('temp_log2');
        $this->assertCount(5, $logs1);
        $this->assertCount(5, $logs2);
        // The order of records in the temp tables is not guaranteed...

        $viewcount = 0;
        $updatecount = 0;
        $logincount = 0;
        foreach ($logs1 as $log) {
            if ($log->course == $course->id) {
                $this->assertEquals('view', $log->action);
                $viewcount++;
            } else {
                $this->assertTrue(in_array($log->action, array('update', 'login')));
                if ($log->action === 'update') {
                    $updatecount++;
                } else {
                    $logincount++;
                }
                $this->assertEquals(SITEID, $log->course);
            }
            $this->assertEquals($user->id, $log->userid);
        }
        $this->assertEquals(1, $viewcount);
        $this->assertEquals(3, $updatecount);
        $this->assertEquals(1, $logincount);

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
        stats_temp_table_drop();
    }

    /**
     * Test the temporary table creation and deletion.
     *
     * @depends test_statslib_temp_table_create_and_drop
     */
    public function test_statslib_temp_table_setup() {
        global $DB;

        $logs = array();
        $this->prepare_db($logs, array('log'));

        stats_temp_table_create();
        stats_temp_table_setup();

        $this->assertEquals(1, $DB->count_records('temp_enroled'));

        stats_temp_table_drop();
    }

    /**
     * Test the function that clean out the temporary tables.
     *
     * @depends test_statslib_temp_table_create_and_drop
     */
    public function test_statslib_temp_table_clean() {
        global $DB;

        $rows = array(
            'temp_log1'             => array('id' => 1, 'course' => 1),
            'temp_log2'             => array('id' => 1, 'course' => 1),
            'temp_stats_daily'      => array('id' => 1, 'courseid' => 1),
            'temp_stats_user_daily' => array('id' => 1, 'courseid' => 1),
        );

        stats_temp_table_create();

        foreach ($rows as $table => $row) {
            $DB->insert_record_raw($table, $row);
            $this->assertEquals(1, $DB->count_records($table));
        }

        stats_temp_table_clean();

        foreach ($rows as $table => $row) {
            $this->assertEquals(0, $DB->count_records($table));
        }

        $this->assertEquals(1, $DB->count_records('stats_daily'));
        $this->assertEquals(1, $DB->count_records('stats_user_daily'));

        stats_temp_table_drop();
    }

    /**
     * Test the daily stats function.
     *
     * @depends test_statslib_get_base_daily
     * @depends test_statslib_get_next_day_start
     * @depends test_statslib_get_start_from
     * @depends test_statslib_temp_table_create_and_drop
     * @depends test_statslib_temp_table_setup
     * @depends test_statslib_temp_table_fill
     * @dataProvider daily_log_provider
     */
    public function test_statslib_cron_daily($xmlfile) {
        global $CFG, $DB;

        $dataset = $this->load_xml_data_file(__DIR__."/fixtures/{$xmlfile}");

        list($logs, $stats) = $dataset;
        $this->prepare_db($logs, array('log'));

        // Stats cron daily uses mtrace, turn on buffering to silence output.
        ob_start();
        stats_cron_daily(1);
        $output = ob_get_contents();
        ob_end_clean();

        $this->verify_stats($stats, $output);
    }

    /**
     * Test the daily stats function.
     *
     * @depends test_statslib_get_base_daily
     * @depends test_statslib_get_next_day_start
     */
    public function test_statslib_cron_daily_no_default_profile_id() {
        global $CFG, $DB;
        $CFG->defaultfrontpageroleid = 0;

        $course1  = $DB->get_record('course', array('shortname' => 'course1'));
        $guestid  = $CFG->siteguest;
        $start    = stats_get_base_daily(1272758400);
        $end      = stats_get_next_day_start($start);
        $fpid     = (int) $CFG->defaultfrontpageroleid;
        $gr       = get_guest_role();

        $dataset = $this->load_xml_data_file(__DIR__."/fixtures/statslib-test10.xml");

        $this->prepare_db($dataset[0], array('log'));

        // Stats cron daily uses mtrace, turn on buffering to silence output.
        ob_start();
        stats_cron_daily($maxdays=1);
        $output = ob_get_contents();
        ob_end_clean();

        $this->verify_stats($dataset[1], $output);
    }
}
