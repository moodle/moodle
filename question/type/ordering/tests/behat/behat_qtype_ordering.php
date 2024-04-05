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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Behat steps definitions for the ordering question type.
 *
 * @package   qtype_ordering
 * @category  test
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_ordering extends behat_base {
    /**
     * Get the xpath for a given item by label.
     * @param string $label the text of the item to drag.
     * @return string the xpath expression.
     */
    protected function item_xpath_by_label(string $label): string {
        return "//li[contains(concat(' ', normalize-space(@class), ' '), 'sortableitem') and contains(normalize-space(.), '" .
            $this->escape($label) . "')]";
    }

    /**
     * Get the xpath for a given drop box.
     * @param string $position the number of place to drop it.
     * @return string the xpath expression.
     */
    protected function item_xpath_by_position(string $position): string {
        return "//li[contains(concat(' ', normalize-space(@class), ' '), 'sortableitem')][" . $position . "]";
    }

    /**
     * Drag the drag item with the given text to the given space.
     *
     * Warning, only works if the question is using a behaviour like Immediate
     * feedback that has a check button.
     *
     * Also, do not use this to drag an item to the last place. Just drag all
     * the other non-last items to their place.
     *
     * @param string $label the text of the item to drag.
     * @param int $position the number of the position to drop it at.
     *
     * @Given /^I drag "(?P<label>[^"]*)" to space "(?P<position>\d+)" in the ordering question$/
     */
    public function i_drag_to_space_in_the_drag_and_drop_into_text_question(string $label, int $position): void {

        $testingpos = $position - 1; // 0-based index.

        $generalcontext = behat_context_helper::get('behat_general');

        $this->execute_script("
                (function() {
                    var droptarget = document.createElement('li');
                    droptarget.setAttribute('class', 'dtb');
                    var items = document.querySelector('.sortablelist');
                    items.insertBefore(droptarget, items.children[$testingpos]);
                }())"
        );

        $generalcontext->i_drag_and_i_drop_it_in(
            $this->item_xpath_by_label($label),
            'xpath_element',
            "//li[contains(concat(' ', normalize-space(@class), ' '), 'dtb')]",
            'xpath_element'
        );

        $this->execute_script("
                (function() {
                    var item = document.querySelector('.dtb');
                    item.parentNode.removeChild(item);
                }())"
        );
    }
}
