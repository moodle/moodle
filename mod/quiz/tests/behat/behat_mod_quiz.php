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
    Behat\Gherkin\Node\TableNode as TableNode;

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
        $addaquestion = $this->escape(get_string('addaquestion', 'quiz'));
        return array_merge(array(
            new Given("I follow \"$quizname\""),
            new Given("I follow \"$editquiz\""),
            new Given("I press \"$addaquestion\""),
                ), $this->finish_adding_question($questiontype, $questiondata));
    }
}
