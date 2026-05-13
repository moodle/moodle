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

namespace core\hook\output;

/**
 * Tests for before_requirejs_config hook.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(before_requirejs_config::class)]
final class before_requirejs_config_test extends \advanced_testcase {
    /**
     * Test that a new hook instance has an empty map.
     */
    public function test_empty_map_by_default(): void {
        $hook = new before_requirejs_config();

        $this->assertSame([], $hook->get_requirejs_map());
    }

    /**
     * Test adding a single map entry with default source.
     */
    public function test_add_requirejs_esm_map_entry_default_source(): void {
        $hook = new before_requirejs_config();

        $hook->add_requirejs_esm_map_entry('core/old_module', 'core/new_module');

        $expected = [
            '*' => [
                'core/old_module' => 'core/new_module',
            ],
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }

    /**
     * Test adding a single map entry with a custom source.
     */
    public function test_add_requirejs_esm_map_entry_custom_source(): void {
        $hook = new before_requirejs_config();

        $hook->add_requirejs_esm_map_entry('core/old_module', 'core/new_module', 'mod_forum/post');

        $expected = [
            'mod_forum/post' => [
                'core/old_module' => 'core/new_module',
            ],
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }

    /**
     * Test adding multiple entries to the same source.
     */
    public function test_add_multiple_entries_same_source(): void {
        $hook = new before_requirejs_config();

        $hook->add_requirejs_esm_map_entry('core/module_a', 'core/replacement_a');
        $hook->add_requirejs_esm_map_entry('core/module_b', 'core/replacement_b');

        $expected = [
            '*' => [
                'core/module_a' => 'core/replacement_a',
                'core/module_b' => 'core/replacement_b',
            ],
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }

    /**
     * Test adding entries to different sources.
     */
    public function test_add_entries_different_sources(): void {
        $hook = new before_requirejs_config();

        $hook->add_requirejs_esm_map_entry('core/module_a', 'core/replacement_a', '*');
        $hook->add_requirejs_esm_map_entry('core/module_b', 'core/replacement_b', 'mod_forum/post');

        $expected = [
            '*' => [
                'core/module_a' => 'core/replacement_a',
            ],
            'mod_forum/post' => [
                'core/module_b' => 'core/replacement_b',
            ],
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }

    /**
     * Test that adding an entry with the same key overwrites the previous value.
     */
    public function test_add_entry_overwrites_existing_key(): void {
        $hook = new before_requirejs_config();

        $hook->add_requirejs_esm_map_entry('core/module_a', 'core/first_value');
        $hook->add_requirejs_esm_map_entry('core/module_a', 'core/second_value');

        $expected = [
            '*' => [
                'core/module_a' => 'core/second_value',
            ],
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }

    /**
     * Test adding multiple entries at once with default source.
     */
    public function test_add_requirejs_esm_map_entries_default_source(): void {
        $hook = new before_requirejs_config();

        $entries = [
            'core/module_a' => 'core/replacement_a',
            'core/module_b' => 'core/replacement_b',
            'core/module_c' => 'core/replacement_c',
        ];
        $hook->add_requirejs_esm_map_entries($entries);

        $expected = [
            '*' => $entries,
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }

    /**
     * Test adding multiple entries at once with a custom source.
     */
    public function test_add_requirejs_esm_map_entries_custom_source(): void {
        $hook = new before_requirejs_config();

        $entries = [
            'core/module_a' => 'core/replacement_a',
            'core/module_b' => 'core/replacement_b',
        ];
        $hook->add_requirejs_esm_map_entries($entries, 'mod_assign/grading');

        $expected = [
            'mod_assign/grading' => $entries,
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }

    /**
     * Test combining add_requirejs_esm_map_entry and add_requirejs_esm_map_entries.
     */
    public function test_combined_entry_and_entries(): void {
        $hook = new before_requirejs_config();

        $hook->add_requirejs_esm_map_entry('core/single', 'core/single_replacement');
        $hook->add_requirejs_esm_map_entries([
            'core/batch_a' => 'core/batch_replacement_a',
            'core/batch_b' => 'core/batch_replacement_b',
        ]);

        $expected = [
            '*' => [
                'core/single' => 'core/single_replacement',
                'core/batch_a' => 'core/batch_replacement_a',
                'core/batch_b' => 'core/batch_replacement_b',
            ],
        ];
        $this->assertSame($expected, $hook->get_requirejs_map());
    }
}
