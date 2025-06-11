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

use core_xapi\xapi_exception;
use core_xapi\local\statement;
use core_xapi\local\statement\item_agent;
use core_xapi\local\statement\item_verb;
use core_xapi\local\statement\item_activity;
use advanced_testcase;
use core_xapi\local\state;
use stdClass;

/**
 * Contains test cases for testing xAPI handler base methods.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @covers     \core_xapi\handler
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class handler_test extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test handler creation.
     */
    public function test_handler_create(): void {
        // Get an existent handler.
        $handler = handler::create('fake_component');
        $this->assertEquals(get_class($handler), 'fake_component\\xapi\\handler');

        // Get a non existent handler.
        $this->expectException(xapi_exception::class);
        $value = handler::create('potato_omelette');
    }

    /**
     * Test xAPI support.
     */
    public function test_supports_xapi(): void {
        // Get an existent handler.
        $result = handler::supports_xapi('fake_component');
        $this->assertTrue($result);

        // Get a non existent handler.
        $result = handler::supports_xapi('potato_omelette');
        $this->assertFalse($result);
    }

    /**
     * Test support group.
     */
    public function test_support_group_actor(): void {
        global $CFG;
        // Get an existent handler.
        $this->resetAfterTest();
        $handler = handler::create('fake_component');
        $this->assertEquals(get_class($handler), 'fake_component\\xapi\\handler');
        $CFG->xapitestforcegroupactors = false;
        $this->assertEquals(false, $handler->supports_group_actors());
    }

    /**
     * Test for process_statements method.
     */
    public function test_process_statements(): void {

        $this->resetAfterTest();
        $this->preventResetByRollback(); // Logging waits till the transaction gets committed.

        $user = $this->getDataGenerator()->create_user();

        $testhelper = new test_helper();
        $testhelper->init_log();

        // Generate a 2 statements array (one accepted one not).
        $statements = [];

        $statement = new statement();
        $statement->set_actor(item_agent::create_from_user($user));
        $statement->set_verb(item_verb::create_from_id('cook'));
        $statement->set_object(item_activity::create_from_id('paella'));
        $statements[] = $statement;

        $statement2 = new statement();
        $statement2->set_actor(item_agent::create_from_user($user));
        $statement2->set_verb(item_verb::create_from_id('invalid'));
        $statement2->set_object(item_activity::create_from_id('paella'));
        $statements[] = $statement2;

        $handler = handler::create('fake_component');
        $result = $handler->process_statements($statements);

        // Check results.
        $this->assertCount(2, $result);
        $this->assertEquals(true, $result[0]);
        $this->assertEquals(false, $result[1]);

        // Check log entries.
        /** @var \core_xapi\event\xapi_test_statement_post $log */
        $log = $testhelper->get_last_log_entry();
        $this->assertNotEmpty($log);

        // Validate statement information on log.
        $value = $log->get_name();
        $this->assertEquals($value, 'xAPI test statement');
        $value = $log->get_description();
        // Due to logstore limitation, event must use a real component (core_xapi).
        $this->assertEquals($value, 'User \''.$user->id.'\' send a statement to component \'core_xapi\'');
        $this->assertTrue($log->compare_statement($statement));
    }

    /**
     * Testing save_state method.
     */
    public function test_save_state(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $component = 'fake_component';
        $handler = handler::create($component);

        // Check the state has been added.
        $state = test_helper::create_state();
        $this->assertEquals(0, $DB->count_records('xapi_states'));
        $result = $handler->save_state($state);
        $this->assertTrue($result);
        $records = $DB->get_records('xapi_states');
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->check_state($component, $state, $record);

        // Check the state has been updated.
        $statedata = '{"progress":0,"answers":[[["BB"],[""]],[{"answers":[]}]],"answered":[true,false]}';
        $state->set_state_data(json_decode($statedata));
        $result = $handler->save_state($state);
        $this->assertTrue($result);
        $records = $DB->get_records('xapi_states');
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->check_state($component, $state, $record);

        // Check an exception is thrown when the state is not valid.
        $this->expectException(xapi_exception::class);
        $state = test_helper::create_state(['stateid' => 'paella']);
        $result = $handler->save_state($state);
    }

    /**
     * Testing load_state method.
     */
    public function test_load_state(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $component = 'fake_component';
        $handler = handler::create($component);

        // Check the state is not found (when there are no states).
        $state = test_helper::create_state();
        $state->set_state_data(null);
        $this->assertEquals(0, $DB->count_records('xapi_states'));
        $result = $handler->load_state($state);
        $this->assertEquals(0, $DB->count_records('xapi_states'));
        $this->assertNull($result);

        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state([], true);

        // Check the state is found when it exists.
        $result = $handler->load_state($state);
        $records = $DB->get_records('xapi_states');
        $this->assertCount(1, $records);
        $record = reset($records);
        $this->check_state($component, $state, $record);
        $this->assertEquals($state->jsonSerialize(), $result->jsonSerialize());

        // Check the state is not found when it doesn't exist.
        $state = test_helper::create_state(['activity' => item_activity::create_from_id('1')]);
        $state->set_state_data(null);
        $result = $handler->load_state($state);
        $records = $DB->get_records('xapi_states');
        $this->assertCount(1, $DB->get_records('xapi_states'));
        $this->assertNull($result);

        // Check an exception is thrown when the state is not valid.
        $this->expectException(xapi_exception::class);
        $state = test_helper::create_state(['stateid' => 'paella']);
        $result = $handler->load_state($state);
    }

    /**
     * Testing delete_state method.
     */
    public function test_delete_state(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $component = 'fake_component';
        $handler = handler::create($component);

        // Check the state is not deleted (when there are no states).
        $state = test_helper::create_state();
        $this->assertEquals(0, $DB->count_records('xapi_states'));
        $result = $handler->delete_state($state);
        $this->assertTrue($result);
        $this->assertEquals(0, $DB->count_records('xapi_states'));

        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state([], true);

        // Check the state is not deleted if the given state doesn't meet its values.
        $state2 = test_helper::create_state(['activity' => item_activity::create_from_id('1')]);
        $result = $handler->delete_state($state2);
        $this->assertTrue($result);
        $this->assertCount(1, $DB->get_records('xapi_states'));

        // Check the state is deleted if it exists.
        $result = $handler->delete_state($state);
        $this->assertTrue($result);
        $this->assertCount(0, $DB->get_records('xapi_states'));

        // Check an exception is thrown when the state is not valid.
        $this->expectException(xapi_exception::class);
        $state = test_helper::create_state(['stateid' => 'paella']);
        $result = $handler->delete_state($state);
    }

    /**
     * Testing reset_states method.
     */
    public function test_reset_states(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $component = 'fake_component';
        $handler = handler::create($component);

        // Check the state is not reset (when there are no states).
        $this->assertCount(0, $DB->get_records_select('xapi_states', 'statedata IS NULL'));
        $handler->reset_states();
        $this->assertCount(0, $DB->get_records_select('xapi_states', 'statedata IS NULL'));

        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state([], true);

        // Check the state is not reset if the given state doesn't meet its values.
        $handler->reset_states('1');
        $this->assertCount(1, $DB->get_records('xapi_states'));
        $this->assertCount(0, $DB->get_records_select('xapi_states', 'statedata IS NULL'));

        // Check the state is reset if it exists.
        $handler->reset_states();
        $this->assertCount(1, $DB->get_records('xapi_states'));
        $this->assertCount(1, $DB->get_records_select('xapi_states', 'statedata IS NULL'));

        // Check the state is reset too when using some of the given parameters.
        test_helper::create_state(['activity' => item_activity::create_from_id('1')], true);
        $handler->reset_states('1');
        $this->assertCount(2, $DB->get_records('xapi_states'));
        $this->assertCount(2, $DB->get_records_select('xapi_states', 'statedata IS NULL'));
    }

    /**
     * Testing wipe_states method.
     */
    public function test_wipe_states(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();
        $component = 'fake_component';
        $handler = handler::create($component);

        // Check the state is not wiped (when there are no states).
        $this->assertCount(0, $DB->get_records('xapi_states'));
        $handler->wipe_states();
        $this->assertCount(0, $DB->get_records('xapi_states'));

        // Add, at least, one xAPI state record to database (with the default values).
        test_helper::create_state([], true);

        // Check the state is not wiped if the given state doesn't meet its values.
        $handler->wipe_states('1');
        $this->assertCount(1, $DB->get_records('xapi_states'));

        // Check the state is wiped if it exists.
        $handler->wipe_states();
        $this->assertCount(0, $DB->get_records('xapi_states'));

        // Check the state is wiped too when using some of the given parameters.
        test_helper::create_state(['activity' => item_activity::create_from_id('1')], true);
        $this->assertCount(1, $DB->get_records('xapi_states'));
        $handler->wipe_states('1');
        $this->assertCount(0, $DB->get_records('xapi_states'));
    }

    /**
     * Check if the given state and record are equals.
     *
     * @param string $component The component name in frankenstyle.
     * @param state $state The state to check.
     * @param stdClass $record The record to be compared with the state.
     */
    private function check_state(string $component, state $state, stdClass $record): void {
        $this->assertEquals($component, $record->component);
        $this->assertEquals($state->get_activity_id(), $record->itemid);
        $this->assertEquals($state->get_user()->id, $record->userid);
        $this->assertEquals(json_encode($state->jsonSerialize()), $record->statedata);
        $this->assertEquals($state->get_registration(), $record->registration);
    }

}
