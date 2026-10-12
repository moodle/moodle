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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace mod_quiz\local;

use advanced_testcase;
use context_module;
use stdClass;

/**
 * Tests for the quiz overrides cache manager.
 *
 * @package     mod_quiz
 * @copyright   2025 Catalyst IT Australia Pty Ltd
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\mod_quiz\cache\quiz_overrides_cache::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\mod_quiz\local\quiz_overrides_cache_manager::class)]
final class quiz_overrides_cache_manager_test extends advanced_testcase {
    /**
     * Builds and returns a reusable quiz overrides testing context.
     *
     * @return stdClass
     */
    private function create_test_data(): stdClass {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $quiz = $generator->create_module('quiz', ['course' => $course->id]);
        $user1 = $generator->create_and_enrol($course);
        $user2 = $generator->create_and_enrol($course);
        $group = $generator->create_group(['courseid' => $course->id]);
        groups_add_member($group->id, $user2->id);

        $manager = new override_manager($quiz, context_module::instance($quiz->cmid));

        return (object) [
            'quiz' => $quiz,
            'manager' => $manager,
            'user1' => $user1,
            'user2' => $user2,
            'group' => $group,
        ];
    }

    /**
     * Ensures getting overrides returns an empty array when none have been created.
     */
    public function test_get_overrides_is_empty_initially(): void {
        $data = $this->create_test_data();

        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));
    }

    /**
     * Ensures a user override is returned only for the specified user.
     */
    public function test_get_overrides_returns_user_override_for_correct_user(): void {
        $data = $this->create_test_data();

        $overrideid = $data->manager->save_override([
            'userid' => $data->user1->id,
            'timelimit' => HOURSECS,
        ]);

        $overridesforuser1 = quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id);
        $this->assertCount(1, $overridesforuser1);
        $this->assertEquals($overrideid, reset($overridesforuser1)->id);

        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));
    }

    /**
     * Ensures a group override is returned only for users who are members of the group.
     */
    public function test_get_overrides_returns_group_override_for_group_member(): void {
        $data = $this->create_test_data();

        $overrideid = $data->manager->save_override([
            'groupid' => $data->group->id,
            'timelimit' => HOURSECS * 2,
        ]);

        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));

        $overridesforuser2 = quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id);
        $this->assertCount(1, $overridesforuser2);
        $this->assertEquals($overrideid, reset($overridesforuser2)->id);
    }

    /**
     * Ensures that deleting an override by its ID invalidates the cache for the affected user.
     */
    public function test_deleting_override_by_id_invalidates_cache(): void {
        $data = $this->create_test_data();

        $useroverrideid = $data->manager->save_override([
            'userid' => $data->user1->id,
            'timelimit' => HOURSECS,
        ]);
        $data->manager->save_override([
            'groupid' => $data->group->id,
            'timelimit' => HOURSECS * 2,
        ]);

        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        $data->manager->delete_overrides_by_id([$useroverrideid], false);

        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));
    }

    /**
     * Ensures that deleting an override by its record invalidates the cache for affected users.
     */
    public function test_deleting_override_record_invalidates_cache(): void {
        global $DB;

        $data = $this->create_test_data();

        $data->manager->save_override([
            'userid' => $data->user1->id,
            'timelimit' => HOURSECS,
        ]);
        $groupoverrideid = $data->manager->save_override([
            'groupid' => $data->group->id,
            'timelimit' => HOURSECS * 2,
        ]);

        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        $groupoverride = $DB->get_record('quiz_overrides', ['id' => $groupoverrideid], '*', MUST_EXIST);
        $data->manager->delete_overrides([$groupoverride], false);

        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));
    }

    /**
     * Ensures that deleting all overrides for a quiz invalidates the cache for all users.
     */
    public function test_deleting_all_overrides_invalidates_cache_for_all_users(): void {
        $data = $this->create_test_data();

        $data->manager->save_override([
            'userid' => $data->user1->id,
            'timelimit' => HOURSECS,
        ]);
        $data->manager->save_override([
            'groupid' => $data->group->id,
            'timelimit' => HOURSECS * 2,
        ]);

        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        $data->manager->delete_all_overrides(false);

        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));
    }

    /**
     * Ensures that changes to group membership invalidate the relevant user caches.
     */
    public function test_group_membership_changes_invalidate_cache(): void {
        $data = $this->create_test_data();

        $data->manager->save_override([
            'userid' => $data->user1->id,
            'timelimit' => HOURSECS,
        ]);
        $data->manager->save_override([
            'groupid' => $data->group->id,
            'timelimit' => HOURSECS * 2,
        ]);

        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        groups_add_member($data->group->id, $data->user1->id);
        $this->assertCount(2, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        groups_remove_member($data->group->id, $data->user1->id);
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        groups_delete_group($data->group->id);
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));
    }

    /**
     * Ensures that purging the cache for an override invalidates the cache for all affected users.
     */
    public function test_purge_for_override(): void {
        global $DB;

        $data = $this->create_test_data();

        $useroverrideid = $data->manager->save_override([
            'userid' => $data->user1->id,
            'timelimit' => HOURSECS,
        ]);
        $data->manager->save_override([
            'groupid' => $data->group->id,
            'timelimit' => HOURSECS * 2,
        ]);
        $records = $DB->get_records('quiz_overrides');

        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        $DB->delete_records('quiz_overrides', ['quiz' => $data->quiz->id]);

        // Cached data still exists until the manager purges the relevant entries.
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        // Purge for the user override only.
        quiz_overrides_cache_manager::purge_for_overrides([$records[$useroverrideid]]);

        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertCount(1, quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));

        // Purge for all overrides.
        quiz_overrides_cache_manager::purge_for_overrides($records);

        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user1->id));
        $this->assertSame([], quiz_overrides_cache_manager::get_overrides($data->quiz->id, $data->user2->id));
    }
}
