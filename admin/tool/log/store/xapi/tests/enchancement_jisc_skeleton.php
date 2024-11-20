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

namespace logstore_xapi;

use logstore_xapi\task\emit_task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/admin/tool/log/store/xapi/lib.php');

/**
 * Test case skeleton for the jisc enhancements.
 *
 * @package    logstore_xapi
 * @author     László Záborski <laszlo.zaborski@learningpool.com>
 * @copyright  2020 Learning Pool Ltd (http://learningpool.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class enchancement_jisc_skeleton extends \advanced_testcase {
    /**
     * @var int Multiple test number.
     */
    protected $multipletestnumber = 5;

    /**
     * @var int Generated history-log events numbers
     */
    protected $generatedhistorylog = 11;

    /**
     * @var int Generated xapi-log events numbers
     */
    protected $generatedxapilog = 1;

    /**
     * @var array Form defaults.
     */
    protected $formdefaults = [
        'datefrom' => XAPI_REPORT_DATEFROM_DEFAULT,
        'dateto' => XAPI_REPORT_DATETO_DEFAULT,
        'eventcontext' => XAPI_REPORT_EVENTCONTEXT_DEFAULT,
        'eventnames' => XAPI_REPORT_EVENTNAMES_DEFAULT,
        'errortype' => XAPI_REPORT_ERROTYPE_DEFAULT,
        'resend' => XAPI_REPORT_RESEND_FALSE,
        'response' => XAPI_REPORT_RESPONSE_DEFAULT,
        'username' => XAPI_REPORT_USERNAME_DEFAULT,
    ];

    /**
     * This method is called before each test.
     */
    protected function setUp(): void {
        global $CFG;

        parent::setUp();

        require($CFG->dirroot . '/version.php');

        if (empty($version)) {
            return;
        }

        // From Moodle 3.9 an extra event has been added.
        if ($version >= 2020061500) {
            $this->generatedhistorylog = 12;
            $this->generatedxapilog = 2;
        }
    }

    /**
     * Investigate given counts.
     *
     * @param stdClass $counts
     */
    protected function assert_store_tables(\stdClass $counts) {
        global $DB;

        if (isset($counts->logstore_standard_log)) {
            $logs = $DB->get_records('logstore_standard_log', array(), 'id ASC');
            $this->assertCount($counts->logstore_standard_log, $logs);
        }

        if (isset($counts->logstore_xapi_log)) {
            $logs = $DB->get_records('logstore_xapi_log', array(), 'id ASC');
            $this->assertCount($counts->logstore_xapi_log, $logs);
        }

        if (isset($counts->logstore_xapi_failed_log)) {
            $logs = $DB->get_records('logstore_xapi_failed_log', array(), 'id ASC');
            $this->assertCount($counts->logstore_xapi_failed_log, $logs);
        }
    }

    /**
     * Generate log data.
     *
     * @param testing_data_generator $generator
     * @return bool|int generated record id or false
     */
    protected function add_test_log_data(\testing_data_generator $generator) {
        global $DB;

        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);

        $record = (object)array(
            'eventname' => '\core\event\course_viewed',
            'component' => 'core',
            'action' => 'viewed',
            'target' => 'course',
            'crud' => 'r',
            'edulevel' => 2,
            'contextid' => $context->id,
            'contextlevel' => $context->contextlevel,
            'contextinstanceid' => $context->instanceid,
            'userid' => $user->id,
            'timecreated' => time()
        );
        $record->logstorestandardlogid = $DB->insert_record('logstore_standard_log', $record);
        $record->type = 0;

        return $DB->insert_record('logstore_xapi_log', $record, false);
    }

    /**
     * Prepare log store for working.
     *
     * @param stdClass $expectedcount
     *          three params possibility: logstore_standard_log, logstore_xapi_log, logstore_xapi_failed_log
     */
    protected function prepare_log_stores_for_logging($expectedcount) {
        // Enable log stores.
        $this->preventResetByRollback();
        set_config('enabled_stores', 'logstore_standard,logstore_xapi', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');
        set_config('logguests', 1, 'logstore_standard');
        set_config('buffersize', 0, 'logstore_xapi');
        set_config('logguests', 1, 'logstore_xapi');

        // We have only one readers.
        $manager = get_log_manager(true);
        $stores = $manager->get_readers();
        $this->assertCount(1, $stores);

        // But both are writter.
        $store = new \logstore_standard\log\store($manager);
        $this->assertInstanceOf('logstore_standard\log\store', $store);
        $this->assertInstanceOf('tool_log\log\writer', $store);
        $this->assertTrue($store->is_logging());

        $store = new \logstore_xapi\log\store($manager);
        $this->assertInstanceOf('logstore_xapi\log\store', $store);
        $this->assertInstanceOf('tool_log\log\writer', $store);
        $this->assertTrue($store->is_logging());

        // We don't have records in store tables.
        $expectedcount = new \stdClass();
        $expectedcount->logstore_standard_log = 0;
        $expectedcount->logstore_xapi_log = 0;
        $expectedcount->logstore_xapi_failed_log = 0;
        $this->assert_store_tables($expectedcount);
    }

    /**
     * Validate submitted form data.
     *
     * @param object $data Form data.
     */
    protected function validate_submitted_data($data) {
        foreach ($this->simulatedsubmitteddata as $key => $value) {
            if ($key == 'id') {
                continue;
            }
            $this->assertObjectHasAttribute($key, $data);

            if ($key == 'eventnames') {
                $this->assertIsArray($value);
                $this->assertIsArray($data->$key);
                $actual = $data->$key;
                $this->assertEquals($value[0], $actual[0]);
                continue;
            }
            $this->assertEquals($value, $data->$key);
        }
    }

    /**
     * General test for checking stores are writeable and readable.
     */
    public function test_general() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Test all plugins are disabled by this command.
        set_config('enabled_stores', '', 'tool_log');

        $manager = get_log_manager(true);
        $stores = $manager->get_readers();

        $this->assertCount(0, $stores);

        $expectedcount = new \stdClass();

        $this->prepare_log_stores_for_logging($expectedcount);

        $generator = $this->getDataGenerator();
        $this->assertTrue($this->add_test_log_data($generator));

        $expectedcount->logstore_standard_log = $this->generatedhistorylog;
        $expectedcount->logstore_xapi_log = $this->generatedxapilog;
        $this->assert_store_tables($expectedcount);

    }

    /**
     * Creating minimum a single course view event to xapi logstore.
     */
    public function test_single_element() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $expectedcount = new \stdClass();

        $this->prepare_log_stores_for_logging($expectedcount);

        $generator = $this->getDataGenerator();
        $this->assertTrue($this->add_test_log_data($generator));

        $expectedcount->logstore_standard_log = $this->generatedhistorylog;
        $expectedcount->logstore_xapi_log = $this->generatedxapilog;
        $expectedcount->logstore_xapi_failed_log = 0;
        $this->assert_store_tables($expectedcount);

        // Run emit_task silently.
        set_debugging(DEBUG_NONE);
        $task = new emit_task();
        ob_start();
        $task->execute();
        ob_end_clean();

        unset($expectedcount->logstore_standard_log);
        $expectedcount->logstore_xapi_log = 0;
        $expectedcount->logstore_xapi_failed_log = $this->generatedxapilog;
        $this->assert_store_tables($expectedcount);
    }

    /**
     * Creating multiple course view events to xapi logstore.
     * Record number depends on $multipletestnumber.
     */
    public function test_multiple_elements() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $expectedcount = new \stdClass();

        $this->prepare_log_stores_for_logging($expectedcount);

        $generator = $this->getDataGenerator();

        for ($i = 1; $i <= $this->multipletestnumber; $i++) {
            $this->assertTrue($this->add_test_log_data($generator));
        }

        unset($expectedcount->logstore_standard_log);
        $expectedcount->logstore_xapi_log = $this->multipletestnumber * $this->generatedxapilog;
        $expectedcount->logstore_xapi_failed_log = 0;
        $this->assert_store_tables($expectedcount);

        // Run emit_task silently.
        set_debugging(DEBUG_NONE);
        $task = new emit_task();
        ob_start();
        $task->execute();
        ob_end_clean();

        $expectedcount->logstore_xapi_log = 0;
        $expectedcount->logstore_xapi_failed_log = $this->multipletestnumber * $this->generatedxapilog;
        $this->assert_store_tables($expectedcount);
    }
}
