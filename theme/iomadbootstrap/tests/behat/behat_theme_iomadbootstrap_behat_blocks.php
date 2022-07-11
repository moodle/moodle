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
 * Step definitions related to blocks overrides for the Classic theme.
 *
 * @package    theme_iomadbootstrap
 * @category   test
 * @copyright  2022 Derick Turner
 * @author    Derick Turner
 * @based on theme_clean by Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../blocks/tests/behat/behat_blocks.php');

/**
 * Blocks management step definitions for the Classic theme.
 *
 * @package    theme_iomadbootstrap
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_iomadbootstrap_behat_blocks extends behat_blocks {

    /**
     * Adds the selected block. Editing mode must be previously enabled.
     *
     * @param string $blockname
     * @return void
     */
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

    /**
     * Ensures that block can be added to the page, but does not add it.
     *
     * @param string $blockname
     * @return void
     */
    public function the_add_block_selector_should_contain_block($blockname) {
        $this->execute('behat_forms::the_select_box_should_contain', [get_string('addblock'), $blockname]);
    }

    /**
     * Ensures that block cannot be added to the page.
     *
     * @param string $blockname
     * @return void
     */
    public function the_add_block_selector_should_not_contain_block($blockname) {
        $this->execute('behat_forms::the_select_box_should_not_contain', [get_string('addblock'), $blockname]);
    }
}
