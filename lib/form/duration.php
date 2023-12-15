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
 * Duration form element
 *
 * Contains class to create length of time for element.
 *
 * @package   core_form
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/group.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/text.php');

/**
 * Duration element
 *
 * HTML class for a length of time. For example, 30 minutes of 4 days. The
 * values returned to PHP is the duration in seconds (an int rounded to the nearest second).
 *
 * @package   core_form
 * @category  form
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_duration extends MoodleQuickForm_group {
    /**
     * Control the field names for form elements
     * optional => if true, show a checkbox beside the element to turn it on (or off)
     * defaultunit => which unit is default when the form is blank (default Minutes).
     * @var array
     */
    protected $_options = ['optional' => false, 'defaultunit' => MINSECS];

    /** @var array associative array of time units (days, hours, minutes, seconds) */
    private $_units = null;

   /**
    * constructor
    *
    * @param ?string $elementName Element's name
    * @param mixed $elementLabel Label(s) for an element
    * @param array $options Options to control the element's display. Recognised values are
    *      'optional' => true/false - whether to display an 'enabled' checkbox next to the element.
    *      'defaultunit' => 1|MINSECS|HOURSECS|DAYSECS|WEEKSECS - the default unit to display when
    *              the time is blank. If not specified, minutes is used.
    *      'units' => array containing some or all of 1, MINSECS, HOURSECS, DAYSECS and WEEKSECS
    *              which unit choices to offer.
    * @param mixed $attributes Either a typical HTML attribute string or an associative array
    */
    public function __construct($elementName = null, $elementLabel = null,
            $options = [], $attributes = null) {
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'duration';

        // Set the options, do not bother setting bogus ones
        if (!is_array($options)) {
            $options = [];
        }
        $this->_options['optional'] = !empty($options['optional']);
        if (isset($options['defaultunit'])) {
            if (!array_key_exists($options['defaultunit'], $this->get_units())) {
                throw new coding_exception($options['defaultunit'] .
                        ' is not a recognised unit in MoodleQuickForm_duration.');
            }
            $this->_options['defaultunit'] = $options['defaultunit'];
        }
        if (isset($options['units'])) {
            if (!is_array($options['units'])) {
                throw new coding_exception(
                        'When creating a duration form field, units option must be an array.');
            }
            // Validate and register requested units.
            $availableunits = $this->get_units();
            $displayunits = [];
            foreach ($options['units'] as $requestedunit) {
                if (!isset($availableunits[$requestedunit])) {
                    throw new coding_exception($requestedunit .
                            ' is not a recognised unit in MoodleQuickForm_duration.');
                }
                $displayunits[$requestedunit] = $availableunits[$requestedunit];
            }
            krsort($displayunits, SORT_NUMERIC);
            $this->_options['units'] = $displayunits;
        }
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_duration($elementName = null, $elementLabel = null,
            $options = [], $attributes = null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * Returns time associative array of unit length.
     *
     * @return array unit length in seconds => string unit name.
     */
    public function get_units() {
        if (is_null($this->_units)) {
            $this->_units = [
                WEEKSECS => get_string('weeks'),
                DAYSECS => get_string('days'),
                HOURSECS => get_string('hours'),
                MINSECS => get_string('minutes'),
                1 => get_string('seconds'),
            ];
        }
        return $this->_units;
    }

    /**
     * Get the units to be used for this field.
     *
     * The ones specified in the options passed to the constructor, or all by default.
     *
     * @return array number of seconds => lang string.
     */
    protected function get_units_used() {
        if (!empty($this->_options['units'])) {
            return $this->_options['units'];
        } else {
            return $this->get_units();
        }
    }

    /**
     * Converts seconds to the best possible time unit. for example
     * 1800 -> [30, MINSECS] = 30 minutes.
     *
     * @param int $seconds an amout of time in seconds.
     * @return array associative array ($number => $unit)
     */
    public function seconds_to_unit($seconds) {
        if (empty($seconds)) {
            return [0, $this->_options['defaultunit']];
        }
        foreach ($this->get_units_used() as $unit => $notused) {
            if (fmod($seconds, $unit) == 0) {
                return [$seconds / $unit, $unit];
            }
        }
        return [$seconds, 1];
    }

    /**
     * Override of standard quickforms method to create this element.
     */
    function _createElements() {
        $attributes = $this->getAttributesForFormElement();
        if (!isset($attributes['size'])) {
            $attributes['size'] = 3;
        }
        $this->_elements = [];
        // E_STRICT creating elements without forms is nasty because it internally uses $this
        $number = $this->createFormElement('text', 'number',
                get_string('time', 'form'), $attributes, true);
        $number->set_force_ltr(true);
        $this->_elements[] = $number;
        unset($attributes['size']);
        $this->_elements[] = $this->createFormElement('select', 'timeunit',
                get_string('timeunit', 'form'), $this->get_units_used(), $attributes, true);
        // If optional we add a checkbox which the user can use to turn if on
        if($this->_options['optional']) {
            $this->_elements[] = $this->createFormElement('checkbox', 'enabled', null,
                    get_string('enable'), $attributes, true);
        }
        foreach ($this->_elements as $element){
            if (method_exists($element, 'setHiddenLabel')){
                $element->setHiddenLabel(true);
            }
        }
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param MoodleQuickForm $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller) {
        $this->setMoodleForm($caller);
        switch ($event) {
            case 'updateValue':
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    // if no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case
                    if ($caller->isSubmitted() && !$caller->is_new_repeat($this->getName())) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (!is_array($value)) {
                    list($number, $unit) = $this->seconds_to_unit($value);
                    $value = ['number' => $number, 'timeunit' => $unit];
                    // If optional, default to off, unless a date was provided
                    if ($this->_options['optional']) {
                        $value['enabled'] = $number != 0;
                    }
                } else {
                    $value['enabled'] = isset($value['enabled']);
                }
                if (null !== $value){
                    $this->setValue($value);
                }
                break;

            case 'createElement':
                if (!empty($arg[2]['optional'])) {
                    $caller->disabledIf($arg[0], $arg[0] . '[enabled]');
                }
                $caller->setType($arg[0] . '[number]', PARAM_FLOAT);
                return parent::onQuickFormEvent($event, $arg, $caller);

            default:
                return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    /**
     * Returns HTML for advchecbox form element.
     *
     * @return string
     */
    function toHtml() {
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer = new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);
        return $renderer->toHtml();
    }

    /**
     * Accepts a renderer
     *
     * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
     * @param bool $required Whether a group is required
     * @param ?string $error An error message associated with a group
     */
    function accept(&$renderer, $required = false, $error = null) {
        $renderer->renderElement($this, $required, $error);
    }

    /**
     * Output a timestamp. Give it the name of the group.
     * Override of standard quickforms method.
     *
     * @param  array $submitValues
     * @param  bool  $assoc  whether to return the value as associative array
     * @return array field name => value. The value is the time interval in seconds.
     */
    function exportValue(&$submitValues, $assoc = false) {
        // Get the values from all the child elements.
        $valuearray = [];
        foreach ($this->_elements as $element) {
            $thisexport = $element->exportValue($submitValues[$this->getName()], true);
            if (!is_null($thisexport)) {
                $valuearray += $thisexport;
            }
        }

        // Convert the value to an integer number of seconds.
        if (empty($valuearray)) {
            return null;
        }
        if ($this->_options['optional'] && empty($valuearray['enabled'])) {
            return $this->_prepareValue(0, $assoc);
        }
        return $this->_prepareValue(
                (int) round($valuearray['number'] * $valuearray['timeunit']), $assoc);
    }
}
