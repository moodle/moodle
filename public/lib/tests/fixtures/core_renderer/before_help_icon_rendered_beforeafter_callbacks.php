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
 * Hook fixture for help icon before/after test.
 *
 * @package   core
 * @category  test
 * @copyright 2026 ISB Bayern
 * @author    Dr. Peter Mayer
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class before_help_icon_rendered_beforeafter_callbacks {
    /**
     * Add content before and after the help icon.
     *
     * @param \core\hook\output\before_help_icon_rendered $hook
     */
    public static function add_before_and_after(
        \core\hook\output\before_help_icon_rendered $hook,
    ): void {
        $hook->add_before_icon('<span class="before-help-icon">Before</span>');
        $hook->add_after_icon('<span class="after-help-icon">After</span>');
    }
}
