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
 * Steps definitions related to mod_feedback.
 *
 * @package   mod_feedback
 * @category  test
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related to mod_feedback.
 *
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_feedback extends behat_base {

    /**
     * Adds a question to the existing feedback with filling the form.
     *
     * The form for creating a question should be on one page.
     *
     * @When /^I add a "(?P<question_type_string>(?:[^"]|\\")*)" question to the feedback with:$/
     * @param string $questiontype
     * @param TableNode $questiondata with data for filling the add question form
     */
    public function i_add_question_to_the_feedback_with($questiontype, TableNode $questiondata) {

        $questiontype = $this->escape($questiontype);
        $additem = $this->escape(get_string('add_item', 'feedback'));

        $this->execute('behat_forms::i_select_from_the_singleselect', array($questiontype, $additem));

        $rows = $questiondata->getRows();
        $modifiedrows = array();
        foreach ($rows as $row) {
            foreach ($row as $key => $value) {
                $row[$key] = preg_replace('|\\\\n|', "\n", $value);
            }
            $modifiedrows[] = $row;
        }
        $newdata = new TableNode($modifiedrows);

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $newdata);

        $saveitem = $this->escape(get_string('save_item', 'feedback'));
        $this->execute("behat_forms::press_button", $saveitem);
    }

    /**
     * Quick way to generate answers to a one-page feedback.
     *
     * @When /^I log in as "(?P<user_name_string>(?:[^"]|\\")*)" and complete feedback "(?P<feedback_name_string>(?:[^"]|\\")*)" in course "(?P<course_name_string>(?:[^"]|\\")*)" with:$/
     * @param string $questiontype
     * @param TableNode $questiondata with data for filling the add question form
     */
    public function i_log_in_as_and_complete_feedback_in_course($username, $feedbackname, $coursename, TableNode $answers) {
        $username = $this->escape($username);
        $coursename = $this->escape($coursename);
        $feedbackname = $this->escape($feedbackname);
        $completeform = $this->escape(get_string('complete_the_form', 'feedback'));

        // Log in as user.
        $this->execute('behat_auth::i_log_in_as', $username);

        // Navigate to feedback complete form.
        $this->execute('behat_general::click_link', $coursename);
        $this->execute('behat_general::click_link', $feedbackname);
        $this->execute('behat_general::click_link', $completeform);

        // Fill form and submit.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $answers);
        $this->execute("behat_forms::press_button", 'Submit your answers');

        // Log out.
        $this->execute('behat_auth::i_log_out');
    }
}
