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
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\recording;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Tests for bigbluebuttonbn activity overview
 *
 * @covers     \mod_bigbluebuttonbn\courseformat\overview
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class overview_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Test get_actions_overview.
     *
     * @covers ::get_actions_overview
     */
    public function test_get_actions_overview(): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(createrecordings: false);
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
     *
     * @covers ::get_due_date_overview
     */
    public function test_get_due_date_overview(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $clock = $this->mock_clock_with_frozen();
        $timeincrement = DAYSECS;
        $bigbluebuttonbntemplate['timeclose'] = $clock->time() + $timeincrement;
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate, createrecordings: false);
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
     *
     * @covers ::get_extra_date_open
     * @dataProvider get_extra_date_data
     */
    public function test_get_extra_date_open(?int $timeincrement): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $clock = $this->mock_clock_with_frozen();
        if (!is_null($timeincrement)) {
            $bigbluebuttonbntemplate['openingtime'] = $clock->time() + $timeincrement;
        }
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate, createrecordings: false);
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
     *
     * @covers ::get_extra_date_close
     * @dataProvider get_extra_date_data
     */
    public function test_get_extra_date_close(?int $timeincrement): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $clock = $this->mock_clock_with_frozen();
        if (!is_null($timeincrement)) {
            $bigbluebuttonbntemplate['closingtime'] = $clock->time() + $timeincrement;
        }
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate, createrecordings: false);
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
     * @return array
     */
    public static function get_extra_date_data(): array {
        return [
            'tomorrow' => [
                'timeincrement' => DAYSECS,
            ],
            'yesterday' => [
                'timeincrement' => -1 * DAYSECS,
            ],
            'today' => [
                'timeincrement' => 0,
            ],
            'No date' => [
                'timeincrement' => null,
            ],
        ];
    }

    /**
     * Test get_extra_date_close method.
     *
     * @param int $roomtype
     * @param string $expectedtype
     *
     * @covers ::get_extra_room_type_overview
     * @dataProvider get_extra_room_type_overview_data
     */
    public function test_get_extra_room_type_overview(int $roomtype, string $expectedtype): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        $bigbluebuttonbntemplate['type'] = $roomtype;
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate, createrecordings: false);
        ['withoutrecordings' => $instancewa] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setUser($users['t3']);
        $overview = overviewfactory::create($cm);
        $overviewitem  = $overview->get_extra_overview_items();
        $this->assertArrayHasKey('roomtype', $overviewitem);
        $this->assertEquals(
            $expectedtype,
            $overviewitem['roomtype']->get_value(),
        );
    }
    /**
     * Data provider for test_get_due_date_overview.
     *
     * @return array
     */
    public static function get_extra_room_type_overview_data(): array {
        return [
            'All' => [
                'roomtype' => instance::TYPE_ALL,
                'expectedtype' => get_string('instance_type_default', 'bigbluebuttonbn'),
            ],
            'Room Only' => [
                'roomtype' => instance::TYPE_ROOM_ONLY,
                'expectedtype' => get_string('instance_type_room_only', 'bigbluebuttonbn'),
            ],
            'Recording Only' => [
                'roomtype' => instance::TYPE_RECORDING_ONLY,
                'expectedtype' => get_string('instance_type_recording_only', 'bigbluebuttonbn'),
            ],
        ];
    }

    /**
     * Test test_get_extra_recordings_overview.
     *
     * @param string $activityname
     * @param int $recordingcount
     * @throws \coding_exception
     * @throws \moodle_exception
     * @covers ::get_extra_recordings_overview
     * @dataProvider get_extra_recordings_overview_data
     */
    public function test_get_extra_recordings_overview(string $activityname, int $recordingcount): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $bigbluebuttonbntemplate = [];
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $bigbluebuttonbntemplate);
        [$activityname => $instance] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users['t3']);
        $overview = overviewfactory::create($cm);
        $overviewitem  = $overview->get_extra_overview_items();
        $this->assertArrayHasKey('recordings', $overviewitem);
        $this->assertEquals(
            $recordingcount,
            $overviewitem['recordings']->get_value(),
        );
    }

    /**
     * Data provider for test_get_extra_studentsattempted_overview and test_get_extra_totalattempts_overview
     *
     * @return array
     */
    public static function get_extra_recordings_overview_data(): array {

        return [
            'with recordings' => [
                'activityname' => 'withrecordings',
                'recordingcount' => 2,
            ],
            'without recordings' => [
                'activityname' => 'withoutrecordings',
                'recordingcount' => 0,
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
            $this->setup_users_and_activity(createusers: false, createrecordings: false);
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
            $this->setup_users_and_activity(createusers: true);
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
     * @param bool $createrecordings whether to create an attempt for the student.
     * @param array|null $instancedata additional data for the instance.
     * @param array|null $grades the grade to set for the student.
     * @param bool $createusers whether to enrol users in the course.
     * @return array indexed array with 'users', 'course' and 'instance'.
     */
    private function setup_users_and_activity(
        int $groupmode = NOGROUPS,
        bool $createrecordings = true,
        ?array $instancedata = null,
        ?array $grades = null,
        bool $createusers = true,
    ): array {
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
                't3' => ['role' => 'teacher', 'groups' => ['g1']], // T3 will be a moderator in the room.
            ];
            // Enrol users in the course.
            foreach ($data as $username => $userinfo) {
                ['role' => $role, 'groups' => $groups] = $userinfo;
                $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
                foreach ($groups as $group) {
                    if (!isset($groups[$group])) {
                        // Create the group if it does not exist.
                        $groups[$group] = $generator->create_group(['courseid' => $course->id, 'name' => $group]);
                    }
                    // Add the user to the group.
                    groups_add_member($groups[$group], $users[$username]->id);
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
            $instancedata['moderators'] = 'user:t3';// Set T3 as moderator in the room.
        }
        $instances = [];
        $instances['withoutrecordings'] =
            $generator->create_module('bigbluebuttonbn', $instancedata); // Create a second instance with no recordings.
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        $recordings = [];
        if ($createrecordings) {
            // We do that only when we want to create recordings.
            $this->initialise_mock_server();
            $instances['withrecordings'] = $generator->create_module('bigbluebuttonbn', $instancedata);
            $instance = instance::get_from_instanceid($instances['withrecordings']->id);
            // We need to create a meeting for the instance in order to create recordings.
            $bbbgenerator->create_meeting([
                'instanceid' => $instance->get_instance_id(),
                'groupid' => $instance->get_group_id(),
            ]);
            $now = time();
            $recordingstatus = [
                recording::RECORDING_STATUS_AWAITING,
                recording::RECORDING_STATUS_PROCESSED,
                recording::RECORDING_STATUS_PROCESSED,
                recording::RECORDING_STATUS_DISMISSED,
            ];
            foreach ($recordingstatus as $status) {
                $recordings[] = $bbbgenerator->create_recording(
                    array_merge([
                        'bigbluebuttonbnid' => $instance->get_instance_id(),
                        'groupid' => $instance->get_group_id(),
                        'starttime' => $now,
                        'endtime' => $now + HOURSECS,
                        'status' => $status,
                    ])
                );
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
