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
use core\task\manager;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\recording;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Class containing the scheduled task for lti module.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2019 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_bigbluebuttonbn\task\upgrade_recordings_task
 */
class upgrade_recordings_task_test extends advanced_testcase {

    use testcase_helper_trait;

    /**
     * @var object $instance
     */
    protected $instance = null;

    /**
     * @var array $groups all groups
     */
    protected $groups = [];

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
    public function test_upgrade_recordings_basic(): void {
        global $DB;
        $this->setup_basic_data();
        upgrade_recordings_task::schedule_upgrade_per_meeting(false);;
        // The first run will lead to all of them being processed, and none left over.
        // A new job is always queued on a successful run.
        $this->runAdhocTasks(upgrade_recordings_task::class);
        $this->assertEquals(0, $DB->count_records('bigbluebuttonbn_logs', ['log' => logger::EVENT_CREATE]));
        $this->assertEquals(75, recording::count_records(['imported' => '0']));
        // Old logs are kept but renamed.
        $this->assertEquals(75, $DB->count_records('bigbluebuttonbn_logs', ['log' => logger::EVENT_CREATE_MIGRATED]));
        $this->assertEquals(15, recording::count_records(['groupid' => $this->groups[0]->id, 'imported' => '0',
            'status' => recording::RECORDING_STATUS_PROCESSED]));
        $this->assertEquals(15, recording::count_records(['groupid' => $this->groups[1]->id, 'imported' => '0',
            'status' => recording::RECORDING_STATUS_PROCESSED]));
        $this->assertEquals(45,
            recording::count_records(['groupid' => 0, 'imported' => '0', 'status' => recording::RECORDING_STATUS_PROCESSED]));

        // The second run will lead to no change in the number of logs, but no further jobs will be queued.
        upgrade_recordings_task::schedule_upgrade_per_meeting();
        $this->runAdhocTasks(upgrade_recordings_task::class);
        $this->assertEquals(75, recording::count_records());
        // The first run will lead to all of them being processed, and none left over.
        // A new job is always queued on a successful run.
        $this->assertEmpty($DB->get_records_select(
            'bigbluebuttonbn_logs',
            'log = :logmatch AND ' . $DB->sql_like('meta', ':match'),
            [
                'logmatch' => 'Create',
                'match' => '%true%'
            ]
        ));
        $this->runAdhocTasks(upgrade_recordings_task::class);
        $this->assertEmpty($DB->get_records_select(
            'bigbluebuttonbn_logs',
            'log = :logmatch AND ' . $DB->sql_like('meta', ':match'),
            [
                'logmatch' => 'Create',
                'match' => '%true%'
            ]
        ));
        $this->assertEquals(0, $DB->count_records('bigbluebuttonbn_logs', ['log' => 'Create']));
        // Ensure that logs match.
        $matchesarray = [
            [
                'Executing .*',
                'Fetching logs for conversion',
                "Creating new recording records",
                'Migrated 30 recordings',
            ],
            [
                'Executing .*',
                'Fetching logs for conversion',
                "Creating new recording records",
                'Migrated 15 recordings',
            ],
            [
                'Executing .*',
                'Fetching logs for conversion',
                "Creating new recording records",
                "Unable to find an activity for .*. This recording is headless",
                'Migrated 15 recordings'
            ]
        ];
        foreach ($matchesarray as $matches) {
            $this->expectOutputRegex('/' . implode('.*', $matches) . '/s');
        }
        $this->resetDebugging(); // We might have debugging message that are sent by get_from_meetingid and can ignore them.
    }

