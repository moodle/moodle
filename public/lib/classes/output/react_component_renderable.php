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

namespace core\output;

/**
 * A renderable object which is rendered as a React component.
 *
 * @package    core
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface react_component_renderable {
    /**
     * Get the name of the React component to use for this renderable.
     *
     * The name will be prefixed with the `@moodle/lms/` namespace,
     * so the component name should be the path to the component within the
     * `lms` package.
     *
     * @return string
     */
    public function get_react_component_name(): string;

    /**
     * Get the props to pass to the React component for this renderable.
     *
     * @param renderer_base $renderer The renderer requesting the props
     * @return \stdClass
     */
    public function get_react_component_props(
        renderer_base $renderer,
    ): \stdClass;
}
