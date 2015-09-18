<?php
// This file is part of Stack - http://stack.bham.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Behat steps definitions for the select missing words question type.
 *
 * @package   qtype_gapselect
 * @category  test
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related with for the select missing words question type.
 *
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_gapselect extends behat_base {

    /**
     * Get the xpath for a given missing word.
     * @param string $spacenumber the number of select menu.
     * @return string the xpath expression.
     */
    protected function space_xpath($spacenumber) {
        $class = 'place' . $spacenumber;
        return "//select[contains(@class, ' $class')]";
    }

    /**
     * Drag the drag item with the given text to the given space.
     *
     * @param int $spacenumber the number of the gap to drop into.
     * @param string $value the text of the response to select.
     *
     * @Given /^I set space "(?P<space_number>\d+)" to "(?P<value>[^"]*)" in the select missing words question$/
     */
    public function i_set_space_to_in_the_select_missing_words_question($spacenumber, $value) {
        $formscontext = behat_context_helper::get('behat_forms');
        $formscontext->i_set_the_field_with_xpath_to($this->space_xpath($spacenumber), $value);
    }
}