    /**
     * Upgrade task test
     */
    public function test_upgrade_recordings_imported_basic(): void {
        global $DB;
        $this->setup_basic_data(true);
        upgrade_recordings_task::schedule_upgrade_per_meeting(true);;
        // The first run will lead to all of them being processed, and none left over.
        // A new job is always queued on a successful run.
        $this->runAdhocTasks(upgrade_recordings_task::class);

        $this->assertEquals(0, $DB->count_records('bigbluebuttonbn_logs', ['log' => logger::EVENT_IMPORT]));
        $this->assertEquals(75, $DB->count_records('bigbluebuttonbn_logs', ['log' => logger::EVENT_IMPORT_MIGRATED]));
        $this->assertEquals(75, recording::count_records(['imported' => '1']));

        $this->assertEquals(15, recording::count_records(['groupid' => $this->groups[0]->id, 'imported' => '1',
            'status' => recording::RECORDING_STATUS_PROCESSED]));
        $this->assertEquals(15, recording::count_records(['groupid' => $this->groups[1]->id, 'imported' => '1',
            'status' => recording::RECORDING_STATUS_PROCESSED]));
        $this->assertEquals(45,
            recording::count_records(['groupid' => 0, 'imported' => '1', 'status' => recording::RECORDING_STATUS_PROCESSED]));

        // The second run will lead to no change in the number of logs, but no further jobs will be queued.
        upgrade_recordings_task::schedule_upgrade_per_meeting();
        $this->runAdhocTasks(upgrade_recordings_task::class);
        $this->assertEquals(75, recording::count_records(['imported' => '1']));
        // The first run will lead to all of them being processed, and none left over.
        // A new job is always queued on a successful run.
        $this->assertEmpty($DB->get_records_select(
            'bigbluebuttonbn_logs',
            'log = :logmatch',
            [
                'logmatch' => 'Import'
            ]
        ));
        // Ensure that logs match.
        $matchesarray = [
            [
                'Executing .*',
                'Fetching logs for conversion',
                "Creating new recording records",
                'Migrated 30 recordings',
            ],
            [
                'Executing .*',
                'Fetching logs for conversion',
                "Creating new recording records",
                'Migrated 15 recordings',
            ],
            [
                'Executing .*',
                'Fetching logs for conversion',
                "Creating new recording records",
                "Unable to find an activity for .*. This recording is headless",
                'Migrated 15 recordings'
            ]
        ];
        foreach ($matchesarray as $matches) {
            $this->expectOutputRegex('/' . implode('.*', $matches) . '/s');
        }
        $this->resetDebugging(); // We might have debugging message that are sent by get_from_meetingid and can ignore them.
    }

    /**
     * Upgrade recordings when we have missing recordings on the server
     * Basically, the recordings are imported and then we cannot the other because logs have been marked as migrated.
     */
    public function test_upgrade_recordings_with_missing_recording_on_bbb_server(): void {
        global $DB;
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        [$teacher, $groups, $instance, $groupedinstance, $deletedinstance] = $this->setup_basic_course_and_meeting();

        $this->create_log_entries($instance, $teacher->id, 5);
        $this->create_log_entries($instance, $teacher->id, 5, false, false);
        $this->assertEquals(10, $DB->count_records('bigbluebuttonbn_logs', ['log' => 'Create']));

        // Schedule the run.
        upgrade_recordings_task::schedule_upgrade_per_meeting();
        $this->runAdhocTasks(upgrade_recordings_task::class);
        // At this point only 5 are created, the rest is still in the queue.
        $this->assertEquals(0, $DB->count_records('bigbluebuttonbn_logs', ['log' => 'Create']));
        $this->assertEquals(5, recording::count_records());
        // Now create 5 recordings on the server.
        // Schedule the run.
        upgrade_recordings_task::schedule_upgrade_per_meeting();
        for ($index = 0; $index < 5; $index++) {
            $plugingenerator->create_recording([
                'bigbluebuttonbnid' => $instance->get_instance_id(),
                'groupid' => $instance->get_group_id(),
                'starttime' => time(),
                'endtime' => time() + HOURSECS,
            ], true); // Create another recording on the server.
        }
        $this->assertEquals(0, $DB->count_records('task_adhoc', ['classname' => '\\' . upgrade_recordings_task::class]));
        $this->runAdhocTasks(upgrade_recordings_task::class);
        // Ensure that logs match.

        // Ensure that logs match.
        $matchesarray = [
            [
                'Executing .*',
                'Fetching logs for conversion',
                "Creating new recording records",
                'Migrated 5 recordings',
            ],
        ];
        foreach ($matchesarray as $matches) {
            $this->expectOutputRegex('/' . implode('.*', $matches) . '/s');
        }
    }

