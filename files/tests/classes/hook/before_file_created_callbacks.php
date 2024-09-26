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

namespace core_files\tests\hook;

/**
 * Helper class for before_file_created hooks.
 *
 * @package    core_files
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_file_created_callbacks {
    /**
     * Before file created hook callback for testing.
     *
     * @param \core_files\hook\before_file_created $hook
     */
    public static function before_file_created(\core_files\hook\before_file_created $hook): void {
        // phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameLowerCase
        global $TESTCALLBACK;

        if (!isset($TESTCALLBACK)) {
            return;
        }
        call_user_func($TESTCALLBACK, $hook);
    }
}
