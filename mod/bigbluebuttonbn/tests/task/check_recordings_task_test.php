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
 * Test class for the check_pending_recordings and check_dismissed_recordings task
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2022 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_bigbluebuttonbn\task\check_dismissed_recordings
 * @covers \mod_bigbluebuttonbn\task\check_pending_recordings
 * @covers \mod_bigbluebuttonbn\recording::sync_pending_recordings_from_server
 */
class check_recordings_task_test extends advanced_testcase {

    use testcase_helper_trait;

    /**
     * @var $RECORDINGS_DATA array fake recording data.
     */
    const RECORDINGS_DATA = [
        ['name' => 'Recording 1'],
        ['name' => 'Recording 2'],
        ['name' => 'Recording 3'],
        ['name' => 'Recording 4'],
    ];

    /**
     * Setup for test
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
        $this->resetAfterTest();
    }

    /**
     * Test that dismissed recordings are retrieved
     */
    public function test_check_dismissed_recordings(): void {
        $this->create_meeting_and_recordings(recording::RECORDING_STATUS_DISMISSED);
        $this->assertEquals(4, recording::count_records());
        $this->assertEquals(0, recording::count_records(['status' => recording::RECORDING_STATUS_PROCESSED]));
        $task = new check_dismissed_recordings();
        ob_start();
        $task->execute();
        ob_end_clean();
        $this->assertEquals(4, recording::count_records(['status' => recording::RECORDING_STATUS_PROCESSED]));
    }

    /**
     * Test that pending recordings are retrieved
     */
    public function test_check_pending_recordings(): void {
        $this->create_meeting_and_recordings();
        $this->assertEquals(4, recording::count_records());
        $this->assertEquals(0, recording::count_records(['status' => recording::RECORDING_STATUS_PROCESSED]));
        $task = new check_pending_recordings();
        ob_start();
        $task->execute();
        ob_end_clean();
        $this->assertEquals(4, recording::count_records(['status' => recording::RECORDING_STATUS_PROCESSED]));
    }

    /**
     * Create sample meeting and recording.
     *
     * @param int $status status for the newly created recordings
     * @return array recording data (not the persistent class but plain object)
     */
    private function create_meeting_and_recordings(int $status = recording::RECORDING_STATUS_AWAITING): array {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $course = $this->getDataGenerator()->create_course();
        $activity = $generator->create_instance([
            'course' => $course->id,
            'type' => instance::TYPE_ALL
        ]);
        $instance = instance::get_from_instanceid($activity->id);
        $generator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id()
        ]);
        foreach (self::RECORDINGS_DATA as $data) {
            $rdata = $generator->create_recording(
                array_merge([
                    'bigbluebuttonbnid' => $instance->get_instance_id(),
                    'groupid' => $instance->get_group_id()
                ], $data)
            );
            $recording = new recording($rdata->id);
            $recording->set('status', $status);
            $recording->save();
            $recordings[] = $rdata;
        }
        return $recordings;
    }
}
