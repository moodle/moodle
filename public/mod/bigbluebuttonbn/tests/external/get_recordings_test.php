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

namespace mod_bigbluebuttonbn\external;

use core_external\external_api;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;
use require_login_exception;

/**
 * Tests for the update_course class.
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2021 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\external\get_recordings
 */
final class get_recordings_test extends \core_external\tests\externallib_testcase {
    use testcase_helper_trait;

    /**
     * Setup for test
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
    }

    /**
     * Helper
     *
     * @param mixed ...$params
     * @return array|bool|mixed
     */
    protected function get_recordings(...$params) {
        $recordings = get_recordings::execute(...$params);

        return external_api::clean_returnvalue(get_recordings::execute_returns(), $recordings);
    }

    /**
     * Test execute API CALL with no instance
     */
    public function test_execute_wrong_instance(): void {
        $this->resetAfterTest();
        $getrecordings = $this->get_recordings(1234);

        $this->assertIsArray($getrecordings);
        $this->assertArrayHasKey('status', $getrecordings);
        $this->assertEquals(false, $getrecordings['status']);
        $this->assertStringContainsString('nosuchinstance', $getrecordings['warnings'][0]['warningcode']);
    }

    /**
     * Test execute API CALL without login
     */
    public function test_execute_without_login(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $this->expectException(require_login_exception::class);
        $this->get_recordings($instance->get_instance_id());
    }

    /**
     * Test execute API CALL with invalid login
     */
    public function test_execute_with_invalid_login(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_user();
        $this->setUser($user);

        $this->expectException(require_login_exception::class);
        $this->get_recordings($instance->get_instance_id());
    }

    /**
     * When login as a student
     */
    public function test_execute_with_valid_login(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'student');
        $this->setUser($user);

        $getrecordings = $this->get_recordings($instance->get_instance_id());

