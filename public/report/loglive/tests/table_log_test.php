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
namespace report_loglive;

use advanced_testcase;
use context_course;
use core_user;

/**
 * Tests for table log and groups.
 *
 * @package    report_loglive
 * @copyright  2024 onwards Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
final class table_log_test extends advanced_testcase {
    /**
     * @var int The course with separate groups.
     */
    const COURSE_SEPARATE_GROUP = 0;
    /**
     * @var int The course with separate groups.
     */
    const COURSE_VISIBLE_GROUP = 1;
    /**
     * @var int The course with separate groups.
     */
    const COURSE_NO_GROUP = 2;
    /**
     * @var array The setup of users.
     */
    const SETUP_USER_DEFS = [
        // Make student2 also member of group1.
        'student' => [
            'student0' => ['group0'],
            'student1' => ['group1'],
            'student2' => ['group0', 'group1'],
            'student3' => [],
        ],
        // Make teacher2 also member of group1.
        'teacher' => [
            'teacher0' => ['group0'],
            'teacher1' => ['group1'],
            'teacher2' => ['group0', 'group1'],
            'teacher3' => [],
        ],
        // Make editingteacher also member of group1.
        'editingteacher' => [
            'editingteacher0' => ['group0'],
            'editingteacher1' => ['group1'],
            'editingteacher2' => ['group0', 'group1'],
        ],
    ];
    /**
     * @var array|\stdClass all users indexed by username.
     */
    private $users = [];
    /**
     * @var array The groups by courses (array of array).
     */
    private $groupsbycourse = [];
    /**
     * @var array The courses.
     */
    private $courses;

    /**
     * Data provider for test_get_table_logs.
     *
     * @return array
     */
    public static function get_report_logs_provider(): array {
        return [
            'separategroups: student 0' => [
                self::COURSE_SEPARATE_GROUP,
                'student0',
                // All users in group 0.
                [
                    'student0', 'student2',
                    'teacher0', 'teacher2',
                    'editingteacher0', 'editingteacher2',
                ],
            ],
            'separategroups: student 1' => [
                self::COURSE_SEPARATE_GROUP,
                'student1',
                // All users in group1.
                [
                    'student1', 'student2',
                    'teacher1', 'teacher2',
                    'editingteacher1', 'editingteacher2',
                ],
            ],
            'separategroups: student 2' => [
                self::COURSE_SEPARATE_GROUP,
                'student2',
                // All users in group0 and group1.
                [
                    'student0', 'student1', 'student2',
                    'teacher0', 'teacher1', 'teacher2',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'separategroups: student3' => [
                self::COURSE_SEPARATE_GROUP,
                'student3',
                // Student 3 is not in any group so should only see user without a group.
                [
                    'student3',
                    'teacher3',
                ],
            ],
            'separategroups: editing teacher 0' => [
                self::COURSE_SEPARATE_GROUP,
                'editingteacher0',
                // All users including student 3 as we can see all users (event the one not in a group).
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'separategroups: teacher 0' => [
                self::COURSE_SEPARATE_GROUP,
                'teacher0',
                // All users in group 0.
                [
                    'student0', 'student2',
                    'teacher0', 'teacher2',
                    'editingteacher0', 'editingteacher2',
                ],
            ],
            'separategroups: teacher 2' => [
                self::COURSE_SEPARATE_GROUP,
                'teacher2',
                // All users in group0 and group1.
                [
                    'student0', 'student1', 'student2',
                    'teacher0', 'teacher1', 'teacher2',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'separategroups: teacher 3' => [
                self::COURSE_SEPARATE_GROUP,
                'teacher3',
                // Teacher 3 is not in any group so should only see user without a group.
                [
                    'student3',
                    'teacher3',
                ],
            ],
            'visiblegroup: editing teacher' => [
                self::COURSE_VISIBLE_GROUP,
                'editingteacher0',
                // All users.
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'visiblegroup: student' => [
                self::COURSE_VISIBLE_GROUP,
                'student0',
                // All users.
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'nogroup: teacher 0' => [
                self::COURSE_VISIBLE_GROUP,
                'teacher2',
                // All users.
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'nogroup: editing teacher 0' => [
                self::COURSE_VISIBLE_GROUP,
                'editingteacher0',
                // All users.
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'nogroup: student' => [
                self::COURSE_VISIBLE_GROUP,
                'student0',
                // All users.
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
        ];
    }

    /**
     * Set up a course with two groups, three students being each in one of the groups,
     * two teachers each in either group while the second teacher is also member of the other group.
     *
     * @return void
     * @throws \coding_exception
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();
        $this->resetAfterTest();
        $this->preventResetByRollback(); // This is to ensure that we can actually trigger event and record them in the log store.
        $this->courses[self::COURSE_SEPARATE_GROUP] = $this->getDataGenerator()->create_course(['groupmode' => SEPARATEGROUPS]);
        $this->courses[self::COURSE_VISIBLE_GROUP] = $this->getDataGenerator()->create_course(['groupmode' => VISIBLEGROUPS]);
        $this->courses[self::COURSE_NO_GROUP] = $this->getDataGenerator()->create_course();

        foreach ($this->courses as $coursetype => $course) {
            if ($coursetype == self::COURSE_NO_GROUP) {
                continue;
            }
            $this->groupsbycourse[$coursetype] = [];
            $this->groupsbycourse[$coursetype]['group0'] =
                $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'group0']);
            $this->groupsbycourse[$coursetype]['group1'] =
                $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'group1']);
        }

        foreach (self::SETUP_USER_DEFS as $role => $userdefs) {
            foreach ($userdefs as $username => $groups) {
                $user = $this->getDataGenerator()->create_user(
                    [
                        'username' => $username,
                        'firstname' => "FN{$role}{$username}",
                        'lastname' => "LN{$role}{$username}",
                    ]);
                foreach ($this->courses as $coursetype => $course) {
                    $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
                    foreach ($groups as $groupname) {
                        if ($coursetype == self::COURSE_NO_GROUP) {
                            continue;
                        }
                        $this->getDataGenerator()->create_group_member([
                            'groupid' => $this->groupsbycourse[$coursetype][$groupname]->id,
                            'userid' => $user->id,
                        ]);
                    }
                }
                $this->users[$username] = $user;
            }
        }
        // Configure log store.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        get_log_manager(true);
        $DB->delete_records('logstore_standard_log');

        foreach ($this->courses as $course) {
            foreach ($this->users as $user) {
                $eventdata = [
                    'context' => context_course::instance($course->id),
                    'userid' => $user->id,
                ];
                $event = \core\event\course_viewed::create($eventdata);
                $event->trigger();
            }
        }
    }

    /**
     * Test table_log
     *
     * @param int $courseindex
     * @param string $username
     * @param array $expectedusers
     * @covers       \report_log_renderable::get_user_list
     * @dataProvider get_report_logs_provider
     * @return void
     */
    public function test_get_table_logs(int $courseindex, string $username, array $expectedusers): void {
        $manager = get_log_manager();
        $stores = $manager->get_readers();
        $store = $stores['logstore_standard'];
        // Build the report.
        $url = new \moodle_url("/report/loglive/index.php");
        $renderable = new \report_loglive_renderable('logstore_standard', $this->courses[$courseindex], $url);
        $table = $renderable->get_table();
        $currentuser = $this->users[$username];
        $this->setUser($currentuser->id);
        $store->flush();
        $table->query_db(100);
        $filteredevents =
            array_filter(
                $table->rawdata, fn($event) => get_class($event) === \core\event\course_viewed::class
            );
        $usernames = array_map(
            function($event) {
                $user = core_user::get_user($event->userid, '*', MUST_EXIST);
                return $user->username;
            },
            $filteredevents);
        sort($expectedusers);
        sort($usernames);
        $this->assertEquals($expectedusers, $usernames);
    }
}
