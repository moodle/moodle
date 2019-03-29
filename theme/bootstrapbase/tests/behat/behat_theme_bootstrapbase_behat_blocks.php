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
 * Steps definitions related with blocks.
 *
 * @package    theme_bootstrapbase
 * @category   test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../blocks/tests/behat/behat_blocks.php');

/**
 * Blocks management steps definitions.
 *
 * @package    theme_bootstrapbase
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_bootstrapbase_behat_blocks extends behat_blocks {

    public function i_add_the_block($blockname) {
        $this->execute('behat_forms::i_set_the_field_to',
                array("bui_addblock", $this->escape($blockname))
        );

        // If we are running without javascript we need to submit the form.
        if (!$this->running_javascript()) {
            $this->execute('behat_general::i_click_on_in_the',
                    array(get_string('go'), "button", "#add_block", "css_element")
            );
        }
    }

    public function i_open_the_blocks_action_menu($blockname) {

        if (!$this->running_javascript()) {
            // Action menu does not need to be open if Javascript is off.
            return;
        }

        // If it is already opened we do nothing.
        $blocknode = $this->get_text_selector_node('block', $blockname);
        if ($blocknode->hasClass('action-menu-shown')) {
            return;
        }

        $this->execute('behat_general::i_click_on_in_the',
                array(get_string('actions'), "link", $this->escape($blockname), "block")
        );
    }

    public function the_add_block_selector_should_contain_block($blockname) {
        $this->execute('behat_forms::the_select_box_should_contain', [get_string('addblock'), $blockname]);
    }

    public function the_add_block_selector_should_not_contain_block($blockname) {
        $this->execute('behat_forms::the_select_box_should_not_contain', [get_string('addblock'), $blockname]);
    }

}
