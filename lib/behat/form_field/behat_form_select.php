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
     * Seems an easy select, but there are lots of combinations
     * of browsers and operative systems and each one manages the
     * autosubmits and the multiple option selects in a diferent way.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {

        // In some browsers we select an option and it triggers all the
        // autosubmits and works as expected but not in all of them, so we
        // try to catch all the possibilities to make this function work as
        // expected.

        // Get the internal id of the element we are going to click.
        // This kind of internal IDs are only available in the selenium wire
        // protocol, so only available using selenium drivers, phantomjs and family.
        if ($this->running_javascript()) {
            $currentelementid = $this->get_internal_field_id();
        }

        // Here we select an option.
        $this->field->selectOption($value);

        // With JS disabled this is enough and we finish here.
        if (!$this->running_javascript()) {
            return;
        }

        // With JS enabled we add more clicks as some selenium
        // drivers requires it to fire JS events.

        // In some browsers the selectOption actions can perform a form submit or reload page
        // so we need to ensure the element is still available to continue interacting
        // with it. We don't wait here.
        $selectxpath = $this->field->getXpath();
        if (!$this->session->getDriver()->find($selectxpath)) {
            return;
        }

        // We also check the selenium internal element id, if it have changed
        // we are dealing with an autosubmit that was already executed, and we don't to
        // execute anything else as the action we wanted was already performed.
        if ($currentelementid != $this->get_internal_field_id()) {
            return;
        }

        // We also check that the option is still there. We neither wait.
        $valueliteral = $this->session->getSelectorsHandler()->xpathLiteral($value);
        $optionxpath = $selectxpath . "/descendant::option[(./@value=$valueliteral or normalize-space(.)=$valueliteral)]";
        if (!$this->session->getDriver()->find($optionxpath)) {
            return;
        }

        // Wrapped in try & catch as the element may disappear if an AJAX request was submitted.
        try {
            $multiple = $this->field->hasAttribute('multiple');
        } catch (Exception $e) {
            // We do not specify any specific Exception type as there are
            // different exceptions that can be thrown by the driver and
            // we can not control them all, also depending on the selenium
            // version the exception type can change.
            return;
        }

        // Wait for all the possible AJAX requests that have been
        // already triggered by selectOption() to be finished.
        $this->session->wait(behat_base::TIMEOUT * 1000, behat_base::PAGE_READY_JS);

        // Single select sometimes needs an extra click in the option.
        if (!$multiple) {

            // Using the driver direcly because Element methods are messy when dealing
            // with elements inside containers.
            $optionnodes = $this->session->getDriver()->find($optionxpath);
            if ($optionnodes) {
                // Wrapped in a try & catch as we can fall into race conditions
                // and the element may not be there.
                try {
                    current($optionnodes)->click();
                } catch (Exception $e) {
                    // We continue and return as this means that the element is not there or it is not the same.
                    return;
                }
            }

        } else {

            // Wrapped in a try & catch as we can fall into race conditions
            // and the element may not be there.
            try {
                // Multiple ones needs the click in the select.
                $this->field->click();
            } catch (Exception $e) {
                // We continue and return as this means that the element is not there or it is not the same.
                return;
            }

            // We ensure that the option is still there.
            if (!$this->session->getDriver()->find($optionxpath)) {
                return;
            }

            // Wait for all the possible AJAX requests that have been
            // already triggered by selectOption() to be finished.
            $this->session->wait(behat_base::TIMEOUT * 1000, behat_base::PAGE_READY_JS);

            // Wrapped in a try & catch as we can fall into race conditions
            // and the element may not be there.
            try {
                // Repeating the select as some drivers (chrome that I know) are moving
                // to another option after the general select field click above.
                $this->field->selectOption($value);
            } catch (Exception $e) {
                // We continue and return as this means that the element is not there or it is not the same.
                return;
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
