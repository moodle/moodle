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
 * @package    mod_scorm
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    /**
     * Test get_actions_overview.
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
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_due_date_overview_data')]
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
     * @return \Generator
     */
    public static function get_due_date_overview_data(): \Generator {
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
     * Test get_extra_studentsattempted_overview.
     *
     * @param string $username
     * @param int $groupmode
     * @param string $activity the activity name to run this test with (there is one created with attemts ('withattempts') and one
     * created without attempts ('withoutattempts')).
     * @param array $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_overview_items_data')]
    public function test_get_extra_totalattempts_overview(
        string $username,
        string $activity,
        int $groupmode,
        array $expected,
    ): void {
        global $PAGE;
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instances' => $instances] = $this->setup_users_and_activity($groupmode);
        $instance = $instances[$activity];

        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users[$username]);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_totalattempts_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        if (!isset($expected['totalattempts'])) {
            $this->assertNull($item);
            return;
        }
        $this->assertEquals(
            $expected['totalattempts']['value'],
            $item->get_value(),
            "Failed for instance: $activity"
        );
        $content = $item->get_content()->export_for_template($PAGE->get_renderer('core'));
        $contentitems = $content['items'] ?? [];
        $this->assertCount(
            count($expected['totalattempts']['items']),
            $contentitems,
        );
        foreach ($expected['totalattempts']['items'] as $item) {
            $currentitem = array_shift($contentitems);
            $this->assertEquals(
                (object) $item,
                $currentitem,
                "Failed for instance: $activity"
            );
        }
    }

    /**
     * Test get_extra_studentsattempted_overview.
     *
     * @param string $username
     * @param int $groupmode
     * @param string $activity the activity name to run this test with (there is one created with attemts ('withattempts') and one
     *  created without attempts ('withoutattempts')).
     * @param array $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_overview_items_data')]
    public function test_get_extra_studentsattempted_overview(
        string $username,
        string $activity,
        int $groupmode,
        array $expected
    ): void {
        $this->resetAfterTest();
        ['users' => $users, 'course' => $course, 'instances' => $instances] = $this->setup_users_and_activity($groupmode);
        $instance = $instances[$activity];
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users[$username]);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_studentsattempted_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        if (!isset($expected['attempted'])) {
            $this->assertNull($item);
            return;
        }
        $this->assertEquals(
            $expected['attempted']['value'],
            $item->get_value(),
            "Failed for instance: $activity"
        );
        $this->assertEquals(
            $expected['attempted']['content'],
            $item->get_content(),
            "Failed for instance: $activity"
        );
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
     * @return \Generator
     */
    public static function get_extra_overview_items_data(): \Generator {
        // Here we intentionally just test the case where course mode is set to NOGROUPS as groups are is not
        // yet supported by the overview page for SCORM module. This will be followed up in a future issue (MDL-85852).
        yield 'teacher 1 - no groups with attempts' => [
            'username' => 't1',
            'activity' => 'withattempts',
            'groupmode' => NOGROUPS,
            'expected' => [
                'attempted' => [
                    'value' => 2,
                    'content' => '<strong>2</strong> of 4',
                ],
                'totalattempts' => [
                    'value' => 3,
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
                            'value' => '1.5',
                        ],
                    ],
                ],
            ],
        ];
        yield 'teacher 1 - no groups without attempts' => [
            'username' => 't1',
            'activity' => 'withoutattempts',
            'groupmode' => NOGROUPS,
            'expected' => [
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
        ];
        yield 'teacher 2 - no groups' => [
            'username' => 't2',
            'activity' => 'withattempts',
            'groupmode' => NOGROUPS,
            'expected' => [
                'attempted' => [
                    'value' => 2,
                    'content' => '<strong>2</strong> of 4',
                ],
                'totalattempts' => [
                    'value' => 3,
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
                            'value' => '1.5',
                        ],
                    ],
                ],
            ],
        ];
        yield 'teacher 1 - separate group' => [
            'username' => 't1',
            'activity' => 'withattempts',
            'groupmode' => SEPARATEGROUPS,
            'expected' => [
                'attempted' => [
                    'value' => 1,
                    'content' => '<strong>1</strong> of 2', // Teacher can also attempt, so s1 and t1 are counted.
                ],
                'totalattempts' => [
                    'value' => 2, // Attempt from s1 only.
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
                            'value' => '2', // Only student 1 in this group attempted twice.
                        ],
                    ],
                ],
            ],
        ];
        // Teacher 2 is not in any group, so no attempt can be counted and the overview will return an error.
        // But still the attempts can be counted if we call directly the manager methods, so we just skip the test here.
        yield 'teacher 1 - visible group' => [
            'username' => 't1',
            'activity' => 'withattempts',
            'groupmode' => VISIBLEGROUPS,
            'expected' => [
                'attempted' => [
                    'value' => 2,
                    'content' => '<strong>2</strong> of 4',
                ],
                'totalattempts' => [
                    'value' => 3,
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
                            'value' => '1.5',
                        ],
                    ],
                ],
            ],
        ];
        yield 'teacher 2 - visible group' => [
            'username' => 't2',
            'activity' => 'withattempts',
            'groupmode' => VISIBLEGROUPS,
            'expected' => [
                'attempted' => [
                    'value' => 2,
                    'content' => '<strong>2</strong> of 4',
                ],
                'totalattempts' => [
                    'value' => 3,
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
                            'value' => '1.5',
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
            $groups = [];
            foreach ($data as $username => $userinfo) {
                ['role' => $role, 'groups' => $groupstoassign] = $userinfo;
                $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
                foreach ($groupstoassign as $grouptoassign) {
                    if (!isset($groups[$grouptoassign])) {
                        // Create the group if it does not exist.
                        $groups[$grouptoassign] = $generator->create_group(['courseid' => $course->id, 'name' => $grouptoassign]);
                    }
                    // Add the user to the group.
                    groups_add_member($groups[$grouptoassign], $users[$username]->id);
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
            // Create an attempt for each student, and two attempts for the student s1.
            foreach ($data as $username => $userinfo) {
                $record = [
                    'userid' => $users[$username]->id,
                    'scormid' => $instances['withattempts']->id,
                ];
                if (isset($grades[$username])) {
                    $record['element'] = 'cmi.core.score.raw';
                    $record['value'] = $grades[$username];
                }
                // Create two attempts for the first student.
                if ($userinfo['role'] === 'student') {
                    if ($username === 's1') {
                        $scormgenerator->create_attempt($record);
                    }
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
