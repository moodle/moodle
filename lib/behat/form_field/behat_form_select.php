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

        // Is the select multiple?
        $multiple = $this->field->hasAttribute('multiple');
        $singleselect = ($this->field->hasClass('singleselect') || $this->field->hasClass('urlselect'));

        // Here we select the option(s).
        if ($multiple) {
            // Split and decode values. Comma separated list of values allowed. With valuable commas escaped with backslash.
            $options = preg_replace('/\\\,/', ',',  preg_split('/(?<!\\\),/', trim($value)));
            // This is a multiple select, let's pass the multiple flag after first option.
            $afterfirstoption = false;
            foreach ($options as $option) {
                $this->field->selectOption(trim($option), $afterfirstoption);
                $afterfirstoption = true;
            }
        } else {
           // By default, assume the passed value is a non-multiple option.
            $this->field->selectOption(trim($value));
       }

        // Wait for all the possible AJAX requests that have been
        // already triggered by selectOption() to be finished.
        if ($this->running_javascript()) {
            // Trigger change event as this is needed by some drivers (Phantomjs). Don't do it for
            // Singleselect as this will cause multiple event fire and lead to race-around condition.
            $browser = \Moodle\BehatExtension\Driver\MoodleSelenium2Driver::getBrowser();
            if (!$singleselect && ($browser == 'phantomjs')) {
                $script = "Syn.trigger('change', {}, {{ELEMENT}})";
                try {
                    $this->session->getDriver()->triggerSynScript($this->field->getXpath(), $script);
                } catch (Exception $e) {
                    // No need to do anything if element has been removed by JS.
                    // This is possible when inline editing element is used.
                }
            }
            $this->session->wait(behat_base::TIMEOUT * 1000, behat_base::PAGE_READY_JS);
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

        $method = 'getHtml';
        if ($returntexts === false) {
            $method = 'getValue';
        }

        // Is the select multiple?
        $multiple = $this->field->hasAttribute('multiple');

        $selectedoptions = array(); // To accumulate found selected options.

        // Driver returns the values as an array or as a string depending
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
        $valueliteral = behat_context_helper::escape(trim($option));
        return $selectxpath . "/descendant::option[(./@value=$valueliteral or normalize-space(.)=$valueliteral)]";
    }
}
