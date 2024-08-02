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

namespace report_log;

use context_course;
use core_user;

/**
 * Class report_log\renderable_test to cover functions in \report_log_renderable.
 *
 * @package    report_log
 * @copyright  2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class renderable_test extends \advanced_testcase {
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
     * Get the data provider for test_get_user_list().
     *
     * @return array
     */
    public static function get_user_visibility_list_provider(): array {
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
            'separategroups: teacher 2 with group set to group 1' => [
                self::COURSE_SEPARATE_GROUP,
                'teacher2',
                // All users in group1.
                [
                    'student1', 'student2',
                    'teacher1', 'teacher2',
                    'editingteacher1', 'editingteacher2',
                ],
                'group1',
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
            'visiblegroup: teacher 0' => [
                self::COURSE_VISIBLE_GROUP,
                'teacher2',
                // All users.
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'visiblegroup: editing teacher 0' => [
                self::COURSE_VISIBLE_GROUP,
                'editingteacher0',
                // All users.
                [
                    'student0', 'student1', 'student2', 'student3',
                    'teacher0', 'teacher1', 'teacher2', 'teacher3',
                    'editingteacher0', 'editingteacher1', 'editingteacher2',
                ],
            ],
            'visiblegroup: teacher 2 with group set to group 1' => [
                self::COURSE_VISIBLE_GROUP,
                'teacher2',
                // All users in group1.
                [
                    'student1', 'student2',
                    'teacher1', 'teacher2',
                    'editingteacher1', 'editingteacher2',
                ],
                'group1',
            ],
        ];
    }

    /**
     * Data provider for test_get_group_list().
     *
     * @return array
     */
    public static function get_group_list_provider(): array {
        return [
            // The student sees his own group only.
            'separategroup: student in one group' => [self::COURSE_SEPARATE_GROUP, 'student0', 1],
            'separategroup: student in two groups' => [self::COURSE_SEPARATE_GROUP, 'student2', 2],
            // While the teacher is not allowed to see all groups.
            'separategroup: teacher in one group' => [self::COURSE_SEPARATE_GROUP, 'teacher0', 1],
            'separategroup: teacher in two groups' => [self::COURSE_SEPARATE_GROUP, 'teacher2', 2],
            // But editing teacher should see all.
            'separategroup: editingteacher' => [self::COURSE_SEPARATE_GROUP, 'editingteacher0', 2],
            // The student sees all groups.
            'visiblegroup: student in one group' => [self::COURSE_VISIBLE_GROUP, 'student0', 2],
            // Same for teacher.
            'visiblegroup: teacher in one group' => [self::COURSE_VISIBLE_GROUP, 'teacher0', 2],
            // And editing teacher.
            'visiblegroup: editingteacher' => [self::COURSE_VISIBLE_GROUP, 'editingteacher0', 2],
            // No group.
            'nogroups: student in one group' => [self::COURSE_NO_GROUP, 'student0', 0],
            // Same for teacher.
            'nogroups: teacher in one group' => [self::COURSE_NO_GROUP, 'teacher0', 0],
            // And editing teacher.
            'nogroups: editingteacher' => [self::COURSE_NO_GROUP, 'editingteacher0', 0],
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
        parent::setUp();
        $this->resetAfterTest();
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
    }

    /**
     * Test report_log_renderable::get_user_list().
     *
     * @param int $courseindex
     * @param string $username
     * @param array $expectedusers
     * @param string|null $groupname
     * @covers       \report_log_renderable::get_user_list
     * @dataProvider get_user_visibility_list_provider
     * @return void
     */
    public function test_get_user_list(int $courseindex, string $username, array $expectedusers,
        ?string $groupname = null): void {
        global $PAGE, $CFG;
        $currentcourse = $this->courses[$courseindex];
        $PAGE->set_url('/report/log/index.php?id=' . $currentcourse->id);
        // Fetch all users of group 1 and the guest user.
        $currentuser = $this->users[$username];
        $this->setUser($currentuser->id);
        $groupid = 0;
        if ($groupname) {
            $groupid = $this->groupsbycourse[$courseindex][$groupname]->id;
        }
        $renderable = new \report_log_renderable(
            "", (int) $currentcourse->id, $currentuser->id, 0, '', $groupid);
        $userlist = $renderable->get_user_list();
        unset($userlist[$CFG->siteguest]); // We ignore guest.
        $usersid = array_keys($userlist);

        $users = array_map(function($userid) {
            return core_user::get_user($userid);
        }, $usersid);

        // Now check that the users are the expected ones.
        asort($expectedusers);
        $userlistbyname = array_column($users, 'username');
        asort($userlistbyname);
        $this->assertEquals(array_values($expectedusers), array_values($userlistbyname));

        // Check that users are in order lastname > firstname > id.
        $sortedusers = $users;
        // Sort user by lastname > firstname > id.
        usort($sortedusers, function($a, $b) {
            if ($a->lastname != $b->lastname) {
                return $a->lastname <=> $b->lastname;
            }
            if ($a->firstname != $b->firstname) {
                return $a->firstname <=> $b->firstname;
            }
            return $a->id <=> $b->id;
        });

        $sortedusernames = array_column($sortedusers, 'username');
        $userlistbyname = array_column($users, 'username');
        $this->assertEquals($sortedusernames, $userlistbyname);

    }

    /**
     * Test report_log_renderable::get_group_list().
     *
     * @covers       \report_log_renderable::get_group_list
     * @dataProvider get_group_list_provider
     * @return void
     */
    public function test_get_group_list($courseindex, $username, $expectedcount): void {
        global $PAGE;
        $PAGE->set_url('/report/log/index.php?id=' . $this->courses[$courseindex]->id);
        $this->setUser($this->users[$username]->id);
        $renderable = new \report_log_renderable("", (int) $this->courses[$courseindex]->id, $this->users[$username]->id);
        $groups = $renderable->get_group_list();
        $this->assertCount($expectedcount, $groups);
    }

    /**
     * Test table_log
     *
     * @param int $courseindex
     * @param string $username
     * @param array $expectedusers
     * @param string|null $groupname
     * @covers       \report_log_renderable::get_user_list
     * @dataProvider get_user_visibility_list_provider
     * @return void
     */
    public function test_get_table_logs(int $courseindex, string $username, array $expectedusers, ?string $groupname = null): void {
        global $DB, $PAGE;
        $this->preventResetByRollback(); // This is to ensure that we can actually trigger event and record them in the log store.
        // Configure log store.
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        $manager = get_log_manager(true);
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
        $stores = $manager->get_readers();
        $store = $stores['logstore_standard'];
        // Build the report.
        $currentuser = $this->users[$username];
        $this->setUser($currentuser->id);
        $groupid = 0;
        if ($groupname) {
            $groupid = $this->groupsbycourse[$courseindex][$groupname]->id;
        }
        $PAGE->set_url('/report/log/index.php?id=' . $this->courses[$courseindex]->id);
        $renderable = new \report_log_renderable("", (int) $this->courses[$courseindex]->id, 0, 0, '', $groupid);
        $renderable->setup_table();
        $table = $renderable->tablelog;
        $store->flush();
        $table->query_db(100);
        $usernames = [];
        foreach ($table->rawdata as $event) {
            if (get_class($event) !== \core\event\course_viewed::class) {
                continue;
            }
            $user = core_user::get_user($event->userid, '*', MUST_EXIST);
            $usernames[] = $user->username;
        }
        sort($expectedusers);
        sort($usernames);
        $this->assertEquals($expectedusers, $usernames);
    }
}
