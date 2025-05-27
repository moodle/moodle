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

namespace mod_wiki;
/**
 * Generator tests class.
 *
 * @package    mod_wiki
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_wiki\manager
 */
final class manager_test extends \advanced_testcase {

    /**
     * Data provider for wiki modes.
     *
     * @return array
     */
    public static function get_wiki_mode_provider(): array {
        return [
            'collaborative' => ['mode' => 'collaborative', 'expected' => wiki_mode::COLLABORATIVE],
            'individual' => ['mode' => 'individual', 'expected' => wiki_mode::INDIVIDUAL],
        ];
    }

    /**
     * Data provider for test_get_all_entries_count.
     *
     * @return array
     */
    public static function get_all_entries_count_provider(): array {
        return [
            'teacher 1 (no group mode, collaborative)' => [
                'username' => 't1',
                'coursegroupmode' => NOGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 2,
            ],
            'teacher 1 (no group mode, undefined)' => [
                'username' => 't1',
                'coursegroupmode' => NOGROUPS,
                'wikimode' => wiki_mode::UNDEFINED,
                'expectedcount' => 2,
            ],
            'teacher 1 (separate group mode, collaborative)' => [
                'username' => 't1',
                'coursegroupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 1,
            ],
            // Teacher 1 belongs to group 1, so should see s1.
            'teacher 1 (visible group mode, collaborative)' => [
                'username' => 't1',
                'coursegroupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 2,
            ],
            // Teacher 2 does not belong to any group.
            'teacher 2 (no group mode, collaborative)' => [
                'username' => 't2',
                'coursegroupmode' => NOGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 2,
            ],
            'teacher 2 (separate group mode, collaborative)' => [
                'username' => 't2',
                'coursegroupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 0,
            ],
            // Teacher 2 does not belong to any group.
            'teacher 2 (visible group mode, collaborative)' => [
                'username' => 't2',
                'coursegroupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 2,
            ],
            // Teacher Individual mode.
            'teacher 1 (no group mode, individual)' => [
                'username' => 't1',
                'coursegroupmode' => NOGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedcount' => 2,
            ],
            'teacher 1 (separate group mode, individual)' => [
                'username' => 't1',
                'coursegroupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedcount' => 1,
            ],
            'teacher 1 (visible group mode, individual)' => [
                'username' => 't1',
                'coursegroupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedcount' => 2,
            ],
            // Student collaborative mode.
            'student 1 (no group mode, collaborative)' => [
                'username' => 's1',
                'coursegroupmode' => NOGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 2,
            ],
            'student 1 (separate group mode, collaborative)' => [
                'username' => 's1',
                'coursegroupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 1,
            ],
            'student 1 (visible group mode, collaborative)' => [
                'username' => 's1',
                'coursegroupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedcount' => 2,
            ],
            // Student individual mode.
            'student 1 (no group mode, individual)' => [
                'username' => 's1',
                'coursegroupmode' => NOGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedcount' => 1,
            ],
            'student 1 (separate group mode, individual)' => [
                'username' => 's1',
                'coursegroupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedcount' => 1,
            ],
            'student 1 (visible group mode, individual)' => [
                'username' => 's1',
                'coursegroupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedcount' => 2,
            ],
        ];
    }

    /**
     * Data provider for test_get_all_entries_count.
     *
     * @return array
     */
    public static function get_user_entries_count_provider(): array {
        return [
            'student 1' => ['s1', 1],
            'student 2' => ['s2', 1],
            'teacher 1' => ['t1', 0],
        ];
    }

    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test creating a manager instance from an instance record.
     *
     * @covers \mod_wiki\manager::create_from_instance
     */
    public function test_create_manager_instance_from_instance_record(): void {
        $this->resetAfterTest();
        ['instance' => $instance] = $this->setup_users_and_activity();
        $manager = manager::create_from_instance($instance);
        $this->assertNotNull($manager);
    }

