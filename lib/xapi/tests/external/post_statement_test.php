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
 * This file contains unit test related to xAPI library.
 *
 * @package    core_xapi
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_xapi\external;

use core_xapi\xapi_exception;
use core_xapi\test_helper;
use core_xapi\external\post_statement;
use core_xapi\local\statement;
use core_xapi\local\statement\item_agent;
use core_xapi\local\statement\item_group;
use core_xapi\local\statement\item_verb;
use core_xapi\local\statement\item_activity;
use externallib_advanced_testcase;
use stdClass;
use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for xAPI statement processing webservice.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_statement_test extends externallib_advanced_testcase {

    /** @var test_helper for generating valid xapi statements. */
    private $testhelper;

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
    }

    /**
     * Setup test.
     */
    public function setUp(): void {
        global $CFG;
        // We disable group actors on the test xapi_handler.
        $CFG->xapitestforcegroupactors = false;
    }

    /**
     * Return a xAPI external webservice class to operate.
     *
     * The test needs to fake a component in order to test without
     * using a real one. This way if in the future any component
     * implement it's xAPI handler this test will continue working.
     *
     * @return post_statement the external class
     */
    private function get_extenal_class(): post_statement {
        $ws = new class extends post_statement {
            protected static function validate_component(string $component): void {
                if ($component != 'fake_component') {
                    parent::validate_component($component);
                }
            }
        };
        return $ws;
    }

    /**
     * This function do all checks from a standard post_statements request.
     *
     * The reason for this function is because statements crafting (special in error
     * scenarios) is complicated to do via data providers because every test need a specific
     * testing conditions. For this reason alls tests creates a scenario and then uses this
     * function to check the results.
     *
     * @param string $component component name
     * @param mixed $data data to encode and send to post_statement
     * @param array $expected expected results (i empty an exception is expected)
     */
    private function post_statements_data(string $component, $data, array $expected) {
        global $USER;

        $testhelper = new test_helper();
        $testhelper->init_log();

        // If no result is expected we will just incur in exception.
        if (empty($expected)) {
            $this->expectException(xapi_exception::class);
        } else {
            $this->preventResetByRollback(); // Logging waits till the transaction gets committed.
        }

        $json = json_encode($data);

        $external = $this->get_extenal_class();
        $result = $external::execute($component, $json);
        $result = external_api::clean_returnvalue($external::execute_returns(), $result);

        // Check results.
        $this->assertCount(count($expected), $result);
        foreach ($expected as $key => $expect) {
            $this->assertEquals($expect, $result[$key]);
        }

        // Check log entries.
        $log = $testhelper->get_last_log_entry();
        $this->assertNotEmpty($log);

        // Validate statement information on log.
        $value = $log->get_name();
        $this->assertEquals($value, 'xAPI test statement');
        $value = $log->get_description();
        // Due to logstore limitation, event must use a real component (core_xapi).
        $this->assertEquals($value, "User '{$USER->id}' send a statement to component 'core_xapi'");
    }

    /**
     * Return a valid statement object with the params passed.
     *
     * All tests are based on craft different types os statements. This function
     * is made to provent redundant code on the test.
     *
     * @param array $items array of overriden statement items (default [])
     * @return statement the resulting statement
     */
    private function get_valid_statement(array $items = []): statement {
        global $USER;

        $actor = $items['actor'] ?? item_agent::create_from_user($USER);
        $verb = $items['verb'] ?? item_verb::create_from_id('cook');
        $object = $items['object'] ?? item_activity::create_from_id('paella');

        $statement = new statement();
        $statement->set_actor($actor);
        $statement->set_verb($verb);
        $statement->set_object($object);

        return $statement;
    }

    /**
     * Testing different component names on valid statements.
     *
     * @dataProvider components_provider
     * @param string $component component name
     * @param array $expected expected results
     */
    public function test_component_names(string $component, array $expected) {

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();

        // Perform test.
        $data = $this->get_valid_statement();
        $this->post_statements_data ($component, $data, $expected);
    }

    /**
     * Data provider for the test_component_names tests.
     *
     * @return  array
     */
    public function components_provider() : array {
        return [
            'Inexistent component' => [
                'inexistent_component', []
            ],
            'Compatible component' => [
                'fake_component', [true]
            ],
            'Incompatible component' => [
                'core_xapi', []
            ],
        ];
    }

    /**
     * Testing raw JSON encoding.
     *
     * This test is used for wrong json format and empty structures.
     *
     * @dataProvider invalid_json_provider
     * @param string $json json string to send
     */
    public function test_invalid_json(string $json) {

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();

        // Perform test.
        $testhelper = new test_helper();
        $testhelper->init_log();

        // If no result is expected we will just incur in exception.
        $this->expectException(xapi_exception::class);

        $external = $this->get_extenal_class();
        $result = $external::execute('fake_component', $json);
        $result = external_api::clean_returnvalue($external::execute_returns(), $result);
    }

    /**
     * Data provider for the test_components tests.
     *
     * @return  array
     */
    public function invalid_json_provider() : array {
        return [
            'Wrong json' => [
                'This is not { a json object /'
            ],
            'Empty string json' => [
                ''
            ],
            'Empty array json' => [
                '[]'
            ],
            'Invalid single statement json' => [
                '{"actor":{"objectType":"Agent","mbox":"noemail@moodle.org"},"verb":{"id":"InvalidVerb"}'
                .',"object":{"objectType":"Activity","id":"somethingwrong"}}'
            ],
            'Invalid multiple statement json' => [
                '[{"actor":{"objectType":"Agent","mbox":"noemail@moodle.org"},"verb":{"id":"InvalidVerb"}'
                .',"object":{"objectType":"Activity","id":"somethingwrong"}}]'
            ],
        ];
    }

    /**
     * Testing agent (user) statements.
     *
     * This function test several scenarios using different combinations
     * of statement rejection motives. Some motives produces a full batch
     * rejection (exception) and other can leed to indivual rejection on
     * each statement. For example,try to post a statement without $USER
     * in it produces a full batch rejection, while using an invalid
     * verb on one statement just reject that specific statement
     * That is the expected behaviour.
     *
     * @dataProvider statement_provider
     * @param bool $multiple if send multiple statements (adds one valid statement)
     * @param bool $validactor if the actor used is valid
     * @param bool $validverb if the verb used is valid
     * @param array $expected expected results
     */
    public function test_statements_agent(bool $multiple, bool $validactor, bool $validverb, array $expected) {
        global $USER;

        $this->resetAfterTest();

        $this->setAdminUser();

        $other = $this->getDataGenerator()->create_user();

        $info = [];

        // Setup actor.
        if ($validactor) {
            $info['actor'] = item_agent::create_from_user($USER);
        } else {
            $info['actor'] = item_agent::create_from_user($other);
        }

        // Setup verb.
        if (!$validverb) {
            $info['verb'] = item_verb::create_from_id('invalid');
        }

        $data = $this->get_valid_statement($info);

        if ($multiple) {
            $data = [
                $this->get_valid_statement(),
                $data,
            ];
        }

        // Perform test.
        $this->post_statements_data ('fake_component', $data, $expected);
    }

    /**
     * Testing group statements.
     *
     * This function test several scenarios using different combinations
     * of statement rejection motives. Some motives produces a full batch
     * rejection (exception) and other can leed to indivual rejection on
     * each statement. For example,try to post a statement without $USER
     * in it produces a full batch rejection, while using an invalid
     * verb on one statement just reject that specific statement
     * That is the expected behaviour.
     *
     * @dataProvider statement_provider
     * @param bool $multiple if send multiple statements (adds one valid statement)
     * @param bool $validactor if the actor used is valid
     * @param bool $validverb if the verb used is valid
     * @param array $expected expected results
     */
    public function test_statements_group(bool $multiple, bool $validactor, bool $validverb, array $expected) {
        global $USER, $CFG;

        $this->resetAfterTest();

        $this->setAdminUser();

        $other = $this->getDataGenerator()->create_user();

        $info = [];

        // Enable group mode in the handle.
        $CFG->xapitestforcegroupactors = true;

        // Create one course and 1 group.
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($USER->id, $course->id);
        $this->getDataGenerator()->enrol_user($other->id, $course->id);

        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $other->id));

        if ($validactor) {
            // Add $USER into a group to make group valid for processing.
            $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $USER->id));
        }
        $info['actor'] = item_group::create_from_group($group);

        // Setup verb.
        if (!$validverb) {
            $info['verb'] = item_verb::create_from_id('invalid');
        }

        $data = $this->get_valid_statement($info);

        if ($multiple) {
            $data = [
                $this->get_valid_statement(),
                $data,
            ];
        }

        // Perform test.
        $this->post_statements_data ('fake_component', $data, $expected);
    }

    /**
     * Data provider for the test_components tests.
     *
     * @return  array
     */
    public function statement_provider() : array {
        return [
            // Single statement with group statements enabled.
            'Single, Valid actor, valid verb' => [
                false, true, true, [true]
            ],
            'Single, Invalid actor, valid verb' => [
                false, false, true, []
            ],
            'Single, Valid actor, invalid verb' => [
                false, true, false, []
            ],
            'Single, Inalid actor, invalid verb' => [
                false, false, false, []
            ],
            // Multi statement with group statements enabled.
            'Multiple, Valid actor, valid verb' => [
                true, true, true, [true, true]
            ],
            'Multiple, Invalid actor, valid verb' => [
                true, false, true, []
            ],
            'Multiple, Valid actor, invalid verb' => [
                true, true, false, [true, false]
            ],
            'Multiple, Inalid actor, invalid verb' => [
                true, false, false, []
            ],
        ];
    }

    /**
     * Test posting group statements to a handler without group actor support.
     *
     * Try to use group statement in components that not support this feature
     * causes a full statements batch rejection.
     *
     * @dataProvider group_statement_provider
     * @param bool $usegroup1 if the 1st statement must be groupal
     * @param bool $usegroup2 if the 2nd statement must be groupal
     * @param array $expected expected results
     */
    public function test_group_disabled(bool $usegroup1, bool $usegroup2, array $expected) {
        global $USER;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create one course and 1 group.
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($USER->id, $course->id);
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group->id, 'userid' => $USER->id));

        $info = ['actor' => item_group::create_from_group($group)];

        $groupstatement = $this->get_valid_statement($info);
        $agentstatement = $this->get_valid_statement();

        $data = [];
        $data[] = ($usegroup1) ? $groupstatement : $agentstatement;
        $data[] = ($usegroup2) ? $groupstatement : $agentstatement;

        // Perform test.
        $this->post_statements_data ('fake_component', $data, $expected);
    }

    /**
     * Data provider for the test_components tests.
     *
     * @return  array
     */
    public function group_statement_provider() : array {
        return [
            // Single statement with group statements enabled.
            'Group statement + group statement without group support' => [
                true, true, []
            ],
            'Group statement + agent statement without group support' => [
                true, false, []
            ],
            'Agent statement + group statement without group support' => [
                true, false, []
            ],
            'Agent statement + agent statement without group support' => [
                false, false, [true, true]
            ],
        ];
    }

    /**
     * Test posting a statements batch not accepted by handler.
     *
     * If all statements from a batch are rejectes by the plugin the full
     * batch is considered rejected and an exception is returned.
     */
    public function test_full_batch_rejected() {
        $this->resetAfterTest();

        $this->setAdminUser();

        $info = ['verb' => item_verb::create_from_id('invalid')];

        $statement = $this->get_valid_statement($info);

        $data = [$statement, $statement];

        // Perform test.
        $this->post_statements_data ('fake_component', $data, []);
    }
}
