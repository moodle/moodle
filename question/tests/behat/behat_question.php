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
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

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

        // Using xpath literal to avoid quotes problems.
        $questiontypename = $this->getSession()->getSelectorsHandler()->xpathLiteral($questiontypename);
        $questiontypexpath = "//span[@class='qtypename'][normalize-space(.)=$questiontypename]" .
            "/ancestor::div[@class='qtypeoption']/descendant::input";

        return array(
            new Given('I follow "' . get_string('questionbank', 'question') . '"'),
            new Given('I press "' . get_string('createnewquestion', 'question') . '"'),
            new Given('I click on "' . $this->escape($questiontypexpath) . '" "xpath_element"'),
            new Given('I click on "Next" "button" in the "#qtypechoicecontainer" "css_element"'),
            new Given('I fill the moodle form with:', $questiondata),
            new Given('I press "' . get_string('savechanges') . '"')
        );
    }

    /**
     * Checks the state of the specified question.
     *
     * @Then /^the state of "(?P<question_description_string>(?:[^"]|\\")*)" question is shown as "(?P<state_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @throws ElementNotFoundException
     * @param string $questiondescription
     * @param string $state
     */
    public function the_state_of_question_is_shown_as($questiondescription, $state) {

        // Using xpath literal to avoid quotes problems.
        $questiondescriptionliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($questiondescription);
        $stateliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($state);

        // Split in two checkings to give more feedback in case of exception.
        $exception = new ElementNotFoundException($this->getSession(), 'Question "' . $questiondescription . '" ');
        $questionxpath = "//div[contains(concat(' ', normalize-space(@class), ' '), ' qtext ')][contains(., $questiondescriptionliteral)]";
        $this->find('xpath', $questionxpath, $exception);

        $exception = new ExpectationException('Question "' . $questiondescription . '" state is not "' . $state . '"', $this->getSession());
        $xpath = $questionxpath . "/ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' que ')]" .
            "/descendant::div[@class='state'][contains(., $stateliteral)]";
        $this->find('xpath', $xpath, $exception);
    }

}