    /**
     * Setup users and activity for testing answers retrieval.
     *
     * @param int $groupmode the group mode to use for the course.
     * @param wiki_mode $mode the wiki mode to use for the instance.
     * @return array indexed array with 'users', 'course' and  'instance'.
     */
    private function setup_users_and_activity(int $groupmode = NOGROUPS, wiki_mode $mode = wiki_mode::COLLABORATIVE): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/wiki/locallib.php');
        $users = [];
        $generator = $this->getDataGenerator();
        $courseparams = [];
        if ($groupmode !== NOGROUPS) {
            // Set the group mode for the course.
            $courseparams['groupmode'] = $groupmode;
        }
        $course = $generator->create_course($courseparams);
        foreach (['s1' => 'student', 's2' => 'student', 't1' => 'teacher', 't2' => 'teacher'] as $username => $role) {
            $users[$username] = $generator->create_and_enrol($course, $role, ['username' => $username]);
        }

        $groups = [];
        if ($groupmode !== NOGROUPS) {
            // Create a group if the group mode is not NOGROUPS.
            $groups[] = $generator->create_group(['courseid' => $course->id]);
            $groups[] = $generator->create_group(['courseid' => $course->id]);
            groups_add_member($groups[0], $users['s1']->id);
            groups_add_member($groups[1], $users['s2']->id);
            groups_add_member($groups[0], $users['t1']->id);
        }
        $instance = $generator->create_module(
            'wiki',
            [
                'course' => $course,
                'wikimode' => $mode->value,
                'groupmode' => $groupmode,
            ],
        );

        $wikigenerator = $generator->get_plugin_generator('mod_wiki');

