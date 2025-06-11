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
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Element\NodeElement as NodeElement;

require_once(__DIR__ . '/../../../../blocks/tests/behat/behat_blocks.php');

/**
 * Overrides to make behat block steps work with Snap.
 *
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_behat_blocks extends behat_blocks {

    public function i_add_the_block($blockname) {
        // Enter block editing mode before adding tbe block, then leave it.
        // Core tests expect you to have enabled edit mode in advance, but Snap
        // does this differently.
        $helper = behat_context_helper::get('behat_general');
        $helper->i_click_on('.editmode-switch-form', 'css_element');
        $helper->click_link("Course Dashboard");
        parent::i_add_the_block($blockname);
        $helper->i_click_on('.editmode-switch-form', 'css_element');
    }
}
