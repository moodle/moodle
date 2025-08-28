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

use core_external\external_api;
use core_xapi\iri;
use core_xapi\local\state;
use core_xapi\local\statement\item_activity;
use core_xapi\local\statement\item_agent;
use core_xapi\test_helper;
use core_xapi\xapi_exception;

/**
 * Unit tests for xAPI get states webservice.
 *
 * @package    core_xapi
 * @covers     \core_xapi\external\get_states
 * @since      Moodle 4.2
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_states_test extends \core_external\tests\externallib_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/xapi/tests/helper.php');
        parent::setUpBeforeClass();
    }

    /**
     * Execute the get_states service from a generate state.
     *
     * @param string $component component name
     * @param state $data the original state to extract the params
     * @param string|null $since the formated timestamp or ISO 8601 date
     * @param array $override overridden params
     * @return string[] array of state ids
     */
    private function execute_service(
        string $component,
        state $data,
        ?string $since = null,
        array $override = []
    ): array {
        // Apply overrides.
        $activityiri = $override['activityiri'] ?? iri::generate($data->get_activity_id(), 'activity');
        $registration = $override['registration'] ?? $data->get_registration();
        $agent = $override['agent'] ?? $data->get_agent();
        if (!empty($override['user'])) {
            $agent = item_agent::create_from_user($override['user']);
        }

        $external = $this->get_external_class();
        $result = $external::execute(
            $component,
            $activityiri,
            json_encode($agent),
            $registration,
            $since
        );
        $result = external_api::clean_returnvalue($external::execute_returns(), $result);

        // Sorting result to make them comparable.
        sort($result);
        return $result;
    }

    /**
     * Return a xAPI external webservice class to operate.
     *
     * The test needs to fake a component in order to test without
     * using a real one. This way if in the future any component
     * implement it's xAPI handler this test will continue working.
     *
     * @return get_states the external class
     */
    private function get_external_class(): get_states {
        $ws = new class extends get_states {
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
     * Testing different component names on valid states.
     *
     * @dataProvider components_provider
     * @param string $component component name
     * @param string|null $exception expect exception
     */
    public function test_component_names(string $component, ?bool $exception): void {
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();

        // Add, at least, one xAPI state record to database.
        $data = test_helper::create_state(
            ['activity' => item_activity::create_from_id('1'), 'stateid' => 'aa'],
            true
        );

        // If no result is expected we will just incur in exception.
        if ($exception) {
            $this->expectException(xapi_exception::class);
        }

        $result = $this->execute_service($component, $data);
        $this->assertEquals(['aa'], $result);
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
                'exception' => true,
            ],
            'Compatible component' => [
                'component' => 'fake_component',
                'exception' => false,
            ],
            'Incompatible component' => [
                'component' => 'core_xapi',
                'exception' => true,
            ],
        ];
    }

    /**
     * Testing different since date formats.
     *
     * @dataProvider since_formats_provider
     * @param string|null $since the formatted timestamps
     * @param string[]|null $expected expected results
     * @param bool $exception expect exception
     */
    public function test_since_formats(?string $since, ?array $expected, bool $exception = false): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $states = $this->generate_states();

        if ($exception) {
            $this->expectException(xapi_exception::class);
        }

        $result = $this->execute_service('fake_component', $states['aa'], $since);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for the test_since_formats tests.
     *
     * @return  array
     */
    public static function since_formats_provider(): array {
        return [
            'Null date' => [
                'since' => null,
                'expected' => ['aa', 'bb', 'cc', 'dd'],
                'exception' => false,
            ],
            'Numeric timestamp' => [
                'since' => '1651100399',
                'expected' => ['aa', 'bb'],
                'exception' => false,
            ],
            'ISO 8601 format 1' => [
                'since' => '2022-04-28T06:59',
                'expected' => ['aa', 'bb'],
                'exception' => false,
            ],
            'ISO 8601 format 2' => [
                'since' => '2022-04-28T06:59:59',
                'expected' => ['aa', 'bb'],
                'exception' => false,
            ],
            'Wrong format' => [
                'since' => 'Spanish omelette without onion',
                'expected' => null,
                'exception' => true,
            ],
        ];
    }

    /**
     * Testing different activity IRI values.
     *
     * @dataProvider activity_iri_provider
     * @param string|null $activityiri
     * @param string[]|null $expected expected results
     */
    public function test_activity_iri(?string $activityiri, ?array $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $states = $this->generate_states();

        $override = ['activityiri' => $activityiri];
        $result = $this->execute_service('fake_component', $states['aa'], null, $override);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for the test_activity_iri tests.
     *
     * @return  array
     */
    public static function activity_iri_provider(): array {
        return [
            'Activity with several states' => [
                'activityiri' => iri::generate('1', 'activity'),
                'expected' => ['aa', 'bb', 'cc', 'dd'],
            ],
            'Activity with one state' => [
                'activityiri' => iri::generate('2', 'activity'),
                'expected' => ['ee'],
            ],
            'Inexistent activity' => [
                'activityiri' => iri::generate('3', 'activity'),
                'expected' => [],
            ],
        ];
    }

    /**
     * Testing different agent values.
     *
     * @dataProvider agent_values_provider
     * @param string|null $agentreference the used agent reference
     * @param string[]|null $expected expected results
     * @param bool $exception expect exception
     */
    public function test_agent_values(?string $agentreference, ?array $expected, bool $exception = false): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $states = $this->generate_states();

        if ($exception) {
            $this->expectException(xapi_exception::class);
        }

        $userreferences = [
            'current' => $states['aa']->get_user(),
            'other' => $this->getDataGenerator()->create_user(),
        ];

        $override = [
            'user' => $userreferences[$agentreference],
        ];
        $result = $this->execute_service('fake_component', $states['aa'], null, $override);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for the test_agent_values tests.
     *
     * @return  array
     */
    public static function agent_values_provider(): array {
        return [
            'Current user' => [
                'agentreference' => 'current',
                'expected' => ['aa', 'bb', 'cc', 'dd'],
                'exception' => false,
            ],
            'Other user' => [
                'agentreference' => 'other',
                'expected' => null,
                'exception' => true,
            ],
        ];
    }

    /**
     * Testing different registration values.
     *
     * @dataProvider registration_values_provider
     * @param string|null $registration
     * @param string[]|null $expected expected results
     */
    public function test_registration_values(?string $registration, ?array $expected): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $states = $this->generate_states();

        $override = ['registration' => $registration];
        $result = $this->execute_service('fake_component', $states['aa'], null, $override);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for the test_registration_values tests.
     *
     * @return  array
     */
    public static function registration_values_provider(): array {
        return [
            'Null registration' => [
                'registration' => null,
                'expected' => ['aa', 'bb', 'cc', 'dd'],
            ],
            'Registration with one state id' => [
                'registration' => 'reg2',
                'expected' => ['cc'],
            ],
            'Registration with two state ids' => [
                'registration' => 'reg',
                'expected' => ['bb', 'dd'],
            ],
            'Registration with no state ids' => [
                'registration' => 'invented',
                'expected' => [],
            ],
        ];
    }

    /**
     * Generate the state for the testing scenarios.
     *
     * Generate a variaty of states from several components, registrations and state ids.
     * Some of the states are registered as they are done in 27-04-2022 07:00:00 while others
     * are updated in 28-04-2022 07:00:00.
     *
     * @return state[]
     */
    private function generate_states(): array {
        global $DB;

        $testdate = \DateTime::createFromFormat('d-m-Y H:i:s', '28-04-2022 07:00:00');
        // Unix timestamp: 1651100400.
        $currenttime = $testdate->getTimestamp();

        $result = [];

        // Add a few xAPI state records to database.
        $states = [
            ['activity' => item_activity::create_from_id('1'), 'stateid' => 'aa'],
            ['activity' => item_activity::create_from_id('1'), 'registration' => 'reg', 'stateid' => 'bb'],
            ['activity' => item_activity::create_from_id('1'), 'registration' => 'reg2', 'stateid' => 'cc'],
            ['activity' => item_activity::create_from_id('1'), 'registration' => 'reg', 'stateid' => 'dd'],
            ['activity' => item_activity::create_from_id('2'), 'stateid' => 'ee'],
            ['activity' => item_activity::create_from_id('3'), 'component' => 'other', 'stateid' => 'gg'],
            ['activity' => item_activity::create_from_id('3'), 'component' => 'other', 'registration' => 'reg', 'stateid' => 'ff'],
        ];
        foreach ($states as $state) {
            $result[$state['stateid']] = test_helper::create_state($state, true);
        }

        $timepast = $currenttime - DAYSECS;
        $DB->set_field('xapi_states', 'timecreated', $timepast);
        $DB->set_field('xapi_states', 'timemodified', $timepast);
        $DB->set_field('xapi_states', 'timemodified', $currenttime, ['stateid' => 'aa']);
        $DB->set_field('xapi_states', 'timemodified', $currenttime, ['stateid' => 'bb']);
        $DB->set_field('xapi_states', 'timemodified', $currenttime, ['stateid' => 'ee']);

        return $result;
    }
}
