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
 * Privacy provider tests.
 *
 * @package    core_backup
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_backup\privacy\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * @var stdClass The user
     */
    protected $user = null;

    /**
     * @var stdClass The course
     */
    protected $course = null;

    /**
     * Basic setup for these tests.
     */
    public function setUp() {
        global $DB;

        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();

        $this->user = $this->getDataGenerator()->create_user();

        // Just insert directly into the 'backup_controllers' table.
        $bcdata = (object) [
            'backupid' => 1,
            'operation' => 'restore',
            'type' => 'course',
            'itemid' => $this->course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $this->user->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time(),
            'timemodified' => time(),
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata);

        // Create another user who will perform a backup operation.
        $user = $this->getDataGenerator()->create_user();
        $bcdata->backupid = 2;
        $bcdata->userid = $user->id;
        $DB->insert_record('backup_controllers', $bcdata);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $contextlist = provider::get_contexts_for_userid($this->user->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $context = context_course::instance($this->course->id);
        $this->assertEquals($context->id, $contextforuser->id);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        global $DB;

        // Create another backup_controllers record.
        $bcdata = (object) [
            'backupid' => 3,
            'operation' => 'backup',
            'type' => 'course',
            'itemid' => $this->course->id,
            'format' => 'moodle2',
            'interactive' => 1,
            'purpose' => 10,
            'userid' => $this->user->id,
            'status' => 1000,
            'execution' => 1,
            'executiontime' => 0,
            'checksum' => 'checksumyolo',
            'timecreated' => time() + DAYSECS,
            'timemodified' => time() + DAYSECS,
            'controller' => ''
        ];
        $DB->insert_record('backup_controllers', $bcdata);

        $coursecontext = context_course::instance($this->course->id);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->user->id, $coursecontext, 'core_backup');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext);
        $this->assertTrue($writer->has_any_data());

        $data = (array) $writer->get_data([get_string('backup'), $this->course->id]);

        $this->assertCount(2, $data);

        $bc1 = array_shift($data);
        $this->assertEquals('restore', $bc1['operation']);

        $bc2 = array_shift($data);
        $this->assertEquals('backup', $bc2['operation']);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Before deletion, we should have 2 operations.
        $count = $DB->count_records('backup_controllers', ['itemid' => $this->course->id]);
        $this->assertEquals(2, $count);

        // Delete data based on context.
        $coursecontext = context_course::instance($this->course->id);
        provider::delete_data_for_all_users_in_context($coursecontext);

        // After deletion, the operations for that course should have been deleted.
        $count = $DB->count_records('backup_controllers', ['itemid' => $this->course->id]);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Before deletion, we should have 2 operations.
        $count = $DB->count_records('backup_controllers', ['itemid' => $this->course->id]);
        $this->assertEquals(2, $count);

        $coursecontext = context_course::instance($this->course->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->user, 'core_backup',
            [context_system::instance()->id, $coursecontext->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, the backup operation for the user should have been deleted.
        $count = $DB->count_records('backup_controllers', ['itemid' => $this->course->id, 'userid' => $this->user->id]);
        $this->assertEquals(0, $count);

        // Confirm we still have the other users record.
        $bcs = $DB->get_records('backup_controllers');
        $this->assertCount(1, $bcs);
        $lastsubmission = reset($bcs);
        $this->assertNotEquals($this->user->id, $lastsubmission->userid);
    }
}
