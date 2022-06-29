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

require_once(__DIR__  . '/behat_form_select.php');

/**
 * Custom interaction with inplace editable elements of type select
 *
 * @package     core_form
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_inplaceeditable_select extends behat_form_select {

    /**
     * Sets the value to a field
     *
     * @param string $value
     */
    public function set_value($value): void {
        // Require JS to run this step.
        self::require_javascript();

        // Enable editing.
        self::execute('behat_general::i_click_on_in_the', [
            '[data-inplaceeditablelink]',
            'css_element',
            $this->field,
            'NodeElement',
        ]);

        // After editing is enabled, set the select field value.
        $select = $this->field->find('css', 'select');
        $select->selectOption($value);
    }
}
