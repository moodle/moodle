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
 * Parent class for tests which need data privacy functionality.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Parent class for tests which need data privacy functionality.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class data_privacy_testcase extends advanced_testcase {

    /**
     * Assign one or more user IDs as site DPO
     *
     * @param stdClass|array $users User ID or array of user IDs to be assigned as site DPO
     * @return void
     */
    protected function assign_site_dpo($users) {
        global $DB;
        $this->resetAfterTest();

        if (!is_array($users)) {
            $users = array($users);
        }

        $context = context_system::instance();

        // Give the manager role with the capability to manage data requests.
        $managerroleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        assign_capability('tool/dataprivacy:managedatarequests', CAP_ALLOW, $managerroleid, $context->id, true);

        // Assign user(s) as manager.
        foreach ($users as $user) {
            role_assign($managerroleid, $user->id, $context->id);
        }

        // Only map the manager role to the DPO role.
        set_config('dporoles', $managerroleid, 'tool_dataprivacy');
    }
}
