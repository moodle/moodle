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

namespace core_badges;

use context_course;
use core_badges\potential_award_selector;
use core_badges\tests\badges_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Unit tests for potential_award_selector class.
 *
 * @package     core_badges
 * @copyright   2025 Dai Nguyen Trong <ngtrdai@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_badges\potential_award_selector::class)]
final class potential_award_selector_test extends badges_testcase {
    /**
     * Create a simple test environment.
     *
     * @param int $usercount Number of users to create.
     * @return object Test environment object.
     */
    private function create_test_environment(int $usercount = 2): object {
        $env = new \stdClass();

        // Create course.
        $env->course = $this->getDataGenerator()->create_course();
        $env->context = context_course::instance($env->course->id);

        // Create badge.
        $badgedata = [
            'type' => BADGE_TYPE_COURSE,
            'courseid' => $env->course->id,
            'status' => BADGE_STATUS_ACTIVE,
        ];
        $env->badge = $this->getDataGenerator()->get_plugin_generator('core_badges')->create_badge($badgedata);

        // Create users.
        $env->users = [];
        for ($i = 0; $i < $usercount; $i++) {
            $user = $this->getDataGenerator()->create_user([
                'firstname' => 'User' . ($i + 1),
                'lastname' => 'Test',
            ]);

            // Enrol user in course.
            $this->getDataGenerator()->enrol_user($user->id, $env->course->id, 'student');
            $env->users[] = $user;
        }

        // Assign capability to earn badges.
        $studentrole = $this->get_student_role();
        assign_capability('moodle/badges:earnbadge', CAP_ALLOW, $studentrole->id, $env->context->id);

        return $env;
    }

    /**
     * Get student role.
     *
     * @return object Student role record.
     */
    private function get_student_role(): object {
        global $DB;
        return $DB->get_record('role', ['shortname' => 'student']);
    }

    /**
     * Create selector options.
     *
     * @param object $env Test environment object.
     * @return array Selector options array.
     */
    private function create_selector_options(object $env): array {
        return [
            'context' => $env->context,
            'badgeid' => $env->badge->id,
            'issuerid' => 1,
            'issuerrole' => $this->get_student_role()->id,
            'currentgroup' => 0,
        ];
    }

    /**
     * Test find_users method with all users as potential recipients.
     */
    public function test_find_users_all_potential(): void {
        $this->resetAfterTest();

        $env = $this->create_test_environment(2);
        $options = $this->create_selector_options($env);

        $selector = new potential_award_selector('potentialrecipients', $options);
        $result = $selector->find_users('');

        // Should return both users as potential recipients.
        $this->assertIsArray($result);
        $this->assertArrayHasKey(get_string('potentialrecipients', 'badges'), $result);

        $recipients = $result[get_string('potentialrecipients', 'badges')];
        $recipientids = array_keys($recipients);

        $this->assertContains((int) $env->users[0]->id, $recipientids);
        $this->assertContains((int) $env->users[1]->id, $recipientids);
    }

    /**
     * Test find_users method excluding existing recipients.
     */
    public function test_find_users_excluding_existing(): void {
        $this->resetAfterTest();
        global $DB;

        $env = $this->create_test_environment(3);

        // Create manual award for first user.
        $DB->insert_record('badge_manual_award', [
            'badgeid' => $env->badge->id,
            'recipientid' => $env->users[0]->id,
            'issuerid' => 1,
            'issuerrole' => $this->get_student_role()->id,
            'datemet' => time(),
        ]);

        $options = $this->create_selector_options($env);
        $selector = new potential_award_selector('potentialrecipients', $options);
        $result = $selector->find_users('');

        // Should return users 2 and 3, excluding user 1.
        $this->assertIsArray($result);
        $this->assertArrayHasKey(get_string('potentialrecipients', 'badges'), $result);

        $recipients = $result[get_string('potentialrecipients', 'badges')];
        $recipientids = array_keys($recipients);

        $this->assertContains((int) $env->users[1]->id, $recipientids);
        $this->assertContains((int) $env->users[2]->id, $recipientids);
        $this->assertNotContains((int) $env->users[0]->id, $recipientids);
    }

    /**
     * Test set_existing_recipients method.
     */
    public function test_set_existing_recipients(): void {
        $this->resetAfterTest();

        $env = $this->create_test_environment(3);
        $options = $this->create_selector_options($env);

        $selector = new potential_award_selector('potentialrecipients', $options);

        // Set existing recipients.
        $existingrecipients = [
            'existing' => [
                $env->users[0]->id => $env->users[0],
                $env->users[1]->id => $env->users[1],
            ],
        ];
        $selector->set_existing_recipients($existingrecipients);

        $result = $selector->find_users('');

        // Should return only user3, excluding user1 and user2.
        $this->assertIsArray($result);
        $this->assertArrayHasKey(get_string('potentialrecipients', 'badges'), $result);

        $recipients = $result[get_string('potentialrecipients', 'badges')];
        $recipientids = array_keys($recipients);

        $this->assertContains((int) $env->users[2]->id, $recipientids);
        $this->assertNotContains((int) $env->users[0]->id, $recipientids);
        $this->assertNotContains((int) $env->users[1]->id, $recipientids);
    }

    /**
     * Test find_users method with search filter.
     */
    public function test_find_users_with_search(): void {
        $this->resetAfterTest();

        $env = $this->create_test_environment(0);

        // Create users with specific names.
        $user1 = $this->getDataGenerator()->create_user(['firstname' => 'Alice', 'lastname' => 'Smith']);
        $user2 = $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'lastname' => 'Jones']);

        // Enrol users.
        $this->getDataGenerator()->enrol_user($user1->id, $env->course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $env->course->id, 'student');

        // Assign capabilities.
        $studentrole = $this->get_student_role();
        assign_capability('moodle/badges:earnbadge', CAP_ALLOW, $studentrole->id, $env->context->id);

        $options = $this->create_selector_options($env);
        $selector = new potential_award_selector('potentialrecipients', $options);

        // Test search for Alice.
        $result = $selector->find_users('Alice');
        $this->assertIsArray($result);

        if (!empty($result)) {
            $recipients = $result[get_string('potentialrecipients', 'badges')];
            $recipientids = array_keys($recipients);
            $this->assertContains((int) $user1->id, $recipientids);
        }
    }

    /**
     * Test find_users method returns empty when no potential recipients.
     */
    public function test_find_users_empty_result(): void {
        $this->resetAfterTest();

        $env = $this->create_test_environment(0);
        $options = $this->create_selector_options($env);

        $selector = new potential_award_selector('potentialrecipients', $options);
        $result = $selector->find_users('');

        // Should return empty array when no potential recipients.
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
