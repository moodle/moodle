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

/**
 * One Roster Enrolment plugin.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster;

use admin_setting_configselect;
use part_of_admin_tree;

/**
 * One Roster Enrolment plugin settings helper.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings {
    /**
     * Add a role mapping setting.
     *
     * @param   part_of_admin_tree $settings
     * @param   string $shortname The name of the One Roster role
     * @param   array $allroles A mapping of roleid => shortname for all possible roles
     * @param   array $roles A mapping of roleid => display name for relevant roles
     * @param   null|string $default The default value
     */
    public static function add_role_mapping(
        part_of_admin_tree $settings,
        string $shortname,
        array $allroles,
        array $roles,
        $default = 0
    ): void {
        $settings->add(new admin_setting_configselect(
            "enrol_oneroster/role_mapping_{$shortname}",
            get_string("settings_rolemapping_{$shortname}", 'enrol_oneroster'),
            get_string("settings_rolemapping_{$shortname}_desc", 'enrol_oneroster'),
            array_search($default, $allroles),
            $roles
        ));
    }
}
