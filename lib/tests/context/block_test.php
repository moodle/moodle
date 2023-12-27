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
 * Unit tests for block context class.
 *
 * NOTE: more tests are in lib/tests/accesslib_test.php
 *
 * @package   core
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\context\block
 */
class block_test extends \advanced_testcase {
    /**
     * Tests legacy class name.
     * @covers \context_block
     */
    public function test_legacy_classname() {
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $this->assertInstanceOf(block::class, $context);
        $this->assertInstanceOf(\context_block::class, $context);
    }

    /**
     * Tests covered methods.
     * @covers ::instance
     * @covers \core\context::instance_by_id
     */
    public function test_factory_methods() {
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $this->assertInstanceOf(block::class, $context);
        $this->assertSame((string)$block->id, $context->instanceid);

        $context = context::instance_by_id($context->id);
        $this->assertInstanceOf(block::class, $context);
        $this->assertSame((string)$block->id, $context->instanceid);
    }

    /**
     * Tests covered method.
     * @covers ::get_short_name
     */
    public function test_get_short_name() {
        $this->assertSame('block', block::get_short_name());
    }

    /**
     * Tests levels.
     * @coversNothing
     */
    public function test_level() {
        $this->assertSame(80, block::LEVEL);
        $this->assertSame(CONTEXT_BLOCK, block::LEVEL);
    }

    /**
     * Tests covered method.
     * @covers ::get_level_name
     */
    public function test_get_level_name() {
        $this->assertSame('Block', block::get_level_name());
    }

    /**
     * Tests covered method.
     * @covers ::get_context_name
     */
    public function test_get_context_name() {
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $this->assertSame('Block: Online users', $context->get_context_name());
        $this->assertSame('Block: Online users', $context->get_context_name(true));
        $this->assertSame('Online users', $context->get_context_name(false));
        $this->assertSame('Online users', $context->get_context_name(false, true));
        $this->assertSame('Block: Online users', $context->get_context_name(true, true, false));
    }

    /**
     * Tests covered method.
     * @covers ::get_url
     */
    public function test_get_url() {
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $expected = new \moodle_url('/');
        $url = $context->get_url();
        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertSame($expected->out(), $url->out());
    }

    /**
     * Tests covered method.
     * @covers ::get_compatible_role_archetypes
     */
    public function test_get_compatible_role_archetypes() {
        global $DB;

        $allarchetypes = $DB->get_fieldset_select('role', 'DISTINCT archetype', 'archetype IS NOT NULL');
        foreach ($allarchetypes as $allarchetype) {
            $levels = context_helper::get_compatible_levels($allarchetype);
            $this->assertNotContains(block::LEVEL, $levels, "$allarchetype is not expected to be compatible with context");
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_possible_parent_levels
     */
    public function test_get_possible_parent_levels() {
        $result = block::get_possible_parent_levels();
        // All except itself.
        $this->assertContains(system::LEVEL, $result);
        $this->assertContains(user::LEVEL, $result);
        $this->assertContains(coursecat::LEVEL, $result);
        $this->assertContains(course::LEVEL, $result);
        $this->assertContains(module::LEVEL, $result);
        $this->assertNotContains(block::LEVEL, $result);

        // Make sure plugin contexts are covered too.
        $all = \core\context_helper::get_all_levels();
        $this->assertCount(count($all) - 1, $result);
    }

    /**
     * Tests covered method.
     * @covers ::get_capabilities
     */
    public function test_get_capabilities() {
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $capabilities = $context->get_capabilities();
        $capabilities = convert_to_array($capabilities);
        $capabilities = array_column($capabilities, 'name');
        $this->assertNotContains('moodle/site:config', $capabilities);
        $this->assertNotContains('moodle/course:view', $capabilities);
        $this->assertNotContains('moodle/category:manage', $capabilities);
        $this->assertNotContains('moodle/user:viewalldetails', $capabilities);
        $this->assertNotContains('mod/page:view', $capabilities);
        $this->assertNotContains('mod/url:view', $capabilities);
    }

    /**
     * Tests covered method.
     * @covers ::create_level_instances
     */
    public function test_create_level_instances() {
        global $DB;
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $DB->delete_records('context', ['id' => $context->id]);
        context_helper::create_instances(block::LEVEL);
        $record = $DB->get_record('context', ['contextlevel' => block::LEVEL, 'instanceid' => $block->id], '*', MUST_EXIST);
    }

    /**
     * Tests covered method.
     * @covers ::get_child_contexts
     */
    public function test_get_child_contexts() {
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $children = $context->get_child_contexts();
        $this->assertCount(0, $children);
    }

    /**
     * Tests covered method.
     * @covers ::get_cleanup_sql
     */
    public function test_get_cleanup_sql() {
        global $DB;
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $DB->delete_records('block_instances', ['id' => $block->id]);

        context_helper::cleanup_instances();
        $this->assertFalse($DB->record_exists('context', ['contextlevel' => block::LEVEL, 'instanceid' => $block->id]));
    }

    /**
     * Tests covered method.
     * @covers ::build_paths
     */
    public function test_build_paths() {
        global $DB;
        $this->resetAfterTest();

        $syscontext = system::instance();
        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $DB->set_field('context', 'depth', 1, ['id' => $context->id]);
        $DB->set_field('context', 'path', '/0', ['id' => $context->id]);

        context_helper::build_all_paths(true);

        $record = $DB->get_record('context', ['id' => $context->id]);
        $this->assertSame('2', $record->depth);
        $this->assertSame('/' . $syscontext->id . '/' . $record->id, $record->path);
    }

    /**
     * Tests covered method.
     * @covers ::set_locked
     */
    public function test_set_locked() {
        global $DB;
        $this->resetAfterTest();

        $block = $this->getDataGenerator()->create_block('online_users');
        $context = block::instance($block->id);

        $context->set_locked(true);
        $context = block::instance($block->id);
        $this->assertTrue($context->locked);
        $record = $DB->get_record('context', ['id' => $context->id]);
        $this->assertSame('1', $record->locked);

        $context->set_locked(false);
        $context = block::instance($block->id);
        $this->assertFalse($context->locked);
        $record = $DB->get_record('context', ['id' => $context->id]);
        $this->assertSame('0', $record->locked);
    }
}
