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

namespace mod_bigbluebuttonbn\task;

use advanced_testcase;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\recording;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Class containing the scheduled task for lti module.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2019 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_bigbluebuttonbn\task\upgrade_recordings
 * @covers \mod_bigbluebuttonbn\task\upgrade_recording_base_task
 */
class upgrade_recordings_test extends advanced_testcase {
    use testcase_helper_trait;
    /**
     * Setup for test
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
        $this->resetAfterTest();
    }
    /**
     * Upgrade task test
     */
    public function test_upgrade_recordings(): void {
        global $DB;
        $generator = $this->getDataGenerator();

        // Create a course with student and teacher, and two groups.
        $course = $generator->create_course();
        $groupa = $generator->create_group(['courseid' => $course->id]);
        $groupb = $generator->create_group(['courseid' => $course->id]);

        $teacher = $generator->create_and_enrol($course, 'editingteacher');
        $generator->create_and_enrol($course, 'student');

        // Create an ungrouped activity.
        $activity = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);
        $instance = instance::get_from_instanceid($activity->id);
        $this->create_legacy_log_entries($instance, $teacher->id, 30);

        // Create an grouped activity.
        $activity = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
            'groupmode' => SEPARATEGROUPS,
        ]);
        $groupedinstance = instance::get_from_instanceid($activity->id);

        $groupainstance = instance::get_group_instance_from_instance($groupedinstance, $groupa->id);
        $this->create_legacy_log_entries($groupainstance, $teacher->id, 15);

        $groupbinstance = instance::get_group_instance_from_instance($groupedinstance, $groupb->id);
        $this->create_legacy_log_entries($groupbinstance, $teacher->id, 15);

        // Create logs for an activity which no longer exists (because we deleted it).
        $activity = $generator->create_module('bigbluebuttonbn', [
            'course' => $course->id,
        ]);
        $oldinstance = instance::get_from_instanceid($activity->id);
        $this->create_legacy_log_entries($oldinstance, $teacher->id, 15);
        course_delete_module($oldinstance->get_cm_id());

        // Truncate the recordings table to reflect what it would have looked like before this version.
        $DB->delete_records('bigbluebuttonbn_recordings');

        $upgraderecording = new upgrade_recordings();
        $rc = new \ReflectionClass(upgrade_recordings::class);
        $rcm = $rc->getMethod('process_bigbluebuttonbn_logs');
        $rcm->setAccessible(true);

        // The first run will lead to all of them being processed, and none left over.
        // A new job is always queued on a successful run.
        $returnvalue = $rcm->invoke($upgraderecording);
        $this->assertTrue($returnvalue);

        $this->assertEquals(0, $DB->count_records('bigbluebuttonbn_logs', ['log' => 'Create']));
        $this->assertEquals(75, recording::count_records(['imported' => '0']));

        $this->assertEquals(15, recording::count_records(['groupid' => $groupa->id, 'imported' => '0']));
        $this->assertEquals(15, recording::count_records(['groupid' => $groupb->id, 'imported' => '0']));
        $this->assertEquals(45, recording::count_records(['groupid' => 0, 'imported' => '0']));

        // The second run will lead to no change in the number of logs, but no further jobs will be queued.
        $returnvalue = $rcm->invoke($upgraderecording);
        $this->assertFalse($returnvalue);

        // Ensure that logs match.
        $matches = [
            'Fetching logs for conversion',
            "Creating new recording records",
            "Unable to find an activity for .*. This recording is headless",
            'Migrated 75 recordings',
            'Deleting migrated log records',
            'Fetching logs for conversion',
            'No logs were found',
        ];
        $this->expectOutputRegex('/' . implode('.*', $matches) . '/s');
    }


}
