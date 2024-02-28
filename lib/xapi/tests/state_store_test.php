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

namespace core_xapi;

use core_xapi\local\statement\item_agent;
use core_xapi\local\statement\item_activity;
use advanced_testcase;

/**
 * Contains test cases for testing xAPI state store methods.
 *
 * @package    core_xapi
 * @since      Moodle 4.2
 * @covers     \core_xapi\state_store
 * @copyright  2023 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state_store_test extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
    }

    /**
     * Testing delete method.
     *
     * @dataProvider states_provider
     * @param array $info Array of overriden state data.
     * @param bool $expected Expected results.
     * @return void
     */
    public function test_state_store_delete(array $info, bool $expected): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state([], true);

        // Get current states in database.
        $currentstates = $DB->count_records('xapi_states');

        // Perform test.
        $component = $info['component'] ?? 'fake_component';
        $state = test_helper::create_state($info);
        $store = new state_store($component);
        $result = $store->delete($state);

        // Check the state has been removed.
        $records = $DB->get_records('xapi_states');
        $this->assertTrue($result);
        if ($expected) {
            $this->assertCount($currentstates - 1, $records);
        } else if ($expected === 'false') {
            $this->assertCount($currentstates, $records);
        }
    }

    /**
     * Testing get method.
     *
     * @dataProvider states_provider
     * @param array $info Array of overriden state data.
     * @param bool $expected Expected results.
     * @return void
     */
    public function test_state_store_get(array $info, bool $expected): void {
        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state([], true);

        // Perform test.
        $component = $info['component'] ?? 'fake_component';
        $state = test_helper::create_state($info);
        // Remove statedata from the state object, to guarantee the get method is working as expected.
        $state->set_state_data(null);
        $store = new state_store($component);
        $result = $store->get($state);

        // Check the returned state has the expected values.
        if ($expected) {
            $this->assertEquals(json_encode($state->jsonSerialize()), json_encode($result->jsonSerialize()));
        } else {
            $this->assertNull($result);
        }
    }

    /**
     * Data provider for the test_state_store_delete and test_state_store_get tests.
     *
     * @return array
     */
    public function states_provider(): array {
        return [
            'Existing and valid state' => [
                'info' => [],
                'expected' => true,
            ],
            'No state (wrong activityid)' => [
                'info' => ['activity' => item_activity::create_from_id('1')],
                'expected' => false,
            ],
            'No state (wrong stateid)' => [
                'info' => ['stateid' => 'food'],
                'expected' => false,
            ],
            'No state (wrong component)' => [
                'info' => ['component' => 'mod_h5pactivity'],
                'expected' => false,
            ],
        ];
    }

    /**
     * Testing put method.
     *
     * @dataProvider put_states_provider
     * @param array $info Array of overriden state data.
     * @param string $expected Expected results.
     * @return void
     */
    public function test_state_store_put(array $info, string $expected): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state([], true);

        // Get current states in database.
        $currentstates = $DB->count_records('xapi_states');

        // Perform test.
        $component = $info['component'] ?? 'fake_component';
        $state = test_helper::create_state($info);
        $store = new state_store($component);
        $result = $store->put($state);

        // Check the state has been added/updated.
        $this->assertTrue($result);
        $recordsnum = $DB->count_records('xapi_states');
        $params = [
            'component' => $component,
            'userid' => $state->get_user()->id,
            'itemid' => $state->get_activity_id(),
            'stateid' => $state->get_state_id(),
            'registration' => $state->get_registration(),
        ];
        $records = $DB->get_records('xapi_states', $params);
        $record = reset($records);
        if ($expected === 'added') {
            $this->assertEquals($currentstates + 1, $recordsnum);
            $this->assertEquals($record->timecreated, $record->timemodified);
        } else if ($expected === 'updated') {
            $this->assertEquals($currentstates, $recordsnum);
            $this->assertGreaterThanOrEqual($record->timecreated, $record->timemodified);
        }

        $this->assertEquals($component, $record->component);
        $this->assertEquals($state->get_activity_id(), $record->itemid);
        $this->assertEquals($state->get_user()->id, $record->userid);
        $this->assertEquals(json_encode($state->jsonSerialize()), $record->statedata);
        $this->assertEquals($state->get_registration(), $record->registration);
    }

    /**
     * Data provider for the test_state_store_put tests.
     *
     * @return array
     */
    public function put_states_provider(): array {
        return [
            'Update existing state' => [
                'info' => [],
                'expected' => 'updated',
            ],
            'Update existing state (change statedata)' => [
                'info' => ['statedata' => '{"progress":0,"answers":[[["BB"],[""]],[{"answers":[]}]],"answered":[true,false]}'],
                'expected' => 'updated',
            ],
            'Add state (with different itemid)' => [
                'info' => ['activity' => item_activity::create_from_id('1')],
                'expected' => 'added',
            ],
            'Add state (with different stateid)' => [
                'info' => ['stateid' => 'food'],
                'expected' => 'added',
            ],
            'Add state (with different component)' => [
                'info' => ['component' => 'mod_h5pactivity'],
                'expected' => 'added',
            ],
        ];
    }

    /**
     * Testing reset method.
     *
     * @dataProvider reset_wipe_states_provider
     * @param array $info Array of overriden state data.
     * @param int $expected The states that will be reset.
     * @return void
     */
    public function test_state_store_reset(array $info, int $expected): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $other = $this->getDataGenerator()->create_user();

        // Add a few xAPI state records to database.
        test_helper::create_state(['activity' => item_activity::create_from_id('1')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('2'), 'stateid' => 'paella'], true);
        test_helper::create_state([
            'activity' => item_activity::create_from_id('3'),
            'agent' => item_agent::create_from_user($other),
            'stateid' => 'paella',
            'registration' => 'ABC',
        ], true);
        test_helper::create_state([
            'activity' => item_activity::create_from_id('4'),
            'agent' => item_agent::create_from_user($other),
        ], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('5'), 'component' => 'my_component'], true);
        test_helper::create_state([
            'activity' => item_activity::create_from_id('6'),
            'component' => 'my_component',
            'stateid' => 'paella',
            'agent' => item_agent::create_from_user($other),
        ], true);

        // Get current states in database.
        $currentstates = $DB->count_records('xapi_states');

        // Perform test.
        $component = $info['component'] ?? 'fake_component';
        $itemid = $info['activity'] ?? null;
        $userid = (array_key_exists('agent', $info) && $info['agent'] === 'other') ? $other->id : null;
        $stateid = $info['stateid'] ?? null;
        $registration = $info['registration'] ?? null;
        $store = new state_store($component);
        $store->reset($itemid, $userid, $stateid, $registration);

        // Check the states haven't been removed.
        $this->assertCount($currentstates, $DB->get_records('xapi_states'));
        $records = $DB->get_records_select('xapi_states', 'statedata IS NULL');
        $this->assertCount($expected, $records);
    }

    /**
     * Testing wipe method.
     *
     * @dataProvider reset_wipe_states_provider
     * @param array $info Array of overriden state data.
     * @param int $expected The removed states.
     * @return void
     */
    public function test_state_store_wipe(array $info, int $expected): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $other = $this->getDataGenerator()->create_user();

        // Add a few xAPI state records to database.
        test_helper::create_state(['activity' => item_activity::create_from_id('1')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('2'), 'stateid' => 'paella'], true);
        test_helper::create_state([
            'activity' => item_activity::create_from_id('3'),
            'agent' => item_agent::create_from_user($other),
            'stateid' => 'paella',
            'registration' => 'ABC',
        ], true);
        test_helper::create_state([
            'activity' => item_activity::create_from_id('4'),
            'agent' => item_agent::create_from_user($other),
        ], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('5'), 'component' => 'my_component'], true);
        test_helper::create_state([
            'activity' => item_activity::create_from_id('6'),
            'component' => 'my_component',
            'stateid' => 'paella',
            'agent' => item_agent::create_from_user($other),
        ], true);

        // Get current states in database.
        $currentstates = $DB->count_records('xapi_states');

        // Perform test.
        $component = $info['component'] ?? 'fake_component';
        $itemid = $info['activity'] ?? null;
        $userid = (array_key_exists('agent', $info) && $info['agent'] === 'other') ? $other->id : null;
        $stateid = $info['stateid'] ?? null;
        $registration = $info['registration'] ?? null;
        $store = new state_store($component);
        $store->wipe($itemid, $userid, $stateid, $registration);

        // Check the states have been removed.
        $records = $DB->get_records('xapi_states');
        $this->assertCount($currentstates - $expected, $records);
    }

    /**
     * Data provider for the test_state_store_reset and test_state_store_wipe tests.
     *
     * @return array
     */
    public function reset_wipe_states_provider(): array {
        return [
            'With fake_component' => [
                'info' => [],
                'expected' => 4,
            ],
            'With my_component' => [
                'info' => ['component' => 'my_component'],
                'expected' => 2,
            ],
            'With unexisting_component' => [
                'info' => ['component' => 'unexisting_component'],
                'expected' => 0,
            ],
            'Existing activity' => [
                'info' => ['activity' => '1'],
                'expected' => 1,
            ],
            'Unexisting activity' => [
                'info' => ['activity' => '1111'],
                'expected' => 0,
            ],
            'Existing userid' => [
                'info' => ['agent' => 'other'],
                'expected' => 2,
            ],
            'Existing stateid' => [
                'info' => ['stateid' => 'paella'],
                'expected' => 2,
            ],
            'Unexisting stateid' => [
                'info' => ['stateid' => 'chorizo'],
                'expected' => 0,
            ],
            'Existing registration' => [
                'info' => ['registration' => 'ABC'],
                'expected' => 1,
            ],
            'Uxexisting registration' => [
                'info' => ['registration' => 'XYZ'],
                'expected' => 0,
            ],
            'Existing stateid combined with activity' => [
                'info' => ['activity' => '3', 'stateid' => 'paella'],
                'expected' => 1,
            ],
            'Uxexisting stateid combined with activity' => [
                'info' => ['activity' => '1', 'stateid' => 'paella'],
                'expected' => 0,
            ],
        ];
    }

    /**
     * Testing cleanup method.
     *
     * @return void
     */
    public function test_state_store_cleanup(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $other = $this->getDataGenerator()->create_user();

        // Add a few xAPI state records to database.
        test_helper::create_state(['activity' => item_activity::create_from_id('1')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('2')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('3')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('4')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('5'), 'component' => 'my_component'], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('6'), 'component' => 'my_component'], true);

        // Get current states in database.
        $currentstates = $DB->count_records('xapi_states');

        // Perform test.
        $component = 'fake_component';
        $store = new state_store($component);
        $store->cleanup();

        // Check no state has been removed (because the entries are not old enough).
        $this->assertEquals($currentstates, $DB->count_records('xapi_states'));

        // Make the existing state entries older.
        $timepast = time() - 2;
        $DB->set_field('xapi_states', 'timecreated', $timepast);
        $DB->set_field('xapi_states', 'timemodified', $timepast);

        // Create 1 more state, that shouldn't be removed after the cleanup.
        test_helper::create_state(['activity' => item_activity::create_from_id('7')], true);

        // Set the config to remove states older than 1 second.
        set_config('xapicleanupperiod', 1);

        // Check old states for fake_component have been removed.
        $currentstates = $DB->count_records('xapi_states');
        $store->cleanup();
        $this->assertEquals($currentstates - 4, $DB->count_records('xapi_states'));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['component' => $component]));
        $this->assertEquals(2, $DB->count_records('xapi_states', ['component' => 'my_component']));
    }

    /**
     * Testing get_state_ids method.
     *
     * @dataProvider get_state_ids_provider
     * @param string $component
     * @param string|null $itemid
     * @param string|null $registration
     * @param bool|null $since
     * @param array $expected the expected result
     * @return void
     */
    public function test_get_state_ids(
        string $component,
        ?string $itemid,
        ?string $registration,
        ?bool $since,
        array $expected,
    ): void {
        global $DB, $USER;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $other = $this->getDataGenerator()->create_user();

        // Add a few xAPI state records to database.
        $states = [
            ['activity' => item_activity::create_from_id('1'), 'stateid' => 'aa'],
            ['activity' => item_activity::create_from_id('1'), 'registration' => 'reg', 'stateid' => 'bb'],
            ['activity' => item_activity::create_from_id('1'), 'registration' => 'reg2', 'stateid' => 'cc'],
            ['activity' => item_activity::create_from_id('2'), 'registration' => 'reg', 'stateid' => 'dd'],
            ['activity' => item_activity::create_from_id('3'), 'stateid' => 'ee'],
            ['activity' => item_activity::create_from_id('4'), 'component' => 'other', 'stateid' => 'ff'],
        ];
        foreach ($states as $state) {
            test_helper::create_state($state, true);
        }

        // Make all existing state entries older except form two.
        $currenttime = time();
        $timepast = $currenttime - 5;
        $DB->set_field('xapi_states', 'timecreated', $timepast);
        $DB->set_field('xapi_states', 'timemodified', $timepast);
        $DB->set_field('xapi_states', 'timemodified', $currenttime, ['stateid' => 'aa']);
        $DB->set_field('xapi_states', 'timemodified', $currenttime, ['stateid' => 'bb']);
        $DB->set_field('xapi_states', 'timemodified', $currenttime, ['stateid' => 'dd']);

        // Perform test.
        $sincetime = ($since) ? $currenttime - 1 : null;
        $store = new state_store($component);
        $stateids = $store->get_state_ids($itemid, $USER->id, $registration, $sincetime);
        sort($stateids);

        $this->assertEquals($expected, $stateids);
    }

    /**
     * Data provider for the test_get_state_ids.
     *
     * @return array
     */
    public function get_state_ids_provider(): array {
        return [
            'empty_component' => [
                'component' => 'empty_component',
                'itemid' => null,
                'registration' => null,
                'since' => null,
                'expected' => [],
            ],
            'filter_by_itemid' => [
                'component' => 'fake_component',
                'itemid' => '1',
                'registration' => null,
                'since' => null,
                'expected' => ['aa', 'bb', 'cc'],
            ],
            'filter_by_registration' => [
                'component' => 'fake_component',
                'itemid' => null,
                'registration' => 'reg',
                'since' => null,
                'expected' => ['bb', 'dd'],
            ],
            'filter_by_since' => [
                'component' => 'fake_component',
                'itemid' => null,
                'registration' => null,
                'since' => true,
                'expected' => ['aa', 'bb', 'dd'],
            ],
            'filter_by_itemid_and_registration' => [
                'component' => 'fake_component',
                'itemid' => '1',
                'registration' => 'reg',
                'since' => null,
                'expected' => ['bb'],
            ],
            'filter_by_itemid_registration_since' => [
                'component' => 'fake_component',
                'itemid' => '1',
                'registration' => 'reg',
                'since' => true,
                'expected' => ['bb'],
            ],
            'filter_by_registration_since' => [
                'component' => 'fake_component',
                'itemid' => null,
                'registration' => 'reg',
                'since' => true,
                'expected' => ['bb', 'dd'],
            ],
        ];
    }

    /**
     * Test delete with a non numeric activity id.
     *
     * The default state store only allows integer itemids.
     *
     * @dataProvider invalid_activityid_format_provider
     * @param string $operation the method to execute
     * @param bool $usestate if the param is a state or the activity id
     */
    public function test_invalid_activityid_format(string $operation, bool $usestate = false): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $state = test_helper::create_state([
            'activity' => item_activity::create_from_id('notnumeric'),
        ]);
        $param = ($usestate) ? $state : 'notnumeric';

        $this->expectException(xapi_exception::class);
        $store = new state_store('fake_component');
        $store->$operation($param);
    }

    /**
     * Data provider for test_invalid_activityid_format.
     *
     * @return array
     */
    public function invalid_activityid_format_provider(): array {
        return [
            'delete' => [
                'operation' => 'delete',
                'usestate' => true,
            ],
            'get' => [
                'operation' => 'get',
                'usestate' => true,
            ],
            'put' => [
                'operation' => 'put',
                'usestate' => true,
            ],
            'reset' => [
                'operation' => 'reset',
                'usestate' => false,
            ],
            'wipe' => [
                'operation' => 'wipe',
                'usestate' => false,
            ],
            'get_state_ids' => [
                'operation' => 'get_state_ids',
                'usestate' => false,
            ],
        ];
    }
}