        $pages = [];
        foreach (['s1', 's2'] as $username) {
            $user = $users[$username];
            // Create a first page for each user.
            $this->setUser($user->id);
            $groups = groups_get_my_groups();
            $userid = $mode === wiki_mode::INDIVIDUAL ? $user->id : 0; // Use user id for individual mode, 0 for collaborative
            // This is similar to the view.php logic, where the first page is created either for group or users.
            foreach ($groups as $group) {
                // Ensure the user is in the group.
                $pages["{$username}{$group->name}"] = $wikigenerator->create_first_page($instance, [
                    'wikiid' => $instance->id,
                    'userid' => $userid,
                    'group' => $group->id,
                    'content' => "Wiki first page content  for $username",
                    'title' => $instance->firstpagetitle, // We need to use the first page title from the instance to make sure
                    // that this page is considered as first page. {@see wiki_get_first_page()}.
                ]);
            }
            if (empty($groups)) {
                $pages["{$username}"] = $wikigenerator->create_page($instance, [
                    'wikiid' => $instance->id,
                    'userid' => $userid,
                    'content' => "Wiki first page content  for $username",
                    'title' => "Wiki first page title  for $username",
                ]);
            }
        }
        return [
            'users' => $users,
            'course' => $course,
            'instance' => $instance,
            'pages' => $pages,

        ];
    }

    /**
     * Test creating a manager instance from a course module.
     *
     * @covers \mod_wiki\manager::create_from_coursemodule
     */
    public function test_create_manager_instance_from_coursemodule(): void {
        $this->resetAfterTest();
        ['instance' => $instance, 'course' => $course] = $this->setup_users_and_activity();
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $manager = manager::create_from_coursemodule($cm);
        $this->assertNotNull($manager);
    }

    /**
     * Test the wiki mode of the wiki instance.
     *
     * @param string $mode the mode of the wiki instance.
     * @param wiki_mode $expected the expected wiki mode.
     *
     * @covers       \mod_wiki\manager::get_wiki_mode
     * @dataProvider get_wiki_mode_provider
     */
    public function test_wiki_mode(string $mode, wiki_mode $expected): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $wiki = $this->getDataGenerator()->create_module('wiki', ['course' => $course, 'wikimode' => $mode]);
        $manager = manager::create_from_instance($wiki);
        $this->assertEquals($expected, $manager->get_wiki_mode());
    }

    /**
     * Test retrieving entries count for all users.
     *
     * @param string $username the username of the user to retrieve entries count for.
     * @param int $coursegroupmode the group mode of the course.
     * @param wiki_mode $wikimode the wiki mode of the instance.
     * @param int $expectedcount the expected count of answers for the user.
     *
     * @covers       \mod_wiki\manager::get_all_entries_count
     * @dataProvider get_all_entries_count_provider
     */
    public function test_get_all_entries_count(
        string $username,
        int $coursegroupmode,
        wiki_mode $wikimode,
        int $expectedcount
    ): void {
        [
            'users' => $users,
            'instance' => $instance
        ] = $this->setup_users_and_activity($coursegroupmode, $wikimode);

        $manager = manager::create_from_instance($instance);
        $count = $manager->get_all_entries_count($users[$username]->id);
        $this->assertEquals($expectedcount, $count);
    }

    /**
     * Test retrieving entries my count for a given user.
     *
     * @param string $username the username of the user to retrieve entries count for.
     * @param int $expectedcount the expected count of answers for the user.
     *
     * @covers       \mod_wiki\manager::get_user_entries_count
     * @dataProvider get_user_entries_count_provider
     */
    public function test_get_user_entries_count(string $username, int $expectedcount): void {
        ['users' => $users, 'instance' => $instance] = $this->setup_users_and_activity();
        $manager = manager::create_from_instance($instance);
        $count = $manager->get_user_entries_count($users[$username]->id);
        $this->assertEquals($expectedcount, $count);
    }

    /**
     * Test the get_wiki_pageid method.
     *
     * @param string $username the username of the user to retrieve the page id for.
     * @param int $groupmode
     * @param wiki_mode $wikimode the wiki mode of the instance.
     * @param string|null $expectedpage the expected page id for the user.
     *
     * @covers       \mod_wiki\manager::get_main_wiki_pageid
     * @dataProvider get_main_wiki_pageid_data_provider
     */
    public function test_get_main_wiki_pageid(string $username, int $groupmode, wiki_mode $wikimode, ?string $expectedpage): void {
        $this->resetAfterTest();
        ['users' => $users, 'instance' => $instance, 'pages' => $pages] = $this->setup_users_and_activity($groupmode, $wikimode);
        $manager = manager::create_from_instance($instance);
        $this->setUser($users[$username]); // Set the user to the one who created the wiki.
        $pageid = $manager->get_main_wiki_pageid();
        $pagestoid = array_map(function($page) {
            return $page->id;
        }, $pages);
        $idtopage = array_flip($pagestoid);
        $this->assertEquals(
            $expectedpage,
            $idtopage[$pageid] ?? null,
            "Page id for user $username does not match expected page id."
        );

    }

    /**
     * Data provider for test_get_wiki_pageid.
     *
     * @return array
     */
    public static function get_main_wiki_pageid_data_provider(): array {
        return [
            'teacher 1 (no group mode)' => [
                'username' => 't1',
                'groupmode' => NOGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => null,
            ],
            'teacher 2 (no group mode)' => [
                'username' => 't2',
                'groupmode' => NOGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => null,
            ],
            'student1 (no group mode, collaborative)' => [
                'username' => 's1',
                'groupmode' => NOGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => null,
            ],
            'student1 (no group mode, no wiki mode)' => [
                'username' => 's1',
                'groupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::UNDEFINED,
                'expectedpage' => 's1group-0001', // This is the first page created for all users (userid = 0).
            ],
            'teacher 1 (separate group mode)' => [
                'username' => 't1',
                'groupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => "s1group-0001",
            ],
            'teacher 2 (separate group mode)' => [
                'username' => 't2',
                'groupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => null,
            ],
            'student 1 (separate group mode)' => [
                'username' => 's1',
                'groupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => "s1group-0001",
            ],
            'student 1 (separate group mode, no wiki mode)' => [
                'username' => 's1',
                'groupmode' => SEPARATEGROUPS,
                'wikimode' => wiki_mode::UNDEFINED,
                'expectedpage' => "s1group-0001",
            ],
            'teacher 1 (visible group mode)' => [
                'username' => 't1',
                'groupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => "s1group-0001",
            ],
            'teacher 2 (visible group mode)' => [
                'username' => 't2',
                'groupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => "s1group-0001",
            ],
            'student 1 (visible group mode)' => [
                'username' => 's1',
                'groupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::COLLABORATIVE,
                'expectedpage' => "s1group-0001",
            ],
            'student 1 (visible group mode, no wiki mode)' => [
                'username' => 's1',
                'groupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::UNDEFINED,
                'expectedpage' => "s1group-0001",
            ],
            'teacher 1 (no group mode) - individual' => [
                'username' => 't1',
                'groupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedpage' => null,
            ],
            'teacher 1 (separate group mode) - individual' => [
                'username' => 't1',
                'groupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedpage' => null,
            ],
            'teacher 1 (visible group mode) - individual' => [
                'username' => 't1',
                'groupmode' => VISIBLEGROUPS,
                'wikimode' => wiki_mode::INDIVIDUAL,
                'expectedpage' => null,
            ],
        ];
    }
}
