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

namespace factor_role;

use tool_mfa\local\factor\object_factor_base;

/**
 * Role factor class.
 *
 * @package     factor_role
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * Role implementation.
     * This factor is a singleton, return single instance.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors($user) {
        global $DB;
        $records = $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $user->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * Role implementation.
     * Factor has no input
     *
     * {@inheritDoc}
     */
    public function has_input() {
        return false;
    }

    /**
     * Role implementation.
     * Checks whether the user has selected roles in any context.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        global $USER;
        $rolestring = get_config('factor_role', 'roles');

        // Nothing selected, everyone passes.
        if (empty($rolestring)) {
            return \tool_mfa\plugininfo\factor::STATE_PASS;
        }

        $selected = explode(',', $rolestring);
        $syscon = \context_system::instance();
        $specials = get_user_roles_with_special($syscon, $USER->id);
        // Transform the special roles to the matching format.
        $specials = array_map(function ($el) {
            return $el->roleid;
        }, $specials);

        foreach ($selected as $id) {
            if ($id === 'admin') {
                if (is_siteadmin()) {
                    return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
                }
            } else {
                if (user_has_role_assignment($USER->id, $id)) {
                    return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
                }

                // Some system default roles do not have an explicit binding. eg Authenticated user.
                if (in_array((int) $id, $specials)) {
                    return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
                }
            }
        }

        // If we got here, no roles matched, allow access.
        return \tool_mfa\plugininfo\factor::STATE_PASS;
    }

    /**
     * Role implementation.
     * Cannot set state, return true.
     *
     * @param mixed $state the state constant to set
     * @return bool
     */
    public function set_state($state) {
        return true;
    }

    /**
     * Role implementation.
     * User can not influence. Result is whatever current state is.
     *
     * @param \stdClass $user
     */
    public function possible_states($user) {
        return [$this->get_state()];
    }

    /**
     * Role implementation
     * Formats the role list nicely.
     *
     * {@inheritDoc}
     */
    public function get_summary_condition() {
        global $DB;

        $selectedroles = get_config('factor_role', 'roles');
        if (empty($selectedroles)) {
            return get_string('summarycondition', 'factor_role', get_string('none'));
        } else {
            $selectedroles = explode(',', $selectedroles);
        }

        $names = [];
        foreach ($selectedroles as $role) {
            if ($role === 'admin') {
                $names[] = get_string('administrator');
            } else {
                $record = $DB->get_record('role', ['id' => $role]);
                $names[] = role_get_name($record);
            }
        }

        $string = implode(', ', $names);
        return get_string('summarycondition', 'factor_role', $string);
    }
}
