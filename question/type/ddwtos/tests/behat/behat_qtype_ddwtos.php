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
 * Behat steps definitions for drag and drop into text.
 *
 * @package   qtype_ddwtos
 * @category  test
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related with the drag and drop into text question type.
 *
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_ddwtos extends behat_base {

    /**
     * Get the xpath for a given drag item.
     * @param string $dragitem the text of the item to drag.
     * @return string the xpath expression.
     */
    protected function drag_xpath($dragitem) {
        return '//span[contains(@class, "draghome") and contains(., "' . $this->escape($dragitem) .
                '") and not(contains(@class, "dragplaceholder"))]';
    }

    /**
     * Get the xpath for a given drop box.
     * @param string $dragitem the number of the drop box.
     * @return string the xpath expression.
     */
    protected function drop_xpath($spacenumber) {
        return '//span[contains(@class, " drop ") and contains(@class, "place' . $spacenumber . ' ")]';
    }

    /**
     * Drag the drag item with the given text to the given space.
     *
     * @param string $dragitem the text of the item to drag.
     * @param int $spacenumber the number of the gap to drop into.
     *
     * @Given /^I drag "(?P<drag_item>[^"]*)" to space "(?P<space_number>\d+)" in the drag and drop into text question$/
     */
    public function i_drag_to_space_in_the_drag_and_drop_into_text_question($dragitem, $spacenumber) {
        $generalcontext = behat_context_helper::get('behat_general');
        $generalcontext->i_drag_and_i_drop_it_in($this->drag_xpath($dragitem),
                'xpath_element', $this->drop_xpath($spacenumber), 'xpath_element');
    }

    /**
     * Type some characters while focussed on a given space.
     *
     * @param string $keys the characters to type.
     * @param int $spacenumber the number of the space to type into.
     *
     * @Given /^I type "(?P<keys>[^"]*)" into space "(?P<space_number>\d+)" in the drag and drop onto image question$/
     */
    public function i_type_into_space_in_the_drag_and_drop_into_text_question($keys, $spacenumber) {
        $node = $this->get_selected_node('xpath_element', $this->drop_xpath($spacenumber));
        $this->ensure_node_is_visible($node);
        foreach (str_split($keys) as $key) {
            $node->keyDown($key);
            $node->keyPress($key);
            $node->keyUp($key);
            $this->wait_for_pending_js();
        }
    }
}
