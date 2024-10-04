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

declare(strict_types=1);

namespace core_reportbuilder\reportbuilder\audience;

use context_system;
use core_reportbuilder\local\audiences\base;
use core_reportbuilder\local\helpers\database;
use MoodleQuickForm;

/**
 * The backend class for Has system role audience type
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class systemrole extends base {

    /**
     * Adds audience's elements to the given mform
     *
     * @param MoodleQuickForm $mform The form to add elements to
     */
    public function get_config_form(MoodleQuickForm $mform): void {
        $roles = get_assignable_roles(context_system::instance(), ROLENAME_ALIAS);

        $mform->addElement('autocomplete', 'roles', get_string('selectrole', 'role'), $roles, ['multiple' => true]);
        $mform->addRule('roles', null, 'required', null, 'client');
    }

    /**
     * Helps to build SQL to retrieve users that matches the current audience
     *
     * @param string $usertablealias
     * @return array array of three elements [$join, $where, $params]
     */
    public function get_sql(string $usertablealias): array {
        global $DB;

        $roles = $this->get_configdata()['roles'];
        [$insql, $inparams] = $DB->get_in_or_equal($roles, SQL_PARAMS_NAMED, database::generate_param_name('_'));

        // Ensure parameter names and aliases are unique, as the same audience type can be added multiple times to a report.
        $paramcontextid = database::generate_param_name();
        [$roleassignments, $context] = database::generate_aliases(2);

        $join = "
            JOIN {role_assignments} {$roleassignments} ON {$roleassignments}.userid = {$usertablealias}.id
            JOIN {context} {$context} ON {$context}.id = {$roleassignments}.contextid";

        $where = "{$roleassignments}.contextid = :{$paramcontextid} AND {$roleassignments}.roleid {$insql}";

        return [$join, $where, $inparams + [$paramcontextid => context_system::instance()->id]];
    }

    /**
     * Return user friendly name of this audience type
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('hassystemrole', 'core_reportbuilder');
    }

    /**
     * Return the description for the audience.
     *
     * @return string
     */
    public function get_description(): string {
        global $DB;
        $rolesids = $this->get_configdata()['roles'];
        $roles = $DB->get_records_list('role', 'id', $rolesids, 'sortorder');
        $rolesfixed = role_fix_names($roles, context_system::instance(), ROLENAME_ALIAS, true);
        return $this->format_description_for_multiselect($rolesfixed);
    }

    /**
     * If the current user is able to add this audience.
     *
     * @return bool
     */
    public function user_can_add(): bool {
        // Check if user is able to assign any role from the system context.
        $roles = get_assignable_roles(context_system::instance(), ROLENAME_ALIAS);
        if (empty($roles)) {
            return false;
        }

        return true;
    }

    /**
     * If the current user is able to edit this audience.
     *
     * @return bool
     */
    public function user_can_edit(): bool {
        global $DB;

        // Check if user can assign all saved role types on this audience instance.
        $roleids = $this->get_configdata()['roles'];
        $roles = get_assignable_roles(context_system::instance(), ROLENAME_ALIAS);

        // Check that all saved roles still exist.
        [$insql, $inparams] = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
        $records = $DB->get_records_select('role', "id $insql", $inparams);

        if (!empty(array_diff(array_keys($records), array_keys($roles)))) {
            return false;
        }

        return true;
    }
}
