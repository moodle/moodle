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
     */
    public function i_set_the_following_system_permissions_of_role($rolename, $table) {

        $parentnodes = get_string('users', 'admin') . ' > ' .
            get_string('permissions', 'role');

        // Go to home page.
        $this->execute("behat_general::i_am_on_homepage");

        // Navigate to course management page via navigation block.
        $this->execute("behat_navigation::i_navigate_to_in_site_administration",
            array($parentnodes . ' > ' . get_string('defineroles', 'role'))
        );

        $this->execute("behat_general::click_link", "Edit " . $this->escape($rolename) . " role");
        $this->execute("behat_permissions::i_fill_the_capabilities_form_with_the_following_permissions", $table);

        $this->execute('behat_forms::press_button', get_string('savechanges'));
    }

    /**
     * Overrides system capabilities at category, course and module levels. This step begins after clicking 'Permissions' link. Expects a table with capability name and permission (Inherit/Allow/Prevent/Prohibit) columns.
     * @Given /^I override the system permissions of "(?P<rolefullname_string>(?:[^"]|\\")*)" role with:$/
     * @param string $rolename
     * @param TableNode $table
     */
    public function i_override_the_system_permissions_of_role_with($rolename, $table) {

        // We don't know the number of overrides so we have to get it to match the option contents.
        $roleoption = $this->find('xpath', '//select[@name="roleid"]/option[contains(.,"' . $this->escape($rolename) . '")]');

        $this->execute('behat_forms::i_set_the_field_to',
            array(get_string('advancedoverride', 'role'), $this->escape($roleoption->getText()))
        );

        if (!$this->running_javascript()) {
            $this->execute("behat_general::i_click_on_in_the", [get_string('go'), 'button', 'region-main', 'region']);
        }

        $this->execute("behat_permissions::i_fill_the_capabilities_form_with_the_following_permissions", $table);

        $this->execute('behat_forms::press_button', get_string('savechanges'));
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
                $advancedtoggle->click();

                // Wait for the page to load.
                $this->getSession()->wait(self::get_timeout() * 1000, self::PAGE_READY_JS);
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

            // Here we wait for the element to appear and exception if it does not exist.
            $radio = $this->find('xpath', '//input[@name="' . $capability . '" and @value="' . $permissionvalue . '"]');
            $field = behat_field_manager::get_field_instance('radio', $radio, $this->getSession());
            $field->set_value(1);
        }
    }

    /**
     * Checks if the capability has the specified permission. Works in the role definition advanced page.
     *
     * @Then /^"(?P<capability_string>(?:[^"]|\\")*)" capability has "(?P<permission_string>Not set|Allow|Prevent|Prohibit)" permission$/
     * @throws ExpectationException
     * @param string $capabilityname
     * @param string $permission
     * @return void
     */
    public function capability_has_permission($capabilityname, $permission) {

        // We already know the name, so we just need the value.
        $radioxpath = "//table[@class='rolecap']/descendant::input[@type='radio']" .
            "[@name='" . $capabilityname . "'][@checked]";

        $checkedradio = $this->find('xpath', $radioxpath);

        switch ($permission) {
            case get_string('notset', 'role'):
                $perm = CAP_INHERIT;
                break;
            case get_string('allow', 'role'):
                $perm = CAP_ALLOW;
                break;
            case get_string('prevent', 'role'):
                $perm = CAP_PREVENT;
                break;
            case get_string('prohibit', 'role'):
                $perm = CAP_PROHIBIT;
                break;
            default:
                throw new ExpectationException('"' . $permission . '" permission does not exist', $this->getSession());
                break;
        }

        if ($checkedradio->getAttribute('value') != $perm) {
            throw new ExpectationException('"' . $capabilityname . '" permission is not "' . $permission . '"', $this->getSession());
        }
    }

    /**
     * Set the allowed role assignments for the specified role.
     *
     * @Given /^I define the allowed role assignments for the "(?P<rolefullname_string>(?:[^"]|\\")*)" role as:$/
     * @param string $rolename
     * @param TableNode $table
     * @return void Executes other steps
     */
    public function i_define_the_allowed_role_assignments_for_a_role_as($rolename, $table) {
        $parentnodes = get_string('users', 'admin') . ' > ' .
            get_string('permissions', 'role');

        // Go to home page.
        $this->execute("behat_general::i_am_on_homepage");

        // Navigate to Define roles page via site administration menu.
        $this->execute("behat_navigation::i_navigate_to_in_site_administration",
                $parentnodes .' > '. get_string('defineroles', 'role')
        );

        $this->execute("behat_general::click_link", "Allow role assignments");
        $this->execute("behat_permissions::i_fill_in_the_allowed_role_assignments_form_for_a_role_with",
            array($rolename, $table)
        );

        $this->execute('behat_forms::press_button', get_string('savechanges'));
    }

    /**
     * Fill in the allowed role assignments form for the specied role.
     *
     * Takes a table with two columns. Each row should contain the target
     * role, and either "Assignable" or "Not assignable".
     *
     * @Given /^I fill in the allowed role assignments form for the "(?P<rolefullname_string>(?:[^"]|\\")*)" role with:$/
     * @param String $sourcerole
     * @param TableNode $table
     * @return void
     */
    public function i_fill_in_the_allowed_role_assignments_form_for_a_role_with($sourcerole, $table) {
        foreach ($table->getRows() as $key => $row) {
            list($targetrole, $allowed) = $row;

            $node = $this->find('xpath', '//input[@title="Allow users with role ' .
                $sourcerole .
                ' to assign the role ' .
                $targetrole . '"]');

            if ($allowed == 'Assignable') {
                if (!$node->isChecked()) {
                    $node->click();
                }
            } else if ($allowed == 'Not assignable') {
                if ($node->isChecked()) {
                    $node->click();
                }
            } else {
                throw new ExpectationException(
                    'The provided permission value "' . $allowed . '" is not valid. Use Assignable, or Not assignable',
                    $this->getSession()
                );
            }
        }
    }
}
