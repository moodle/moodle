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
 * Hook fixture for help icon replacement test.
 *
 * @package   core
 * @category  test
 * @copyright 2026 ISB Bayern
 * @author    Dr. Peter Mayer
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class before_help_icon_rendered_replacement_callbacks {
    /**
     * Replace the help icon output entirely.
     *
     * @param \core\hook\output\before_help_icon_rendered $hook
     */
    public static function replace_help_icon(
        \core\hook\output\before_help_icon_rendered $hook,
    ): void {
        $hook->set_replacement('<span class="custom-replacement">Replaced help icon</span>');
    }
}
