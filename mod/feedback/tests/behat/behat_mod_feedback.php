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

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode,
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
        $rv = array();
        $questiontype = $this->escape($questiontype);
        $additem = $this->escape(get_string('add_item', 'feedback'));
        $rv[] = new Given("I select \"{$questiontype}\" from the \"{$additem}\" singleselect");

        $newdata = new TableNode();
        $rows = $questiondata->getRows();
        foreach ($rows as $row) {
            foreach ($row as $key => $value) {
                $row[$key] = preg_replace('|\\\\n|', "\n", $value);
            }
            $newdata->addRow($row);
        }
        $rv[] = new Given('I set the following fields to these values:', $newdata);

        $saveitem = $this->escape(get_string('save_item', 'feedback'));
        $rv[] = new Given("I press \"{$saveitem}\"");

        return $rv;
    }
}
