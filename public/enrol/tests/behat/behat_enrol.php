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
 * Enrolment steps definitions.
 *
 * @package    core_enrol
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Steps definitions for general enrolment actions.
 *
 * @package    core_enrol
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_enrol extends behat_base {

    /**
     * Add the specified enrolment method to the specified course filling the form with the provided data.
     *
     * @Given /^I add "(?P<enrolment_method_name_string>(?:[^"]|\\")*)" enrolment method in "(?P<course_identifier_string>(?:[^"]|\\")*)" with:$/
     * @param string $enrolmethod The enrolment method being used
     * @param string $courseidentifier The courseidentifier such as short name
     * @param TableNode $table Enrolment details
     */
    public function i_add_enrolment_method_for_with(string $enrolmethod, string $courseidentifier, TableNode $table): void {
        $this->execute("behat_navigation::i_am_on_page_instance", [$courseidentifier, 'enrolment methods']);

        // Select enrolment method.
        $this->execute('behat_forms::i_select_from_the_singleselect',
            array($this->escape($enrolmethod), get_string('addinstance', 'enrol'))
        );

        // Wait again, for page to reloaded.
        $this->execute('behat_general::i_wait_to_be_redirected');

        // Set form fields.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $table);

        // Ensure we get button in focus, before pressing button.
        if ($this->running_javascript()) {
            $this->execute('behat_general::i_press_named_key', ['', 'tab']);
        }

        // Save changes.
        $this->execute("behat_forms::press_button", get_string('addinstance', 'enrol'));
    }

    /**
     * Enrols the specified user in the current course without options.
     *
     * This is a simple step, to set enrolment options would be better to
     * create a separate step as a TableNode will be required.
     *
     * @Given /^I enrol "(?P<user_fullname_string>(?:[^"]|\\")*)" user as "(?P<rolename_string>(?:[^"]|\\")*)"$/
     * @param string $userfullname
     * @param string $rolename
     */
    public function i_enrol_user_as($userfullname, $rolename) {

        // Navigate to enrolment page.
        try {
            $parentnodes = get_string('users', 'admin');
            $this->execute("behat_navigation::i_navigate_to_in_current_page_administration",
                array($parentnodes . ' > '. get_string('enrolledusers', 'enrol'))
            );
        } catch (Exception $e) {
            $this->execute("behat_general::i_click_on", [get_string('participants'), 'link']);
        }

        $this->execute("behat_forms::press_button", get_string('enrolusers', 'enrol'));

        if ($this->running_javascript()) {
            $this->execute('behat_forms::i_set_the_field_to', array(get_string('assignrole', 'enrol_manual'), $rolename));

            // We have a div here, not a tr.
            $this->execute('behat_forms::i_set_the_field_to', array(get_string('selectusers', 'enrol_manual'), $userfullname));

            $enrolusers = get_string('enrolusers', 'enrol_manual');
            $this->execute('behat_general::i_click_on_in_the', [$enrolusers, 'button', $enrolusers, 'dialogue']);

        } else {
            $this->execute('behat_forms::i_set_the_field_to', array(get_string('assignrole', 'role'), $rolename));
            $this->execute('behat_forms::i_set_the_field_to', array("addselect", $userfullname));
            $this->execute("behat_forms::press_button", "add");
        }
    }

}
