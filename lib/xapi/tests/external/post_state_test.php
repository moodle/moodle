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
use core_xapi\test_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for xAPI post state webservice.
 *
 * @package    core_xapi
 * @covers     \core_xapi\external\post_state
 * @since      Moodle 4.2
 * @copyright  2023 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_state_test extends externallib_advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/xapi/tests/helper.php');
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

        // Perform test.
        $data = test_helper::create_state();
        $this->post_state_data($component, $data, $expected);
    }

    /**
     * Data provider for the test_component_names tests.
     *
     * @return  array
     */
    public function components_provider() : array {
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
     * Testing invalid agent.
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
        $data = test_helper::create_state($info);
        $this->post_state_data('fake_component', $data, null);
    }

    /**
     * Testing valid/invalid state.
     *
     * @dataProvider states_provider
     * @param string $stateid The xAPI state id.
     * @param string|null $expected Expected results.
     * @return void
     */
    public function test_post_state(string $stateid, ?string $expected): void {
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();

        // Perform test.
        $info = [
            'stateid' => $stateid,
        ];
        $data = test_helper::create_state($info);
        $this->post_state_data('fake_component', $data, $expected);
    }

    /**
     * Data provider for the test_post_state tests.
     *
     * @return array
     */
    public function states_provider() : array {
        return [
            'Empty stateid' => [
                'stateid' => '',
                'expected' => 'true',
            ],
            'Valid stateid (any value but paella)' => [
                'stateid' => 'sangria',
                'expected' => 'true',
            ],
            'Invalid stateid' => [
                'stateid' => 'paella',
                'expected' => null,
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
     * @return post_state the external class
     */
    private function get_external_class(): post_state {
        $ws = new class extends post_state {
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
     * This function do all checks from a standard post_state request.
     *
     * The reason for this function is because states crafting (special in error
     * scenarios) is complicated to do via data providers because every test need a specific
     * testing conditions. For this reason alls tests creates a scenario and then uses this
     * function to check the results.
     *
     * @param string $component component name
     * @param state $data data to encode and send to post_state
     * @param string $expected expected results (if null an exception is expected)
     */
    private function post_state_data(string $component, state $data, ?string $expected): void {
        global $DB;

        // Get current states in database.
        $currentstates = $DB->count_records('xapi_states');

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
            json_encode($data->jsonSerialize()),
            $data->get_registration()
        );
        $result = external_api::clean_returnvalue($external::execute_returns(), $result);

        // Check the state has been saved with the expected values.
        $this->assertTrue($result);
        $records = $DB->get_records('xapi_states');
        $this->assertCount($currentstates + 1, $records);
        $record = reset($records);
        $this->assertEquals($component, $record->component);
        $this->assertEquals($data->get_activity_id(), $record->itemid);
        $this->assertEquals($data->get_user()->id, $record->userid);
        $this->assertEquals(json_encode($data->jsonSerialize()), $record->statedata);
        $this->assertEquals($data->get_registration(), $record->registration);
    }
}