    /**
     * Upgrade task test with more recordings on the server than in the log : we add all recording and should have
     * no more logs.
     */
    public function test_upgrade_recordings_with_more_recordings_on_bbb_server(): void {
        global $DB;
        $generator = $this->getDataGenerator();
        // Create a course with student and teacher, and two groups.
        $this->course = $generator->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($this->course);
        // Create an ungrouped activity.
        $activity = $generator->create_module('bigbluebuttonbn', [
            'course' => $this->course->id,
        ]);
        $this->instance = instance::get_from_instanceid($activity->id);
        // We create 5 recordings in the log but no recording instance on the server.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $plugingenerator->create_meeting([
            'instanceid' => $this->instance->get_instance_id(),
            'groupid' => $this->instance->get_group_id(),
        ]);
        $this->create_log_entries($this->instance, $user->id, 5);
        $plugingenerator->create_recording([
            'bigbluebuttonbnid' => $this->instance->get_instance_id(),
            'groupid' => $this->instance->get_group_id(),
            'starttime' => time(),
            'endtime' => time() + HOURSECS,
        ], true); // Create another recording on the server.

        $this->assertEquals(5, $DB->count_records('bigbluebuttonbn_logs', ['log' => 'Create']));
        upgrade_recordings_task::schedule_upgrade_per_meeting();
        $this->runAdhocTasks(upgrade_recordings_task::class);
        $this->assertEquals(6, recording::count_records(['status' => recording::RECORDING_STATUS_PROCESSED]));
        $this->assertEquals(0, $DB->count_records('bigbluebuttonbn_logs', ['log' => 'Create']));
        // Ensure that logs match.
        $matches = [
            'Executing .*',
            'Fetching logs for conversion',
            "Creating new recording records",
            'Migrated 6 recordings',
        ];
        $this->expectOutputRegex('/' . implode('.*', $matches) . '/s');
    }

    /**
     * Setup basic data for tests
     *
     * @param bool $importedrecording
     * @return void
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function setup_basic_data($importedrecording = false) {
        global $DB;
        [$teacher, $groups, $instance, $groupedinstance, $deletedinstance] = $this->setup_basic_course_and_meeting();

        $this->create_log_entries($instance, $teacher->id, 30, $importedrecording);
        foreach ($groups as $group) {
            $groupinstance = instance::get_group_instance_from_instance($groupedinstance, $group->id);
            $this->create_log_entries($groupinstance, $teacher->id, 15, $importedrecording);
        }
        $this->create_log_entries($deletedinstance, $teacher->id, 15, $importedrecording);
        course_delete_module($deletedinstance->get_cm_id());
        // Truncate the recordings table to reflect what it would have looked like before this version.
        $DB->delete_records('bigbluebuttonbn_recordings');
        $this->groups = $groups;
        $this->instance = $instance;
    }

    /**
     * Setup basic data for tests
     *
     * @return array
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function setup_basic_course_and_meeting() {
        $generator = $this->getDataGenerator();
        // Create a course with student and teacher, and two groups.
        $this->course = $generator->create_course();
        $groups = [];
        $groups[] = $generator->create_group(['courseid' => $this->course->id]);
        $groups[] = $generator->create_group(['courseid' => $this->course->id]);

        $teacher = $generator->create_and_enrol($this->course, 'editingteacher');
        $generator->create_and_enrol($this->course, 'student');

        // Create a "normal" meeting.
        $instance = $this->create_meeting_for_logs();
        // Create an grouped activity.
        $groupedinstance = $this->create_meeting_for_logs($groups);
        // Create an instance that will then be deleted.
        $deletedinstance = $this->create_meeting_for_logs();
        // Create logs for an activity which no longer exists (because we deleted it).
        return [$teacher, $groups, $instance, $groupedinstance, $deletedinstance];
    }

    /**
     * Create a meeting and return its instance
     *
     * @param array|null $groups
     * @return instance
     */
    protected function create_meeting_for_logs(?array $groups = null) {
        $generator = $this->getDataGenerator();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $params = [
            'course' => $this->course->id,
        ];
        if (!empty($groups)) {
            $params['groupmode'] = SEPARATEGROUPS;
        }
        $activity = $generator->create_module('bigbluebuttonbn', $params);
        $instance = instance::get_from_instanceid($activity->id);
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $groupinstance = instance::get_group_instance_from_instance($instance, $group->id);
                $plugingenerator->create_meeting([
                    'instanceid' => $groupinstance->get_instance_id(),
                    'groupid' => $groupinstance->get_group_id(),
                ]);
            }
        } else {
            $plugingenerator->create_meeting([
                'instanceid' => $instance->get_instance_id(),
                'groupid' => $instance->get_group_id(),
            ]);
        }
        return $instance;
    }
}
