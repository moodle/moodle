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

namespace core\tests\output;

/**
 * Fixture renderable implementing react_component_renderable, for use in renderer_base_test.
 *
 * @package   core
 * @category  test
 * @copyright 2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class react_component_renderable_fixture implements \core\output\react_component_renderable, \core\output\renderable {
    #[\Override]
    public function get_react_component_name(): string {
        return 'core/example';
    }

    #[\Override]
    public function get_react_component_props(
        \core\output\renderer_base $renderer,
    ): \stdClass {
        $props = new \stdClass();
        $props->foo = 'bar';
        return $props;
    }
}
