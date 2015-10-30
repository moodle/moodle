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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Events tests.
 *
 * @package auth_db
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/auth/db/tests/db_test.php');

class auth_db_events_testcase extends advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Tests that the locations in the auth_db API that create a user trigger the user_created event.
     */
    public function test_user_created() {
        global $DB;

        $this->preventResetByRollback();

        // Initialise the database.
        $authdbtestcase = new auth_db_testcase();
        $authdbtestcase->init_auth_database();

        $auth = get_auth_plugin('db');
        $auth->db_init();

        // Add a user to the auth_db_users table - we will then call sync_users to
        // deal with the record here. In this case it will create the user.
        $user = new stdClass();
        $user->name = 'mark';
        $user->pass = 'password123';
        $user->email = 'what@example.com';
        $user->id = $DB->insert_record('auth_db_users', $user);

        // Run sync_users and capture the user_created event.
        $sink = $this->redirectEvents();
        $trace = new null_progress_trace();
        $auth->sync_users($trace, false);
        $events = $sink->get_events();
        $sink->close();

        // Check that there is only one event.
        $this->assertEquals(1, count($events));

        // Get the event.
        $event = array_pop($events);

        // Test that the user created event was triggered - no need to test the other
        // details of the event as that is done extensively in other unit tests.
        $this->assertInstanceOf('\core\event\user_created', $event);
    }

    /**
     * Tests that the locations in the auth_db API that update a user trigger the user_updated event.
     */
    public function test_user_updated() {
        global $CFG, $DB;

        $this->preventResetByRollback();

        // Initialise the database.
        $authdbtestcase = new auth_db_testcase();
        $authdbtestcase->init_auth_database();

        $auth = get_auth_plugin('db');
        $auth->db_init();

        // Add a suspended user.
        $user = array();
        $user['username'] = 'mark';
        $user['suspended'] = '1';
        $user['mnethostid'] = $CFG->mnet_localhost_id;
        $user['auth'] = 'db';
        $this->getDataGenerator()->create_user($user);

        // Add a user to the auth_db_users table - we will then call sync_users to
        // deal with the record here. In this case it will un-suspend the user.
        $user = new stdClass();
        $user->name = 'mark';
        $user->pass = 'password123';
        $user->email = 'what@example.com';
        $user->id = $DB->insert_record('auth_db_users', $user);

        // Set the config to remove the suspension on the user.
        set_config('removeuser', AUTH_REMOVEUSER_SUSPEND, 'auth/db');
        $auth->config->removeuser = AUTH_REMOVEUSER_SUSPEND;

        // Run sync_users and capture the user_updated event.
        $sink = $this->redirectEvents();
        $trace = new null_progress_trace();
        $auth->sync_users($trace, false);
        $events = $sink->get_events();
        $sink->close();

        // Check that there is only one event.
        $this->assertEquals(1, count($events));

        // Get the event.
        $event = array_pop($events);

        // Test that the user updated event was triggered - no need to test the other
        // details of the event as that is done extensively in other unit tests.
        $this->assertInstanceOf('\core\event\user_updated', $event);

        // Run sync_users and capture the user_updated event.
        $sink = $this->redirectEvents();
        $auth->update_user_record('mark');
        $events = $sink->get_events();
        $sink->close();

        // Check that there is only one event.
        $this->assertEquals(1, count($events));

        // Get the event.
        $event = array_pop($events);

        // Test that the user updated event was triggered - no need to test the other
        // details of the event as that is done extensively in other unit tests.
        $this->assertInstanceOf('\core\event\user_updated', $event);
    }
}
