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

namespace mod_wiki\courseformat;

use mod_wiki\wiki_mode;
use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for Wiki integration.
 *
 * @package    mod_wiki
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    /**
     * Data provider for wiki modes.
     *
     * @return \Generator
     */
    public static function get_wiki_mode_provider(): \Generator {
        yield 'collaborative' => ['mode' => wiki_mode::COLLABORATIVE];
        yield 'individual' => ['mode' => wiki_mode::INDIVIDUAL];
    }

    /**
     * Test get_extra_my_entries method.
     *
     * @param string $username
     * @param int|null $expectedcount
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_extra_my_entries_provider')]
    public function test_get_extra_my_entries(string $username, ?int $expectedcount = null): void {
        $this->resetAfterTest();
        ['users' => $users, 'instance' => $instance, 'course' => $course] = $this->setup_users_and_activity();
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $overview = overviewfactory::create($cm);
        $this->setUser($users[$username]->id);
        $items = $overview->get_extra_overview_items();
        $item = $items['my_entries'] ?? null;

        $this->assertEquals(
            $expectedcount,
            $item?->get_value()
        );
    }

    /**
     * Data provider for get_extra_my_entries.
     *
     * @return \Generator
     */
    public static function get_extra_my_entries_provider(): \Generator {
        yield 'student 1' => ['s1', 1];
        yield 'student 2' => ['s2', 1];
        yield 'teacher 1' => ['t1', null]; // Teacher 1 does not have any entries.
    }

    /**
     * Test the wiki mode of the wiki instance.
     *
     * @param wiki_mode $mode the expected wiki mode.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_wiki_mode_provider')]
    public function test_wiki_mode(wiki_mode $mode): void {
        $this->resetAfterTest();
        ['users' => $users, 'instance' => $instance, 'course' => $course] =
            $this->setup_users_and_activity(NOGROUPS, $mode->value);
        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $overview = overviewfactory::create($cm);
        $this->setUser($users['t1']->id);
        $items = $overview->get_extra_overview_items();
        $item = $items['wiki_type'] ?? null;

        $this->assertEquals(
            $mode->value,
            $item->get_value(),
        );
    }

    /**
     * Setup users and activity for testing answers retrieval.
     *
     * @param int $groupmode the group mode to use for the course.
     * @param string $mode the mode of the wiki instance.
     * @return array indexed array with 'users', 'course' and  'instance'.
     */
    private function setup_users_and_activity(int $groupmode = NOGROUPS, string $mode = 'collaborative'): array {
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
                'wikimode' => $mode,
                'groupmode' => $groupmode,
                'firstpagetitle' => 'Wiki first page title',
            ],
        );

        $wikigenerator = $generator->get_plugin_generator('mod_wiki');

        $pages = [];
        foreach (['s1', 's2'] as $username) {
            $user = $users[$username];
            // Create a first page for each user.
            $this->setUser($user->id);
            $groups = groups_get_my_groups();
            foreach ($groups as $group) {
                $authorid = ($mode === wiki_mode::INDIVIDUAL->value) ? $user->id : 0;

                // Ensure the user is in the group.
                $pages[] = $wikigenerator->create_first_page(
                    $instance,
                    [
                        'wikiid' => $instance->id,
                        'userid' => $authorid,
                        'group' => $group->id,
                        'content' => "Wiki first page content for $username",
                        'title' => "Wiki first page title",
                    ],
                );
            }
            if (empty($groups)) {
                $pages[] = $wikigenerator->create_page(
                    $instance,
                    [
                        'wikiid' => $instance->id,
                        'userid' => $user->id,
                        'content' => "Wiki first page content for $username",
                        'title' => "Wiki first page title",
                    ],
                );
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
     * Test get_extra_entries method.
     *
     * @param string $username
     * @param int $coursegroupmode
     * @param int $expectedcount
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_extra_entries')]
    public function test_get_extra_entries(
        string $username,
        int $coursegroupmode,
        int $expectedcount
    ): void {
        $this->resetAfterTest();
        [
            'users' => $users,
            'instance' => $instance,
            'course' => $course
        ] = $this->setup_users_and_activity($coursegroupmode);

        $cm = get_fast_modinfo($course)->get_cm($instance->cmid);
        $this->setUser($users[$username]->id);

        $overview = overviewfactory::create($cm);
        $items = $overview->get_extra_overview_items();
        $item = $items['totalentries'] ?? null;
        $this->assertEquals($expectedcount, $item->get_value());
    }

    /**
     * Data provider for get_extra_entries.
     *
     * @return array
     */
    public static function data_provider_get_extra_entries(): array {
        return [
            'teacher 1 (no group mode)' => ['t1', NOGROUPS, 2],
            'teacher 1 (separate group mode)' => ['t1', SEPARATEGROUPS, 1], // Teacher 1 belongs to group 1, so should see s1.
            'teacher 1 (visible group mode)' => ['t1', VISIBLEGROUPS, 2],
            // Teacher 2 does not belong to any group.
            'teacher 2 (no group mode)' => ['t2', NOGROUPS, 2],
            'teacher 2 (separate group mode)' => ['t2', SEPARATEGROUPS, 0], // Teacher 2 does not belong to any group.
            'teacher 2 (visible group mode)' => ['t2', VISIBLEGROUPS, 2],
        ];
    }

    /**
     * Test get_extra_entries method.
     */
    public function test_get_actions_overview(): void {
        $this->resetAfterTest();

        [
            'users' => $users,
            'instance' => $instancecol,
            'course' => $course
        ] = $this->setup_users_and_activity(
            groupmode: SEPARATEGROUPS, // Use separate groups to initialise pages too.
            mode: wiki_mode::COLLABORATIVE->value,
        );

        $instanceind = $this->getDataGenerator()->create_module(
            'wiki',
            [
                'course' => $course,
                'wikimode' => wiki_mode::INDIVIDUAL->value,
            ]
        );

        $this->setUser($users['t1']->id);

        $cmcol = get_fast_modinfo($course)->get_cm($instancecol->cmid);
        $overview = overviewfactory::create($cmcol);
        $item = $overview->get_actions_overview();
        $this->assertNotNull($item);
        $this->assertStringContainsString('wiki/map.php', $item->get_content()->url->out(false));

        // Test the individual wiki instance, which is also empty, has actions.
        $cmind = get_fast_modinfo($course)->get_cm($instanceind->cmid);
        $overview = overviewfactory::create($cmind);
        $item = $overview->get_actions_overview();
        $this->assertStringContainsString('wiki/view.php', $item->get_content()->url->out(false));
    }
}
