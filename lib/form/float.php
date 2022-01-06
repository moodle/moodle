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
 * Float type form element
 *
 * Contains HTML class for a float type element
 *
 * @package   core_form
 * @category  form
 * @copyright 2019 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/text.php');

/**
 * Float type form element.
 *
 * This is preferred over the text element when working with float numbers, and takes care of the fact that different languages
 * may use different symbols as the decimal separator.
 * Using this element, submitted float numbers will be automatically translated from the localised format into the computer format,
 * and vice versa when they are being displayed.
 *
 * @copyright 2019 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_float extends MoodleQuickForm_text {

    /**
     * MoodleQuickForm_float constructor.
     *
     * @param string $elementName (optional) name of the float field
     * @param string $elementLabel (optional) float field label
     * @param string $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null) {
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_type = 'float';
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element.
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        switch ($event) {
            case 'updateValue':
                if ($value = $this->_findValue($caller->_constantValues)) {
                    $value = $this->format_float($value);
                }
                if (null === $value) {
                    $value = $this->_findValue($caller->_submitValues);
                    if (null === $value) {
                        if ($value = $this->_findValue($caller->_defaultValues)) {
                            $value = $this->format_float($value);
                        }
                    }
                }
                if (null !== $value) {
                    parent::setValue($value);
                }
                return true;
            case 'createElement':
                $caller->setType($arg[0], PARAM_RAW_TRIMMED);
            default:
                return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    /**
     * Checks that the submitted value is a valid float number.
     *
     * @param string $value The localised float number that is submitted.
     * @return string|null Validation error message or null.
     */
    public function validateSubmitValue($value) {
        if (false === unformat_float($value, true)) {
            return get_string('err_numeric', 'core_form');
        }
    }

    /**
     * Sets the value of the form element.
     *
     * @param string $value Default value of the form element
     */
    public function setValue($value) {
        $value = $this->format_float($value);
        parent::setValue($value);
    }

    /**
     * Returns the value of the form element.
     *
     * @return false|float
     */
    public function getValue() {
        $value = parent::getValue();
        if ($value) {
            $value = unformat_float($value, true);
        }
        return $value;
    }

    /**
     * Returns a 'safe' element's value.
     *
     * @param  array   $submitValues array of submitted values to search
     * @param  bool    $assoc whether to return the value as associative array
     * @return mixed
     */
    public function exportValue(&$submitValues, $assoc = false) {
        $value = $this->_findValue($submitValues);
        if (null === $value) {
            $value = $this->getValue();
        } else if ($value) {
            $value = unformat_float($value, true);
        }
        return $this->_prepareValue($value, $assoc);
    }

    /**
     * Used by getFrozenHtml() to pass the element's value if _persistantFreeze is on.
     *
     * @return string
     */
    public function _getPersistantData() {
        if (!$this->_persistantFreeze) {
            return '';
        } else {
            $id = $this->getAttribute('id');
            if (isset($id)) {
                // Id of persistant input is different then the actual input.
                $id = array('id' => $id . '_persistant');
            } else {
                $id = array();
            }

            return '<input' . $this->_getAttrString(array(
                        'type'  => 'hidden',
                        'name'  => $this->getAttribute('name'),
                        'value' => $this->getAttribute('value')
                    ) + $id) . ' />';
        }
    }

    /**
     * Given a float, prints it nicely.
     * This function reserves the number of decimal places.
     *
     * @param float|null $value The float number to format
     * @return string Localised float
     */
    private function format_float($value) {
        if (is_numeric($value)) {
            // We want to keep trailing zeros after the decimal point if there is any.
            // Therefore we cannot just call format_float() and pass -1 as the number of decimal points.
            $pieces = preg_split('/E/i', $value); // In case it is in the scientific format.
            $decimalpos = strpos($pieces[0], '.');
            if ($decimalpos !== false) {
                $decimalpart = substr($pieces[0], $decimalpos + 1);
                $decimals = strlen($decimalpart);
            } else {
                $decimals = 0;
            }
            $pieces[0] = format_float($pieces[0], $decimals);
            $value = implode('E', $pieces);
        }
        return $value;
    }
}
