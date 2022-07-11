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

namespace core_grades\output;

use advanced_testcase;
use grade_helper;
use context_course;
use moodle_url;

/**
 * A test class used to test general_action_bar.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class general_action_bar_test extends advanced_testcase {

    /**
     * Test the exported data for the general action bar for different user roles and settings.
     *
     * @dataProvider test_export_for_template_provider
     * @param string $userrole The user role to test
     * @param bool $enableoutcomes Whether to enable outcomes
     * @param array $expectedoptions The expected options returned in the general action selector
     */
    public function test_export_for_template_admin(string $userrole, bool $enableoutcomes, array $expectedoptions) {
        global $PAGE;

        $this->resetAfterTest();
        // Reset the cache.
        grade_helper::reset_caches();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        if ($userrole === 'admin') {
            $this->setAdminUser();
        } else {
            // Enrol user to the course.
            $user = $this->getDataGenerator()->create_and_enrol($course, $userrole);
            $this->setUser($user);
        }

        if ($enableoutcomes) {
            set_config('enableoutcomes', 1);
        }

        $generalactionbar = new general_action_bar($coursecontext,
            new moodle_url('/grade/report/user/index.php', ['id' => $course->id]), 'report', 'user');
        $renderer = $PAGE->get_renderer('core');
        $generalactionbardata = $generalactionbar->export_for_template($renderer);

        $this->assertCount(1, $generalactionbardata);
        $this->assertArrayHasKey('generalnavselector', $generalactionbardata);

        $generalnavselector = $generalactionbardata['generalnavselector'];
        // Assert the correct number of available option groups in the general navigation selector.
        foreach ($generalnavselector->options as $option) {
            if ($option['isgroup']) {
                $groupname = $option['name'];
                $groupoptions = $option['options'];
                // Assert that the group name exists.
                $this->assertArrayHasKey($groupname, $expectedoptions);
                // Assert that the actual number of group options matches the number of expected options.
                $this->assertEquals(count($expectedoptions[$groupname]), count($groupoptions));

                foreach ($groupoptions as $option) {
                    $this->assertTrue(in_array($option['name'], $expectedoptions[$groupname]));
                }
            }
        }
    }

    /**
     * Data provider for the test_export_for_template test.
     *
     * @return array
     */
    public function test_export_for_template_provider() : array {
        return [
            'Gradebook general navigation for admin; outcomes disabled.' => [
                'admin',
                false,
                [
                    'View' => [
                        'Grader report',
                        'Grade history',
                        'Overview report',
                        'Single view',
                        'User report',
                    ],
                    'Setup' => [
                        'Gradebook setup',
                        'Course grade settings',
                        'Preferences: Grader report',
                    ],
                    'More' => [
                        'Scales',
                        'Grade letters',
                        'Import',
                        'Export',
                    ],
                ],
            ],
            'Gradebook general navigation for admin; outcomes enabled.' => [
                'admin',
                true,
                [
                    'View' => [
                        'Grader report',
                        'Grade history',
                        'Outcomes report',
                        'Overview report',
                        'Single view',
                        'User report',
                    ],
                    'Setup' => [
                        'Gradebook setup',
                        'Course grade settings',
                        'Preferences: Grader report',
                    ],
                    'More' => [
                        'Scales',
                        'Outcomes',
                        'Grade letters',
                        'Import',
                        'Export',
                    ],
                ],
            ],
            'Gradebook general navigation for editing teacher; outcomes disabled.' => [
                'editingteacher',
                false,
                [
                    'View' => [
                        'Grader report',
                        'Grade history',
                        'Overview report',
                        'Single view',
                        'User report',
                    ],
                    'Setup' => [
                        'Gradebook setup',
                        'Course grade settings',
                        'Preferences: Grader report',
                    ],
                    'More' => [
                        'Scales',
                        'Grade letters',
                        'Import',
                        'Export',
                    ],
                ],
            ],
            'Gradebook general navigation for editing teacher; outcomes enabled.' => [
                'editingteacher',
                true,
                [
                    'View' => [
                        'Grader report',
                        'Grade history',
                        'Outcomes report',
                        'Overview report',
                        'Single view',
                        'User report',
                    ],
                    'Setup' => [
                        'Gradebook setup',
                        'Course grade settings',
                        'Preferences: Grader report',
                    ],
                    'More' => [
                        'Scales',
                        'Outcomes',
                        'Grade letters',
                        'Import',
                        'Export',
                    ],
                ],
            ],
            'Gradebook general navigation for non-editing teacher; outcomes enabled.' => [
                'teacher',
                true,
                [
                    'View' => [
                        'Grader report',
                        'Grade history',
                        'Outcomes report',
                        'Overview report',
                        'User report',
                    ],
                    'Setup' => [
                        'Preferences: Grader report',
                    ],
                    'More' => [
                        'Export',
                    ],
                ],
            ],
            'Gradebook general navigation for student; outcomes enabled.' => [
                'student',
                true,
                [
                    'View' => [
                        'Overview report',
                        'User report',
                    ],
                ],
            ],
        ];
    }
}
