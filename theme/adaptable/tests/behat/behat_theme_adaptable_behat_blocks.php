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
 * Overrides for behat blocks.
 *
 * @package   theme_adaptable
 * @copyright Copyright (c) 2020 Titus
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException,
    Behat\Mink\Element\NodeElement;

require_once(__DIR__ . '/../../../../blocks/tests/behat/behat_blocks.php');

/**
 * Overrides to make behat block steps work with adaptable.
 *
 * @package   theme_adaptable
 * @copyright Copyright (c) 2020 Titus.
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class behat_theme_adaptable_behat_blocks extends behat_blocks {
    /**
     * Adds the selected block. Editing mode must be previously enabled.
     *
     * @param string $blockname
     * @return void
     */
    public function i_add_the_block($blockname) {
        $this->execute(
            'behat_forms::i_set_the_field_to',
            ["bui_addblock", $this->escape($blockname)]
        );

        // If we are running without javascript we need to submit the form.
        if (!$this->running_javascript()) {
            $this->execute(
                'behat_general::i_click_on_in_the',
                [get_string('go'), "button", "#add_block", "css_element"]
            );
        }
    }
}
