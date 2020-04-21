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
            // Users where the keyword matches firstname, lastname, or username.
            'Users with basic names' => (object) [
                'users' => [
                    'adam.ant' => [
                        'firstname' => 'Adam',
                        'lastname' => 'Ant',
                    ],
                    'barbara.bennett' => [
                        'firstname' => 'Barbara',
                        'lastname' => 'Bennett',
                    ],
                    'colin.carnforth' => [
                        'firstname' => 'Colin',
                        'lastname' => 'Carnforth',
                    ],
                    'tony.rogers' => [
                        'firstname' => 'Anthony',
                        'lastname' => 'Rogers',
                    ],
                    'sarah.rester' => [
                        'firstname' => 'Sarah',
                        'lastname' => 'Rester',
                        'email' => 'zazu@example.com',
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
                    'ANY: First name only' => (object) [
                        'keywords' => ['adam'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'adam.ant',
                        ],
                    ],
                    'ANY: Last name only' => (object) [
                        'keywords' => ['BeNNeTt'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],
                    'ANY: First/Last name' => (object) [
                        'keywords' => ['ant'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'adam.ant',
                            'tony.rogers',
                        ],
                    ],
                    'ANY: Username (no match)' => (object) [
                        'keywords' => ['sara.rester'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ANY: Email' => (object) [
                        'keywords' => ['zazu'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 1,
                        'expectedusers' => [
                            'sarah.rester',
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
                    'ALL: First name only' => (object) [
                        'keywords' => ['adam'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'adam.ant',
                        ],
                    ],
                    'ALL: Last name only' => (object) [
                        'keywords' => ['BeNNeTt'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'barbara.bennett',
                        ],
                    ],
                    'ALL: First/Last name' => (object) [
                        'keywords' => ['ant'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'adam.ant',
                            'tony.rogers',
                        ],
                    ],
                    'ALL: Username (no match)' => (object) [
                        'keywords' => ['sara.rester'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 0,
                        'expectedusers' => [],
                    ],
                    'ALL: Email' => (object) [
                        'keywords' => ['zazu'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'sarah.rester',
                        ],
                    ],
                    'ALL: Multiple keywords' => (object) [
                        'keywords' => ['ant', 'rog'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 1,
                        'expectedusers' => [
                            'tony.rogers',
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
                ],
                'expect' => [
                    // Tests for jointype: ANY.
                    'ANY: No filter' => (object) [
                        'statuses' => [],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],
                    'ANY: Active only' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],
                    'ANY: Suspended only' => (object) [
                        'statuses' => [ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'c',
                            'd',
                        ],
                    ],
                    'ANY: Multiple statuses' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE, ENROL_USER_SUSPENDED],
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
                       'statuses' => [],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 4,
                        'expectedusers' => [
                            'a',
                            'b',
                            'c',
                            'd',
                        ],
                    ],
                    'ALL: Active only' => (object) [
                        'statuses' => [ENROL_USER_ACTIVE],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'b',
                        ],
                    ],
                    'ALL: Suspended only' => (object) [
                        'statuses' => [ENROL_USER_SUSPENDED],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'c',
                            'd',
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
                    'ANY: Manual enrolments only' => (object) [
                        'enrolmethods' => ['manual'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
                        ],
                    ],
                    'ANY: Self enrolments only' => (object) [
                        'enrolmethods' => ['self'],
                        'jointype' => filter::JOINTYPE_ANY,
                        'count' => 2,
                        'expectedusers' => [
                            'b',
                            'c',
                        ],
                    ],
                    'ANY: Multiple enrolment methods' => (object) [
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
                    'ALL: Manual enrolments only' => (object) [
                        'enrolmethods' => ['manual'],
                        'jointype' => filter::JOINTYPE_ALL,
                        'count' => 2,
                        'expectedusers' => [
                            'a',
                            'c',
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
                    'enrolmethods' => $expectdata->enrolmethods,
                    'jointype' => $expectdata->jointype,
                    'count' => $expectdata->count,
                    'expectedusers' => $expectdata->expectedusers,
                ];
            }
        }

        return $finaltests;
    }
}
