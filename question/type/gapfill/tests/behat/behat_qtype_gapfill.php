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
 * Behat steps definitions for gapfill drag drop into text.
 *
 * @package   qtype_gapfill
 * @category  test
 * @copyright 2016 Marcus Green
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related with the drag and drop into text question type.
 *
 * @copyright 2016 Marcus Green
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_gapfill extends behat_base {

    /**
     * Get the xpath for a given gap
     * @param string $gapnumber the gap id number
     * @return string the xpath expression.
     */
    protected function drop_xpath($gapnumber) {
        return '//input[contains(@class, "droptarget ") and contains(@id, "_p' . $gapnumber . '")]';
    }

    /**
     * Get the xpath for a given drag item.
     * @param string $dragitem the text of the item to drag.
     * @return string the xpath expression.
     */
    protected function drag_xpath($dragitem) {
        return '//span[(contains(@class,"draggable ") and contains(text(),"' . $dragitem . '"))]';
    }

    /**
     *  Drag an answer option into a given gap.
     *
     * @param string $dragitem  a potential answer
     * @param int $gapnumber the number of the gap to type into.
     *
     * @When I drag :dragitem into gap :gapnumber in the gapfill question
     */
    public function i_drag_into_gap_in_the_gapfill_question($dragitem, $gapnumber) {
        $generalcontext = behat_context_helper::get('behat_general');
        $generalcontext->i_drag_and_i_drop_it_in($this->drag_xpath($dragitem), 'xpath_element',
                $this->drop_xpath($gapnumber), 'xpath_element');
    }

    /**
     * Type some characters while focused on a given gap.
     *
     * @param string $gapresponse the text to enter into the gap
     * @param int $gapnumber the number of the gap to type into.
     *
     * @Given /^I type "(?P<keys>[^"]*)" into gap "(?P<gap_number>\d+)" in the gapfill question$/
     */
    public function i_type_into_gap_in_the_gapfill_question($gapresponse, $gapnumber) {
        $xpath = $this->drop_xpath($gapnumber);
        $this->execute('behat_forms::i_set_the_field_with_xpath_to', array($xpath, $gapresponse));
    }

}
