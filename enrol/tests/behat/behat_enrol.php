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

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode;

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

        return array(
            new Given('I expand "' . get_string('users', 'admin') . '" node'),
            new Given('I follow "' . get_string('type_enrol_plural', 'plugin') . '"'),
            new Given('I set the field "' . get_string('addinstance', 'enrol') . '" to "' . $this->escape($enrolmethod) . '"'),
            new Given('I set the following fields to these values:', $table),
            new Given('I press "' . get_string('addinstance', 'enrol') . '"')
        );
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
     * @return Given[]
     */
    public function i_enrol_user_as($userfullname, $rolename) {

        $steps = array(
            new Given('I follow "' . get_string('enrolledusers', 'enrol') . '"'),
            new Given('I press "' . get_string('enrolusers', 'enrol') . '"')
        );

        if ($this->running_javascript()) {

            // We have a div here, not a tr.
            $userliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($userfullname);
            $userrowxpath = "//div[contains(concat(' ',normalize-space(@class),' '),' user ')][contains(., $userliteral)]";

            $steps[] = new Given('I set the field "' . get_string('assignroles', 'role') . '" to "' . $rolename . '"');
            $steps[] = new Given('I click on "' . get_string('enrol', 'enrol') . '" "button" in the "' . $userrowxpath . '" "xpath_element"');
            $steps[] = new Given('I press "' . get_string('finishenrollingusers', 'enrol') . '"');

        } else {

            $steps[] = new Given('I set the field "' . get_string('assignrole', 'role') . '" to "' . $rolename . '"');
            $steps[] = new Given('I set the field "addselect" to "' . $userfullname . '"');
            $steps[] = new Given('I press "add"');
        }

        return $steps;
    }

}
