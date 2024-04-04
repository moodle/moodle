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

namespace test_fixtures\core_renderer;

/**
 * Hook fixture for \core_renderer::htmlattributes.
 *
 * @package   core
 * @category  test
 * @copyright 2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class htmlattributes {
    /**
     * Fixture for adding a data attribute to the HTML element.
     *
     * @param \core\hook\output\before_html_attributes $hook
     */
    public static function before_html_attributes(\core\hook\output\before_html_attributes $hook): void {
        $hook->add_attribute('data-test', 'test');
    }
}
