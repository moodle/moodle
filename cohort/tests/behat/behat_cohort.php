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
 * @package    core_cohort
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Steps definitions for cohort actions.
 *
 * @package    core_cohort
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_cohort extends behat_base {

    /**
     * Adds the user to the specified cohort. The user should be specified like "Firstname Lastname (user@example.com)".
     *
     * @Given /^I add "(?P<user_fullname_string>(?:[^"]|\\")*)" user to "(?P<cohort_idnumber_string>(?:[^"]|\\")*)" cohort members$/
     * @param string $user
     * @param string $cohortidnumber
     */
    public function i_add_user_to_cohort_members($user, $cohortidnumber) {

        // If we are not in the cohorts management we should move there before anything else.
        if (!$this->getSession()->getPage()->find('css', 'input#cohort_search_q')) {

            // With JS enabled we should expand a few tree nodes.
            $parentnodes = get_string('administrationsite') . ' > ' .
                get_string('users', 'admin') . ' > ' .
                get_string('accounts', 'admin');

            $this->execute("behat_general::i_am_on_homepage");
            $this->execute("behat_navigation::i_navigate_to_node_in",
                array(get_string('cohorts', 'cohort'), $parentnodes)
            );
        }

        $this->execute('behat_general::i_click_on_in_the',
            array(get_string('assign', 'cohort'), "link", $this->escape($cohortidnumber), "table_row")
        );

        $this->execute("behat_forms::i_set_the_field_to",
            array(get_string('potusers', 'cohort'), $this->escape($user))
        );

        $this->execute("behat_forms::press_button", get_string('add'));
        $this->execute("behat_forms::press_button", get_string('backtocohorts', 'cohort'));

    }
}
