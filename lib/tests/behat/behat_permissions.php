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
 * Steps definitions related with permissions.
 *
 * @package   core
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Steps definitions to set up permissions to capabilities.
 *
 * @package   core
 * @category  test
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_permissions extends behat_base {

    /**
     * Set system level permissions to the specified role. Expects a table with capability name and permission (Inherit/Allow/Prevent/Prohibit) columns.
     * @Given /^I set the following system permissions of "(?P<rolefullname_string>(?:[^"]|\\")*)" role:$/
     * @param string $rolename
     * @param TableNode $table
     * @return void Executes other steps
     */
    public function i_set_the_following_system_permissions_of_role($rolename, $table) {

        return array(
            new Given('I am on homepage'),
            new Given('I collapse "' . get_string('frontpagesettings', 'admin') . '" node'),
            new Given('I expand "' . get_string('administrationsite') . '" node'),
            new Given('I expand "' . get_string('users', 'admin') . '" node'),
            new Given('I expand "' . get_string('permissions', 'role') . '" node'),
            new Given('I follow "' . get_string('defineroles', 'role') . '"'),
            new Given('I follow "Edit ' . $this->escape($rolename) . ' role"'),
            new Given('I fill the capabilities form with the following permissions:', $table),
            new Given('I press "' . get_string('savechanges') . '"')
        );
    }

    /**
     * Overrides system capabilities at category, course and module levels. This step begins after clicking 'Permissions' link. Expects a table with capability name and permission (Inherit/Allow/Prevent/Prohibit) columns.
     * @Given /^I override the system permissions of "(?P<rolefullname_string>(?:[^"]|\\")*)" role with:$/
     * @param string $rolename
     * @param TableNode $table
     * @return void Executes other steps
     */
    public function i_override_the_system_permissions_of_role_with($rolename, $table) {

        // We don't know the number of overrides so we have to get it to match the option contents.
        $roleoption = $this->find('xpath', '//select[@name="roleid"]/option[contains(.,"' . $this->escape($rolename) . '")]');

        return array(
            new Given('I select "' . $this->escape($roleoption->getText()) . '" from "' . get_string('advancedoverride', 'role') . '"'),
            new Given('I fill the capabilities form with the following permissions:', $table),
            new Given('I press "' . get_string('savechanges') . '"')
        );
    }

    /**
     * Fills the advanced permissions form with the provided data. Expects a table with capability name and permission (Inherit/Allow/Prevent/Prohibit) columns.
     * @Given /^I fill the capabilities form with the following permissions:$/
     * @param TableNode $table
     * @return void
     */
    public function i_fill_the_capabilities_form_with_the_following_permissions($table) {

        // Ensure we are using the advanced view.
        // Wrapped in a try/catch to capture the exception and continue execution, we don't know if advanced mode was already enabled.
        try {
            $advancedtoggle = $this->find_button(get_string('showadvanced', 'form'));
            if ($advancedtoggle) {
                $this->getSession()->getPage()->pressButton(get_string('showadvanced', 'form'));
            }
        } catch (Exception $e) {
            // We already are in advanced mode.
        }

        // Using getRows() as we are not sure if tests writers will add the header.
        foreach ($table->getRows() as $key => $row) {

            if (count($row) !== 2) {
                throw new ExpectationException('You should specify a table with capability/permission columns', $this->getSession());
            }

            list($capability, $permission) = $row;

            // Skip the headers row if it was provided
            if (strtolower($capability) == 'capability' || strtolower($capability) == 'capabilities') {
                continue;
            }

            // Checking the permission value.
            $permissionconstant = 'CAP_'. strtoupper($permission);
            if (!defined($permissionconstant)) {
                throw new ExpectationException(
                    'The provided permission value "' . $permission . '" is not valid. Use Inherit, Allow, Prevent or Prohibited',
                    $this->getSession()
                );
            }

            // Converting from permission to constant value.
            $permissionvalue = constant($permissionconstant);

            // Here we wait for the element to appear and exception if it does not exists.
            $radio = $this->find('xpath', '//input[@name="' . $capability . '" and @value="' . $permissionvalue . '"]');
            $radio->click();
        }
    }

}
