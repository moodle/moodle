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
     * Sets the value(s) of a select element.
     *
     * Seems an easy select, but there are lots of combinations
     * of browsers and operative systems and each one manages the
     * autosubmits and the multiple option selects in a different way.
     *
     * @param string $value plain value or comma separated values if multiple. Commas in values escaped with backslash.
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

        // Is the select multiple?
        $multiple = $this->field->hasAttribute('multiple');

        // By default, assume the passed value is a non-multiple option.
        $options = array(trim($value));

        // Here we select the option(s).
        if ($multiple) {
            // Split and decode values. Comma separated list of values allowed. With valuable commas escaped with backslash.
            $options = preg_replace('/\\\,/', ',',  preg_split('/(?<!\\\),/', $value));
            // This is a multiple select, let's pass the multiple flag after first option.
            $afterfirstoption = false;
            foreach ($options as $option) {
                $this->field->selectOption(trim($option), $afterfirstoption);
                $afterfirstoption = true;
            }
        } else {
            // This is a single select, let's pass the last one specified.
            $this->field->selectOption(end($options));
        }

        // With JS disabled this is enough and we finish here.
        if (!$this->running_javascript()) {
            return;
        }

        // With JS enabled we add more clicks as some selenium
        // drivers requires it to fire JS events.

        // In some browsers the selectOption actions can perform a form submit or reload page
        // so we need to ensure the element is still available to continue interacting
        // with it. We don't wait here.
        // getXpath() does not send a query to selenium, so we don't need to wrap it in a try & catch.
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

        // Wait for all the possible AJAX requests that have been
        // already triggered by selectOption() to be finished.
        $this->session->wait(behat_base::TIMEOUT * 1000, behat_base::PAGE_READY_JS);

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

        // Single select sometimes needs an extra click in the option.
        if (!$multiple) {

            // Var $options only contains 1 option.
            $optionxpath = $this->get_option_xpath(end($options), $selectxpath);

            // Using the driver direcly because Element methods are messy when dealing
            // with elements inside containers.
            if ($optionnodes = $this->session->getDriver()->find($optionxpath)) {

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

            // We also check that the option(s) are still there. We neither wait.
            foreach ($options as $option) {
                $optionxpath = $this->get_option_xpath($option, $selectxpath);
                if (!$this->session->getDriver()->find($optionxpath)) {
                    return;
                }
            }

            // Wait for all the possible AJAX requests that have been
            // already triggered by clicking on the field to be finished.
            $this->session->wait(behat_base::TIMEOUT * 1000, behat_base::PAGE_READY_JS);

            // Wrapped in a try & catch as we can fall into race conditions
            // and the element may not be there.
            try {

                // Repeating the select(s) as some drivers (chrome that I know) are moving
                // to another option after the general select field click above.
                $afterfirstoption = false;
                foreach ($options as $option) {
                    $this->field->selectOption(trim($option), $afterfirstoption);
                    $afterfirstoption = true;
                }
            } catch (Exception $e) {
                // We continue and return as this means that the element is not there or it is not the same.
                return;
            }
        }
    }

    /**
     * Returns the text of the currently selected options.
     *
     * @return string Comma separated if multiple options are selected. Commas in option texts escaped with backslash.
     */
    public function get_value() {
        return $this->get_selected_options();
    }

    /**
     * Returns whether the provided argument matches the current value.
     *
     * @param mixed $expectedvalue
     * @return bool
     */
    public function matches($expectedvalue) {

        $multiple = $this->field->hasAttribute('multiple');

        // Same implementation as the parent if it is a single select.
        if (!$multiple) {
            $cleanexpectedvalue = trim($expectedvalue);
            $selectedtext = trim($this->get_selected_options());
            $selectedvalue = trim($this->get_selected_options(false));
            if ($cleanexpectedvalue != $selectedvalue && $cleanexpectedvalue != $selectedtext) {
                return false;
            }
            return true;
        }

        // We are dealing with a multi-select.

        // Can pass multiple comma separated, with valuable commas escaped with backslash.
        $expectedarr = array(); // Array of passed text options to test.

        // Unescape + trim all options and flip it to have the expected values as keys.
        $expectedoptions = $this->get_unescaped_options($expectedvalue);

        // Get currently selected option's texts.
        $texts = $this->get_selected_options(true);
        $selectedoptiontexts = $this->get_unescaped_options($texts);

        // Get currently selected option's values.
        $values = $this->get_selected_options(false);
        $selectedoptionvalues = $this->get_unescaped_options($values);

        // Precheck to speed things up.
        if (count($expectedoptions) !== count($selectedoptiontexts) ||
                count($expectedoptions) !== count($selectedoptionvalues)) {
            return false;
        }

        // We check against string-ordered lists of options.
        if ($expectedoptions != $selectedoptiontexts &&
                $expectedoptions != $selectedoptionvalues) {
            return false;
        }

        return true;
    }

    /**
     * Cleans the list of options and returns it as a string separating options with |||.
     *
     * @param string $value The string containing the escaped options.
     * @return string The options
     */
    protected function get_unescaped_options($value) {

        // Can be multiple comma separated, with valuable commas escaped with backslash.
        $optionsarray = array_map(
            'trim',
            preg_replace('/\\\,/', ',',
                preg_split('/(?<!\\\),/', $value)
           )
        );

        // Sort by value (keeping the keys is irrelevant).
        core_collator::asort($optionsarray, SORT_STRING);

        // Returning it as a string which is easier to match against other values.
        return implode('|||', $optionsarray);
    }

    /**
     * Returns the field selected values.
     *
     * Externalized from the common behat_form_field API method get_value() as
     * matches() needs to check against both values and texts.
     *
     * @param bool $returntexts Returns the options texts or the options values.
     * @return string
     */
    protected function get_selected_options($returntexts = true) {

        $method = 'getText';
        if ($returntexts === false) {
            $method = 'getValue';
        }

        // Is the select multiple?
        $multiple = $this->field->hasAttribute('multiple');

        $selectedoptions = array(); // To accumulate found selected options.

        // Selenium getValue() implementation breaks - separates - values having
        // commas within them, so we'll be looking for options with the 'selected' attribute instead.
        if ($this->running_javascript()) {
            // Get all the options in the select and extract their value/text pairs.
            $alloptions = $this->field->findAll('xpath', '//option');
            foreach ($alloptions as $option) {
                // Is it selected?
                if ($option->hasAttribute('selected')) {
                    if ($multiple) {
                        // If the select is multiple, text commas must be encoded.
                        $selectedoptions[] = trim(str_replace(',', '\,', $option->{$method}()));
                    } else {
                        $selectedoptions[] = trim($option->{$method}());
                    }
                }
            }

        } else {
            // Goutte does not keep the 'selected' attribute updated, but its getValue() returns
            // the selected elements correctly, also those having commas within them.

            // Goutte returns the values as an array or as a string depending
            // on whether multiple options are selected or not.
            $values = $this->field->getValue();
            if (!is_array($values)) {
                $values = array($values);
            }

            // Get all the options in the select and extract their value/text pairs.
            $alloptions = $this->field->findAll('xpath', '//option');
            foreach ($alloptions as $option) {
                // Is it selected?
                if (in_array($option->getValue(), $values)) {
                    if ($multiple) {
                        // If the select is multiple, text commas must be encoded.
                        $selectedoptions[] = trim(str_replace(',', '\,', $option->{$method}()));
                    } else {
                        $selectedoptions[] = trim($option->{$method}());
                    }
                }
            }
        }

        return implode(', ', $selectedoptions);
    }

    /**
     * Returns the opton XPath based on it's select xpath.
     *
     * @param string $option
     * @param string $selectxpath
     * @return string xpath
     */
    protected function get_option_xpath($option, $selectxpath) {
        $valueliteral = $this->session->getSelectorsHandler()->xpathLiteral(trim($option));
        return $selectxpath . "/descendant::option[(./@value=$valueliteral or normalize-space(.)=$valueliteral)]";
    }
}
