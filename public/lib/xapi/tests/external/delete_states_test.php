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

use core\context\module;
use core_external\external_api;
use core_xapi\iri;
use core_xapi\local\statement\item_activity;
use core_xapi\local\statement\item_agent;
use core_xapi\test_helper;
use core_xapi\xapi_exception;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for xAPI delete states webservice.
 *
 * @package    core_xapi
 * @covers     \core_xapi\external\delete_states
 * @since      Moodle 4.3
 * @copyright  2023 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class delete_states_test extends externallib_advanced_testcase {

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
     * @param object|null $expected expected results
     */
    public function test_component_names(string $component, ?object $expected): void {
        global $DB, $USER;
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();

        // Perform test.
        $info = [
            'agent' => item_agent::create_from_user($USER),
            'activity' => item_activity::create_from_id('12345'),
        ];
        test_helper::create_state($info, true);
        if (!empty($expected->exception)) {
            $this->expectException($expected->exception);
        }
        $this->execute($component,
            iri::generate($info['activity']->get_id(), 'activity'),
            json_encode($info['agent'])
        );

        if (isset($expected->expectedcount)) {
            $this->assertEquals($expected->expectedcount, $DB->record_exists('xapi_states', []));
        }
    }

    /**
     * This function execute the delete_states_data
     *
     * @param string $component component name
     * @param string $activityiri
     * @param string $agent
     * @param string|null $registration
     * @return array empty array
     */
    private function execute(string $component,
        string $activityiri,
        string $agent,
        ?string $registration = null
    ): void {
        $external = $this->get_external_class();
        $external::execute(
            $component,
            $activityiri,
            $agent,
            $registration
        );
    }

    /**
     * Return a xAPI external webservice class to operate.
     *
     * The test needs to fake a component in order to test without
     * using a real one. This way if in the future any component
     * implement it's xAPI handler this test will continue working.
     *
     * @return delete_states the external class
     */
    private function get_external_class(): delete_states {
        $ws = new class extends delete_states {
            /**
             * Method to override validate_component.
             *
             * @param string $component The component name in frankenstyle.
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
     * Data provider for the test_component_names tests.
     *
     * @return  array
     */
    public static function components_provider(): array {
        return [
            'Inexistent component' => [
                'component' => 'inexistent_component',
                'expected' => (object) ['exception' => xapi_exception::class],
            ],
            'Compatible component' => [
                'component' => 'fake_component',
                'expected' => (object) ['expectedcount' => 0],
            ],
            'Incompatible component' => [
                'component' => 'core_xapi',
                'expected' => (object) ['exception' => xapi_exception::class],
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
            'activity' => item_activity::create_from_id('12345'),
        ];
        test_helper::create_state($info, true);
        $this->expectException(xapi_exception::class);
        $this->execute(
            'fake_component',
            iri::generate($info['activity']->get_id(), 'activity'),
            json_encode($info['agent'])
        );
    }

    /**
     * Testing deleting states
     *
     * @dataProvider states_provider
     * @param string $testedusername
     * @param string $testedcomponent
     * @param string $testedactivityname
     * @param array $states
     * @param array $expectedstates
     * @return void
     */
    public function test_delete_states(
        string $testedusername,
        string $testedcomponent,
        string $testedactivityname,
        array $states,
        array $expectedstates
    ): void {
        global $DB;
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        $activities = [];
        $users = [];
        // Create a set of states for different users and components.
        foreach ($states as $stateinfo) {
            $params = [
                'component' => $stateinfo['component'] ?? 'mod_h5pactivity',
            ];
            $uname = $stateinfo['user'];
            $user = $users[$uname] ?? null;
            if (empty($user)) {
                $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
                $users[$uname] = $user;
            }
            $activityname = $stateinfo['activity'];
            $activity = $activities[$activityname] ?? null;
            if (empty($activity)) {
                if (empty($stateinfo['activityid'])) {
                    $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
                    $activitycontext = module::instance($activity->cmid);
                    $activities[$activityname] = item_activity::create_from_id($activitycontext->id);
                } else {
                    $activities[$activityname] = item_activity::create_from_id($stateinfo['activityid']);
                }
            }
            $params['activity'] = $activities[$activityname];
            $params['agent'] = item_agent::create_from_user($user);
            test_helper::create_state($params, true);
        }
        if (empty($users[$testedusername])) {
            $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
            $users[$testedusername] = $user;
        }
        $this->setUser($users[$testedusername]);
        $activity = $activities[$testedactivityname];
        $activityiri = iri::generate($activity->get_id(), 'activity');
        $agent = json_encode(item_agent::create_from_user($users[$testedusername]));
        $this->execute($testedcomponent,
            $activityiri,
            $agent);

        $statesleft = $DB->get_records('xapi_states');
        // Check that we have the expected leftover records.
        $this->assertCount(count($expectedstates), $statesleft);
        foreach ($expectedstates as $expectedstate) {
            $expectedactivityid = $activities[$expectedstate['activity']]->get_id();
            $expecteduserid = $users[$expectedstate['user']]->id;
            $found = false;
            foreach ($statesleft as $state) {
                if ($state->userid == $expecteduserid && $state->itemid == $expectedactivityid) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'State not found:' . json_encode($statesleft));
        }
    }

    /**
     * Data provider for the test_get_state tests.
     *
     * @return array
     */
    public static function states_provider(): array {
        return [
            'Activities with different users and components' => [
                'testedusername' => 'user1',
                'testedcomponent' => 'mod_h5pactivity',
                'testedactivityname' => 'Activity 1',
                'states' => [
                    [
                        'user' => 'user1',
                        'activity' => 'Activity 1',
                        'component' => 'mod_h5pactivity'
                    ],
                    [
                        'user' => 'user2',
                        'activity' => 'Activity 1',
                        'component' => 'mod_h5pactivity'
                    ],
                    [
                        'user' => 'user1',
                        'activity' => 'Activity 3',
                        'activityid' => '1',
                        'component' => 'core_xapi'
                    ],
                    [
                        'user' => 'user1',
                        'activity' => 'Activity 1',
                        'component' => 'mod_h5pactivity'
                    ],
                ],
                'expectedstates' => [
                    ['user' => 'user2', 'activity' => 'Activity 1'],
                    ['user' => 'user1', 'activity' => 'Activity 3']
                ]
            ],
            'Activities with one single user' => [
                'testedusername' => 'user1',
                'testedcomponent' => 'mod_h5pactivity',
                'testedactivityname' => 'Activity 1',
                'states' => [
                    [
                        'user' => 'user1',
                        'activity' => 'Activity 1',
                        'component' => 'mod_h5pactivity'
                    ],
                    [
                        'user' => 'user1',
                        'activity' => 'Activity 1',
                        'component' => 'mod_h5pactivity'
                    ],
                    [
                        'user' => 'user1',
                        'activity' => 'Activity 1',
                        'component' => 'mod_h5pactivity'
                    ],
                ],
                'expectedstates' => []
            ],
        ];
    }
}
