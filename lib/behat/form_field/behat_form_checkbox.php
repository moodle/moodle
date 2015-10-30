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
 * Single checkbox form element.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_field.php');

/**
 * Checkbox form field.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_checkbox extends behat_form_field {

    /**
     * Sets the value of a checkbox.
     *
     * Anything !empty() is considered checked.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {

        if (!empty($value) && !$this->field->isChecked()) {

            if (!$this->running_javascript()) {
                $this->field->check();
                return;
            }

            // Check it if it should be checked and it is not.
            $this->field->click();

            // Trigger the onchange event as triggered when 'checking' the checkbox.
            $this->session->getDriver()->triggerSynScript(
                $this->field->getXPath(),
                "Syn.trigger('change', {}, {{ELEMENT}})"
            );

        } else if (empty($value) && $this->field->isChecked()) {

            if (!$this->running_javascript()) {
                $this->field->uncheck();
                return;
            }

            // Uncheck if it is checked and shouldn't.
            $this->field->click();

            // Trigger the onchange event as triggered when 'checking' the checkbox.
            $this->session->getDriver()->triggerSynScript(
                $this->field->getXPath(),
                "Syn.trigger('change', {}, {{ELEMENT}})"
            );
        }
    }

    /**
     * Returns whether the field is checked or not.
     *
     * @return bool True if it is checked and false if it's not.
     */
    public function get_value() {
        return $this->field->isChecked();
    }

    /**
     * Is it enabled?
     *
     * @param string $expectedvalue Anything !empty() is considered checked.
     * @return bool
     */
    public function matches($expectedvalue = false) {

        $ischecked = $this->field->isChecked();

        // Any non-empty value provided means that it should be checked.
        if (!empty($expectedvalue) && $ischecked) {
            return true;
        } else if (empty($expectedvalue) && !$ischecked) {
            return true;
        }

        return false;
    }

}
