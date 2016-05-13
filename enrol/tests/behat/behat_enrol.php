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
     * Adds the specified enrolment method to the current course filling the form with the provided data.
     *
     * @Given /^I add "(?P<enrolment_method_name_string>(?:[^"]|\\")*)" enrolment method with:$/
     * @param string $enrolmethod
     * @param TableNode $table
     */
    public function i_add_enrolment_method_with($enrolmethod, TableNode $table) {
        // Navigate to enrolment method page.
        $parentnodes = get_string('courseadministration') . ' > ' . get_string('users', 'admin');
        $this->execute("behat_navigation::i_navigate_to_node_in",
            array(get_string('type_enrol_plural', 'plugin'), $parentnodes)
        );

        // Select enrolment method.
        $this->execute('behat_forms::i_select_from_the_singleselect',
            array($this->escape($enrolmethod), get_string('addinstance', 'enrol'))
        );

        // Set form fields.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $table);

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
        $parentnodes = get_string('courseadministration') . ' > ' . get_string('users', 'admin');
        $this->execute("behat_navigation::i_navigate_to_node_in",
            array(get_string('enrolledusers', 'enrol'), $parentnodes)
        );

        $this->execute("behat_forms::press_button", get_string('enrolusers', 'enrol'));

        $this->execute('behat_forms::i_set_the_field_to', array(get_string('assignroles', 'role'), $rolename));

        if ($this->running_javascript()) {

            // We have a div here, not a tr.
            $userliteral = behat_context_helper::escape($userfullname);
            $userrowxpath = "//div[contains(concat(' ',normalize-space(@class),' '),' user ')][contains(., $userliteral)]";

            $this->execute('behat_general::i_click_on_in_the',
                array(get_string('enrol', 'enrol'), "button", $userrowxpath, "xpath_element")
            );
            $this->execute("behat_forms::press_button", get_string('finishenrollingusers', 'enrol'));

        } else {
            $this->execute('behat_forms::i_set_the_field_to', array("addselect", $userfullname));
            $this->execute("behat_forms::press_button", "add");
        }
    }

}
