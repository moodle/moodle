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
 * Radio input form element.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_checkbox.php');

/**
 * Radio input form field.
 *
 * Extends behat_form_checkbox as the set_value() behaviour
 * is the same.
 *
 * This form field type can be added to forms as any other
 * moodle form element, but it does not make sense without
 * a group of radio inputs, so is hard to find it alone and
 * detect it by behat_field_manager::get_form_field(), where is useful
 * is when the default behat_form_field class is being used, it
 * finds a input[type=radio] and it delegates set_value() and
 * get_value() to behat_form_radio.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_radio extends behat_form_checkbox {

    /**
     * Returns the radio input value attribute.
     *
     * @return string The value attribute
     */
    public function get_value() {
        return $this->field->getValue();
    }
}
