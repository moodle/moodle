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
 * Methods related to Wiris Quizzes question types.
 * @package    question
 * @subpackage wq
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

class behat_wq_base extends behat_base {

    /**
     * @Then I choose the question type :questiontypename
     */
    public function i_choose_the_question_type($questiontypename) {
        $this->execute('behat_forms::i_set_the_field_to', array($this->escape($questiontypename), 1));
        $this->execute("behat_general::i_click_on", array('.submitbutton', "css_element"));
    }

    /**
     * Opens the Wiris Quizzes Studio when editing a question.
     *
     * @When I open Wiris Quizzes Studio
     */
    public function i_open_wiris_quizzes_studio() {
        $node = $this->get_text_selector_node(
            'xpath_element',
            "//*[@id='wrsUI_openStudio']"
        );
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Goes back in the Wiris Quizzes Studio interface.
     *
     * @When I go back in Wiris Quizzes Studio
     */
    public function i_go_back_in_wiris_quizzes_studio() {
        $node = $this->get_text_selector_node(
            'xpath_element',
            "//*[@id='wrsUI_quizzesStudioBackButton']"
        );
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Saves Wiris Quizzes Studio.
     *
     * @When I save Wiris Quizzes Studio
     */
    public function i_save_wiris_quizzes_studio() {
        $node = $this->get_text_selector_node(
            'xpath_element',
            "//*[@id='wrsUI_quizzesStudioHomeSaveButton']"
        );
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Opens the n instance of Wiris Quizzes Studio when editing a question.
     *
     * @When I Open Wiris Quizzes Studio Instance :instance
     */
    public function i_open_wiris_quizzes_studio_instance($instance) {
        $node = $this->get_text_selector_node(
            'xpath_element',
            "//*[@id='wrsUI_openStudio_".$instance."']"
        );
        $this->ensure_node_is_visible($node);
        $node->click();
    }

    /**
     * Checks if there is a readonly input.
     *
     * @Then I should have a readonly input
     */
    public function i_should_have_a_readonly_input() {
        $session = $this->getSession();
        $readonly = $session->getPage()->find('css', '.wrsUI_readOnly');
        if (empty($readonly)) {
            throw new Exception('Readonly field not found.');
        }
    }

    /**
     * @When I add the variable :varname with value :value
     */
    public function i_add_the_variable_with_value($varname, $value) {
        $this->execute('behat_general::i_wait_seconds', 2);
        $this->execute('behat_general::i_type', $varname);
        $this->execute('behat_general::i_type', " = ");
        $this->execute('behat_general::i_type', $value);
        $this->execute('behat_general::i_press_named_key', ['', 'enter']);
    }

    /**
     * @Then Feedback should exist
     */
    public function feedback_should_exist() {
        $session = $this->getSession();
        $readonly = $session->getPage()->find('css', '.feedback');
        if (empty($readonly)) {
            throw new Exception('Readonly field not found.');
        }
    }

    /**
     * @Then Generalfeedback should exist
     */
    public function generalfeedback_should_exist() {
        $session = $this->getSession();
        $readonly = $session->getPage()->find('css', '.generalfeedback');
        if (empty($readonly)) {
            throw new Exception('Readonly field not found.');
        }
    }

    /**
     * Clears all the content in a focused field.
     *
     * @When I clear the field
     */
    public function i_clear_the_field() {
        $this->getSession()->executeScript('this.value=""');
    }
}
