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
 * Date form field class.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_group.php');

use Behat\Mink\Exception\ExpectationException;

/**
 * Date form field.
 *
 * This class will be refactored in case we are interested in
 * creating more complex formats to fill date and date-time fields.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_date extends behat_form_group {

    /**
     * Sets the value to a date field.
     *
     * @param string $value The value to be assigned to the date selector field. The string value must be either
     *                      parsable into a UNIX timestamp or equal to 'disabled' (if disabling the date selector).
     * @return void
     * @throws ExpectationException If the value is invalid.
     */
    public function set_value($value) {

        if ($value === 'disabled') {
            // Disable the given date selector field.
            $this->set_child_field_value('enabled', false);
        } else if (is_numeric($value)) { // The value is numeric (unix timestamp).
            // Assign the mapped values to each form element in the date selector field.
            foreach ($this->get_mapped_fields($value) as $childname => $childvalue) {
                $this->set_child_field_value($childname, $childvalue);
            }
        } else { // Invalid value.
            // Get the name of the field.
            $fieldname = $this->field->find('css', 'legend')->getHtml();
            throw new ExpectationException("Invalid value for '{$fieldname}'", $this->session);
        }
    }

    /**
     * Returns the date field identifiers and the values that should be assigned to them.
     *
     * @param int $timestamp The UNIX timestamp
     * @return array
     */
    protected function get_mapped_fields(int $timestamp): array {
        return [
            'enabled' => true,
            'day' => date('j', $timestamp),
            'month' => date('n', $timestamp),
            'year' => date('Y', $timestamp),
        ];
    }

    /**
     * Sets a value to a child element in the date form field.
     *
     * @param string $childname The name of the child field
     * @param string|bool $childvalue The value
     */
    private function set_child_field_value(string $childname, $childvalue) {
        // Find the given child form element in the date selector field.
        $childelement = $this->field->find('css', "*[name$='[{$childname}]']");
        if ($childelement) {
            // Get the field instance for the given child form element.
            $childinstance = $this->get_field_instance_for_element($childelement);
            // Set the value to the child form element.
            $childinstance->set_value($childvalue);
        }
    }
}
