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
 * Single select form field class.
 *
 * @package    core_form
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_field.php');

/**
 * Single select form field.
 *
 * @package    core_form
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_select extends behat_form_field {

    /**
     * Sets the value of a single select.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {
        $this->field->selectOption($value);

        // Adding a click as Selenium requires it to fire some JS events.
        if ($this->running_javascript()) {

            // In some browsers the selectOption actions can perform a page reload
            // so we need to ensure the element is still available to continue interacting
            // with it. We don't wait here.
            if (!$this->session->getDriver()->find($this->field->getXpath())) {
                return;
            }

            // Single select needs an extra click in the option.
            if (!$this->field->hasAttribute('multiple')) {

                $value = $this->session->getSelectorsHandler()->xpathLiteral($value);

                // Using the driver direcly because Element methods are messy when dealing
                // with elements inside containers.
                $optionxpath = $this->field->getXpath() .
                    "/descendant::option[(./@value=$value or normalize-space(.)=$value)]";
                $optionnodes = $this->session->getDriver()->find($optionxpath);
                if ($optionnodes) {
                    current($optionnodes)->click();
                }

            } else {
                // Multiple ones needs the click in the select.
                $this->field->click();
            }
        }
    }

    /**
     * Returns the text of the current value.
     *
     * @return string
     */
    public function get_value() {
        $selectedoption = $this->field->find('xpath', '//option[@selected="selected"]');
        return $selectedoption->getText();
    }
}
