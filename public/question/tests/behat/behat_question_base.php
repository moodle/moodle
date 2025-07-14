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
 * Behat question-related helper code.
 *
 * @package    core_question
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode,
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
class behat_question_base extends behat_base {

    /**
     * Helper used by {@link i_add_a_question_filling_the_form_with()} and
     * {@link behat_mod_quiz::i_add_question_to_the_quiz_with to finish creating()}.
     *
     * @param string $questiontypename The question type name
     * @param TableNode $questiondata The data to fill the question type form
     */
    protected function finish_adding_question($questiontypename, TableNode $questiondata) {

        $this->execute('behat_forms::i_set_the_field_to', [$this->escape($questiontypename), 1]);
        $this->execute("behat_general::i_click_on", ['.submitbutton', "css_element"]);

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $questiondata);
        $this->execute("behat_forms::press_button", 'id_submitbutton');
    }
}
