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
 * Custom interaction with inplace editable elements.
 *
 * @package    core_form
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_text.php');

/**
 * Custom interaction with inplace editable elements.
 *
 * @package    core_form
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_inplaceeditable extends behat_form_text {
    /**
     * Sets the value to a field.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {
        // Require JS to run this step.
        self::require_javascript();

        // Click to enable editing.
        self::execute(
            'behat_general::i_click_on_in_the',
            [
                '[data-inplaceeditablelink]',
                'css_element',
                $this->field,
                'NodeElement',
            ]
        );

        // Note: It is not possible to use the NodeElement->keyDown() and related functions because
        // this can trigger a focusOnElement call each time.
        // Instead use the behat_base::type_keys() function.

        // The inplace editable selects all existing content on focus.
        // Clear the existing value.
        self::type_keys($this->session, [behat_keys::BACKSPACE]);

        // Type in the new value, followed by ENTER to save the value.
        self::type_keys($this->session, array_merge(
            str_split($value),
            [behat_keys::ENTER]
        ));
    }
}