        $this->assertIsArray($getrecordings);
        $this->assertArrayHasKey('status', $getrecordings);
        $this->assertEquals(true, $getrecordings['status']);
        $this->assertNotEmpty($getrecordings['tabledata']);
        $this->assertEquals('[]', $getrecordings['tabledata']['data']);
    }

    /**
     * Check if tools are present for teacher/moderator
     */
    public function test_get_recordings_tools(): void {
        $this->resetAfterTest();
        $dataset = [
            'type' => instance::TYPE_ALL,
            'groups' => null,
            'users' => [['username' => 't1', 'role' => 'editingteacher'], ['username' => 's1', 'role' => 'student']],
            'recordingsdata' => [
                [['name' => 'Recording1']],
                [['name' => 'Recording2']]
            ],
        ];
        $activityid = $this->create_from_dataset($dataset);
        $instance = instance::get_from_instanceid($activityid);

        $context = \context_course::instance($instance->get_course_id());
        foreach ($dataset['users'] as $userdef) {
            $user = \core_user::get_user_by_username($userdef['username']);
            $this->setUser($user);
            $getrecordings = $this->get_recordings($instance->get_instance_id());
            // Check users see or do not see recording dependings on their groups.
            foreach ($dataset['recordingsdata'] as $recordingdata) {
                foreach ($recordingdata as $recording) {
                    if (has_capability('moodle/course:update', $context)) {
                        $this->assertStringContainsString('data-action=\"delete\"', $getrecordings['tabledata']['data'],
                            "User $user->username, should be able to delete the recording {$recording['name']}");
                        $this->assertStringContainsString('data-action=\"publish\"', $getrecordings['tabledata']['data'],
                            "User $user->username, should be able to publish the recording {$recording['name']}");
                    } else {
                        $this->assertStringNotContainsString('data-action=\"delete\"', $getrecordings['tabledata']['data'],
                            "User $user->username, should not be able to delete the recording {$recording['name']}");
                    }
                }
            }
        }
        // Now without delete.
        foreach ($dataset['users'] as $userdef) {
            $user = \core_user::get_user_by_username($userdef['username']);
            $this->setUser($user);
            $getrecordings = $this->get_recordings($instance->get_instance_id(), 'protect');
            // Check users see or do not see recording dependings on their groups.
            foreach ($dataset['recordingsdata'] as $recordingdata) {
                foreach ($recordingdata as $recording) {
                    $this->assertStringNotContainsString('data-action=\"delete\"', $getrecordings['tabledata']['data'],
                        "User $user->username, should not be able to delete the recording {$recording['name']}");
                }
            }
        }
    }

    /**
     * Check preview is present and displayed
     */
    public function test_get_recordings_preview(): void {
        $this->resetAfterTest();
        $dataset = [
            'type' => instance::TYPE_ALL,
            'additionalsettings' => [
                'recordings_preview' => 1
            ],
            'groups' => null,
            'users' => [['username' => 't1', 'role' => 'editingteacher'], ['username' => 's1', 'role' => 'student']],
            'recordingsdata' => [
                [['name' => 'Recording1']],
                [['name' => 'Recording2']]
            ],
        ];
        $activityid = $this->create_from_dataset($dataset);
        $instance = instance::get_from_instanceid($activityid);

        $context = \context_course::instance($instance->get_course_id());
        foreach ($dataset['users'] as $userdef) {
            $user = \core_user::get_user_by_username($userdef['username']);
            $this->setUser($user);
            $getrecordings = $this->get_recordings($instance->get_instance_id());
            $this->assertNotEmpty($getrecordings['tabledata']['columns']['3']);
            $this->assertEquals('preview', $getrecordings['tabledata']['columns']['3']['key']);
        }
    }

    /**
     * Check we can see all recording from a course in a room only instance
     * @covers \mod_bigbluebuttonbn\external\get_recordings::execute
     */
    public function test_get_recordings_room_only(): void {
        $this->resetAfterTest();
        set_config('bigbluebuttonbn_importrecordings_enabled', 1);
        $dataset = [
            'type' => instance::TYPE_ALL,
            'groups' => null,
            'users' => [['username' => 't1', 'role' => 'editingteacher'], ['username' => 's1', 'role' => 'student']],
            'recordingsdata' => [
                [['name' => 'Recording1']],
                [['name' => 'Recording2']]
            ],
        ];
        $activityid = $this->create_from_dataset($dataset);
        $instance = instance::get_from_instanceid($activityid);

        // Now create a recording only activity.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Now create a new activity and import the first record.
        $newactivity = $plugingenerator->create_instance([
            'course' => $instance->get_course_id(),
            'type' => instance::TYPE_RECORDING_ONLY,
            'name' => 'Example 2'
        ]);
        $plugingenerator->create_meeting([
            'instanceid' => $newactivity->id,
        ]); // We need to have a meeting created in order to import recordings.
        $newinstance = instance::get_from_instanceid($newactivity->id);
        $this->create_recordings_for_instance($newinstance, [['name' => 'Recording3']]);

        foreach ($dataset['users'] as $userdef) {
            $user = \core_user::get_user_by_username($userdef['username']);
            $this->setUser($user);
            $getrecordings = $this->get_recordings($newinstance->get_instance_id());
            // Check users see or do not see recording dependings on their groups.
            $data = json_decode($getrecordings['tabledata']['data']);
            $this->assertCount(3, $data);
        }
    }

    /**
     * Check if we can see the imported recording in a new instance
     * @covers \mod_bigbluebuttonbn\external\get_recordings::execute
     */
    public function test_get_recordings_imported(): void {
        $this->resetAfterTest();
        set_config('bigbluebuttonbn_importrecordings_enabled', 1);
        $dataset = [
            'type' => instance::TYPE_ALL,
            'groups' => null,
            'users' => [['username' => 't1', 'role' => 'editingteacher'], ['username' => 's1', 'role' => 'student']],
            'recordingsdata' => [
                [['name' => 'Recording1']],
                [['name' => 'Recording2']]
            ],
        ];

        $activityid = $this->create_from_dataset($dataset);
        $instance = instance::get_from_instanceid($activityid);

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Now create a new activity and import the first record.
        $newactivity = $plugingenerator->create_instance([
            'course' => $instance->get_course_id(),
            'type' => instance::TYPE_ALL,
            'name' => 'Example 2'
        ]);
        $plugingenerator->create_meeting([
            'instanceid' => $newactivity->id,
        ]); // We need to have a meeting created in order to import recordings.
        $newinstance = instance::get_from_instanceid($newactivity->id);
        $recordings = $instance->get_recordings();
        foreach ($recordings as $recording) {
            if ($recording->get('name') == 'Recording1') {
                $recording->create_imported_recording($newinstance);
            }
        }

        foreach ($dataset['users'] as $userdef) {
            $user = \core_user::get_user_by_username($userdef['username']);
            $this->setUser($user);
            $getrecordings = $this->get_recordings($newinstance->get_instance_id());
            // Check users see or do not see recording dependings on their groups.
            foreach ($dataset['recordingsdata'] as $index => $recordingdata) {
                foreach ($recordingdata as $recording) {
                    if ($instance->can_manage_recordings()) {
                        $this->assertStringContainsString('data-action=\"delete\"', $getrecordings['tabledata']['data'],
                            "User $user->username, should be able to delete the recording {$recording['name']}");
                    } else {
                        $this->assertStringNotContainsString('data-action=\"delete\"', $getrecordings['tabledata']['data'],
                            "User $user->username, should not be able to delete the recording {$recording['name']}");
                    }
                    if ($index === 0) {
                        $this->assertStringContainsString($recording['name'], $getrecordings['tabledata']['data']);
                    } else {
                        $this->assertStringNotContainsString($recording['name'], $getrecordings['tabledata']['data']);
                    }
                }
            }

        }
    }

    /**
     * Check we can see only imported recordings in a recordings only instance when "Show only imported links" enabled.
     * @covers \mod_bigbluebuttonbn\external\get_recordings::execute
     */
    public function test_get_imported_recordings_only(): void {
        $this->resetAfterTest();
        set_config('bigbluebuttonbn_importrecordings_enabled', 1);
        $dataset = [
            'type' => instance::TYPE_ALL,
            'groups' => null,
            'users' => [['username' => 's1', 'role' => 'student']],
            'recordingsdata' => [
                [['name' => 'Recording1']],
                [['name' => 'Recording2']]
            ],
        ];
        $activityid = $this->create_from_dataset($dataset);
        $instance = instance::get_from_instanceid($activityid);

        // Now create a recording only activity.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Now create a new activity and import the first record.
        $newactivity = $plugingenerator->create_instance([
            'course' => $instance->get_course_id(),
            'type' => instance::TYPE_RECORDING_ONLY,
            'name' => 'Example 2'
        ]);
        $plugingenerator->create_meeting([
            'instanceid' => $newactivity->id,
        ]); // We need to have a meeting created in order to import recordings.
        $newinstance = instance::get_from_instanceid($newactivity->id);
        $recordings = $instance->get_recordings();
        foreach ($recordings as $recording) {
            if ($recording->get('name') == 'Recording1') {
                $recording->create_imported_recording($newinstance);
            }
        }
        $user = \core_user::get_user_by_username('s1');
        $this->setUser($user);
        $getrecordings = $this->get_recordings($newinstance->get_instance_id());
        $data = json_decode($getrecordings['tabledata']['data']);
        // Check that all recordings including the imported recording appear.
        $this->assertCount(3, $data);
        // Set the flags to enable "Show only imported links".
        set_config('bigbluebuttonbn_recordings_imported_default', 1);
        set_config('bigbluebuttonbn_recordings_imported_editable', 0);
        $getrecordings = $this->get_recordings($newinstance->get_instance_id());
        $data = json_decode($getrecordings['tabledata']['data']);
        $this->assertCount(1, $data);
    }

    /**
     * Check if recording are visible/invisible depending on the group.
     *
     * @param string $type
     * @param array $groups
     * @param array $users
     * @param array $recordingsdata
     * @param array $test
     * @param int $coursemode
     *
     * @covers   \mod_bigbluebuttonbn\external\get_recordings::execute
     * @dataProvider recording_group_test_data
     */
    public function test_get_recordings_groups($type, $groups, $users, $recordingsdata, $test, $coursemode): void {
        $this->resetAfterTest();
        $dataset = compact('type', 'groups', 'users', 'recordingsdata', 'test', 'coursemode');
        $activityid = $this->create_from_dataset($dataset);
        $instance = instance::get_from_instanceid($activityid);

        foreach ($dataset['users'] as $userdef) {
            $user = \core_user::get_user_by_username($userdef['username']);
            $this->setUser($user);
            $groups = array_values(groups_get_my_groups());
            $mygroup = !empty($groups) ? end($groups) : null;

            $getrecordings = $this->get_recordings(
                $instance->get_instance_id(), null, !empty($mygroup) ? $mygroup->id : null);
            $allrecordingsnames = [];
            foreach ($recordingsdata as $groups => $rsinfo) {
                $rnames = array_map(function($rdata) {
                    return $rdata['name'];
                }, $rsinfo);
                $allrecordingsnames = array_merge($allrecordingsnames, $rnames);
            }
            // Check users see or do not see recording dependings on their groups.
            foreach ($dataset['test'][$user->username] as $viewablerecordings) {
                $viewablerecordings = $dataset['test'][$user->username];
                $invisiblerecordings = array_diff($allrecordingsnames, $viewablerecordings);
                foreach ($viewablerecordings as $viewablerecordingname) {
                    $this->assertStringContainsString($viewablerecordingname, $getrecordings['tabledata']['data'],
                        "User $user->username, should see recording {$viewablerecordingname}");
                }
                foreach ($invisiblerecordings as $invisiblerecordingname) {
                    $this->assertStringNotContainsString($invisiblerecordingname, $getrecordings['tabledata']['data'],
                        "User $user->username, should not see recording {$viewablerecordingname}");
                }
            }
        }
    }

    /**
     * Recording group test
     *
     * @return array[]
     */
    public static function recording_group_test_data(): array {
        return [
            'visiblegroups' => [
                'type' => instance::TYPE_ALL,
                'groups' => ['G1' => ['s1'], 'G2' => ['s2']],
                'users' => [
                    ['username' => 't1', 'role' => 'editingteacher'],
                    ['username' => 's1', 'role' => 'student'],
                    ['username' => 's2', 'role' => 'student'],
                    ['username' => 's3', 'role' => 'student']
                ],
                'recordingsdata' => [
                    'G1' => [['name' => 'Recording1']],
                    'G2' => [['name' => 'Recording2']],
                    '' => [['name' => 'Recording3']]
                ],
                'test' => [
                    't1' => ['Recording1', 'Recording2', 'Recording3'], // A moderator should see all recordings.
                    's1' => ['Recording1'], // S1 can only see the recordings from his group.
                    's2' => ['Recording2'], // S2 can only see the recordings from his group.
                    's3' => ['Recording3', 'Recording2', 'Recording1']
                    // S3 should see recordings which have no groups and his groups's recording.
                ],
                'coursemode' => VISIBLEGROUPS
            ],
            'separategroups' => [
                'type' => instance::TYPE_ALL,
                'groups' => ['G1' => ['s1'], 'G2' => ['s2']],
                'users' => [
                    ['username' => 't1', 'role' => 'editingteacher'],
                    ['username' => 's1', 'role' => 'student'],
                    ['username' => 's2', 'role' => 'student']
                ],
                'recordingsdata' => [
                    'G1' => [['name' => 'Recording1']],
                    'G2' => [['name' => 'Recording2']],
                    '' => [['name' => 'Recording3']]
                ],
                'test' => [
                    't1' => ['Recording1', 'Recording2', 'Recording3'], // A moderator should see all recordings.
                    's1' => ['Recording1'], // S1 can only see the recordings from his group.
                    's2' => ['Recording2'], // S2 can only see the recordings from his group.
                    's3' => ['Recording3'] // S3 should see recordings which have no groups.
                ],
                'coursemode' => SEPARATEGROUPS
            ]
        ];
    }
}
