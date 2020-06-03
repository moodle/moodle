<?php
// This file is part of Moodle - https://moodle.org/
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
 * Provides {@link core_user_table_participants_search_test} class.
 *
 * @package   core_user
 * @category  test
 * @copyright 2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_user\table;

use advanced_testcase;
use context_course;
use context_coursecat;
use core_table\local\filter\filter;
use core_table\local\filter\integer_filter;
use core_table\local\filter\string_filter;
use core_user\table\participants_filterset;
use core_user\table\participants_search;
use moodle_recordset;
use stdClass;

/**
 * Tests for the implementation of {@link core_user_table_participants_search} class.
 *
 * @copyright 2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_search_test extends advanced_testcase {

    /**
     * Helper to convert a moodle_recordset to an array of records.
     *
     * @param moodle_recordset $recordset
     * @return array
     */
    protected function convert_recordset_to_array(moodle_recordset $recordset): array {
        $records = [];
        foreach ($recordset as $record) {
            $records[$record->id] = $record;
        }
        $recordset->close();

        return $records;
    }

    /**
     * Create and enrol a set of users into the specified course.
     *
     * @param stdClass $course
     * @param int $count
     * @param null|string $role
     * @return array
     */
    protected function create_and_enrol_users(stdClass $course, int $count, ?string $role = null): array {
        $this->resetAfterTest(true);
        $users = [];

        for ($i = 0; $i < $count; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $course->id, $role);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Create a new course with several types of user.
     *
     * @param int $editingteachers The number of editing teachers to create in the course.
     * @param int $teachers The number of non-editing teachers to create in the course.
     * @param int $students The number of students to create in the course.
     * @param int $norole The number of users with no role to create in the course.
     * @return stdClass
     */
    protected function create_course_with_users(int $editingteachers, int $teachers, int $students, int $norole): stdClass {
        $data = (object) [
            'course' => $this->getDataGenerator()->create_course(),
            'editingteachers' => [],
            'teachers' => [],
            'students' => [],
            'norole' => [],
        ];

        $data->context = context_course::instance($data->course->id);

        $data->editingteachers = $this->create_and_enrol_users($data->course, $editingteachers, 'editingteacher');
        $data->teachers = $this->create_and_enrol_users($data->course, $teachers, 'teacher');
        $data->students = $this->create_and_enrol_users($data->course, $students, 'student');
        $data->norole = $this->create_and_enrol_users($data->course, $norole);

        return $data;
    }
    /**
     * Ensure that the roles filter works as expected with the provided test cases.
     *
     * @param array $usersdata The list of users and their roles to create
     * @param array $testroles The list of roles to filter by
     * @param int $jointype The join type to use when combining filter values
     * @param int $count The expected count
     * @param array $expectedusers
     * @dataProvider role_provider
     */
    public function test_roles_filter(array $usersdata, array $testroles, int $jointype, int $count, array $expectedusers): void {
        global $DB;

        $roles = $DB->get_records_menu('role', [], '', 'shortname, id');

        // Remove the default role.
        set_config('roleid', 0, 'enrol_manual');

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        $category = $DB->get_record('course_categories', ['id' => $course->category]);
        $categorycontext = context_coursecat::instance($category->id);

        $users = [];

        foreach ($usersdata as $username => $userdata) {
            $user = $this->getDataGenerator()->create_user(['username' => $username]);

            if (array_key_exists('courseroles', $userdata)) {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, null);
                foreach ($userdata['courseroles'] as $rolename) {
                    $this->getDataGenerator()->role_assign($roles[$rolename], $user->id, $coursecontext->id);
                }
            }

            if (array_key_exists('categoryroles', $userdata)) {
                foreach ($userdata['categoryroles'] as $rolename) {
                    $this->getDataGenerator()->role_assign($roles[$rolename], $user->id, $categorycontext->id);
                }
            }
            $users[$username] = $user;
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(1, 1, 1, 1);

        // Create the basic filter.
        $filterset = new participants_filterset();
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Create the role filter.
        $rolefilter = new integer_filter('roles');
        $filterset->add_filter($rolefilter);

        // Configure the filter.
        foreach ($testroles as $rolename) {
            $rolefilter->add_filter_value((int) $roles[$rolename]);
        }
        $rolefilter->set_join_type($jointype);

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);
        $rs = $search->get_participants();
        $this->assertInstanceOf(moodle_recordset::class, $rs);
        $records = $this->convert_recordset_to_array($rs);

        $this->assertCount($count, $records);
        $this->assertEquals($count, $search->get_total_participants_count());

        foreach ($expectedusers as $expecteduser) {
            $this->assertArrayHasKey($users[$expecteduser]->id, $records);
        }
    }

    /**
     * Data provider for role tests.
     *
     * @return array
     */
    public function role_provider(): array {
        $tests = [
            // Users who only have one role each.
            'Users in each role' => (object) [
                'users' => [
                    'a' => [
                        'courseroles' => [
                            'student',
                        ],
                    ],
                    'b' => [
                        'courseroles' => [
                            'student',
                        ],
                    ],
                    'c' => [
                        'courseroles' => [
                            'editingteacher',
                        ],
                    ],
                    'd' => [
                        'courseroles' => [
                            'editingteacher',
                        ],
                    ],
                    'e' => [
                        'courseroles' => [
                            'teacher',
                        ],
                    ],
                    'f' => [
                        'courseroles' => [
                            'teacher',
                        ],
                    ],
                    // User is enrolled in the course without role.
                    'g' => [
                        'courseroles' => [
                        ],
                    ],

                    // User is a category manager and also enrolled without role in the course.
                    'h' => [
                        'courseroles' => [
                        ],
                        'categoryroles' => [
                            'manager',
                        ],
                    ],

                    // User is a category manager and not enrolled in the course.
                    // This user should not show up in any filter.
                    'i' => [
                        'categoryroles' => [
                            'manager',
                        ],
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No role filter' => (object) [
                        'roles' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 8,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'ANY: Filter on student' => (object) [
                        'roles' => ['student'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],
                    'ANY: Filter on student, teacher' => (object) [
                        'roles' => ['student', 'teacher'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'e',
                            'f',
                        ],
                    ],
                    'ANY: Filter on student, manager (category level role)' => (object) [
                        'roles' => ['student', 'manager'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'h',
                        ],
                    ],
                    'ANY: Filter on student, coursecreator (not assigned)' => (object) [
                        'roles' => ['student', 'coursecreator'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No role filter' => (object) [
                        'roles' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 8,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'ALL: Filter on student' => (object) [
                        'roles' => ['student'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],
                    'ALL: Filter on student, teacher' => (object) [
                        'roles' => ['student', 'teacher'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Filter on student, manager (category level role))' => (object) [
                        'roles' => ['student', 'manager'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Filter on student, coursecreator (not assigned))' => (object) [
                        'roles' => ['student', 'coursecreator'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No role filter' => (object) [
                        'roles' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 8,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on student' => (object) [
                        'roles' => ['student'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 6,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on student, teacher' => (object) [
                        'roles' => ['student', 'teacher'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'c',
                            'd',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on student, manager (category level role))' => (object) [
                        'roles' => ['student', 'manager'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                        ],
                    ],
                    'NONE: Filter on student, coursecreator (not assigned))' => (object) [
                        'roles' => ['student', 'coursecreator'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 6,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                ],
            ],
            'Users with multiple roles' => (object) [
                'users' => [
                    'a' => [
                        'courseroles' => [
                            'student',
                        ],
                    ],
                    'b' => [
                        'courseroles' => [
                            'student',
                            'teacher',
                        ],
                    ],
                    'c' => [
                        'courseroles' => [
                            'editingteacher',
                        ],
                    ],
                    'd' => [
                        'courseroles' => [
                            'editingteacher',
                        ],
                    ],
                    'e' => [
                        'courseroles' => [
                            'teacher',
                            'editingteacher',
                        ],
                    ],
                    'f' => [
                        'courseroles' => [
                            'teacher',
                        ],
                    ],

                    // User is enrolled in the course without role.
                    'g' => [
                        'courseroles' => [
                        ],
                    ],

                    // User is a category manager and also enrolled without role in the course.
                    'h' => [
                        'courseroles' => [
                        ],
                        'categoryroles' => [
                            'manager',
                        ],
                    ],

                    // User is a category manager and not enrolled in the course.
                    // This user should not show up in any filter.
                    'i' => [
                        'categoryroles' => [
                            'manager',
                        ],
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No role filter' => (object) [
                        'roles' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 8,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'ANY: Filter on student' => (object) [
                        'roles' => ['student'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],
                    'ANY: Filter on teacher' => (object) [
                        'roles' => ['teacher'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'b',
                            'e',
                            'f',
                        ],
                    ],
                    'ANY: Filter on editingteacher' => (object) [
                        'roles' => ['editingteacher'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ANY: Filter on student, teacher' => (object) [
                        'roles' => ['student', 'teacher'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'e',
                            'f',
                        ],
                    ],
                    'ANY: Filter on teacher, editingteacher' => (object) [
                        'roles' => ['teacher', 'editingteacher'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 5,
                        'expectedusers' => [
                            'b',
                            'c',
                            'd',
                            'e',
                            'f',
                        ],
                    ],
                    'ANY: Filter on student, manager (category level role)' => (object) [
                        'roles' => ['student', 'manager'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'h',
                        ],
                    ],
                    'ANY: Filter on student, coursecreator (not assigned)' => (object) [
                        'roles' => ['student', 'coursecreator'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No role filter' => (object) [
                        'roles' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 8,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'ALL: Filter on student' => (object) [
                        'roles' => ['student'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],
                    'ALL: Filter on teacher' => (object) [
                        'roles' => ['teacher'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'b',
                            'e',
                            'f',
                        ],
                    ],
                    'ALL: Filter on editingteacher' => (object) [
                        'roles' => ['editingteacher'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ALL: Filter on student, teacher' => (object) [
                        'roles' => ['student', 'teacher'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'b',
                        ],
                    ],
                    'ALL: Filter on teacher, editingteacher' => (object) [
                        'roles' => ['teacher', 'editingteacher'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'e',
                        ],
                    ],
                    'ALL: Filter on student, manager (category level role)' => (object) [
                        'roles' => ['student', 'manager'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Filter on student, coursecreator (not assigned)' => (object) [
                        'roles' => ['student', 'coursecreator'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No role filter' => (object) [
                        'roles' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 8,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on student' => (object) [
                        'roles' => ['student'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 6,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on teacher' => (object) [
                        'roles' => ['teacher'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'c',
                            'd',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on editingteacher' => (object) [
                        'roles' => ['editingteacher'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on student, teacher' => (object) [
                        'roles' => ['student', 'teacher'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on student, teacher' => (object) [
                        'roles' => ['teacher', 'editingteacher'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'g',
                            'h',
                        ],
                    ],
                    'NONE: Filter on student, manager (category level role)' => (object) [
                        'roles' => ['student', 'manager'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                        ],
                    ],
                    'NONE: Filter on student, coursecreator (not assigned)' => (object) [
                        'roles' => ['student', 'coursecreator'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 6,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                            'f',
                            'g',
                            'h',
                        ],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'roles' => $expectdata->roles,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }

    /**
     * Ensure that the keywords filter works as expected with the provided test cases.
     *
     * @param array $usersdata The list of users to create
     * @param array $keywords The list of keywords to filter by
     * @param int $jointype The join type to use when combining filter values
     * @param int $count The expected count
     * @param array $expectedusers
     * @dataProvider keywords_provider
     */
    public function test_keywords_filter(array $usersdata, array $keywords, int $jointype, int $count, array $expectedusers): void {
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $users = [];

        foreach ($usersdata as $username => $userdata) {
            // Prevent randomly generated field values that may cause false fails.
            $userdata['firstnamephonetic'] = $userdata['firstnamephonetic'] ?? $userdata['firstname'];
            $userdata['lastnamephonetic'] = $userdata['lastnamephonetic'] ?? $userdata['lastname'];
            $userdata['middlename'] = $userdata['middlename'] ?? '';
            $userdata['alternatename'] = $userdata['alternatename'] ?? $username;

            $user = $this->getDataGenerator()->create_user($userdata);
            $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
            $users[$username] = $user;
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(10, 10, 10, 10);

        // Create the basic filter.
        $filterset = new participants_filterset();
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Create the keyword filter.
        $keywordfilter = new string_filter('keywords');
        $filterset->add_filter($keywordfilter);

        // Configure the filter.
        foreach ($keywords as $keyword) {
            $keywordfilter->add_filter_value($keyword);
        }
        $keywordfilter->set_join_type($jointype);

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);
        $rs = $search->get_participants();
        $this->assertInstanceOf(moodle_recordset::class, $rs);
        $records = $this->convert_recordset_to_array($rs);

        $this->assertCount($count, $records);
        $this->assertEquals($count, $search->get_total_participants_count());

        foreach ($expectedusers as $expecteduser) {
            $this->assertArrayHasKey($users[$expecteduser]->id, $records);
        }
    }

    /**
     * Data provider for keywords tests.
     *
     * @return array
     */
    public function keywords_provider(): array {
        $tests = [
            // Users where the keyword matches basic user fields such as names and email.
            'Users with basic names' => (object) [
                'users' => [
                    'adam.ant' => [
                        'firstname' => 'Adam',
                        'lastname' => 'Ant',
                    ],
                    'barbara.bennett' => [
                        'firstname' => 'Barbara',
                        'lastname' => 'Bennett',
                        'alternatename' => 'Babs',
                        'firstnamephonetic' => 'Barbra',
                        'lastnamephonetic' => 'Benit',
                    ],
                    'colin.carnforth' => [
                        'firstname' => 'Colin',
                        'lastname' => 'Carnforth',
                        'middlename' => 'Jeffery',
                    ],
                    'tony.rogers' => [
                        'firstname' => 'Anthony',
                        'lastname' => 'Rogers',
                        'lastnamephonetic' => 'Rowjours',
                    ],
                    'sarah.rester' => [
                        'firstname' => 'Sarah',
                        'lastname' => 'Rester',
                        'email' => 'zazu@example.com',
                        'firstnamephonetic' => 'Sera',
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filter' => (object) [
                        'keywords' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 5,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'ANY: Filter on first name only' => (object) [
                        'keywords' => ['adam'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'adam.ant',
                        ],
                    ],
                    'ANY: Filter on last name only' => (object) [
                        'keywords' => ['BeNNeTt'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],
                    'ANY: Filter on first/Last name' => (object) [
                        'keywords' => ['ant'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'adam.ant',
                            'tony.rogers',
                        ],
                    ],
                    'ANY: Filter on middlename only' => (object) [
                        'keywords' => ['Jeff'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'colin.carnforth',
                        ],
                    ],
                    'ANY: Filter on username (no match)' => (object) [
                        'keywords' => ['sara.rester'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ANY: Filter on email only' => (object) [
                        'keywords' => ['zazu'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'sarah.rester',
                        ],
                    ],
                    'ANY: Filter on first name phonetic only' => (object) [
                        'keywords' => ['Sera'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'sarah.rester',
                        ],
                    ],
                    'ANY: Filter on last name phonetic only' => (object) [
                        'keywords' => ['jour'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'tony.rogers',
                        ],
                    ],
                    'ANY: Filter on alternate name only' => (object) [
                        'keywords' => ['Babs'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],
                    'ANY: Filter on multiple keywords (first/middle/last name)' => (object) [
                        'keywords' => ['ant', 'Jeff', 'rog'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'adam.ant',
                            'colin.carnforth',
                            'tony.rogers',
                        ],
                    ],
                    'ANY: Filter on multiple keywords (phonetic/alternate names)' => (object) [
                        'keywords' => ['era', 'Bab', 'ours'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'barbara.bennett',
                            'sarah.rester',
                            'tony.rogers',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No filter' => (object) [
                        'keywords' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 5,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'ALL: Filter on first name only' => (object) [
                        'keywords' => ['adam'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'adam.ant',
                        ],
                    ],
                    'ALL: Filter on last name only' => (object) [
                        'keywords' => ['BeNNeTt'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],
                    'ALL: Filter on first/Last name' => (object) [
                        'keywords' => ['ant'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'adam.ant',
                            'tony.rogers',
                        ],
                    ],
                    'ALL: Filter on middlename only' => (object) [
                        'keywords' => ['Jeff'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'colin.carnforth',
                        ],
                    ],
                    'ALL: Filter on username (no match)' => (object) [
                        'keywords' => ['sara.rester'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Filter on email only' => (object) [
                        'keywords' => ['zazu'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'sarah.rester',
                        ],
                    ],
                    'ALL: Filter on first name phonetic only' => (object) [
                        'keywords' => ['Sera'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'sarah.rester',
                        ],
                    ],
                    'ALL: Filter on last name phonetic only' => (object) [
                        'keywords' => ['jour'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'tony.rogers',
                        ],
                    ],
                    'ALL: Filter on alternate name only' => (object) [
                        'keywords' => ['Babs'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],
                    'ALL: Filter on multiple keywords (first/last name)' => (object) [
                        'keywords' => ['ant', 'rog'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'tony.rogers',
                        ],
                    ],
                    'ALL: Filter on multiple keywords (first/middle/last name)' => (object) [
                        'keywords' => ['ant', 'Jeff', 'rog'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Filter on multiple keywords (phonetic/alternate names)' => (object) [
                        'keywords' => ['Bab', 'bra', 'nit'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No filter' => (object) [
                        'keywords' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'NONE: Filter on first name only' => (object) [
                        'keywords' => ['ara'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'adam.ant',
                            'colin.carnforth',
                            'tony.rogers',
                        ],
                    ],
                    'NONE: Filter on last name only' => (object) [
                        'keywords' => ['BeNNeTt'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'NONE: Filter on first/Last name' => (object) [
                        'keywords' => ['ar'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 2,
                        'expectedusers' => [
                            'adam.ant',
                            'tony.rogers',
                        ],
                    ],
                    'NONE: Filter on middlename only' => (object) [
                        'keywords' => ['Jeff'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'NONE: Filter on username (no match)' => (object) [
                        'keywords' => ['sara.rester'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'NONE: Filter on email' => (object) [
                        'keywords' => ['zazu'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                        ],
                    ],
                    'NONE: Filter on first name phonetic only' => (object) [
                        'keywords' => ['Sera'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                        ],
                    ],
                    'NONE: Filter on last name phonetic only' => (object) [
                        'keywords' => ['jour'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'sarah.rester',
                        ],
                    ],
                    'NONE: Filter on alternate name only' => (object) [
                        'keywords' => ['Babs'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'NONE: Filter on multiple keywords (first/last name)' => (object) [
                        'keywords' => ['ara', 'rog'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 2,
                        'expectedusers' => [
                            'adam.ant',
                            'colin.carnforth',
                        ],
                    ],
                    'NONE: Filter on multiple keywords (first/middle/last name)' => (object) [
                        'keywords' => ['ant', 'Jeff', 'rog'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 2,
                        'expectedusers' => [
                            'barbara.bennett',
                            'sarah.rester',
                        ],
                    ],
                    'NONE: Filter on multiple keywords (phonetic/alternate names)' => (object) [
                        'keywords' => ['Bab', 'bra', 'nit'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'keywords' => $expectdata->keywords,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }

    /**
     * Ensure that the enrolment status filter works as expected with the provided test cases.
     *
     * @param array $usersdata The list of users to create
     * @param array $statuses The list of statuses to filter by
     * @param int $jointype The join type to use when combining filter values
     * @param int $count The expected count
     * @param array $expectedusers
     * @dataProvider status_provider
     */
    public function test_status_filter(array $usersdata, array $statuses, int $jointype, int $count, array $expectedusers): void {
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $users = [];

        // Ensure sufficient capabilities to view all statuses.
        $this->setAdminUser();

        // Ensure all enrolment methods enabled.
        $enrolinstances = enrol_get_instances($course->id, false);
        foreach ($enrolinstances as $instance) {
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
        }

        foreach ($usersdata as $username => $userdata) {
            $user = $this->getDataGenerator()->create_user(['username' => $username]);

            if (array_key_exists('statuses', $userdata)) {
                foreach ($userdata['statuses'] as $enrolmethod => $status) {
                    $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student', $enrolmethod, 0, 0, $status);
                }
            }

            $users[$username] = $user;
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(1, 1, 1, 1);

        // Create the basic filter.
        $filterset = new participants_filterset();
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Create the status filter.
        $statusfilter = new integer_filter('status');
        $filterset->add_filter($statusfilter);

        // Configure the filter.
        foreach ($statuses as $status) {
            $statusfilter->add_filter_value($status);
        }
        $statusfilter->set_join_type($jointype);

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);
        $rs = $search->get_participants();
        $this->assertInstanceOf(moodle_recordset::class, $rs);
        $records = $this->convert_recordset_to_array($rs);

        $this->assertCount($count, $records);
        $this->assertEquals($count, $search->get_total_participants_count());

        foreach ($expectedusers as $expecteduser) {
            $this->assertArrayHasKey($users[$expecteduser]->id, $records);
        }
    }

    /**
     * Data provider for status filter tests.
     *
     * @return array
     */
    public function status_provider(): array {
        $tests = [
            // Users with different statuses and enrolment methods (so multiple statuses are possible for the same user).
            'Users with different enrolment statuses' => (object) [
                'users' => [
                    'a' => [
                        'statuses' => [
                            'manual' => ENROL_USER_ACTIVE,
                        ]
                    ],
                    'b' => [
                        'statuses' => [
                            'self' => ENROL_USER_ACTIVE,
                        ]
                    ],
                    'c' => [
                        'statuses' => [
                            'manual' => ENROL_USER_SUSPENDED,
                        ]
                    ],
                    'd' => [
                        'statuses' => [
                            'self' => ENROL_USER_SUSPENDED,
                        ]
                    ],
                    'e' => [
                        'statuses' => [
                            'manual' => ENROL_USER_ACTIVE,
                            'self' => ENROL_USER_SUSPENDED,
                        ]
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filter' => (object) [
                        'statuses' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ANY: Filter on active only' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'e',
                        ],
                    ],
                    'ANY: Filter on suspended only' => (object) [
                        'statuses' => [ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ANY: Filter on multiple statuses' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE, ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No filter' => (object) [
                       'statuses' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ALL: Filter on active only' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'e',
                        ],
                    ],
                    'ALL: Filter on suspended only' => (object) [
                        'statuses' => [ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ALL: Filter on multiple statuses' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE, ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'e',
                        ],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No filter' => (object) [
                       'statuses' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'NONE: Filter on active only' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'NONE: Filter on suspended only' => (object) [
                        'statuses' => [ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'e',
                        ],
                    ],
                    'NONE: Filter on multiple statuses' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE, ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'statuses' => $expectdata->statuses,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }

    /**
     * Ensure that the enrolment methods filter works as expected with the provided test cases.
     *
     * @param array $usersdata The list of users to create
     * @param array $enrolmethods The list of enrolment methods to filter by
     * @param int $jointype The join type to use when combining filter values
     * @param int $count The expected count
     * @param array $expectedusers
     * @dataProvider enrolments_provider
     */
    public function test_enrolments_filter(array $usersdata, array $enrolmethods, int $jointype, int $count,
            array $expectedusers): void {

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $users = [];

        // Ensure all enrolment methods enabled and mapped for setting the filter later.
        $enrolinstances = enrol_get_instances($course->id, false);
        $enrolinstancesmap = [];
        foreach ($enrolinstances as $instance) {
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);

            $enrolinstancesmap[$instance->enrol] = (int) $instance->id;
        }

        foreach ($usersdata as $username => $userdata) {
            $user = $this->getDataGenerator()->create_user(['username' => $username]);

            if (array_key_exists('enrolmethods', $userdata)) {
                foreach ($userdata['enrolmethods'] as $enrolmethod) {
                    $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student', $enrolmethod);
                }
            }

            $users[$username] = $user;
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(1, 1, 1, 1);

        // Create the basic filter.
        $filterset = new participants_filterset();
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Create the enrolment methods filter.
        $enrolmethodfilter = new integer_filter('enrolments');
        $filterset->add_filter($enrolmethodfilter);

        // Configure the filter.
        foreach ($enrolmethods as $enrolmethod) {
            $enrolmethodfilter->add_filter_value($enrolinstancesmap[$enrolmethod]);
        }
        $enrolmethodfilter->set_join_type($jointype);

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);
        $rs = $search->get_participants();
        $this->assertInstanceOf(moodle_recordset::class, $rs);
        $records = $this->convert_recordset_to_array($rs);

        $this->assertCount($count, $records);
        $this->assertEquals($count, $search->get_total_participants_count());

        foreach ($expectedusers as $expecteduser) {
            $this->assertArrayHasKey($users[$expecteduser]->id, $records);
        }
    }

    /**
     * Data provider for enrolments filter tests.
     *
     * @return array
     */
    public function enrolments_provider(): array {
        $tests = [
            // Users with different enrolment methods.
            'Users with different enrolment methods' => (object) [
                'users' => [
                    'a' => [
                        'enrolmethods' => [
                            'manual',
                        ]
                    ],
                    'b' => [
                        'enrolmethods' => [
                            'self',
                        ]
                    ],
                    'c' => [
                        'enrolmethods' => [
                            'manual',
                            'self',
                        ]
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filter' => (object) [
                        'enrolmethods' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'ANY: Filter by manual enrolments only' => (object) [
                        'enrolmethods' => ['manual'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ANY: Filter by self enrolments only' => (object) [
                        'enrolmethods' => ['self'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'b',
                            'c',
                        ],
                    ],
                    'ANY: Filter by multiple enrolment methods' => (object) [
                        'enrolmethods' => ['manual', 'self'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No filter' => (object) [
                       'enrolmethods' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'ALL: Filter by manual enrolments only' => (object) [
                        'enrolmethods' => ['manual'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ALL: Filter by multiple enrolment methods' => (object) [
                        'enrolmethods' => ['manual', 'self'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'c',
                        ],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No filter' => (object) [
                       'enrolmethods' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'NONE: Filter by manual enrolments only' => (object) [
                        'enrolmethods' => ['manual'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 1,
                        'expectedusers' => [
                            'b',
                        ],
                    ],
                    'NONE: Filter by multiple enrolment methods' => (object) [
                        'enrolmethods' => ['manual', 'self'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'enrolmethods' => $expectdata->enrolmethods,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }

    /**
     * Ensure that the groups filter works as expected with the provided test cases.
     *
     * @param array $usersdata The list of users to create
     * @param array $groupsavailable The names of groups that should be created in the course
     * @param array $filtergroups The names of groups to filter by
     * @param int $jointype The join type to use when combining filter values
     * @param int $count The expected count
     * @param array $expectedusers
     * @dataProvider groups_provider
     */
    public function test_groups_filter(array $usersdata, array $groupsavailable, array $filtergroups, int $jointype, int $count,
            array $expectedusers): void {

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $users = [];

        // Prepare data for filtering by users in no groups.
        $nogroupsdata = (object) [
            'id' => USERSWITHOUTGROUP,
        ];

        // Map group names to group data.
         $groupsdata = ['nogroups' => $nogroupsdata];
        foreach ($groupsavailable as $groupname) {
            $groupinfo = [
                'courseid' => $course->id,
                'name' => $groupname,
            ];

            $groupsdata[$groupname] = $this->getDataGenerator()->create_group($groupinfo);
        }

        foreach ($usersdata as $username => $userdata) {
            $user = $this->getDataGenerator()->create_user(['username' => $username]);
            $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

            if (array_key_exists('groups', $userdata)) {
                foreach ($userdata['groups'] as $groupname) {
                    $userinfo = [
                        'userid' => $user->id,
                        'groupid' => (int) $groupsdata[$groupname]->id,
                    ];
                    $this->getDataGenerator()->create_group_member($userinfo);
                }
            }

            $users[$username] = $user;
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(1, 1, 1, 1);

        // Create the basic filter.
        $filterset = new participants_filterset();
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Create the groups filter.
        $groupsfilter = new integer_filter('groups');
        $filterset->add_filter($groupsfilter);

        // Configure the filter.
        foreach ($filtergroups as $filtergroupname) {
            $groupsfilter->add_filter_value((int) $groupsdata[$filtergroupname]->id);
        }
        $groupsfilter->set_join_type($jointype);

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);
        $rs = $search->get_participants();
        $this->assertInstanceOf(moodle_recordset::class, $rs);
        $records = $this->convert_recordset_to_array($rs);

        $this->assertCount($count, $records);
        $this->assertEquals($count, $search->get_total_participants_count());

        foreach ($expectedusers as $expecteduser) {
            $this->assertArrayHasKey($users[$expecteduser]->id, $records);
        }
    }

    /**
     * Data provider for groups filter tests.
     *
     * @return array
     */
    public function groups_provider(): array {
        $tests = [
            'Users in different groups' => (object) [
                'groupsavailable' => [
                    'groupa',
                    'groupb',
                    'groupc',
                ],
                'users' => [
                    'a' => [
                        'groups' => ['groupa'],
                    ],
                    'b' => [
                        'groups' => ['groupb'],
                    ],
                    'c' => [
                        'groups' => ['groupa', 'groupb'],
                    ],
                    'd' => [
                        'groups' => [],
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filter' => (object) [
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],
                    'ANY: Filter on a single group' => (object) [
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ANY: Filter on a group with no members' => (object) [
                        'groups' => ['groupc'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ANY: Filter on multiple groups' => (object) [
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'ANY: Filter on members of no groups only' => (object) [
                        'groups' => ['nogroups'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'd',
                        ],
                    ],
                    'ANY: Filter on a single group or no groups' => (object) [
                        'groups' => ['groupa', 'nogroups'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'c',
                            'd',
                        ],
                    ],
                    'ANY: Filter on multiple groups or no groups' => (object) [
                        'groups' => ['groupa', 'groupb', 'nogroups'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No filter' => (object) [
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],
                    'ALL: Filter on a single group' => (object) [
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ALL: Filter on a group with no members' => (object) [
                        'groups' => ['groupc'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Filter on members of no groups only' => (object) [
                        'groups' => ['nogroups'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'd',
                        ],
                    ],
                    'ALL: Filter on multiple groups' => (object) [
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'c',
                        ],
                    ],
                    'ALL: Filter on a single group and no groups' => (object) [
                        'groups' => ['groupa', 'nogroups'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Filter on multiple groups and no groups' => (object) [
                        'groups' => ['groupa', 'groupb', 'nogroups'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No filter' => (object) [
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],
                    'NONE: Filter on a single group' => (object) [
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 2,
                        'expectedusers' => [
                            'b',
                            'd',
                        ],
                    ],
                    'NONE: Filter on a group with no members' => (object) [
                        'groups' => ['groupc'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],
                    'NONE: Filter on members of no groups only' => (object) [
                        'groups' => ['nogroups'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'NONE: Filter on multiple groups' => (object) [
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 1,
                        'expectedusers' => [
                            'd',
                        ],
                    ],
                    'NONE: Filter on a single group and no groups' => (object) [
                        'groups' => ['groupa', 'nogroups'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 1,
                        'expectedusers' => [
                            'b',
                        ],
                    ],
                    'NONE: Filter on multiple groups and no groups' => (object) [
                        'groups' => ['groupa', 'groupb', 'nogroups'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'groupsavailable' => $testdata->groupsavailable,
                    'filtergroups' => $expectdata->groups,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }

    /**
     * Ensure that the groups filter works as expected when separate groups mode is enabled, with the provided test cases.
     *
     * @param array $usersdata The list of users to create
     * @param array $groupsavailable The names of groups that should be created in the course
     * @param array $filtergroups The names of groups to filter by
     * @param int $jointype The join type to use when combining filter values
     * @param int $count The expected count
     * @param array $expectedusers
     * @param string $loginusername The user to login as for the tests
     * @dataProvider groups_separate_provider
     */
    public function test_groups_filter_separate_groups(array $usersdata, array $groupsavailable, array $filtergroups, int $jointype,
            int $count, array $expectedusers, string $loginusername): void {

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $users = [];

        // Enable separate groups mode on the course.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);

        // Prepare data for filtering by users in no groups.
        $nogroupsdata = (object) [
            'id' => USERSWITHOUTGROUP,
        ];

        // Map group names to group data.
         $groupsdata = ['nogroups' => $nogroupsdata];
        foreach ($groupsavailable as $groupname) {
            $groupinfo = [
                'courseid' => $course->id,
                'name' => $groupname,
            ];

            $groupsdata[$groupname] = $this->getDataGenerator()->create_group($groupinfo);
        }

        foreach ($usersdata as $username => $userdata) {
            $user = $this->getDataGenerator()->create_user(['username' => $username]);
            $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

            if (array_key_exists('groups', $userdata)) {
                foreach ($userdata['groups'] as $groupname) {
                    $userinfo = [
                        'userid' => $user->id,
                        'groupid' => (int) $groupsdata[$groupname]->id,
                    ];
                    $this->getDataGenerator()->create_group_member($userinfo);
                }
            }

            $users[$username] = $user;

            if ($username == $loginusername) {
                $loginuser = $user;
            }
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(1, 1, 1, 1);

        // Log in as the user to be tested.
        $this->setUser($loginuser);

        // Create the basic filter.
        $filterset = new participants_filterset();
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Create the groups filter.
        $groupsfilter = new integer_filter('groups');
        $filterset->add_filter($groupsfilter);

        // Configure the filter.
        foreach ($filtergroups as $filtergroupname) {
            $groupsfilter->add_filter_value((int) $groupsdata[$filtergroupname]->id);
        }
        $groupsfilter->set_join_type($jointype);

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);

        // Tests on user in no groups should throw an exception as they are not supported (participants are not visible to them).
        if (in_array('exception', $expectedusers)) {
            $this->expectException(\coding_exception::class);
            $rs = $search->get_participants();
        } else {
            // All other cases are tested as normal.
            $rs = $search->get_participants();
            $this->assertInstanceOf(moodle_recordset::class, $rs);
            $records = $this->convert_recordset_to_array($rs);

            $this->assertCount($count, $records);
            $this->assertEquals($count, $search->get_total_participants_count());

            foreach ($expectedusers as $expecteduser) {
                $this->assertArrayHasKey($users[$expecteduser]->id, $records);
            }
        }
    }

    /**
     * Data provider for groups filter tests.
     *
     * @return array
     */
    public function groups_separate_provider(): array {
        $tests = [
            'Users in different groups with separate groups mode enabled' => (object) [
                'groupsavailable' => [
                    'groupa',
                    'groupb',
                    'groupc',
                ],
                'users' => [
                    'a' => [
                        'groups' => ['groupa'],
                    ],
                    'b' => [
                        'groups' => ['groupb'],
                    ],
                    'c' => [
                        'groups' => ['groupa', 'groupb'],
                    ],
                    'd' => [
                        'groups' => [],
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filter, user in one group' => (object) [
                        'loginuser' => 'a',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ANY: No filter, user in multiple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'ANY: No filter, user in no groups' => (object) [
                        'loginuser' => 'd',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 0,
                        'expectedusers' => ['exception'],
                    ],
                    'ANY: Filter on a single group, user in one group' => (object) [
                        'loginuser' => 'a',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ANY: Filter on a single group, user in multple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ANY: Filter on a single group, user in no groups' => (object) [
                        'loginuser' => 'd',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 0,
                        'expectedusers' => ['exception'],
                    ],
                    'ANY: Filter on multiple groups, user in one group (ignore invalid groups)' => (object) [
                        'loginuser' => 'a',
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ANY: Filter on multiple groups, user in multiple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'ANY: Filter on multiple groups or no groups, user in multiple groups (ignore no groups)' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa', 'groupb', 'nogroups'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No filter, user in one group' => (object) [
                        'loginuser' => 'a',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ALL: No filter, user in multiple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'ALL: No filter, user in no groups' => (object) [
                        'loginuser' => 'd',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => ['exception'],
                    ],
                    'ALL: Filter on a single group, user in one group' => (object) [
                        'loginuser' => 'a',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ALL: Filter on a single group, user in multple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ALL: Filter on a single group, user in no groups' => (object) [
                        'loginuser' => 'd',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => ['exception'],
                    ],
                    'ALL: Filter on multiple groups, user in one group (ignore invalid groups)' => (object) [
                        'loginuser' => 'a',
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ALL: Filter on multiple groups, user in multiple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'c',
                        ],
                    ],
                    'ALL: Filter on multiple groups or no groups, user in multiple groups (ignore no groups)' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa', 'groupb', 'nogroups'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'c',
                        ],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No filter, user in one group' => (object) [
                        'loginuser' => 'a',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'NONE: No filter, user in multiple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'NONE: No filter, user in no groups' => (object) [
                        'loginuser' => 'd',
                        'groups' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => ['exception'],
                    ],
                    'NONE: Filter on a single group, user in one group' => (object) [
                        'loginuser' => 'a',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'NONE: Filter on a single group, user in multple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 1,
                        'expectedusers' => [
                            'b',
                        ],
                    ],
                    'NONE: Filter on a single group, user in no groups' => (object) [
                        'loginuser' => 'd',
                        'groups' => ['groupa'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => ['exception'],
                    ],
                    'NONE: Filter on multiple groups, user in one group (ignore invalid groups)' => (object) [
                        'loginuser' => 'a',
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'NONE: Filter on multiple groups, user in multiple groups' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa', 'groupb'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'NONE: Filter on multiple groups or no groups, user in multiple groups (ignore no groups)' => (object) [
                        'loginuser' => 'c',
                        'groups' => ['groupa', 'groupb', 'nogroups'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'groupsavailable' => $testdata->groupsavailable,
                    'filtergroups' => $expectdata->groups,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                    'loginusername' => $expectdata->loginuser,
                ];
            }
        }

        return $finaltests;
    }


    /**
     * Ensure that the last access filter works as expected with the provided test cases.
     *
     * @param array $usersdata The list of users to create
     * @param array $accesssince The last access data to filter by
     * @param int $jointype The join type to use when combining filter values
     * @param int $count The expected count
     * @param array $expectedusers
     * @dataProvider accesssince_provider
     */
    public function test_accesssince_filter(array $usersdata, array $accesssince, int $jointype, int $count,
            array $expectedusers): void {

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $users = [];

        foreach ($usersdata as $username => $userdata) {
            $usertimestamp = empty($userdata['lastlogin']) ? 0 : strtotime($userdata['lastlogin']);

            $user = $this->getDataGenerator()->create_user(['username' => $username]);
            $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

            // Create the record of the user's last access to the course.
            if ($usertimestamp > 0) {
                $this->getDataGenerator()->create_user_course_lastaccess($user, $course, $usertimestamp);
            }

            $users[$username] = $user;
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(1, 1, 1, 1);

        // Create the basic filter.
        $filterset = new participants_filterset();
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Create the last access filter.
        $lastaccessfilter = new integer_filter('accesssince');
        $filterset->add_filter($lastaccessfilter);

        // Configure the filter.
        foreach ($accesssince as $accessstring) {
            $lastaccessfilter->add_filter_value(strtotime($accessstring));
        }
        $lastaccessfilter->set_join_type($jointype);

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);
        $rs = $search->get_participants();
        $this->assertInstanceOf(moodle_recordset::class, $rs);
        $records = $this->convert_recordset_to_array($rs);

        $this->assertCount($count, $records);
        $this->assertEquals($count, $search->get_total_participants_count());

        foreach ($expectedusers as $expecteduser) {
            $this->assertArrayHasKey($users[$expecteduser]->id, $records);
        }
    }

    /**
     * Data provider for last access filter tests.
     *
     * @return array
     */
    public function accesssince_provider(): array {
        $tests = [
            // Users with different last access times.
            'Users in different groups' => (object) [
                'users' => [
                    'a' => [
                        'lastlogin' => '-3 days',
                    ],
                    'b' => [
                        'lastlogin' => '-2 weeks',
                    ],
                    'c' => [
                        'lastlogin' => '-5 months',
                    ],
                    'd' => [
                        'lastlogin' => '-11 months',
                    ],
                    'e' => [
                        // Never logged in.
                        'lastlogin' => '',
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filter' => (object) [
                        'accesssince' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ANY: Filter on last login more than 1 year ago' => (object) [
                        'accesssince' => ['-1 year'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'e',
                        ],
                    ],
                    'ANY: Filter on last login more than 6 months ago' => (object) [
                        'accesssince' => ['-6 months'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'd',
                            'e',
                        ],
                    ],
                    'ANY: Filter on last login more than 3 weeks ago' => (object) [
                        'accesssince' => ['-3 weeks'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ANY: Filter on last login more than 5 days ago' => (object) [
                        'accesssince' => ['-5 days'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 4,
                        'expectedusers' => [
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ANY: Filter on last login more than 2 days ago' => (object) [
                        'accesssince' => ['-2 days'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No filter' => (object) [
                        'accesssince' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ALL: Filter on last login more than 1 year ago' => (object) [
                        'accesssince' => ['-1 year'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'e',
                        ],
                    ],
                    'ALL: Filter on last login more than 6 months ago' => (object) [
                        'accesssince' => ['-6 months'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'd',
                            'e',
                        ],
                    ],
                    'ALL: Filter on last login more than 3 weeks ago' => (object) [
                        'accesssince' => ['-3 weeks'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ALL: Filter on last login more than 5 days ago' => (object) [
                        'accesssince' => ['-5 days'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 4,
                        'expectedusers' => [
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'ALL: Filter on last login more than 2 days ago' => (object) [
                        'accesssince' => ['-2 days'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No filter' => (object) [
                        'accesssince' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 5,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                            'e',
                        ],
                    ],
                    'NONE: Filter on last login more than 1 year ago' => (object) [
                        'accesssince' => ['-1 year'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],
                    'NONE: Filter on last login more than 6 months ago' => (object) [
                        'accesssince' => ['-6 months'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 3,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    'NONE: Filter on last login more than 3 weeks ago' => (object) [
                        'accesssince' => ['-3 weeks'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],
                    'NONE: Filter on last login more than 5 days ago' => (object) [
                        'accesssince' => ['-5 days'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 1,
                        'expectedusers' => [
                            'a',
                        ],
                    ],
                    'NONE: Filter on last login more than 2 days ago' => (object) [
                        'accesssince' => ['-2 days'],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'accesssince' => $expectdata->accesssince,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }

    /**
     * Ensure that the joins between filters in the filterset work as expected with the provided test cases.
     *
     * @param array $usersdata The list of users to create
     * @param array $filterdata The data to filter by
     * @param array $groupsavailable The names of groups that should be created in the course
     * @param int $jointype The join type to used between each filter being applied
     * @param int $count The expected count
     * @param array $expectedusers
     * @dataProvider filterset_joins_provider
     */
    public function test_filterset_joins(array $usersdata, array $filterdata, array $groupsavailable, int $jointype, int $count,
            array $expectedusers): void {
        global $DB;

        // Ensure sufficient capabilities to view all statuses.
        $this->setAdminUser();

        // Remove the default role.
        set_config('roleid', 0, 'enrol_manual');

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $roles = $DB->get_records_menu('role', [], '', 'shortname, id');
        $users = [];

        // Ensure all enrolment methods are enabled (and mapped where required for filtering later).
        $enrolinstances = enrol_get_instances($course->id, false);
        $enrolinstancesmap = [];
        foreach ($enrolinstances as $instance) {
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);

            $enrolinstancesmap[$instance->enrol] = (int) $instance->id;
        }

        // Create the required course groups and mapping.
        $nogroupsdata = (object) [
            'id' => USERSWITHOUTGROUP,
        ];

         $groupsdata = ['nogroups' => $nogroupsdata];
        foreach ($groupsavailable as $groupname) {
            $groupinfo = [
                'courseid' => $course->id,
                'name' => $groupname,
            ];

            $groupsdata[$groupname] = $this->getDataGenerator()->create_group($groupinfo);
        }

        // Create test users.
        foreach ($usersdata as $username => $userdata) {
            $usertimestamp = empty($userdata['lastlogin']) ? 0 : strtotime($userdata['lastlogin']);
            unset($userdata['lastlogin']);

            // Prevent randomly generated field values that may cause false fails.
            $userdata['firstnamephonetic'] = $userdata['firstnamephonetic'] ?? $userdata['firstname'];
            $userdata['lastnamephonetic'] = $userdata['lastnamephonetic'] ?? $userdata['lastname'];
            $userdata['middlename'] = $userdata['middlename'] ?? '';
            $userdata['alternatename'] = $userdata['alternatename'] ?? $username;

            $user = $this->getDataGenerator()->create_user($userdata);

            foreach ($userdata['enrolments'] as $details) {
                $this->getDataGenerator()->enrol_user($user->id, $course->id, $roles[$details['role']],
                        $details['method'], 0, 0, $details['status']);
            }

            foreach ($userdata['groups'] as $groupname) {
                $userinfo = [
                    'userid' => $user->id,
                    'groupid' => (int) $groupsdata[$groupname]->id,
                ];
                $this->getDataGenerator()->create_group_member($userinfo);
            }

            if ($usertimestamp > 0) {
                $this->getDataGenerator()->create_user_course_lastaccess($user, $course, $usertimestamp);
            }

            $users[$username] = $user;
        }

        // Create a secondary course with users. We should not see these users.
        $this->create_course_with_users(10, 10, 10, 10);

        // Create the basic filterset.
        $filterset = new participants_filterset();
        $filterset->set_join_type($jointype);
        $filterset->add_filter(new integer_filter('courseid', null, [(int) $course->id]));

        // Apply the keywords filter if required.
        if (array_key_exists('keywords', $filterdata)) {
            $keywordfilter = new string_filter('keywords');
            $filterset->add_filter($keywordfilter);

            foreach ($filterdata['keywords']['values'] as $keyword) {
                $keywordfilter->add_filter_value($keyword);
            }
            $keywordfilter->set_join_type($filterdata['keywords']['jointype']);
        }

        // Apply enrolment methods filter if required.
        if (array_key_exists('enrolmethods', $filterdata)) {
            $enrolmethodfilter = new integer_filter('enrolments');
            $filterset->add_filter($enrolmethodfilter);

            foreach ($filterdata['enrolmethods']['values'] as $enrolmethod) {
                $enrolmethodfilter->add_filter_value($enrolinstancesmap[$enrolmethod]);
            }
            $enrolmethodfilter->set_join_type($filterdata['enrolmethods']['jointype']);
        }

        // Apply roles filter if required.
        if (array_key_exists('courseroles', $filterdata)) {
            $rolefilter = new integer_filter('roles');
            $filterset->add_filter($rolefilter);

            foreach ($filterdata['courseroles']['values'] as $rolename) {
                $rolefilter->add_filter_value((int) $roles[$rolename]);
            }
            $rolefilter->set_join_type($filterdata['courseroles']['jointype']);
        }

        // Apply status filter if required.
        if (array_key_exists('status', $filterdata)) {
            $statusfilter = new integer_filter('status');
            $filterset->add_filter($statusfilter);

            foreach ($filterdata['status']['values'] as $status) {
                $statusfilter->add_filter_value($status);
            }
            $statusfilter->set_join_type($filterdata['status']['jointype']);
        }

        // Apply groups filter if required.
        if (array_key_exists('groups', $filterdata)) {
            $groupsfilter = new integer_filter('groups');
            $filterset->add_filter($groupsfilter);

            foreach ($filterdata['groups']['values'] as $filtergroupname) {
                $groupsfilter->add_filter_value((int) $groupsdata[$filtergroupname]->id);
            }
            $groupsfilter->set_join_type($filterdata['groups']['jointype']);
        }

        // Apply last access filter if required.
        if (array_key_exists('accesssince', $filterdata)) {
            $lastaccessfilter = new integer_filter('accesssince');
            $filterset->add_filter($lastaccessfilter);

            foreach ($filterdata['accesssince']['values'] as $accessstring) {
                $lastaccessfilter->add_filter_value(strtotime($accessstring));
            }
            $lastaccessfilter->set_join_type($filterdata['accesssince']['jointype']);
        }

        // Run the search.
        $search = new participants_search($course, $coursecontext, $filterset);
        $rs = $search->get_participants();
        $this->assertInstanceOf(moodle_recordset::class, $rs);
        $records = $this->convert_recordset_to_array($rs);

        $this->assertCount($count, $records);
        $this->assertEquals($count, $search->get_total_participants_count());

        foreach ($expectedusers as $expecteduser) {
            $this->assertArrayHasKey($users[$expecteduser]->id, $records);
        }
    }

    /**
     * Data provider for filterset join tests.
     *
     * @return array
     */
    public function filterset_joins_provider(): array {
        $tests = [
            // Users with different configurations.
            'Users with different configurations' => (object) [
                'groupsavailable' => [
                    'groupa',
                    'groupb',
                    'groupc',
                ],
                'users' => [
                    'adam.ant' => [
                        'firstname' => 'Adam',
                        'lastname' => 'Ant',
                        'enrolments' => [
                            [
                                'role' => 'student',
                                'method' => 'manual',
                                'status' => ENROL_USER_ACTIVE,
                            ],
                        ],
                        'groups' => ['groupa'],
                        'lastlogin' => '-3 days',
                    ],
                    'barbara.bennett' => [
                        'firstname' => 'Barbara',
                        'lastname' => 'Bennett',
                        'enrolments' => [
                            [
                                'role' => 'student',
                                'method' => 'manual',
                                'status' => ENROL_USER_ACTIVE,
                            ],
                            [
                                'role' => 'teacher',
                                'method' => 'manual',
                                'status' => ENROL_USER_ACTIVE,
                            ],
                        ],
                        'groups' => ['groupb'],
                        'lastlogin' => '-2 weeks',
                    ],
                    'colin.carnforth' => [
                        'firstname' => 'Colin',
                        'lastname' => 'Carnforth',
                        'enrolments' => [
                            [
                                'role' => 'editingteacher',
                                'method' => 'self',
                                'status' => ENROL_USER_SUSPENDED,
                            ],
                        ],
                        'groups' => ['groupa', 'groupb'],
                        'lastlogin' => '-5 months',
                    ],
                    'tony.rogers' => [
                        'firstname' => 'Anthony',
                        'lastname' => 'Rogers',
                        'enrolments' => [
                            [
                                'role' => 'editingteacher',
                                'method' => 'self',
                                'status' => ENROL_USER_SUSPENDED,
                            ],
                        ],
                        'groups' => [],
                        'lastlogin' => '-10 months',
                    ],
                    'sarah.rester' => [
                        'firstname' => 'Sarah',
                        'lastname' => 'Rester',
                        'email' => 'zazu@example.com',
                        'enrolments' => [
                            [
                                'role' => 'teacher',
                                'method' => 'manual',
                                'status' => ENROL_USER_ACTIVE,
                            ],
                            [
                                'role' => 'editingteacher',
                                'method' => 'self',
                                'status' => ENROL_USER_SUSPENDED,
                            ],
                        ],
                        'groups' => [],
                        'lastlogin' => '-11 months',
                    ],
                    'morgan.crikeyson' => [
                        'firstname' => 'Morgan',
                        'lastname' => 'Crikeyson',
                        'enrolments' => [
                            [
                                'role' => 'teacher',
                                'method' => 'manual',
                                'status' => ENROL_USER_ACTIVE,
                            ],
                        ],
                        'groups' => ['groupa'],
                        'lastlogin' => '-1 week',
                    ],
                    'jonathan.bravo' => [
                        'firstname' => 'Jonathan',
                        'lastname' => 'Bravo',
                        'enrolments' => [
                            [
                                'role' => 'student',
                                'method' => 'manual',
                                'status' => ENROL_USER_ACTIVE,
                            ],
                        ],
                        'groups' => [],
                        // Never logged in.
                        'lastlogin' => '',
                    ],
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filters in filterset' => (object) [
                        'filterdata' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 7,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                            'morgan.crikeyson',
                            'jonathan.bravo',
                        ],
                    ],
                    'ANY: Filterset containing a single filter type' => (object) [
                        'filterdata' => [
                            'enrolmethods' => [
                                'values' => ['self'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                        ],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 3,
                        'expectedusers' => [
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'ANY: Filterset matching all filter types on different users' => (object) [
                        'filterdata' => [
                            // Match Adam only.
                            'keywords' => [
                                'values' => ['adam'],
                                'jointype' => filter::JOINTYPE_ALL,
                            ],
                            // Match Sarah only.
                            'enrolmethods' => [
                                'values' => ['manual', 'self'],
                                'jointype' => filter::JOINTYPE_ALL,
                            ],
                            // Match Barbara only.
                            'courseroles' => [
                                'values' => ['student', 'teacher'],
                                'jointype' => filter::JOINTYPE_ALL,
                            ],
                            // Match Sarah only.
                            'statuses' => [
                                'values' => ['active', 'suspended'],
                                'jointype' => filter::JOINTYPE_ALL,
                            ],
                            // Match Colin only.
                            'groups' => [
                                'values' => ['groupa', 'groupb'],
                                'jointype' => filter::JOINTYPE_ALL,
                            ],
                            // Match Jonathan only.
                            'accesssince' => [
                                'values' => ['-1 year'],
                                'jointype' => filter::JOINTYPE_ALL,
                                ],
                        ],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 5,
                        // Morgan and Tony are not matched, to confirm filtering is not just returning all users.
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'sarah.rester',
                            'jonathan.bravo',
                        ],
                    ],

                    // Tests for jointype: ALL.
                    'ALL: No filters in filterset' => (object) [
                        'filterdata' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 7,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                            'morgan.crikeyson',
                            'jonathan.bravo',
                        ],
                    ],
                    'ALL: Filterset containing a single filter type' => (object) [
                        'filterdata' => [
                            'enrolmethods' => [
                                'values' => ['self'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                        ],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 3,
                        'expectedusers' => [
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                        ],
                    ],
                    'ALL: Filterset combining all filter types' => (object) [
                        'filterdata' => [
                            // Exclude Adam, Tony, Morgan and Jonathan.
                            'keywords' => [
                                'values' => ['ar'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                            // Exclude Colin and Tony.
                            'enrolmethods' => [
                                'values' => ['manual'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                            // Exclude Adam, Barbara and Jonathan.
                            'courseroles' => [
                                'values' => ['student'],
                                'jointype' => filter::JOINTYPE_NONE,
                            ],
                            // Exclude Colin and Tony.
                            'statuses' => [
                                'values' => ['active'],
                                'jointype' => filter::JOINTYPE_ALL,
                            ],
                            // Exclude Barbara.
                            'groups' => [
                                'values' => ['groupa', 'nogroups'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                            // Exclude Adam, Colin and Barbara.
                            'accesssince' => [
                                'values' => ['-6 months'],
                                'jointype' => filter::JOINTYPE_ALL,
                                ],
                        ],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'sarah.rester',
                        ],
                    ],

                    // Tests for jointype: NONE.
                    'NONE: No filters in filterset' => (object) [
                        'filterdata' => [],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 7,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'colin.carnforth',
                            'tony.rogers',
                            'sarah.rester',
                            'morgan.crikeyson',
                            'jonathan.bravo',
                        ],
                    ],
                    'NONE: Filterset containing a single filter type' => (object) [
                        'filterdata' => [
                            'enrolmethods' => [
                                'values' => ['self'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                        ],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 4,
                        'expectedusers' => [
                            'adam.ant',
                            'barbara.bennett',
                            'morgan.crikeyson',
                            'jonathan.bravo',
                        ],
                    ],
                    'NONE: Filterset combining all filter types' => (object) [
                        'filterdata' => [
                            // Excludes Adam.
                            'keywords' => [
                                'values' => ['adam'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                            // Excludes Colin, Tony and Sarah.
                            'enrolmethods' => [
                                'values' => ['self'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                            // Excludes Jonathan.
                            'courseroles' => [
                                'values' => ['student'],
                                'jointype' => filter::JOINTYPE_NONE,
                            ],
                            // Excludes Colin, Tony and Sarah.
                            'statuses' => [
                                'values' => ['suspended'],
                                'jointype' => filter::JOINTYPE_ALL,
                            ],
                            // Excludes Adam, Colin, Tony, Sarah, Morgan and Jonathan.
                            'groups' => [
                                'values' => ['groupa', 'nogroups'],
                                'jointype' => filter::JOINTYPE_ANY,
                            ],
                            // Excludes Tony and Sarah.
                            'accesssince' => [
                                'values' => ['-6 months'],
                                'jointype' => filter::JOINTYPE_ALL,
                                ],
                        ],
                        'jointype' => filter::JOINTYPE_NONE,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],
                ],
            ],
        ];

        $finaltests = [];
        foreach ($tests as $testname => $testdata) {
            foreach ($testdata->expect as $expectname => $expectdata) {
                $finaltests["{$testname} => {$expectname}"] = [
                    'users' => $testdata->users,
                    'filterdata' => $expectdata->filterdata,
                    'groupsavailable' => $testdata->groupsavailable,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }
}
