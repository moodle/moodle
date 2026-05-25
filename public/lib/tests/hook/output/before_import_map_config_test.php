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

namespace core\hook\output;

use core\output\requirements\import_map;

/**
 * Tests for the before_import_map_config hook.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(before_import_map_config::class)]
final class before_import_map_config_test extends \advanced_testcase {
    /**
     * Helper: create a hook wrapping a fresh import_map and a default loader.
     *
     * @return array{before_import_map_config, import_map}
     */
    private function create_hook_and_map(): array {
        $map = \core\di::get(import_map::class);
        $map->set_default_loader(new \core\url('https://example.com/esm/1/'));
        $hook = new before_import_map_config($map);
        return [$hook, $map];
    }

    /**
     * Test that add_import() registers a specifier resolved by the default loader.
     */
    public function test_add_import_default_loader(): void {
        [$hook, $map] = $this->create_hook_and_map();

        $hook->add_import('my-lib');

        $data = $map->jsonSerialize();
        $this->assertArrayHasKey('my-lib', $data['imports']);
        $this->assertEquals('https://example.com/esm/1/my-lib', $data['imports']['my-lib']);
    }

    /**
     * Test that add_import() with an explicit \core\url uses that URL verbatim.
     */
    public function test_add_import_explicit_loader(): void {
        [$hook, $map] = $this->create_hook_and_map();

        $hook->add_import(
            specifier: 'my-external-lib',
            loader: new \core\url('https://cdn.example.com/my-external-lib/index.js'),
        );

        $data = $map->jsonSerialize();
        $this->assertArrayHasKey('my-external-lib', $data['imports']);
        $this->assertEquals(
            'https://cdn.example.com/my-external-lib/index.js',
            $data['imports']['my-external-lib'],
        );
    }

    /**
     * Test that multiple add_import() calls each register their specifier.
     */
    public function test_add_import_multiple_specifiers(): void {
        [$hook, $map] = $this->create_hook_and_map();

        $hook->add_import('lib-a');
        $hook->add_import('lib-b');
        $hook->add_import('lib-c');

        $data = $map->jsonSerialize();
        $this->assertArrayHasKey('lib-a', $data['imports']);
        $this->assertArrayHasKey('lib-b', $data['imports']);
        $this->assertArrayHasKey('lib-c', $data['imports']);
    }

    /**
     * Test that add_import() with a urlsuffix appends it to the resolved URL.
     */
    public function test_add_import_url_suffix(): void {
        [$hook, $map] = $this->create_hook_and_map();

        $hook->add_import(
            specifier: 'my-pkg/',
            urlsuffix: '/index.js',
        );

        $data = $map->jsonSerialize();
        $this->assertArrayHasKey('my-pkg/', $data['imports']);
        $this->assertEquals('https://example.com/esm/1/my-pkg//index.js', $data['imports']['my-pkg/']);
    }

    /**
     * Test that add_import() can override a standard specifier already registered by import_map.
     */
    public function test_add_import_overrides_existing_specifier(): void {
        [$hook, $map] = $this->create_hook_and_map();

        $customloader = new \core\url('https://cdn.example.com/react/custom.js');
        $hook->add_import(specifier: 'react', loader: $customloader);

        $data = $map->jsonSerialize();
        $this->assertEquals('https://cdn.example.com/react/custom.js', $data['imports']['react']);
    }

    /**
     * Test that standard specifiers are unaffected when add_import() is not called.
     */
    public function test_standard_imports_preserved_without_hook_calls(): void {
        [$hook, $map] = $this->create_hook_and_map();

        // No add_import() calls on the hook.

        $data = $map->jsonSerialize();
        $this->assertArrayHasKey('react', $data['imports']);
        $this->assertArrayHasKey('react/', $data['imports']);
        $this->assertArrayHasKey('@moodle/lms/', $data['imports']);
    }
}
