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

namespace core_grades\external;

use core_external\external_api;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for the core_grades\external\get_gradable_users.
 *
 * @package    core_grades
 * @category   external
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @covers     \core_grades\external\get_gradable_users
 */
class get_gradable_users_test extends \externallib_advanced_testcase {

    /**
     * Test the behaviour of get_gradable_users.
     *
     * @dataProvider execute_data
     * @param bool $onlyactiveenrol if we should only return active enrolments
     * @param bool $grouprestricted if we should only return users within a group
     * @param array $expected expected users
     */
    public function test_execute(bool $onlyactiveenrol, bool $grouprestricted, array $expected): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create and enrol test users.
        $student1 = $generator->create_user(['username' => 'student1', 'firstname' => 'Apple', 'lastname' => 'Apricot']);
        $student2 = $generator->create_user(['username' => 'student2', 'firstname' => 'Banana', 'lastname' => 'Blueberry']);
        $student3 = $generator->create_user(['username' => 'student3', 'firstname' => 'Cherry', 'lastname' => 'Cranberry']);
        $student4 = $generator->create_user(['username' => 'student4', 'firstname' => 'Durian', 'lastname' => 'Dracontomelon']);
        $student5 = $generator->create_user(['username' => 'student5', 'firstname' => 'Eggplant', 'lastname' => 'Ensete']);

        $role = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
        $generator->enrol_user($student1->id, $course->id, $role->id);
        $generator->enrol_user($student2->id, $course->id, $role->id);
        $generator->enrol_user($student3->id, $course->id, $role->id);
        $generator->enrol_user($student4->id, $course->id, $role->id);
        $generator->enrol_user($student5->id, $course->id, $role->id, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);

        $generator->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $generator->create_group_member(['userid' => $student1->id, 'groupid' => $group2->id]);
        $generator->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $generator->create_group_member(['userid' => $student3->id, 'groupid' => $group2->id]);

        $DB->set_field('course', 'groupmode', SEPARATEGROUPS, ['id' => $course->id]);

        $teacher = $generator->create_user(['username' => 'teacher1']);
        $role = $DB->get_record('role', ['shortname' => 'editingteacher'], '*', MUST_EXIST);
        $generator->enrol_user($teacher->id, $course->id, $role->id);

        $generator->create_module('assign', ['course' => $course->id]);

        $groupid = $grouprestricted ? $group2->id : 0;
        $result = get_gradable_users::execute($course->id, $groupid, $onlyactiveenrol);
        $result = external_api::clean_returnvalue(get_gradable_users::execute_returns(), $result);

        $mapped = array_map(function($user) {
            return [
                'fullname' => $user['fullname'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'profileimageurl' => $user['profileimageurl'],
            ];
        }, array_values($result['users']));
        $this->assertEquals($expected, $mapped);
    }

    /**
     * Data provider for test_execute.
     *
     * @return array
     */
    public static function execute_data(): array {
        return [
            'All users' => [
                false,
                false,
                [
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Apple',
                        'lastname' => 'Apricot',
                        'fullname' => 'Apple Apricot',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Banana',
                        'lastname' => 'Blueberry',
                        'fullname' => 'Banana Blueberry',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Cherry',
                        'lastname' => 'Cranberry',
                        'fullname' => 'Cherry Cranberry',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Durian',
                        'lastname' => 'Dracontomelon',
                        'fullname' => 'Durian Dracontomelon',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Eggplant',
                        'lastname' => 'Ensete',
                        'fullname' => 'Eggplant Ensete',
                    ],
                ],
            ],
            'Only active enrolment' => [
                true,
                false,
                [
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Apple',
                        'lastname' => 'Apricot',
                        'fullname' => 'Apple Apricot',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Banana',
                        'lastname' => 'Blueberry',
                        'fullname' => 'Banana Blueberry',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Cherry',
                        'lastname' => 'Cranberry',
                        'fullname' => 'Cherry Cranberry',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Durian',
                        'lastname' => 'Dracontomelon',
                        'fullname' => 'Durian Dracontomelon',
                    ],
                ],
            ],
            'Group restricted' => [
                false,
                true,
                [
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Apple',
                        'lastname' => 'Apricot',
                        'fullname' => 'Apple Apricot',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Banana',
                        'lastname' => 'Blueberry',
                        'fullname' => 'Banana Blueberry',
                    ],
                    [
                        'profileimageurl' => 'https://www.example.com/moodle/theme/image.php/boost/core/1/u/f1',
                        'firstname' => 'Cherry',
                        'lastname' => 'Cranberry',
                        'fullname' => 'Cherry Cranberry',
                    ],
                ],
            ],
        ];
    }
}
