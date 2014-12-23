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
 * @package    mod_quiz
 * @category   test
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * @package    mod_quiz
 * @category   test
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        global $CFG, $DB;
        require_once(__DIR__ . '/../../editlib.php');

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
        return array_merge(array(
            new Given("I follow \"$quizname\""),
            new Given("I navigate to \"$editquiz\" node in \"$quizadmin\""),
            new Given("I press \"$addaquestion\""),
                ), $this->finish_adding_question($questiontype, $questiondata));
    }
}
