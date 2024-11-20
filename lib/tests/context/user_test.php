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

namespace core\context;

use core\context, core\context_helper;

/**
 * Unit tests for user context class.
 *
 * NOTE: more tests are in lib/tests/accesslib_test.php
 *
 * @package   core
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\context\user
 */
class user_test extends \advanced_testcase {
    /**
     * Tests legacy class.
     * @coversNothing
     */
    public function test_legacy_classname(): void {
        $admin = get_admin();
        $context = \context_user::instance($admin->id);
        $this->assertInstanceOf(user::class, $context);
        $this->assertInstanceOf(\context_user::class, $context);
    }

    /**
     * Tests covered methods.
     * @covers ::instance
     * @covers \core\context::instance_by_id
     */
    public function test_factory_methods(): void {
        $admin = get_admin();
        $context = user::instance($admin->id);
        $this->assertInstanceOf(user::class, $context);
        $this->assertSame($admin->id, $context->instanceid);

        $context = context::instance_by_id($context->id);
        $this->assertInstanceOf(user::class, $context);
        $this->assertSame($admin->id, $context->instanceid);
    }

    /**
     * Tests covered method.
     * @covers ::get_short_name
     */
    public function test_get_short_name(): void {
        $this->assertSame('user', user::get_short_name());
    }

    /**
     * Tests context level.
     * @coversNothing
     */
    public function test_level(): void {
        $this->assertSame(30, user::LEVEL);
        $this->assertSame(CONTEXT_USER, user::LEVEL);
    }

    /**
     * Tests covered method.
     * @covers ::get_level_name
     */
    public function test_get_level_name(): void {
        $this->assertSame('User', user::get_level_name());
    }

    /**
     * Tests covered method.
     * @covers ::get_context_name
     */
    public function test_get_context_name(): void {
        $admin = get_admin();
        $context = user::instance($admin->id);
        $this->assertSame('User: Admin User', $context->get_context_name());
        $this->assertSame('User: Admin User', $context->get_context_name(true));
        $this->assertSame('Admin User', $context->get_context_name(false));
        $this->assertSame('Admin User', $context->get_context_name(false, true));
        $this->assertSame('User: Admin User', $context->get_context_name(true, true, false));
    }

    /**
     * Tests covered method.
     * @covers ::get_url
     */
    public function test_get_url(): void {
        $admin = get_admin();
        $context = user::instance($admin->id);
        $expected = new \moodle_url('/user/profile.php', ['id' => $admin->id]);
        $url = $context->get_url();
        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertSame($expected->out(), $url->out());
    }

    /**
     * Tests covered methods.
     * @covers ::get_instance_table()
     * @covers ::get_behat_reference_columns()
     * @covers \core\context_helper::resolve_behat_reference
     */
    public function test_resolve_behat_reference(): void {
        $this->resetAfterTest();

        $instance = $this->getDataGenerator()->create_user();
        $context = context\user::instance($instance->id);

        $result = context_helper::resolve_behat_reference('User', $instance->username);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('user', $instance->username);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('30', $instance->username);
        $this->assertSame($context->id, $result->id);

        $result = context_helper::resolve_behat_reference('User', 'dshjkdshjkhjsadjhdsa');
        $this->assertNull($result);

        $result = context_helper::resolve_behat_reference('User', '');
        $this->assertNull($result);
    }

    /**
     * Tests covered method.
     * @covers ::get_compatible_role_archetypes
     */
    public function test_get_compatible_role_archetypes(): void {
        global $DB;

        $allarchetypes = $DB->get_fieldset_select('role', 'DISTINCT archetype', 'archetype IS NOT NULL');
        foreach ($allarchetypes as $allarchetype) {
            $levels = context_helper::get_compatible_levels($allarchetype);
            $this->assertNotContains(user::LEVEL, $levels, "$allarchetype is not expected to be compatible with context");
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_possible_parent_levels
     */
    public function test_get_possible_parent_levels(): void {
        $this->assertSame([system::LEVEL], user::get_possible_parent_levels());
    }

    /**
     * Tests covered method.
     * @covers ::get_capabilities
     */
    public function test_get_capabilities(): void {
        $admin = get_admin();

        $context = user::instance($admin->id);
        $capabilities = $context->get_capabilities();
        $capabilities = convert_to_array($capabilities);
        $capabilities = array_column($capabilities, 'name');

        $this->assertContains('moodle/user:viewalldetails', $capabilities);
        $this->assertContains('moodle/grade:viewall', $capabilities);
        $this->assertNotContains('moodle/course:view', $capabilities);
        $this->assertNotContains('moodle/site:config', $capabilities);
    }

    /**
     * Tests covered method.
     * @covers ::create_level_instances
     */
    public function test_create_level_instances(): void {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = user::instance($user->id);

        $DB->delete_records('context', ['id' => $usercontext->id]);
        context_helper::create_instances(user::LEVEL);
        $record = $DB->get_record('context', ['contextlevel' => user::LEVEL, 'instanceid' => $user->id], '*', MUST_EXIST);
    }

    /**
     * Tests covered method.
     * @covers ::get_child_contexts
     */
    public function test_get_child_contexts(): void {
        $admin = get_admin();

        $context = user::instance($admin->id);
        $children = $context->get_child_contexts();
        $this->assertCount(0, $children);
    }

    /**
     * Tests covered method.
     * @covers ::get_cleanup_sql
     */
    public function test_get_cleanup_sql(): void {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = user::instance($user->id);

        $DB->set_field('user', 'deleted', 1, ['id' => $user->id]);

        context_helper::cleanup_instances();
        $this->assertFalse($DB->record_exists('context', ['contextlevel' => user::LEVEL, 'instanceid' => $user->id]));
    }

    /**
     * Tests covered method.
     * @covers ::build_paths
     */
    public function test_build_paths(): void {
        global $DB;
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = user::instance($user->id);
        $syscontext = system::instance();

        $DB->set_field('context', 'depth', 1, ['id' => $usercontext->id]);
        $DB->set_field('context', 'path', '/0', ['id' => $usercontext->id]);

        context_helper::build_all_paths(true);

        $record = $DB->get_record('context', ['id' => $usercontext->id]);
        $this->assertSame('2', $record->depth);
        $this->assertSame('/' . $syscontext->id . '/' . $record->id, $record->path);
    }

    /**
     * Tests covered method.
     * @covers ::set_locked
     */
    public function test_set_locked(): void {
        global $DB;
        $this->resetAfterTest();

        $admin = get_admin();
        $context = user::instance($admin->id);
        $this->assertFalse($context->locked);

        $context->set_locked(true);
        $this->assertTrue($context->locked);
        $record = $DB->get_record('context', ['id' => $context->id]);
        $this->assertSame('1', $record->locked);

        $context->set_locked(false);
        $this->assertFalse($context->locked);
        $record = $DB->get_record('context', ['id' => $context->id]);
        $this->assertSame('0', $record->locked);
    }
}
