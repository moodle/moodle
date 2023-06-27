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
 * Step definitions related to mod_quiz overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../mod/quiz/tests/behat/behat_mod_quiz.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Step definitions related to mod_quiz overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_classic_behat_mod_quiz extends behat_mod_quiz {

    /**
     * Adds a question to the existing quiz with filling the form.
     * The form for creating a question should be on one page.
     *
     * @param string $questiontype
     * @param string $quizname
     * @param TableNode $questiondata with data for filling the add question form
     */
    public function i_add_question_to_the_quiz_with($questiontype, $quizname, TableNode $questiondata) {
        $quizname = $this->escape($quizname);
        $editquiz = $this->escape(get_string('editquiz', 'quiz'));
        $menuxpath = "//div[contains(@class, ' page-add-actions ')][last()]//a[contains(@class, ' dropdown-toggle')]";
        $itemxpath = "//div[contains(@class, ' page-add-actions ')][last()]//a[contains(@class, ' addquestion ')]";

        $this->execute('behat_general::click_link', $quizname);

        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration", $editquiz);

        $this->execute("behat_general::i_click_on", array($menuxpath, "xpath_element"));
        $this->execute("behat_general::i_click_on", array($itemxpath, "xpath_element"));

        $this->finish_adding_question($questiontype, $questiondata);
    }
}
