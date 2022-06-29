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

namespace core_xapi;

use core_xapi\xapi_exception;
use core_xapi\local\statement;
use core_xapi\local\statement\item_agent;
use core_xapi\local\statement\item_verb;
use core_xapi\local\statement\item_activity;
use advanced_testcase;
use core\event\base;

defined('MOODLE_INTERNAL') || die();

/**
 * Contains test cases for testing xAPI statement handler base methods.
 *
 * @package    core_xapi
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler_test extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
    }

    /**
     * Test handler creation.
     */
    public function test_handler_create() {
        // Get an existent handler.
        $handler = handler::create('fake_component');
        $this->assertEquals(get_class($handler), 'fake_component\\xapi\\handler');

        // Get a non existent handler.
        $this->expectException(xapi_exception::class);
        $value = handler::create('potato_omelette');
    }

    /**
     * Test support group.
     */
    public function test_support_group_actor() {
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
    public function test_process_statements() {

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
}
