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

namespace core_xapi\task;

use core_xapi\local\statement\item_activity;
use advanced_testcase;
use core_xapi\test_helper;

/**
 * Contains test cases for testing the scheduled task state_cleanup_task.
 *
 * @package    core_xapi
 * @since      Moodle 4.2
 * @covers     \core_xapi\task\state_cleanup_task
 * @copyright  2023 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state_cleanup_task_test extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/xapi/tests/helper.php');
        parent::setUpBeforeClass();
    }

    /**
     * Testing execute method in state_cleanup_task.
     */
    public function test_state_cleanup_task(): void {
        global $DB;

        $this->resetAfterTest();

        // Scenario.
        $this->setAdminUser();

        // Add a few xAPI state records to database.
        test_helper::create_state(['activity' => item_activity::create_from_id('1')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('2')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('3')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('4')], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('5'), 'component' => 'mod_h5pactivity'], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('6'), 'component' => 'mod_h5pactivity'], true);
        test_helper::create_state(['activity' => item_activity::create_from_id('7'), 'component' => 'mod_h5pactivity'], true);

        // Perform test.
        $task = new state_cleanup_task();
        $task->execute();

        // Check no state has been removed yet (because the entries are not old enough).
        $this->assertEquals(7, $DB->count_records('xapi_states'));

        // Make the existing state entries older.
        $timepast = time() - 2;
        $DB->set_field('xapi_states', 'timecreated', $timepast);
        $DB->set_field('xapi_states', 'timemodified', $timepast);

        // Create 1 more state, that shouldn't be removed after the cleanup.
        test_helper::create_state(['activity' => item_activity::create_from_id('8'), 'component' => 'mod_h5pactivity'], true);

        // Set the config to remove states older than 1 second.
        set_config('xapicleanupperiod', 1);

        // Check old states have been removed.
        $task->execute();
        $this->assertEquals(5, $DB->count_records('xapi_states'));
        $this->assertEquals(4, $DB->count_records('xapi_states', ['component' => 'fake_component']));
        $this->assertEquals(1, $DB->count_records('xapi_states', ['component' => 'mod_h5pactivity']));
        $this->assertEquals(0, $DB->count_records('xapi_states', ['component' => 'my_component']));
    }
}
