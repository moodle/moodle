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

namespace mod_scorm\courseformat;

use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for SCORM activity overview
 *
 * @covers     \mod_scorm\courseformat\overview
 * @package    mod_scorm
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class overview_test extends \advanced_testcase {
    /**
     * Test get_actions_overview.
     *
     * @covers ::get_actions_overview
     */
    public function test_get_actions_overview(): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instances' => $instances] = $this->setup_users_and_activity();
        ['withattempts' => $instancewa] = $instances;
        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        // Students have no action column.
        $this->setUser($users['s1']);
        $this->assertNull(overviewfactory::create($cm)->get_actions_overview());

        // Teachers have a 'View results' button.
        $this->setUser($users['t1']);
        $items = overviewfactory::create($cm)->get_actions_overview();
        $this->assertNotNull($items);
        $this->assertEquals(get_string('actions'), $items->get_name());
    }

    /**
     * Test get_due_date_overview method.
     *
     * @param int|null $timeincrement
     *
     * @covers ::get_due_date_overview
     * @dataProvider get_due_date_overview_data
     */
    public function test_get_due_date_overview(?int $timeincrement = null): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $scormtemplate = [];
        $clock = $this->mock_clock_with_frozen();
        if (!is_null($timeincrement)) {
            $scormtemplate['timeclose'] = $clock->time() + $timeincrement;
        }
        ['users' => $users, 'course' => $course, 'instances' => $instances] =
            $this->setup_users_and_activity(instancedata: $scormtemplate);
        ['withattempts' => $instancewa] = $instances;

        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setUser($users['s1']);
        $overview = overviewfactory::create($cm);
        $this->assertEquals(
            is_null($timeincrement) ? null : $clock->time() + $timeincrement,
            $overview->get_due_date_overview()->get_value(),
        );
    }

    /**
     * Data provider for test_get_due_date_overview.
     *
     * @return array
     */
    public static function get_due_date_overview_data(): array {
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
     * Test get_extra_studentsattempted_overview.
     *
     * @param string $username
     * @param int $groupmode
     * @param array $expected
     *
     * @covers ::get_extra_studentsattempted_overview
     * @dataProvider get_extra_overview_items_data
     */
    public function test_get_extra_totalattempts_overview(string $username, int $groupmode, array $expected): void {
        global $PAGE;
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instances' => $instances] = $this->setup_users_and_activity($groupmode);

        foreach ($expected as $instancekey => $expectedvalues) {
            $instance = $instances[$instancekey];
            $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
            $this->setUser($users[$username]);
            $items = overviewfactory::create($cm)->get_extra_overview_items();
            if (!isset($expectedvalues['totalattempts'])) {
                $this->assertArrayNotHasKey('totalattempts', $items);
                return;
            }
            $this->assertEquals(
                $expectedvalues['totalattempts']['value'],
                $items['totalattempts']->get_value(),
                "Failed for instance: $instancekey"
            );
            $content = $items['totalattempts']->get_content()->export_for_template($PAGE->get_renderer('core'));
            $contentitems = $content['items'] ?? [];
            $this->assertCount(
                count($expectedvalues['totalattempts']['items']),
                $contentitems,
            );
            foreach ($expectedvalues['totalattempts']['items'] as $item) {
                $currentitem = array_shift($contentitems);
                $this->assertEquals(
                    (object) $item,
                    $currentitem,
                    "Failed for instance: $instancekey"
                );
            }
        }
    }

    /**
     * Test get_extra_studentsattempted_overview.
     *
     * @param string $username
     * @param int $groupmode
     * @param array $expected
     *
     * @covers ::get_extra_studentsattempted_overview
     * @dataProvider get_extra_overview_items_data
     */
    public function test_get_extra_studentsattempted_overview(string $username, int $groupmode, array $expected): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instances' => $instances] = $this->setup_users_and_activity($groupmode);

        foreach ($expected as $instancekey => $expectedvalues) {
            $instance = $instances[$instancekey];
            $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
            $this->setUser($users[$username]);
            $items = overviewfactory::create($cm)->get_extra_overview_items();
            if (!isset($expectedvalues['attempted'])) {
                $this->assertArrayNotHasKey('attempted', $items);
                return;
            }
            $this->assertEquals(
                $expectedvalues['attempted']['value'],
                $items['attempted']->get_value(),
                "Failed for instance: $instancekey"
            );
            $this->assertEquals(
                $expectedvalues['attempted']['content'],
                $items['attempted']->get_content(),
                "Failed for instance: $instancekey"
            );
        }
    }

    /**
     * Test get_extra_overview_items when there are no users in the course
     *
     * @return void
     */
    public function test_get_extra_overview_no_users(): void {
        global $PAGE;
        $this->resetAfterTest();
        ['course' => $course, 'instances' => $instances] = $this->setup_users_and_activity(createusers: false);
        ['withattempts' => $instancewa] = $instances; // This has no user so no attempt too.
        $cm = get_fast_modinfo($course)->get_cm($instancewa->cmid);
        $this->setAdminUser();
        $items = overviewfactory::create($cm)->get_extra_overview_items();
        $this->assertArrayHasKey('attempted', $items);
        $this->assertEquals(
            0,
            $items['attempted']->get_value(),
            'Expected no attempts when there are no users in the course.'
        );
        $this->assertEquals(
            '<strong>0</strong> of 0',
            $items['attempted']->get_content(),
            'Expected no attempts when there are no users in the course.'
        );
        $this->assertArrayHasKey('totalattempts', $items);
        $this->assertEquals(
            0,
            $items['totalattempts']->get_value(),
            'Expected no total attempts when there are no users in the course.'
        );
        $content = $items['totalattempts']->get_content()->export_for_template($PAGE->get_renderer('core'));
        $this->assertCount(
            3,
            $content['items'],
            'Expected items even if the content is empty.'
        );
        $items = $content['items'];
        $this->assertEquals(
            (object) ['label' => 'Grading method', 'value' => 'Highest attempt'],
            array_shift($items),
            'Expected grading method item.'
        );
        $this->assertEquals(
            (object) ['label' => 'Allowed attempts per student', 'value' => 'Unlimited'],
            array_shift($items),
            'Expected allowed attempts item.'
        );
        $this->assertEquals(
            (object) ['label' => 'Average attempts per student', 'value' => '0'],
            array_shift($items),
            'Expected average attempts item.'
        );
    }

    /**
     * Data provider for test_get_extra_studentsattempted_overview and test_get_extra_totalattempts_overview
     *
     * @return array
     */
    public static function get_extra_overview_items_data(): array {
        // Here we intentionally just test the case where course mode is set to NOGROUPS as groups are is not
        // yet supported by the overview page for SCORM module. This will be followed up in a future issue (MDL-85852).
        return [
            'teacher 1 - no groups' => [
                'username' => 't1',
                'groupmode' => NOGROUPS,
                'expected' => [
                    'withattempts' => [
                        'attempted' => [
                            'value' => 2,
                            'content' => '<strong>2</strong> of 4',
                        ],
                        'totalattempts' => [
                            'value' => 2,
                            'items' => [
                                [
                                    'label' => 'Grading method',
                                    'value' => 'Highest attempt',
                                ],
                                [
                                    'label' => 'Allowed attempts per student',
                                    'value' => 'Unlimited',
                                ],
                                [
                                    'label' => 'Average attempts per student',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                    'withoutattempts' => [
                        'attempted' => [
                            'value' => 0,
                            'content' => '<strong>0</strong> of 4',
                        ],
                        'totalattempts' => [
                            'value' => 0,
                            'items' => [
                                [
                                    'label' => 'Grading method',
                                    'value' => 'Highest attempt',
                                ],
                                [
                                    'label' => 'Allowed attempts per student',
                                    'value' => 'Unlimited',
                                ],
                                [
                                    'label' => 'Average attempts per student',
                                    'value' => '0',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'teacher 2 - no groups' => [
                'username' => 't2',
                'groupmode' => NOGROUPS,
                'expected' => [
                    'withattempts' => [
                        'attempted' => [
                            'value' => 2,
                            'content' => '<strong>2</strong> of 4',
                        ],
                        'totalattempts' => [
                            'value' => 2,
                            'items' => [
                                [
                                    'label' => 'Grading method',
                                    'value' => 'Highest attempt',
                                ],
                                [
                                    'label' => 'Allowed attempts per student',
                                    'value' => 'Unlimited',
                                ],
                                [
                                    'label' => 'Average attempts per student',
                                    'value' => '1',
                                ],
                            ],
                        ],
                    ],
                    'withoutattempts' => [
                        'attempted' => [
                            'value' => 0,
                            'content' => '<strong>0</strong> of 4',
                        ],
                        'totalattempts' => [
                            'value' => 0,
                            'items' => [
                                [
                                    'label' => 'Grading method',
                                    'value' => 'Highest attempt',
                                ],
                                [
                                    'label' => 'Allowed attempts per student',
                                    'value' => 'Unlimited',
                                ],
                                [
                                    'label' => 'Average attempts per student',
                                    'value' => '0',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Setup users and activity for testing answers retrieval.
     *
     * @param int $groupmode the group mode to use for the course.
     * @param bool $createattempt whether to create an attempt for the student.
     * @param array|null $instancedata additional data for the instance.
     * @param array|null $grades the grade to set for the student.
     * @param bool $createusers whether to enrol users in the course.
     * @return array indexed array with 'users', 'course' and 'instance'.
     */
    private function setup_users_and_activity(
        int $groupmode = NOGROUPS,
        bool $createattempt = true,
        ?array $instancedata = null,
        ?array $grades = null,
        bool $createusers = true,
    ): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/scorm/locallib.php');
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
                't1' => ['role' => 'teacher', 'groups' => ['g1']],
                't2' => ['role' => 'teacher', 'groups' => []],
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
            'grademethod' => GRADEHIGHEST, // Use highest grade for grading.
            'whatgrade' => HIGHESTATTEMPT,
        ]);
        $instances = [];
        $instances['withattempts'] = $generator->create_module('scorm', $instancedata);
        $instances['withoutattempts'] = $generator->create_module('scorm', $instancedata); // Create a second instance with no
        // attempt for testing purposes.
        if ($createattempt && $createusers) {
            $scormgenerator = $this->getDataGenerator()->get_plugin_generator('mod_scorm');
            // Create attempts for the students.
            // Two attempts for the first student, one for the second.
            foreach ($data as $username => $userinfo) {
                $record = [
                    'userid' => $users[$username]->id,
                    'scormid' => $instances['withattempts']->id,
                ];
                if (isset($grades[$username])) {
                    $record['element'] = 'cmi.core.score.raw';
                    $record['value'] = $grades[$username];
                }
                // Create an attempt for each student.
                if ($userinfo['role'] === 'student') {
                    $scormgenerator->create_attempt($record);
                }
            }
        }
        return [
            'users' => $users,
            'course' => $course,
            'instances' => $instances,
        ];
    }
}
