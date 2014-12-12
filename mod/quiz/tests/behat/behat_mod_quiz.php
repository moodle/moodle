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
 * Steps definitions related to mod_quiz.
 *
 * @package   mod_quiz
 * @category  test
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../../question/tests/behat/behat_question_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related to mod_quiz.
 *
 * @copyright 2014 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_quiz extends behat_question_base {

    /**
     * Put the specified questions on the specified pages of a given quiz.
     *
     * Give the question name in the first column, and that page number in the
     * second column. You may optionally give the desired maximum mark for each
     * question in a third column.
     *
     * @param string $quizname the name of the quiz to add questions to.
     * @param TableNode $data information about the questions to add.
     *
     * @Given /^quiz "([^"]*)" contains the following questions:$/
     */
    public function quiz_contains_the_following_questions($quizname, TableNode $data) {
        global $DB;

        $quiz = $DB->get_record('quiz', array('name' => $quizname), '*', MUST_EXIST);

        // The action depends on the field type.
        foreach ($data->getRows() as $questiondata) {
            if (count($questiondata) < 2 || count($questiondata) > 3) {
                throw new ExpectationException('When adding questions to a quiz, you should give 2 or three 3 things: ' .
                        ' the question name, the page number, and optionally a the maxiumum mark. ' .
                        count($questiondata) . ' values passed.', $this->getSession());
            }

            list($questionname, $rawpage) = $questiondata;
            if (!isset($questiondata[2]) || $questiondata[2] === '') {
                $maxmark = null;
            } else {
                $maxmark = clean_param($questiondata[2], PARAM_FLOAT);
                if (!is_numeric($questiondata[2]) || $maxmark < 0) {
                    throw new ExpectationException('When adding questions to a quiz, the max mark must be a positive number.',
                            $this->getSession());
                }
            }

            $page = clean_param($rawpage, PARAM_INT);
            if ($page <= 0 || (string) $page !== $rawpage) {
                throw new ExpectationException('When adding questions to a quiz, the page number must be a positive integer.',
                        $this->getSession());
            }

            $questionid = $DB->get_field('question', 'id', array('name' => $questionname), MUST_EXIST);
            quiz_add_quiz_question($questionid, $quiz, $page, $maxmark);
        }
        quiz_update_sumgrades($quiz);
    }

    /**
     * Adds a question to the existing quiz with filling the form.
     *
     * The form for creating a question should be on one page.
     *
     * @When /^I add a "(?P<question_type_string>(?:[^"]|\\")*)" question to the "(?P<quiz_name_string>(?:[^"]|\\")*)" quiz with:$/
     * @param string $questiontype
     * @param string $quizname
     * @param TableNode $questiondata with data for filling the add question form
     */
    public function i_add_question_to_the_quiz_with($questiontype, $quizname, TableNode $questiondata) {
        $quizname = $this->escape($quizname);
        $editquiz = $this->escape(get_string('editquiz', 'quiz'));
        $quizadmin = $this->escape(get_string('pluginadministration', 'quiz'));
        $addaquestion = $this->escape(get_string('addaquestion', 'quiz'));
        $menuxpath = "//div[contains(@class, ' page-add-actions ')][last()]//a[contains(@class, ' textmenu')]";
        $itemxpath = "//div[contains(@class, ' page-add-actions ')][last()]//a[contains(@class, ' addquestion ')]";
        return array_merge(array(
            new Given("I follow \"$quizname\""),
            new Given("I navigate to \"$editquiz\" node in \"$quizadmin\""),
            new Given("I click on \"$menuxpath\" \"xpath_element\""),
            new Given("I click on \"$itemxpath\" \"xpath_element\""),
                ), $this->finish_adding_question($questiontype, $questiondata));
    }

    /**
     * Set the max mark for a question on the Edit quiz page.
     *
     * @When /^I set the max mark for question "(?P<question_name_string>(?:[^"]|\\")*)" to "(?P<new_mark_string>(?:[^"]|\\")*)"$/
     * @param string $questionname the name of the question to set the max mark for.
     * @param string $newmark the mark to set
     */
    public function i_set_the_max_mark_for_quiz_question($questionname, $newmark) {
        return array(
            new Given('I follow "' . $this->escape(get_string('editmaxmark', 'quiz')) . '"'),
            new Given('I wait until "li input[name=maxmark]" "css_element" exists'),
            new Given('I should see "' . $this->escape(get_string('edittitleinstructions')) . '"'),
            new Given('I set the field "maxmark" to "' . $this->escape($newmark) . chr(10) . '"'),
        );
    }

    /**
     * Open the add menu on a given page, or at the end of the Edit quiz page.
     * @Given /^I open the "(?P<page_n_or_last_string>(?:[^"]|\\")*)" add to quiz menu$/
     * @param string $pageorlast either "Page n" or "last".
     */
    public function i_open_the_add_to_quiz_menu_for($pageorlast) {

        if (!$this->running_javascript()) {
            throw new DriverException('Activities actions menu not available when Javascript is disabled');
        }

        if ($pageorlast == 'last') {
            $xpath = "//div[@class = 'last-add-menu']//a[contains(@class, 'textmenu') and contains(., 'Add')]";
        } else if (preg_match('~Page (\d+)~', $pageorlast, $matches)) {
            $xpath = "//li[@id = 'page-{$matches[1]}']//a[contains(@class, 'textmenu') and contains(., 'Add')]";
        } else {
            throw new ExpectationException("The I open the add to quiz menu step must specify either 'Page N' or 'last'.");
        }
        $menu = $this->find('xpath', $xpath)->click();
    }

    /**
     * Click on a given link in the moodle-actionmenu that is currently open.
     * @Given /^I follow "(?P<link_string>(?:[^"]|\\")*)" in the open menu$/
     * @param string $linkstring the text (or id, etc.) of the link to click.
     * @return array of steps.
     */
    public function i_follow_in_the_open_menu($linkstring) {
        $openmenuxpath = "//div[contains(@class, 'moodle-actionmenu') and contains(@class, 'show')]";
        return array(
            new Given('I click on "' . $linkstring . '" "link" in the "' . $openmenuxpath . '" "xpath_element"'),
        );
    }

    /**
     * Check whether a particular question is on a particular page of the quiz on the Edit quiz page.
     * @Given /^I should see "(?P<question_name>(?:[^"]|\\")*)" on quiz page "(?P<page_number>\d+)"$/
     * @param string $questionname the name of the question we are looking for.
     * @param number $pagenumber the page it should be found on.
     * @return array of steps.
     */
    public function i_should_see_on_quiz_page($questionname, $pagenumber) {
        $xpath = "//li[contains(., '" . $this->escape($questionname) .
                "')][./preceding-sibling::li[contains(@class, 'pagenumber')][1][contains(., 'Page " .
                $pagenumber . "')]]";
        return array(
            new Given('"' . $xpath . '" "xpath_element" should exist'),
        );
    }

    /**
     * Check whether a particular question is not on a particular page of the quiz on the Edit quiz page.
     * @Given /^I should not see "(?P<question_name>(?:[^"]|\\")*)" on quiz page "(?P<page_number>\d+)"$/
     * @param string $questionname the name of the question we are looking for.
     * @param number $pagenumber the page it should be found on.
     * @return array of steps.
     */
    public function i_should_not_see_on_quiz_page($questionname, $pagenumber) {
        $xpath = "//li[contains(., '" . $this->escape($questionname) .
                "')][./preceding-sibling::li[contains(@class, 'pagenumber')][1][contains(., 'Page " .
                $pagenumber . "')]]";
        return array(
            new Given('"' . $xpath . '" "xpath_element" should not exist'),
        );
    }

    /**
     * Check whether one question comes before another on the Edit quiz page.
     * The two questions must be on the same page.
     * @Given /^I should see "(?P<first_q_name>(?:[^"]|\\")*)" before "(?P<second_q_name>(?:[^"]|\\")*)" on the edit quiz page$/
     * @param string $firstquestionname the name of the question that should come first in order.
     * @param string $secondquestionname the name of the question that should come immediately after it in order.
     * @return array of steps.
     */
    public function i_should_see_before_on_the_edit_quiz_page($firstquestionname, $secondquestionname) {
        $xpath = "//li[contains(@class, ' slot ') and contains(., '" . $this->escape($firstquestionname) .
                "')]/following-sibling::li[contains(@class, ' slot ')][1]" .
                "[contains(., '" . $this->escape($secondquestionname) . "')]";
        return array(
            new Given('"' . $xpath . '" "xpath_element" should exist'),
        );
    }

    /**
     * Check the number displayed alongside a question on the Edit quiz page.
     * @Given /^"(?P<question_name>(?:[^"]|\\")*)" should have number "(?P<number>(?:[^"]|\\")*)" on the edit quiz page$/
     * @param string $questionname the name of the question we are looking for.
     * @param number $number the number (or 'i') that should be displayed beside that question.
     * @return array of steps.
     */
    public function should_have_number_on_the_edit_quiz_page($questionname, $number) {
        $xpath = "//li[contains(@class, 'slot') and contains(., '" . $this->escape($questionname) .
                "')]//span[contains(@class, 'slotnumber') and normalize-space(text()) = '" . $this->escape($number) . "']";
        return array(
            new Given('"' . $xpath . '" "xpath_element" should exist'),
        );
    }

    /**
     * Get the xpath for a partcular add/remove page-break icon.
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon.
     * @return string the requried xpath.
     */
    protected function get_xpath_page_break_icon_after_question($addorremoves, $questionname) {
        return "//li[contains(@class, 'slot') and contains(., '" . $this->escape($questionname) .
                "')]//a[contains(@class, 'page_split_join') and @title = '" . $addorremoves . " page break']";
    }

    /**
     * Click the add or remove page-break icon after a particular question.
     * @When /^I click on the "(Add|Remove)" page break icon after question "(?P<question_name>(?:[^"]|\\")*)"$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     * @return array of steps.
     */
    public function i_click_on_the_page_break_icon_after_question($addorremoves, $questionname) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);
        return array(
            new Given('I click on "' . $xpath . '" "xpath_element"'),
        );
    }

    /**
     * Assert the add or remove page-break icon after a particular question exists.
     * @When /^the "(Add|Remove)" page break icon after question "(?P<question_name>(?:[^"]|\\")*)" should exist$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     * @return array of steps.
     */
    public function the_page_break_icon_after_question_should_exist($addorremoves, $questionname) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);
        return array(
            new Given('"' . $xpath . '" "xpath_element" should exist'),
        );
    }

    /**
     * Assert the add or remove page-break icon after a particular question does not exist.
     * @When /^the "(Add|Remove)" page break icon after question "(?P<question_name>(?:[^"]|\\")*)" should not exist$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     * @return array of steps.
     */
    public function the_page_break_icon_after_question_should_not_exist($addorremoves, $questionname) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);
        return array(
            new Given('"' . $xpath . '" "xpath_element" should not exist'),
        );
    }

    /**
     * Check the add or remove page-break link after a particular question contains the given parameters in its url.
     * @When /^the "(Add|Remove)" page break link after question "(?P<question_name>(?:[^"]|\\")*) should contain:"$/
     * @param string $addorremoves 'Add' or 'Remove'.
     * @param string $questionname the name of the question before the icon to click.
     * @param TableNode $paramdata with data for checking the page break url
     * @return array of steps.
     */
    public function the_page_break_link_after_question_should_contain($addorremoves, $questionname, $paramdata) {
        $xpath = $this->get_xpath_page_break_icon_after_question($addorremoves, $questionname);
        return array(
            new Given('I click on "' . $xpath . '" "xpath_element"'),
        );
    }

    /**
     * Move a question on the Edit quiz page by first clicking on the Move icon,
     * then clicking one of the "After ..." links.
     * @When /^I move "(?P<question_name>(?:[^"]|\\")*)" to "(?P<target>(?:[^"]|\\")*)" in the quiz by clicking the move icon$/
     * @param string $questionname the name of the question we are looking for.
     * @param string $target the target place to move to. One of the links in the pop-up like
     *      "After Page 1" or "After Question N".
     * @return array of steps.
     */
    public function i_move_question_after_item_by_clicking_the_move_icon($questionname, $target) {
        $iconxpath = "//li[contains(@class, ' slot ') and contains(., '" . $this->escape($questionname) .
                "')]//span[contains(@class, 'editing_move')]";
        return array(
            new Given('I click on "' . $iconxpath . '" "xpath_element"'),
            new Given('I click on "' . $this->escape($target) . '" "text"'),
        );
    }

    /**
     * Move a question on the Edit quiz page by dragging a given question on top of another item.
     * @When /^I move "(?P<question_name>(?:[^"]|\\")*)" to "(?P<target>(?:[^"]|\\")*)" in the quiz by dragging$/
     * @param string $questionname the name of the question we are looking for.
     * @param string $target the target place to move to. Ether a question name, or "Page N"
     * @return array of steps.
     */
    public function i_move_question_after_item_by_dragging($questionname, $target) {
        $iconxpath = "//li[contains(@class, ' slot ') and contains(., '" . $this->escape($questionname) .
                "')]//span[contains(@class, 'editing_move')]//img";
        $destinationxpath = "//li[contains(@class, ' slot ') or contains(@class, 'pagenumber ')]" .
                "[contains(., '" . $this->escape($target) . "')]";
        return array(
            new Given('I drag "' . $iconxpath . '" "xpath_element" ' .
                'and I drop it in "' . $destinationxpath . '" "xpath_element"'),
        );
    }

    /**
     * Delete a question on the Edit quiz page by first clicking on the Delete icon,
     * then clicking one of the "After ..." links.
     * @When /^I delete "(?P<question_name>(?:[^"]|\\")*)" in the quiz by clicking the delete icon$/
     * @param string $questionname the name of the question we are looking for.
     * @return array of steps.
     */
    public function i_delete_question_by_clicking_the_delete_icon($questionname) {
        $slotxpath = "//li[contains(@class, ' slot ') and contains(., '" . $this->escape($questionname) .
                "')]";
        $deletexpath = "//a[contains(@class, 'editing_delete')]";
        return array(
            new Given('I click on "' . $slotxpath . $deletexpath . '" "xpath_element"'),
            new Given('I click on "Yes" "button" in the "Confirm" "dialogue"'),
        );
    }
}
