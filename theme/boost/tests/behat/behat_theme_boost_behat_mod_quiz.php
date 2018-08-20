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
 * Steps definitions related to mod_quiz overrides.
 *
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../mod/quiz/tests/behat/behat_mod_quiz.php');

use Behat\Gherkin\Node\TableNode as TableNode;

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related to mod_quiz overrides.
 *
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_boost_behat_mod_quiz extends behat_mod_quiz {

    public function i_add_question_to_the_quiz_with($questiontype, $quizname, TableNode $questiondata) {
        $quizname = $this->escape($quizname);
        $editquiz = $this->escape(get_string('editquiz', 'quiz'));
        $quizadmin = $this->escape(get_string('pluginadministration', 'quiz'));
        $addaquestion = $this->escape(get_string('addaquestion', 'quiz'));

        $this->execute('behat_general::click_link', $quizname);

        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration",
                $quizadmin . ' > ' . $editquiz);

        if ($this->running_javascript()) {
            $this->execute("behat_action_menu::i_open_the_action_menu_in", array('.slots', "css_element"));
            $this->execute("behat_action_menu::i_choose_in_the_open_action_menu", array($addaquestion));
        } else {
            $this->execute('behat_general::click_link', $addaquestion);
        }

        $this->finish_adding_question($questiontype, $questiondata);
    }

    public function i_should_see_question_in_section_in_the_quiz_navigation($questionnumber, $sectionheading) {

        // Using xpath literal to avoid quotes problems.
        $questionnumberliteral = behat_context_helper::escape('Question ' . $questionnumber);
        $headingliteral = behat_context_helper::escape($sectionheading);

        // Split in two checkings to give more feedback in case of exception.
        $exception = new ExpectationException('Question "' . $questionnumber . '" is not in section "' .
                $sectionheading . '" in the quiz navigation.', $this->getSession());
        $xpath = "//*[@id = 'mod_quiz_navblock']//*[contains(concat(' ', normalize-space(@class), ' '), ' qnbutton ') and " .
                "contains(., {$questionnumberliteral}) and contains(preceding-sibling::h3[1], {$headingliteral})]";
        $this->find('xpath', $xpath);
    }

    public function i_open_the_add_to_quiz_menu_for($pageorlast) {

        if (!$this->running_javascript()) {
            throw new DriverException('Activities actions menu not available when Javascript is disabled');
        }

        if ($pageorlast == 'last') {
            $xpath = "//div[@class = 'last-add-menu']//a[contains(@data-toggle, 'dropdown') and contains(., 'Add')]";
        } else if (preg_match('~Page (\d+)~', $pageorlast, $matches)) {
            $xpath = "//li[@id = 'page-{$matches[1]}']//a[contains(@data-toggle, 'dropdown') and contains(., 'Add')]";
        } else {
            throw new ExpectationException("The I open the add to quiz menu step must specify either 'Page N' or 'last'.");
        }
        $this->find('xpath', $xpath)->click();
    }
}
