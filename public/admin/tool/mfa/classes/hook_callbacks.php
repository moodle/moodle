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

namespace tool_mfa;

use core\hook\after_config;

/**
 * Callbacks for hooks.
 *
 * @package    tool_mfa
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Listener for the after_config hook.
     *
     * @param after_config $hook
     */
    public static function after_config(\core\hook\after_config $hook): void {
        global $CFG, $SESSION;

        if (during_initial_install() || isset($CFG->upgraderunning)) {
            // Do nothing during installation or upgrade.
            return;
        }

        // Tests for hooks being fired to test patches.
        // Store in $CFG, $SESSION not present at this point.
        if (PHPUNIT_TEST) {
            $CFG->mfa_config_hook_test = true;
        }

        // Check for not logged in.
        if (isloggedin() && !isguestuser()) {
            // If not authenticated, force login required.
            if (empty($SESSION->tool_mfa_authenticated)) {
                \tool_mfa\manager::require_auth();
            }
        }
    }
}
