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

namespace tool_mfa\local\hooks;

/**
 * Extend user bulk actions menu
 *
 * @package    tool_mfa
 * @copyright  2024 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extend_bulk_user_actions {

    /**
     * Add action to reset MFA factors
     *
     * @param \core_user\hook\extend_bulk_user_actions $hook
     */
    public static function callback(\core_user\hook\extend_bulk_user_actions $hook): void {
        if (has_capability('moodle/site:config', \context_system::instance())) {
            $hook->add_action('tool_mfa_reset_factors', new \action_link(
                new \moodle_url('/admin/tool/mfa/reset_factor.php'),
                get_string('resetfactor', 'tool_mfa')
            ));
        }
    }
}
