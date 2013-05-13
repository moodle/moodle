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
 * Cohorts steps definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;

/**
 * Steps definitions for cohort actions.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_cohort extends behat_base {

    /**
     * Adds the user to the specified cohort.
     *
     * @Given /^I add "(?P<user_username_string>(?:[^"]|\\")*)" user to "(?P<cohort_idnumber_string>(?:[^"]|\\")*)" cohort$/
     * @param string $username
     * @param string $cohortidnumber
     */
    public function i_add_user_to_cohort($username, $cohortidnumber) {
        global $DB;

        // The user was created by the data generator, executed by the same PHP process that is
        // running this step, not by any Selenium action.
        $userid = $DB->get_field('user', 'id', array('username' => $username));

        $steps = array(
            new Given('I click on "Assign" "link" in the "//table[@id=\'cohorts\']//tr[contains(., \'' . $cohortidnumber . '\')]" "xpath_element"'),
            new Given('I select "' . $userid . '" from "Potential users"'),
            new Given('I press "Add"'),
            new Given('I press "Back to cohorts"')
        );

        // If we are not in the cohorts management we should move there before anything else.
        if (!$this->getSession()->getPage()->find('css', 'input#cohort_search_q')) {
            $steps = array_merge(
                array(
                    new Given('I am on homepage'),
                    new Given('I collapse "Front page settings" node'),
                    new Given('I expand "Site administration" node'),
                    new Given('I expand "Users" node'),
                    new Given('I expand "Accounts" node'),
                    new Given('I follow "Cohorts"')
                ),
                $steps
            );
        }

        return $steps;
    }
}
