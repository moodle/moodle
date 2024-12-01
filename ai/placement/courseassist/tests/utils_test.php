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

namespace aiplacement_courseassist;

use core_ai\aiactions\generate_text;
use core_ai\aiactions\summarise_text;
use core_ai\manager;

/**
 * AI Placement course assist utils test.
 *
 * @package    aiplacement_courseassist
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiplacement_courseassist\utils
 */
final class utils_test extends \advanced_testcase {

    /**
     * Test is_course_assist_available method.
     */
    public function test_is_course_assist_available(): void {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'manager');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'editingteacher');

        // Provider is not enabled.
        $this->setUser($user1);
        $this->assertFalse(utils::is_course_assist_available($context));

        // Provider is enabled, but plugin is not enabled.
        set_config('enabled', 1, 'aiprovider_openai');
        set_config('apikey', '123', 'aiprovider_openai');
        set_config('enabled', 0, 'aiplacement_courseassist');
        $this->assertFalse(utils::is_course_assist_available($context));

        // Plugin is enabled but user does not have capability.
        assign_capability('aiplacement/courseassist:summarise_text', CAP_PROHIBIT, $teacherrole->id, $context);
        assign_capability('aiplacement/courseassist:explain_text', CAP_PROHIBIT, $teacherrole->id, $context);
        $this->setUser($user2);
        set_config('enabled', 1, 'aiplacement_courseassist');
        $this->assertFalse(utils::is_course_assist_available($context));

        // Plugin is enabled, user has capability and placement action is not available.
        $this->setUser($user1);
        set_config('summarise_text', 0, 'aiplacement_courseassist');
        set_config('explain_text', 0, 'aiplacement_courseassist');
        $this->assertFalse(utils::is_course_assist_available($context));

        // Plugin is enabled, user has capability and provider action is not available.
        $this->setUser($user1);
        set_config('summarise_text', 0, 'aiprovider_openai');
        set_config('summarise_text', 1, 'aiplacement_courseassist');
        set_config('explain_text', 0, 'aiprovider_openai');
        set_config('explain_text', 1, 'aiplacement_courseassist');
        $this->assertFalse(utils::is_course_assist_available($context));

        // Plugin is enabled, user has capability, placement action is available and provider action is available.
        $mockmanager = $this->createMock(manager::class);
        $mockmanager->method('is_action_available')->willReturn(true);
        $mockmanager->method('is_action_enabled')->willReturn(true);
        $mockmanager->method('get_providers_for_actions')->willReturn([
            summarise_text::class => ['aiprovider_openai'],
        ]);

        \core\di::set(manager::class, function() use ($mockmanager) {
            return $mockmanager;
        });

        $this->setUser($user1);
        set_config('summarise_text', 1, 'aiplacement_courseassist');
        set_config('explain_text', 1, 'aiprovider_openai');
        set_config('explain_text', 1, 'aiplacement_courseassist');
        $this->assertTrue(utils::is_course_assist_available($context));
    }

    /**
     * Test get_actions_available method.
     */
    public function test_get_actions_available(): void {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'manager');
        $this->setUser($user1);

        // Two actions enabled.
        set_config('enabled', 1, 'aiprovider_openai');
        set_config('apikey', '123', 'aiprovider_openai');
        set_config('explain_text', 1, 'aiplacement_courseassist');
        set_config('summarise_text', 1, 'aiplacement_courseassist');
        $manager = \core\di::get(manager::class);
        $manager->create_provider_instance(
            classname: '\aiprovider_openai\provider',
            name: 'dummy',
            enabled: true,
            config: ['apikey' => '123'],
        );
        $this->assertCount(2, utils::get_actions_available($context));

        // One action enabled.
        set_config('summarise_text', 0, 'aiplacement_courseassist');
        $this->assertCount(1, utils::get_actions_available($context));

        // No actions enabled.
        set_config('explain_text', 0, 'aiplacement_courseassist');
        $this->assertCount(0, utils::get_actions_available($context));
    }
}
