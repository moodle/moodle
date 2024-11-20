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
 * Unit tests for system context class.
 *
 * NOTE: more tests are in lib/tests/accesslib_test.php
 *
 * @package   core
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\context\system
 */
class system_test extends \advanced_testcase {
    /**
     * Tests legacy class.
     * @coversNothing
     */
    public function test_legacy_classname(): void {
        $context = \context_system::instance();
        $this->assertInstanceOf(system::class, $context);
        $this->assertInstanceOf(\context_system::class, $context);
    }

    /**
     * Tests covered methods.
     * @covers ::instance
     * @covers \core\context::instance_by_id
     */
    public function test_factory_methods(): void {
        $context = system::instance();
        $this->assertInstanceOf(system::class, $context);
        $this->assertEquals(SYSCONTEXTID, $context->id);

        $context = context::instance_by_id($context->id);
        $this->assertInstanceOf(system::class, $context);
        $this->assertEquals(SYSCONTEXTID, $context->id);
    }

    /**
     * Tests covered method.
     * @covers ::get_short_name
     */
    public function test_get_short_name(): void {
        $this->assertSame('system', system::get_short_name());
    }

    /**
     * Tests context level.
     * @coversNothing
     */
    public function test_level(): void {
        $this->assertSame(10, system::LEVEL);
        $this->assertSame(CONTEXT_SYSTEM, system::LEVEL);
    }

    /**
     * Tests covered method.
     * @covers ::get_level_name
     */
    public function test_get_level_name(): void {
        $this->assertSame('System', system::get_level_name());
    }

    /**
     * Tests covered method.
     * @covers ::get_context_name
     */
    public function test_get_context_name(): void {
        $context = system::instance();
        $this->assertSame('System', $context->get_context_name());
        $this->assertSame('System', $context->get_context_name(true));
        $this->assertSame('System', $context->get_context_name(false));
        $this->assertSame('System', $context->get_context_name(false, true));
        $this->assertSame('System', $context->get_context_name(true, true, false));
    }

    /**
     * Tests covered method.
     * @covers ::get_url
     */
    public function test_get_url(): void {
        $context = system::instance();
        $expected = new \moodle_url('/');
        $url = $context->get_url();
        $this->assertInstanceOf(\moodle_url::class, $url);
        $this->assertSame($expected->out(), $url->out());
    }

    /**
     * Tests covered method.
     * @covers \core\context_helper::resolve_behat_reference
     */
    public function test_resolve_behat_reference(): void {
        $syscontext = context\system::instance();

        $result = context_helper::resolve_behat_reference('System', '');
        $this->assertSame($syscontext->id, $result->id);

        $result = context_helper::resolve_behat_reference('System', '44');
        $this->assertSame($syscontext->id, $result->id);

        $result = context_helper::resolve_behat_reference('system', '');
        $this->assertSame($syscontext->id, $result->id);

        $result = context_helper::resolve_behat_reference('10', '');
        $this->assertSame($syscontext->id, $result->id);
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
            if ($allarchetype === 'manager' || $allarchetype === 'coursecreator') {
                $this->assertContains(system::LEVEL, $levels, "$allarchetype is expected to be compatible with context");
            } else {
                $this->assertNotContains(system::LEVEL, $levels, "$allarchetype is not expected to be compatible with context");
            }
        }
    }

    /**
     * Tests covered method.
     * @covers ::get_possible_parent_levels
     */
    public function test_get_possible_parent_levels(): void {
        $this->assertSame([], system::get_possible_parent_levels());
    }

    /**
     * Tests covered method.
     * @covers ::get_capabilities
     */
    public function test_get_capabilities(): void {
        global $DB;

        $context = system::instance();
        $capabilities = $context->get_capabilities();
        $expected = $DB->count_records('capabilities', []);
        $this->assertCount($expected, $capabilities);
    }

    /**
     * Tests covered method.
     * @covers ::create_level_instances
     */
    public function test_create_level_instances(): void {
        context_helper::create_instances(system::LEVEL);
    }

    /**
     * Tests covered method.
     * @covers ::get_child_contexts
     */
    public function test_get_child_contexts(): void {
        global $DB;

        $context = system::instance();
        $children = $context->get_child_contexts();
        $expected = $DB->count_records('context', []) - 1;
        $this->assertCount($expected, $children);
        $this->assertDebuggingCalled('Fetching of system context child courses is strongly '
            . 'discouraged on production servers (it may eat all available memory)!');
    }

    /**
     * Tests covered method.
     * @covers ::get_cleanup_sql
     */
    public function test_get_cleanup_sql(): void {
        // Nothing to clean up actually.
        context_helper::cleanup_instances();
    }

    /**
     * Tests covered method.
     * @covers ::build_paths
     */
    public function test_build_paths(): void {
        global $DB;
        $this->resetAfterTest();

        $DB->set_field('context', 'depth', 2, ['id' => SYSCONTEXTID]);
        $DB->set_field('context', 'path', '/0', ['id' => SYSCONTEXTID]);

        context_helper::build_all_paths(true);

        $record = $DB->get_record('context', ['id' => SYSCONTEXTID]);
        $this->assertSame('1', $record->depth);
        $this->assertSame('/' . $record->id, $record->path);
    }

    /**
     * Tests covered method.
     * @covers ::set_locked
     */
    public function test_set_locked(): void {
        $context = system::instance();

        $context->set_locked(false);

        try {
            $context->set_locked(true);
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf(\coding_exception::class, $e);
            $this->assertSame('Coding error detected, it must be fixed by a programmer: '
                . 'It is not possible to lock the system context', $e->getMessage());
        }
    }
}
