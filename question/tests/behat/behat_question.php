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
 * Behat question-related steps definitions.
 *
 * @package    core_question
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Steps definitions related with the question bank management.
 *
 * @package    core_question
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_question extends behat_base {

    /**
     * Creates a question in the current course questions bank with the provided data. This step can only be used when creating question types composed by a single form.
     *
     * @Given /^I add a "(?P<question_type_name_string>(?:[^"]|\\")*)" question filling the form with:$/
     * @param string $questiontypename The question type name
     * @param TableNode $questiondata The data to fill the question type form
     */
    public function i_add_a_question_filling_the_form_with($questiontypename, TableNode $questiondata) {

        $questiontypexpath = "//span[@class='qtypename'][.='" . $questiontypename . "']
/ancestor::div[@class='qtypeoption']/descendant::input";

        return array(
            new Given('I follow "' . get_string('questionbank', 'question') . '"'),
            new Given('I press "' . get_string('createnewquestion', 'question') . '"'),
            new Given('I click on "' . $questiontypexpath . '" "xpath_element"'),
            new Given('I click on "Next" "button" in the "#qtypechoicecontainer" "css_element"'),
            new Given('I fill the moodle form with:', $questiondata),
            new Given('I press "Save changes"')
        );
    }

}
