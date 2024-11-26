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
 * Overrides for behat navigation.
 *
 * @package   theme_adaptable
 * @author    Marcus Green
 * @copyright Titus Learning 2020
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException,
    Behat\Mink\Element\NodeElement;

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../lib/tests/behat/behat_permissions.php');

/**
 * Overrides to make behat permissions work with adaptable.
 *
 * @package   theme_adaptable
 * @author    Marcus Green
 * @copyright Titus Learning 2020
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class behat_theme_adaptable_behat_permissions extends behat_permissions {
    /**
     * Set system level permissions to the specified role.  Expects a table with capability name
     * and permission (Inherit/Allow/Prevent/Prohibit) columns.
     * @Given /^I set the following system permissions of "(?P<rolefullname_string>(?:[^"]|\\")*)" role:$/
     * @param string $rolename
     * @param TableNode $table
     */
    public function i_set_the_following_system_permissions_of_role($rolename, $table) {
        global $DB;

        if ($rolename == "Teacher") {
            $rolename = "editingteacher";
        }
        // Find role by name.
        $roleid = $DB->get_field('role', 'id', ['shortname' => strtolower($rolename)], MUST_EXIST);
        // Spawn a new system context instance.
        $systemcontext = context_system::instance();
        /* Add capabilities to role given the table of capabilities.
           Using getRows() as we are not sure if tests writers will add the header. */
        foreach ($table->getRows() as $key => $row) {
            if (count($row) !== 2) {
                $msg = 'You should specify a table with capability/permission columns';
                throw new ExpectationException($msg, $this->getSession());
            }

            [$capability, $permission] = $row;

            // Skip the headers row if it was provided.
            if (strtolower($capability) == 'capability' || strtolower($capability) == 'capabilities') {
                continue;
            }
            // Checking the permission value.
            $permissionconstant = 'CAP_' . strtoupper($permission);
            if (!defined($permissionconstant)) {
                throw new ExpectationException(
                    'The provided permission value "' . $permission . '" is not valid. Use Inherit, Allow, Prevent or Prohibited',
                    $this->getSession()
                );
            }

            // Converting from permission to constant value.
            $permissionvalue = constant($permissionconstant);
            \assign_capability(
                $capability,
                $permissionvalue,
                $roleid,
                $systemcontext->id,
                true
            );
        }
        $systemcontext->mark_dirty();
        accesslib_clear_role_cache($roleid);
    }

    /**
     * Overrides system capabilities at category, course and module levels.
     * This step begins after clicking 'Permissions' link. Expects a table with capability name
     * and permission (Inherit/Allow/Prevent/Prohibit) columns.
     * @param string $rolename
     * @param TableNode $table
     */
    public function i_override_the_system_permissions_of_role_with($rolename, $table) {
        global $DB;
        // On the interface Role Teacher points to editing teacher.
        if ($rolename == "Teacher") {
            $rolename = "editingteacher";
        }
        // Find role by name.
        $roleid = $DB->get_field('role', 'id', ['shortname' => strtolower($rolename)], MUST_EXIST);
        // Spawn a new system context instance.
        $systemcontext = context_system::instance();
        /* Add capabilities to role given the table of capabilities.
           Using getRows() as we are not sure if tests writers will add the header. */
        foreach ($table->getRows() as $key => $row) {
            if (count($row) !== 2) {
                $msg = 'You should specify a table with capability/permission columns';
                throw new ExpectationException($msg, $this->getSession());
            }

            [$capability, $permission] = $row;

            // Skip the headers row if it was provided.
            if (strtolower($capability) == 'capability' || strtolower($capability) == 'capabilities') {
                continue;
            }
            // Checking the permission value.
            $permissionconstant = 'CAP_' . strtoupper($permission);
            if (!defined($permissionconstant)) {
                throw new ExpectationException(
                    'The provided permission value "' . $permission . '" is not valid. Use Inherit, Allow, Prevent or Prohibited',
                    $this->getSession()
                );
            }

            // Converting from permission to constant value.
            $permissionvalue = constant($permissionconstant);

            \assign_capability(
                $capability,
                $permissionvalue,
                $roleid,
                $systemcontext->id,
                true
            );
            $systemcontext->mark_dirty();

            accesslib_clear_role_cache($roleid);
        }
    }
}
