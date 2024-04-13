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

namespace core_xapi\external;

use core_xapi\xapi_exception;
use core_xapi\local\statement\item_agent;
use externallib_advanced_testcase;
use core_external\external_api;
use core_xapi\iri;
use core_xapi\local\state;
use core_xapi\local\statement\item_activity;
use core_xapi\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for xAPI get state webservice.
 *
 * @package    core_xapi
 * @covers     \core_xapi\external\get_state
 * @since      Moodle 4.2
 * @copyright  2023 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_state_test extends externallib_advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/xapi/tests/helper.php');
        parent::setUpBeforeClass();
    }

    /**
     * Testing different component names on valid states.
     *
     * @dataProvider components_provider
     * @param string $component component name
     * @param string|null $expected expected results
     */
    public function test_component_names(string $component, ?string $expected): void {
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();

        // Add, at least, one xAPI state record to database.
        $data = test_helper::create_state([], true);

        // Perform test.
        $this->get_state_data($component, $data, $expected);
    }

    /**
     * Data provider for the test_component_names tests.
     *
     * @return  array
     */
    public static function components_provider(): array {
        return [
            'Inexistent component' => [
                'component' => 'inexistent_component',
                'expected' => null,
            ],
            'Compatible component' => [
                'component' => 'fake_component',
                'expected' => 'true',
            ],
            'Incompatible component' => [
                'component' => 'core_xapi',
                'expected' => null,
            ],
        ];
    }

    /**
     * Testing invalid agent for get_state.
     *
     */
    public function test_invalid_agent(): void {
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $other = $this->getDataGenerator()->create_user();

        // Invalid agent (use different user, instead of the current one).
        $info = [
            'agent' => item_agent::create_from_user($other),
        ];
        $data = test_helper::create_state($info, true);
        $this->get_state_data('fake_component', $data, null);
    }

    /**
     * Testing valid/invalid state.
     *
     * @dataProvider states_provider
     * @param array $info The xAPI state information (to override default values).
     * @param string $expected Expected results.
     */
    public function test_get_state(array $info, string $expected): void {
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $component = $info['component'] ?? 'fake_component';
        $params = [];
        if ($component === 'mod_h5pactivity') {
            // For the mod_h5pactivity component, the activity needs to be created too.
            $course = $this->getDataGenerator()->create_course();
            $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
            $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);

            $activitycontext = \context_module::instance($activity->cmid);
            $info['activity'] = item_activity::create_from_id($activitycontext->id);
            $params['activity'] = $info['activity'];
            $this->setUser($user);
        }

        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state($params, true);

        // Perform test.
        $data = test_helper::create_state($info);
        $component = $info['component'] ?? 'fake_component';
        $this->get_state_data($component, $data, $expected);
    }

    /**
     * Data provider for the test_get_state tests.
     *
     * @return array
     */
    public static function states_provider(): array {
        return [
            'Existing and valid state' => [
                'info' => [],
                'expected' => 'true',
            ],
            'No state (wrong activityid)' => [
                'info' => ['activity' => item_activity::create_from_id('1')],
                'expected' => 'false',
            ],
            'No state (wrong stateid)' => [
                'info' => ['stateid' => 'food'],
                'expected' => 'false',
            ],
            'No state (wrong component)' => [
                'info' => ['component' => 'mod_h5pactivity'],
                'expected' => 'false',
            ],
        ];
    }

    /**
     * Return a xAPI external webservice class to operate.
     *
     * The test needs to fake a component in order to test without
     * using a real one. This way if in the future any component
     * implement it's xAPI handler this test will continue working.
     *
     * @return get_state the external class
     */
    private function get_external_class(): get_state {
        $ws = new class extends get_state {
            /**
             * Method to override validate_component.
             *
             * @param string $component  The component name in frankenstyle.
             */
            protected static function validate_component(string $component): void {
                if ($component != 'fake_component') {
                    parent::validate_component($component);
                }
            }
        };
        return $ws;
    }

    /**
     * This function do all checks from a standard get_state request.
     *
     * The reason for this function is because states crafting (special in error
     * scenarios) is complicated to do via data providers because every test need a specific
     * testing conditions. For this reason alls tests creates a scenario and then uses this
     * function to check the results.
     *
     * @param string $component component name
     * @param state $data data to encode and send to get_state
     * @param string $expected expected results (if null an exception is expected)
     */
    private function get_state_data(string $component, state $data, ?string $expected): void {
        // When no result is expected, an exception will be incurred.
        if (is_null($expected)) {
            $this->expectException(xapi_exception::class);
        }

        $external = $this->get_external_class();
        $result = $external::execute(
            $component,
            iri::generate($data->get_activity_id(), 'activity'),
            json_encode($data->get_agent()),
            $data->get_state_id(),
            $data->get_registration()
        );
        $result = external_api::clean_returnvalue($external::execute_returns(), $result);

        // Check the returned state has the expected values.
        if ($expected === 'true') {
            $this->assertEquals(json_encode($data->jsonSerialize()), $result);
        } else {
            $this->assertNull($result);
        }
    }
}
