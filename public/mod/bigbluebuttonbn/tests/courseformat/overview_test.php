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

namespace mod_bigbluebuttonbn\courseformat;

use core_courseformat\local\overview\overviewfactory;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\recording;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Tests for bigbluebuttonbn activity overview
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    use testcase_helper_trait;
    /**
     * Test get_actions_overview.
     */
    public function test_get_actions_overview(): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity();
        ['withoutrecordings' => $instancewa] = $instances;
        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        // Students or non moderators have no action column.
        foreach (['s1', 's2', 't1', 't2'] as $username) {
            $this->setUser($users[$username]);
            $this->assertNull(overviewfactory::create($cm)->get_actions_overview());
        }
        // T3 is a moderator and should have an action column..
        $this->setUser($users['t3']);
        $items = overviewfactory::create($cm)->get_actions_overview();
        $this->assertNotNull($items);
        $this->assertEquals(get_string('actions'), $items->get_name());
        // Admin should have an action column too.
        $this->setAdminUser();
        $this->assertNotNull(overviewfactory::create($cm)->get_actions_overview());
        $this->assertNotNull($items);
        $this->assertEquals(get_string('actions'), $items->get_name());
    }

    /**
     * Test get_due_date_overview method.
     *
     * Note here: we do not use the due date overview for bigbluebuttonbn activities as we have a opening date
     * and a closing date instead. If we were to use the due date overview as a closing date, this column
     * would not be displayed in the right order - next to closing date column (probably separated by the completion status column).
     * So we decided to not use the due date overview at all and instead add two extra date columns:
     * - opens: the opening date of the activity.
     * - closes: the closing date of the activity.
     * This test just checks that the due date overview is not used in the bigbluebuttonbn activity overview.
     */
    public function test_get_due_date_overview(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $clock = $this->mock_clock_with_frozen();
        $timeincrement = DAYSECS;
        $bigbluebuttonbntemplate['timeclose'] = $clock->time() + $timeincrement;
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate);
        ['withoutrecordings' => $instancewa] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setUser($users['s1']);
        $overview = overviewfactory::create($cm);
        $this->assertNull($overview->get_due_date_overview());
    }

    /**
     * Test get_extra_date_open method.
     *
     * @param int|null $timeincrement
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_date_data')]
    public function test_get_extra_date_open(?int $timeincrement): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $clock = $this->mock_clock_with_frozen();
        if (!is_null($timeincrement)) {
            $bigbluebuttonbntemplate['openingtime'] = $clock->time() + $timeincrement;
        }
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate);
        ['withoutrecordings' => $instancewa] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setUser($users['s1']);
        $overview = overviewfactory::create($cm);
        $this->assertEquals(
            is_null($timeincrement) ? null : $clock->time() + $timeincrement,
            $overview->get_extra_date_open()->get_value(),
        );
    }

    /**
     * Test get_extra_date_close method.
     *
     * @param int|null $timeincrement
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_date_data')]
    public function test_get_extra_date_close(?int $timeincrement): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $clock = $this->mock_clock_with_frozen();
        if (!is_null($timeincrement)) {
            $bigbluebuttonbntemplate['closingtime'] = $clock->time() + $timeincrement;
        }
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate);
        ['withoutrecordings' => $instancewa] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setUser($users['s1']);
        $overview = overviewfactory::create($cm);
        $this->assertEquals(
            is_null($timeincrement) ? null : $clock->time() + $timeincrement,
            $overview->get_extra_date_close()->get_value(),
        );
    }

    /**
     * Data provider for test_get_due_date_overview.
     *
     * @return \Generator
     */
    public static function get_extra_date_data(): \Generator {
        yield 'tomorrow' => [
            'timeincrement' => DAYSECS,
        ];
        yield 'yesterday' => [
            'timeincrement' => -1 * DAYSECS,
        ];
        yield 'today' => [
            'timeincrement' => 0,
        ];
        yield 'No date' => [
            'timeincrement' => null,
        ];
    }

    /**
     * Test get_extra_date_close method.
     *
     * @param int $roomtype
     * @param string $expectedtype
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_room_type_overview_data')]
    public function test_get_extra_room_type_overview(int $roomtype, string $expectedtype): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $bigbluebuttonbntemplate['type'] = $roomtype;
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate);
        ['withoutrecordings' => $instancewa] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setUser($users['t3']);
        $overview = overviewfactory::create($cm);
        $overviewitem = $overview->get_extra_overview_items();
        $this->assertArrayHasKey('roomtype', $overviewitem);
        $this->assertEquals(
            $expectedtype,
            $overviewitem['roomtype']->get_value(),
        );
    }

    /**
     * Data provider for test_get_due_date_overview.
     *
     * @return \Generator
     */
    public static function get_extra_room_type_overview_data(): \Generator {
        yield 'All' => [
            'roomtype' => instance::TYPE_ALL,
            'expectedtype' => get_string('instance_type_default', 'bigbluebuttonbn'),
        ];
        yield 'Room Only' => [
            'roomtype' => instance::TYPE_ROOM_ONLY,
            'expectedtype' => get_string('instance_type_room_only', 'bigbluebuttonbn'),
        ];
        yield 'Recording Only' => [
            'roomtype' => instance::TYPE_RECORDING_ONLY,
            'expectedtype' => get_string('instance_type_recording_only', 'bigbluebuttonbn'),
        ];
    }

    /**
     * Test test_get_extra_recordings_overview.
     *
     * @param string $activityname
     * @param int $recordingcount
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_recordings_overview_data')]
    public function test_get_extra_recordings_overview(string $activityname, int $recordingcount): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(
                instancedata: $bigbluebuttonbntemplate,
                recordingstocreate: [
                    ['status' => recording::RECORDING_STATUS_AWAITING],
                    ['status' => recording::RECORDING_STATUS_PROCESSED],
                    ['status' => recording::RECORDING_STATUS_PROCESSED],
                    ['status' => recording::RECORDING_STATUS_DISMISSED],
                ]
            );
        [$activityname => $instance] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users['t3']);
        $overview = overviewfactory::create($cm);
        $overviewitem = $overview->get_extra_overview_items();
        $this->assertArrayHasKey('recordings', $overviewitem);
        $this->assertEquals(
            $recordingcount,
            $overviewitem['recordings']->get_value(),
            'User t3 should see ' . $recordingcount . ' recordings, but got: ' . $overviewitem['recordings']->get_value()
        );
    }

    /**
     * Data provider for test_get_extra_studentsattempted_overview and test_get_extra_totalattempts_overview
     *
     * @return \Generator
     */
    public static function get_extra_recordings_overview_data(): \Generator {
        yield 'with recordings' => [
            'activityname' => 'withrecordings',
            'recordingcount' => 2,
        ];
        yield 'without recordings' => [
            'activityname' => 'withoutrecordings',
            'recordingcount' => 0,
        ];
    }

    /**
     * Test test_get_extra_recordings_overview.
     *
     * @param string $activityname
     * @param int $groupmode
     * @param array $recordingcounts Array of recording counts per user in the form of ['group' => string|null, status=> int].
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_recordings_overview_with_groups_data')]
    public function test_get_extra_recordings_overview_with_groups(
        string $activityname,
        int $groupmode,
        array $recordingcounts
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(
                groupmode: $groupmode,
                instancedata: $bigbluebuttonbntemplate,
                recordingstocreate: [
                    ['group' => 'g1', 'status' => recording::RECORDING_STATUS_PROCESSED],
                    ['group' => 'g1', 'status' => recording::RECORDING_STATUS_PROCESSED],
                    ['group' => 'g2', 'status' => recording::RECORDING_STATUS_PROCESSED],
                    ['group' => null, 'status' => recording::RECORDING_STATUS_PROCESSED], // No group for the this recording.
                ],
                moderators: 'user:t1,user:t2,user:t3,user:t4,user:t5'
            );
        [$activityname => $instance] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        foreach ($recordingcounts as $username => $recordingcount) {
            if (!isset($users[$username])) {
                continue; // Skip if user does not exist.
            }
            $this->setUser($users[$username]);
            $overview = overviewfactory::create($cm);
            $overviewitem = $overview->get_extra_overview_items();
            if ($overview->has_error()) {
                $this->assertNull($recordingcount);
                continue; // If there is an error we should not check the recording count because it is not displayed.
            }
            $this->assertArrayHasKey('recordings', $overviewitem);
            $this->assertNotNull($overviewitem['recordings']);
            $this->assertEquals(
                $recordingcount,
                $overviewitem['recordings']->get_value(),
                "Failed for user: {$username}"
            );
        }
    }

    /**
     * Data provider for test_get_extra_studentsattempted_overview and test_get_extra_totalattempts_overview
     *
     * @return \Generator
     */
    public static function get_extra_recordings_overview_with_groups_data(): \Generator {
        // The setup is as follows:
        // - T1 is in group but is editing teacher so can see recordings from group g1 and g2.
        // - T2 is not in any group but is editing teacher so can see recordings from group g1 and g2.
        // - T3 is in group g1.
        // - T4 is not in any group.
        // - T5 is in group g2.
        // - T1, T2, T3, T4 and T5 are all moderators in the room.
        // We have 3 recordings in total (all processed:
        // - 2 recordings in group g1.
        // - 1 recording in group g2.
        yield 'With separate groups' => [
            'activityname' => 'withrecordings',
            'groupmode' => SEPARATEGROUPS,
            'recordingcounts' => [
                't1' => 4, // T1 is in group g1 and can see recordings from group g2 (as has all access as editing teacher).
                't2' => 4, // T2 is not in any group but can see recordings from group g1 and g2 as editing teacher.
                't3' => 2, // T3 is in group g1 so can see recordings from group g1.
                't4' => null, // T4 is not in any group so should not see any recording.
                't5' => 1, // T5 is in group g2 and can see recordings from group g2.
            ],
        ];
        yield 'With no groups' => [
            'activityname' => 'withrecordings',
            'groupmode' => NOGROUPS,
            'recordingcounts' => [
                't1' => 4,
                't2' => 4,
                't3' => 4,
                't4' => 4,
                't5' => 4,
            ],
        ];
        yield 'With visible groups' => [
            'activityname' => 'withrecordings',
            'groupmode' => VISIBLEGROUPS,
            'recordingcounts' => [
                't1' => 4,
                't2' => 4,
                't3' => 4,
                't4' => 4,
                't5' => 4,
            ],
        ];
    }

    /**
     * Test get_extra_overview_items when there are no users in the course
     *
     * @return void
     */
    public function test_get_extra_overview_no_users(): void {
        $this->resetAfterTest();
        ['course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(createusers: false);
        ['withoutrecordings' => $instancewa] = $instances; // This has no user so no attempt too.
        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setAdminUser();
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertArrayHasKey('recordings', $items);
        $this->assertEquals(0, $items['recordings']->get_value());
        $this->assertArrayHasKey('roomtype', $items);
    }

    /**
     * Test check columns and content depeding on role (moderator, admin, other (viewer)).
     *
     * @return void
     */
    public function test_all_get_extra_overview_items(): void {
        $this->resetAfterTest();
        ['course' => $course, 'instances' => $instances, 'users' => $users] =
            $this->setup_users_and_activity();
        ['withoutrecordings' => $instancewa] = $instances; // This has no user so no attempt too.
        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setAdminUser();
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertNotEmpty($items['opens']);
        $this->assertNotEmpty($items['closes']);
        $this->assertNotEmpty($items['roomtype']);
        $this->assertNotEmpty($items['recordings']);
        // Normal viewers.
        $viewers = ['s1', 's2', 't1', 't2'];
        foreach ($viewers as $username) {
            $this->setUser($users[$username]);
            $items = overviewfactory::create($cm)->get_extra_overview_items();
            $this->assertNotEmpty($items['opens']);
            $this->assertNotEmpty($items['closes']);
            $this->assertEmpty($items['roomtype']);
            $this->assertEmpty($items['recordings']);
        }
        $moderators = ['t3']; // T3 is a moderator in the room.
        foreach ($moderators as $username) {
            $this->setUser($users[$username]);
            $items = overviewfactory::create($cm)->get_extra_overview_items();
            $this->assertNotEmpty($items['opens']);
            $this->assertNotEmpty($items['closes']);
            $this->assertNotEmpty($items['roomtype']);
            $this->assertNotEmpty($items['recordings']);
        }
    }

    /**
     * Setup users and activity for testing answers retrieval.
     *
     * @param int $groupmode the group mode to use for the course.
     * @param array|null $instancedata additional data for the instance.
     * @param array|null $grades the grade to set for the student.
     * @param bool $createusers whether to enrol users in the course.
     * @param array $recordingstocreate create recordings for the instance in the form of
     *  ['group' => null, 'status' => recording::RECORDING_STATUS_PROCESSED].
     * @param string $moderators the moderators to set for the instance.
     * @return array indexed array with 'users', 'course' and 'instance'.
     */
    private function setup_users_and_activity(
        int $groupmode = NOGROUPS,
        ?array $instancedata = null,
        ?array $grades = null,
        bool $createusers = true,
        array $recordingstocreate = [],
        string $moderators = 'user:t3'
    ): array {
        global $CFG;
        require_once($CFG->dirroot . '/lib/grade/constants.php');
        $users = [];
        $generator = $this->getDataGenerator();
        $courseparams = [];
        if ($groupmode !== NOGROUPS) {
            // Set the group mode for the course.
            $courseparams['groupmode'] = $groupmode;
            $courseparams['groupmodeforce'] = 1; // Force the group mode.
        }
        $course = $generator->create_course($courseparams);
        $groups = [];
        if ($createusers) {
            $data = [
                's1' => ['role' => 'student', 'groups' => ['g1']],
                's2' => ['role' => 'student', 'groups' => ['g2']],
                't1' => ['role' => 'editingteacher', 'groups' => ['g1']],
                't2' => ['role' => 'editingteacher', 'groups' => []],
                't3' => ['role' => 'teacher', 'groups' => ['g1']],
                't4' => ['role' => 'teacher', 'groups' => []],
                't5' => ['role' => 'teacher', 'groups' => ['g2']],
            ];
            // Enrol users in the course.
            foreach ($data as $username => $userinfo) {
                ['role' => $role, 'groups' => $usergroups] = $userinfo;
                $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
                foreach ($usergroups as $usergroup) {
                    if (!isset($groups[$usergroup])) {
                        // Create the group if it does not exist.
                        $groups[$usergroup] = $generator->create_group(['courseid' => $course->id, 'name' => $usergroup]);
                    }
                    // Add the user to the group.
                    groups_add_member($groups[$usergroup], $users[$username]->id);
                }
            }
        }
        $this->setAdminUser();
        $instancedata = $instancedata ?? [];
        $instancedata = array_merge($instancedata, [
            'course' => $course->id,
            'gradetype' => GRADE_TYPE_VALUE, // Use highest grade for grading.
        ]);
        if ($createusers) {
            // Add the users to the instance data.
            // By default, the moderators are set to t3.
            $instancedata['moderators'] = $moderators;
        }
        $instances = [];
        $instances['withoutrecordings'] =
            $generator->create_module('bigbluebuttonbn', $instancedata); // Create a second instance with no recordings.
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $recordings = [];
        if (!empty($recordingstocreate)) {
            // We do that only when we want to create recordings.
            $this->initialise_mock_server();
            $instances['withrecordings'] = $generator->create_module('bigbluebuttonbn', $instancedata);
            $instance = instance::get_from_instanceid($instances['withrecordings']->id);
            // We need to create a meeting for the instance in order to create recordings.
            $now = $this->mock_clock_with_frozen()->time();
            $hasmeetingforgroup = [];
            foreach ($recordingstocreate as $recordinginfo) {
                if ($groupmode === NOGROUPS) {
                    $groupname = null;
                } else {
                    $groupname = $recordinginfo['group'] ?? null;
                }

                $status = $recordinginfo['status'] ?? recording::RECORDING_STATUS_PROCESSED;
                $currentgroupid = $groups[$groupname]->id ?? 0;
                $instance = instance::get_from_instanceid($instances['withrecordings']->id);
                if (!isset($hasmeetingforgroup[$currentgroupid])) {
                    // Create a meeting for the group if it does not exist.
                    $meetingdata = [
                        'instanceid' => $instance->get_instance_id(),
                    ];
                    if ($currentgroupid) {
                        $meetingdata['groupid'] = $currentgroupid;
                    }
                    $bbbgenerator->create_meeting($meetingdata);
                    $hasmeetingforgroup[$currentgroupid] = true;
                }
                $recordingdata = [
                    'bigbluebuttonbnid' => $instance->get_instance_id(),
                    'starttime' => $now,
                    'endtime' => $now + HOURSECS,
                    'status' => $status,
                ];
                if (!empty($currentgroupid)) {
                    $recordingdata['groupid'] = $currentgroupid;
                }
                $recordings[$currentgroupid] = $bbbgenerator->create_recording($recordingdata);
            }
        }
        if ($grades) {
            if ($grades) {
                foreach ($instances as $instance) {
                    foreach ($grades as $grade) {
                        $instancedata = (object) [
                            'iteminstance' => $instance->id,
                            'itemmodule' => 'bigbluebuttonbn',
                            'itemtype' => 'mod',
                            'courseid' => $course->id,
                        ];
                        $instancedata->rawgrade = $grade;
                        bigbluebuttonbn_grade_item_update($instancedata);
                    }
                }
            }
        }
        return [
            'users' => $users,
            'course' => $course,
            'instances' => $instances,
            'recordings' => $recordings,
        ];
    }
}
