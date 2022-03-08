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
 * Recording tests.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

namespace mod_bigbluebuttonbn;

use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Recording test class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @covers \mod_bigbluebuttonbn\recording
 * @coversDefaultClass \mod_bigbluebuttonbn\recording
 */
class recording_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Setup for test
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
    }

    /**
     * Test for bigbluebuttonbn_get_allrecordings status refresh.
     *
     * @param int $status
     * @dataProvider get_status_provider
     * @covers ::get
     */
    public function test_get_allrecordings_status_refresh(int $status) {
        $this->resetAfterTest();
        ['recordings' => $recordings] = $this->create_activity_with_recordings(
            $this->get_course(),
            instance::TYPE_ALL,
            [['status' => $status]]
        );

        $this->assertEquals($status, (new recording($recordings[0]->id))->get('status'));
    }

    /**
     * Get name
     *
     * @covers ::get_name
     */
    public function test_get_name(): void {
        $this->resetAfterTest();
        ['recordings' => $recordings] = $this->create_activity_with_recordings(
            $this->get_course(),
            instance::TYPE_ALL,
            [['name' => 'Example name']]
        );

        $this->assertEquals('Example name', (new recording($recordings[0]->id))->get('name'));
    }

    /**
     * Test get description
     *
     * @covers ::get_description
     */
    public function test_get_description(): void {
        $this->resetAfterTest();
        ['recordings' => $recordings] = $this->create_activity_with_recordings(
            $this->get_course(),
            instance::TYPE_ALL,
            [['description' => 'Example description']]
        );

        $this->assertEquals('Example description', (new recording($recordings[0]->id))->get('description'));
    }

    /**
     * Get possible status
     *
     * @return array[]
     */
    public function get_status_provider(): array {
        return [
            [recording::RECORDING_STATUS_PROCESSED],
            [recording::RECORDING_STATUS_DISMISSED],
        ];
    }

    /**
     * Test for bigbluebuttonbn_get_allrecordings()
     *
     * @param int $type The activity type
     * @dataProvider get_allrecordings_types_provider
     * @covers ::get_recordings_for_instance
     */
    public function test_get_allrecordings(int $type): void {
        $this->resetAfterTest();
        $recordingcount = 2; // Two recordings only.
        ['activity' => $activity] = $this->create_activity_with_recordings(
            $this->get_course(),
            $type,
            array_pad([], $recordingcount, [])
        );

        // Fetch the recordings for the instance.
        // The count shoudl match the input count.
        $recordings = recording::get_recordings_for_instance(instance::get_from_instanceid($activity->id));
        $this->assertCount($recordingcount, $recordings);
    }

    /**
     * Get possible type for recording / tests
     *
     * @return array[]
     */
    public function get_allrecordings_types_provider(): array {
        return [
            'Instance Type ALL' => [
                'type' => instance::TYPE_ALL
            ],
            'Instance Type ROOM Only' => [
                'type' => instance::TYPE_ROOM_ONLY,
            ],
            'Instance Type Recording only' => [
                'type' => instance::TYPE_RECORDING_ONLY
            ],
        ];
    }

    /**
     * Test for bigbluebuttonbn_get_allrecordings().
     *
     * @param int $type
     * @dataProvider get_allrecordings_types_provider
     */
    public function test_get_recording_for_group($type) {
        $this->resetAfterTest();

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');

        $testcourse = $this->getDataGenerator()->create_course(['groupmodeforce' => true, 'groupmode' => VISIBLEGROUPS]);
        $teacher = $this->getDataGenerator()->create_and_enrol($testcourse, 'editingteacher');

        $group1 = $this->getDataGenerator()->create_group(['G1', 'courseid' => $testcourse->id]);
        $student1 = $this->getDataGenerator()->create_and_enrol($testcourse);
        $this->getDataGenerator()->create_group_member(['userid' => $student1, 'groupid' => $group1->id]);

        $group2 = $this->getDataGenerator()->create_group(['G2', 'courseid' => $testcourse->id]);
        $student2 = $this->getDataGenerator()->create_and_enrol($testcourse);
        $this->getDataGenerator()->create_group_member(['userid' => $student2, 'groupid' => $group2->id]);

        // No group.
        $student3 = $this->getDataGenerator()->create_and_enrol($testcourse);

        $activity = $plugingenerator->create_instance([
            'course' => $testcourse->id,
            'type' => $type,
            'name' => 'Example'
        ]);
        $instance = instance::get_from_instanceid($activity->id);
        $instance->set_group_id(0);
        $this->create_recordings_for_instance($instance, [['name' => "Pre-Recording 1"], ['name' => "Pre-Recording 2"]]);
        $instance->set_group_id($group1->id);
        $this->create_recordings_for_instance($instance, [['name' => "Group 1 Recording 1"]]);
        $instance->set_group_id($group2->id);
        $this->create_recordings_for_instance($instance, [['name' => "Group 2 Recording 1"]]);

        $this->setUser($student1);
        $instance1 = instance::get_from_instanceid($activity->id);
        $instance1->set_group_id($group1->id);
        $recordings = recording::get_recordings_for_instance($instance1);
        $this->assertCount(1, $recordings);
        $this->assert_has_recording_by_name('Group 1 Recording 1', $recordings);

        $this->setUser($student2);
        $instance2 = instance::get_from_instanceid($activity->id);
        $instance2->set_group_id($group2->id);
        $recordings = recording::get_recordings_for_instance($instance2);
        $this->assertCount(1, $recordings);
        $this->assert_has_recording_by_name('Group 2 Recording 1', $recordings);

        $this->setUser($student3);
        $instance3 = instance::get_from_instanceid($activity->id);
        $recordings = recording::get_recordings_for_instance($instance3);
        $this->assertIsArray($recordings);
        $this->assertCount(4, $recordings);
        $this->assert_has_recording_by_name('Pre-Recording 1', $recordings);
        $this->assert_has_recording_by_name('Pre-Recording 2', $recordings);
    }

    /**
     * Test that we can get recordings from a deleted activity
     *
     * @param int $type
     * @dataProvider get_allrecordings_types_provider
     */
    public function test_get_recordings_from_deleted_activity($type) {
        $this->resetAfterTest(true);
        $this->initialise_mock_server();
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');

        $testcourse = $this->getDataGenerator()->create_course();

        $activity = $plugingenerator->create_instance([
            'course' => $testcourse->id,
            'type' => $type,
            'name' => 'Example'
        ]);
        $instance = instance::get_from_instanceid($activity->id);
        $this->create_recordings_for_instance($instance, [['name' => "Deleted Recording 1"]]);
        $activity2 = $plugingenerator->create_instance([
            'course' => $testcourse->id,
            'type' => $type,
            'name' => 'Example'
        ]);
        $instance2 = instance::get_from_instanceid($activity2->id);
        $this->create_recordings_for_instance($instance2, [['name' => "Recording 1"]]);

        bigbluebuttonbn_delete_instance($activity->id);
        $recordings = recording::get_recordings_for_course($testcourse->id, [], false, false, true);
        $this->assertCount(2, $recordings);
        $this->assert_has_recording_by_name('Deleted Recording 1', $recordings);
        $this->assert_has_recording_by_name('Recording 1', $recordings);
        $recordings = recording::get_recordings_for_course($testcourse->id, [], false, false, false);
        $this->assertCount(1, $recordings);
    }

    /**
     * Check that a recording exist in the list of recordings
     *
     * @param string $recordingname
     * @param array $recordings
     */
    public function assert_has_recording_by_name($recordingname, $recordings) {
        $recordingnames = array_map(function ($r) {
            return $r->get('name');
        }, $recordings);
        $this->assertContains($recordingname, $recordingnames);
    }

    /**
     * Simple recording with breakoutroom fetcher test
     *
     * @return void
     */
    public function test_recordings_breakoutroom() {
        $this->resetAfterTest();
        $this->initialise_mock_server();
        [$context, $cm, $bbbactivity] = $this->create_instance();
        $instance = instance::get_from_instanceid($bbbactivity->id);
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $mainmeeting = $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
        ]);
        // This creates a meeting to receive the recordings (specific to the mock server implementation). See recording_proxy_test.
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'isBreakout' => true,
            'sequence' => 1
        ]);
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'isBreakout' => true,
            'sequence' => 2
        ]);
        // For now only recording from the main room have been created.
        $this->create_recordings_for_instance($instance, [
            ['name' => 'Recording 1'],
        ]);
        $recordings = recording::get_recordings_for_instance($instance);
        $this->assertCount(1, $recordings);

        // Now the breakoutroom recordings appears.
        $this->create_recordings_for_instance($instance, [
            ['name' => 'Recording 2', 'isBreakout' => true, 'sequence' => 1],
            ['name' => 'Recording 3', 'isBreakout' => true, 'sequence' => 2]
        ]);
        $recordings = recording::get_recordings_for_instance($instance);
        $this->assertCount(3, $recordings);
    }
}
