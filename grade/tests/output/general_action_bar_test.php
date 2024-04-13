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
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/grade/lib.php");
        parent::setUpBeforeClass();
    }

    /**
     * Search array $options for an element which is an array containing 'name' => $name.
     *
     * @param array $options the array of options.
     * @param string $name the name to find.
     * @return array|null the particular option if found, else null.
     */
    protected function find_option_by_name(array $options, string $name): ?array {
        foreach ($options as $option) {
            if ($option['name'] == $name) {
                return $option;
            }
        }
        return null;
    }

    /**
     * Test the exported data for the general action bar for different user roles and settings.
     *
     * @dataProvider export_for_template_provider
     * @param string $userrole The user role to test
     * @param bool $enableoutcomes Whether to enable outcomes
     * @param array $expectedoptions The expected options returned in the general action selector
     * @covers \core_grades\output\general_action_bar::export_for_template
     */
    public function test_export_for_template(string $userrole, bool $enableoutcomes, array $expectedoptions): void {
        global $PAGE;

        // There may be additional plugins installed in the codebase where this
        // test is being run, therefore, we need to know which links can be
        // present in a standard Moodle install, and only check them.
        $allcorenavlinks = [
            get_string('view') => [
                get_string('pluginname', 'gradereport_grader'),
                get_string('pluginname', 'gradereport_history'),
                get_string('pluginname', 'gradereport_outcomes'),
                get_string('pluginname', 'gradereport_overview'),
                get_string('pluginname', 'gradereport_singleview'),
                get_string('pluginname', 'gradereport_summary'),
                get_string('pluginname', 'gradereport_user'),
            ],
            get_string('setup', 'grades') => [
                get_string('gradebooksetup', 'grades'),
                get_string('coursegradesettings', 'grades'),
                get_string('preferences', 'grades') . ': ' . get_string('pluginname', 'gradereport_grader'),
            ],
            get_string('moremenu') => [
                get_string('scales'),
                get_string('outcomes', 'grades'),
                get_string('gradeletters', 'grades'),
                get_string('import', 'grades'),
                get_string('export', 'grades'),
            ],
        ];

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

        // Assert that the right links are present in each group.
        foreach ($allcorenavlinks as $groupname => $corelinks) {
            $actualgroup = $this->find_option_by_name($generalnavselector->options, $groupname);

            if (!isset($expectedoptions[$groupname])) {
                // This group should not be present.
                $this->assertNull($actualgroup, "Nav link group '$groupname' should not be present, but is.");
                continue;
            }

            $this->assertNotNull($actualgroup, "Nav link group '$groupname' should be present, but is not.");
            $this->assertTrue($actualgroup['isgroup'], "the thing claiming to be nav link group '$groupname' is not a group.");

            foreach ($corelinks as $corelinkname) {
                $actuallink = $this->find_option_by_name($actualgroup['options'], $corelinkname);

                if (!in_array($corelinkname, $expectedoptions[$groupname])) {
                    $this->assertNull($actuallink,
                            "Nav link '$corelinkname' should not be present in group '$groupname', but is.");
                } else {
                    $this->assertNotNull($actuallink,
                            "Nav link '$corelinkname' should be present in group '$groupname', but is not.");
                }
            }
        }
    }

    /**
     * Data provider for the test_export_for_template test.
     *
     * @return array
     */
    public static function export_for_template_provider(): array {
        $graderpluginname = get_string('pluginname', 'gradereport_grader');
        $historypluginname = get_string('pluginname', 'gradereport_history');
        $outcomespluginname = get_string('pluginname', 'gradereport_outcomes');
        $overviewpluginname = get_string('pluginname', 'gradereport_overview');
        $singleviewpluginname = get_string('pluginname', 'gradereport_singleview');
        $summarypluginname = get_string('pluginname', 'gradereport_summary');
        $userpluginname = get_string('pluginname', 'gradereport_user');

        $viewstr = get_string('view');
        $setupstr = get_string('setup', 'grades');
        $morestr = get_string('moremenu');

        $gradebooksetupstr = get_string('gradebooksetup', 'grades');
        $coursegradesettingsstr = get_string('coursegradesettings', 'grades');
        $graderpreferencesstr = get_string('preferences', 'grades') . ': ' . get_string('pluginname', 'gradereport_grader');

        $scalesstr = get_string('scales');
        $outcomesstr = get_string('outcomes', 'grades');
        $gradelettersstr = get_string('gradeletters', 'grades');
        $importstr = get_string('import', 'grades');
        $exportstr = get_string('export', 'grades');

        return [
            'Gradebook general navigation for admin; outcomes disabled.' => [
                'admin',
                false,
                [
                    $viewstr => [
                        $graderpluginname,
                        $historypluginname,
                        $overviewpluginname,
                        $singleviewpluginname,
                        $summarypluginname,
                        $userpluginname,
                    ],
                    $setupstr => [
                        $gradebooksetupstr,
                        $coursegradesettingsstr,
                        $graderpreferencesstr,
                    ],
                    $morestr => [
                        $scalesstr,
                        $gradelettersstr,
                        $importstr,
                        $exportstr,
                    ],
                ],
            ],
            'Gradebook general navigation for admin; outcomes enabled.' => [
                'admin',
                true,
                [
                    $viewstr => [
                        $graderpluginname,
                        $historypluginname,
                        $outcomespluginname,
                        $overviewpluginname,
                        $singleviewpluginname,
                        $summarypluginname,
                        $userpluginname,
                    ],
                    $setupstr => [
                        $gradebooksetupstr,
                        $coursegradesettingsstr,
                        $graderpreferencesstr,
                    ],
                    $morestr => [
                        $scalesstr,
                        $outcomesstr,
                        $gradelettersstr,
                        $importstr,
                        $exportstr,
                    ],
                ],
            ],
            'Gradebook general navigation for editing teacher; outcomes disabled.' => [
                'editingteacher',
                false,
                [
                    $viewstr => [
                        $graderpluginname,
                        $historypluginname,
                        $overviewpluginname,
                        $singleviewpluginname,
                        $summarypluginname,
                        $userpluginname,
                    ],
                    $setupstr => [
                        $gradebooksetupstr,
                        $coursegradesettingsstr,
                        $graderpreferencesstr,
                    ],
                    $morestr => [
                        $scalesstr,
                        $gradelettersstr,
                        $importstr,
                        $exportstr,
                    ],
                ],
            ],
            'Gradebook general navigation for editing teacher; outcomes enabled.' => [
                'editingteacher',
                true,
                [
                    $viewstr => [
                        $graderpluginname,
                        $historypluginname,
                        $outcomespluginname,
                        $overviewpluginname,
                        $singleviewpluginname,
                        $summarypluginname,
                        $userpluginname,
                    ],
                    $setupstr => [
                        $gradebooksetupstr,
                        $coursegradesettingsstr,
                        $graderpreferencesstr,
                    ],
                    $morestr => [
                        $scalesstr,
                        $outcomesstr,
                        $gradelettersstr,
                        $importstr,
                        $exportstr,
                    ],
                ],
            ],
            'Gradebook general navigation for non-editing teacher; outcomes enabled.' => [
                'teacher',
                true,
                [
                    $viewstr => [
                        $graderpluginname,
                        $historypluginname,
                        $outcomespluginname,
                        $overviewpluginname,
                        $summarypluginname,
                        $userpluginname,
                    ],
                    $setupstr => [
                        $graderpreferencesstr,
                    ],
                    $morestr => [
                        $exportstr,
                    ],
                ],
            ],
            'Gradebook general navigation for student; outcomes enabled.' => [
                'student',
                true,
                [
                    $viewstr => [
                        $overviewpluginname,
                        $userpluginname,
                    ],
                ],
            ],
        ];
    }
}
