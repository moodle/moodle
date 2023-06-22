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
 * Group of date and time input element
 *
 * Contains class for a group of elements used to input a date and time.
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/group.php');
require_once($CFG->libdir . '/formslib.php');

/**
 * Element used to input a date and time.
 *
 * Class for a group of elements used to input a date and time.
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_date_time_selector extends MoodleQuickForm_group {

    /**
     * Options for the element.
     *
     * startyear => int start of range of years that can be selected
     * stopyear => int last year that can be selected
     * defaulttime => default time value if the field is currently not set
     * timezone => int|float|string (optional) timezone modifier used for edge case only.
     *      If not specified, then date is caclulated based on current user timezone.
     *      Note: dst will be calculated for string timezones only
     *      {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * step => step to increment minutes by
     * optional => if true, show a checkbox beside the date to turn it on (or off)
     * @var array
     */
    protected $_options = array();

    /**
     * @var array These complement separators, they are appended to the resultant HTML.
     */
    protected $_wrap = array('', '');

    /**
     * @var null|bool Keeps track of whether the date selector was initialised using createElement
     *                or addElement. If true, createElement was used signifying the element has been
     *                added to a group - see MDL-39187.
     */
    protected $_usedcreateelement = true;

    /**
     * Class constructor
     *
     * @param string $elementName Element's name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        // Get the calendar type used - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        $this->_options = array('startyear' => $calendartype->get_min_year(), 'stopyear' => $calendartype->get_max_year(),
            'defaulttime' => 0, 'timezone' => 99, 'step' => 1, 'optional' => false);

        // TODO MDL-52313 Replace with the call to parent::__construct().
        HTML_QuickForm_element::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'date_time_selector';
        // set the options, do not bother setting bogus ones
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (isset($this->_options[$name])) {
                    if (is_array($value) && is_array($this->_options[$name])) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_date_time_selector($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * This will create date group element constisting of day, month and year.
     *
     * @access private
     */
    function _createElements() {
        global $OUTPUT;

        // Get the calendar type used - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        for ($i = 0; $i <= 23; $i++) {
            $hours[$i] = sprintf("%02d", $i);
        }
        for ($i = 0; $i < 60; $i += $this->_options['step']) {
            $minutes[$i] = sprintf("%02d", $i);
        }

        $this->_elements = array();
        // If optional we add a checkbox which the user can use to turn if on.
        if ($this->_options['optional']) {
            $this->_elements[] = $this->createFormElement('checkbox', 'enabled', null,
                get_string('enable'), $this->getAttributes(), true);
        }
        $dateformat = $calendartype->get_date_order($this->_options['startyear'], $this->_options['stopyear']);
        if (right_to_left()) {   // Display time to the right of date, in RTL mode.
            $this->_elements[] = $this->createFormElement('select', 'minute', get_string('minute', 'form'),
                $minutes, $this->getAttributes(), true);
            $this->_elements[] = $this->createFormElement('select', 'hour', get_string('hour', 'form'),
                $hours, $this->getAttributes(), true);
            // Reverse date element (Should be: Day, Month, Year), in RTL mode.
            $dateformat = array_reverse($dateformat);
        }
        foreach ($dateformat as $key => $date) {
            // E_STRICT creating elements without forms is nasty because it internally uses $this
            $this->_elements[] = $this->createFormElement('select', $key, get_string($key, 'form'), $date, $this->getAttributes(), true);
        }
        if (!right_to_left()) {   // Display time to the left of date, in LTR mode.
            $this->_elements[] = $this->createFormElement('select', 'hour', get_string('hour', 'form'), $hours,
                $this->getAttributes(), true);
            $this->_elements[] = $this->createFormElement('select', 'minute', get_string('minute', 'form'), $minutes,
                $this->getAttributes(), true);
        }
        // The YUI2 calendar only supports the gregorian calendar type so only display the calendar image if this is being used.
        if ($calendartype->get_name() === 'gregorian') {
            $image = $OUTPUT->pix_icon('i/calendar', get_string('calendar', 'calendar'), 'moodle');
            $this->_elements[] = $this->createFormElement('link', 'calendar',
                    null, '#', $image);
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
     * @param object $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller) {
        $this->setMoodleForm($caller);
        switch ($event) {
            case 'updateValue':
                // Constant values override both default and submitted ones
                // default values are overriden by submitted.
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    // If no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case.
                    if ($caller->isSubmitted() && !$caller->is_new_repeat($this->getName())) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                $requestvalue=$value;
                if ($value == 0 || $value === '') {
                    $value = $this->_options['defaulttime'];
                    if (!$value) {
                        $value = time();
                    }
                }
                if (!is_array($value)) {
                    $calendartype = \core_calendar\type_factory::get_calendar_instance();
                    $currentdate = $calendartype->timestamp_to_date_array($value, $this->_options['timezone']);
                    // Round minutes to the previous multiple of step.
                    $currentdate['minutes'] -= $currentdate['minutes'] % $this->_options['step'];
                    $value = array(
                        'minute' => $currentdate['minutes'],
                        'hour' => $currentdate['hours'],
                        'day' => $currentdate['mday'],
                        'month' => $currentdate['mon'],
                        'year' => $currentdate['year']);
                    // If optional, default to off, unless a date was provided.
                    if ($this->_options['optional']) {
                        $value['enabled'] = $requestvalue != 0;
                    }
                } else {
                    $value['enabled'] = isset($value['enabled']);
                }
                if (null !== $value) {
                    $this->setValue($value);
                }
                break;
            case 'createElement':
                if (isset($arg[2]['optional']) && $arg[2]['optional']) {
                    // When using the function addElement, rather than createElement, we still
                    // enter this case, making this check necessary.
                    if ($this->_usedcreateelement) {
                        $caller->disabledIf($arg[0] . '[day]', $arg[0] . '[enabled]');
                        $caller->disabledIf($arg[0] . '[month]', $arg[0] . '[enabled]');
                        $caller->disabledIf($arg[0] . '[year]', $arg[0] . '[enabled]');
                        $caller->disabledIf($arg[0] . '[hour]', $arg[0] . '[enabled]');
                        $caller->disabledIf($arg[0] . '[minute]', $arg[0] . '[enabled]');
                    } else {
                        $caller->disabledIf($arg[0], $arg[0] . '[enabled]');
                    }
                }
                return parent::onQuickFormEvent($event, $arg, $caller);
                break;
            case 'addElement':
                $this->_usedcreateelement = false;
                return parent::onQuickFormEvent($event, $arg, $caller);
                break;
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

        $html = $this->_wrap[0];
        if ($this->_usedcreateelement) {
            $html .= html_writer::tag('span', $renderer->toHtml(), array('class' => 'fdate_time_selector'));
        } else {
            $html .= $renderer->toHtml();
        }
        $html .= $this->_wrap[1];

        return $html;
    }

    /**
     * Accepts a renderer
     *
     * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
     * @param bool $required Whether a group is required
     * @param string $error An error message associated with a group
     */
    function accept(&$renderer, $required = false, $error = null) {
        form_init_date_js();
        $renderer->renderElement($this, $required, $error);
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return array|stdClass
     */
    public function export_for_template(renderer_base $output) {
        form_init_date_js();
        return parent::export_for_template($output);
    }

    /**
     * Output a timestamp. Give it the name of the group.
     *
     * @param array $submitValues values submitted.
     * @param bool $assoc specifies if returned array is associative
     * @return array
     */
    function exportValue(&$submitValues, $assoc = false) {
        $valuearray = array();
        foreach ($this->_elements as $element){
            $thisexport = $element->exportValue($submitValues[$this->getName()], true);
            if ($thisexport!=null){
                $valuearray += $thisexport;
            }
        }
        if (count($valuearray)){
            if($this->_options['optional']) {
                // If checkbox is on, the value is zero, so go no further
                if(empty($valuearray['enabled'])) {
                    return $this->_prepareValue(0, $assoc);
                }
            }
            // Get the calendar type used - see MDL-18375.
            $calendartype = \core_calendar\type_factory::get_calendar_instance();
            $gregoriandate = $calendartype->convert_to_gregorian($valuearray['year'],
                                                                 $valuearray['month'],
                                                                 $valuearray['day'],
                                                                 $valuearray['hour'],
                                                                 $valuearray['minute']);
            $value = make_timestamp($gregoriandate['year'],
                                                      $gregoriandate['month'],
                                                      $gregoriandate['day'],
                                                      $gregoriandate['hour'],
                                                      $gregoriandate['minute'],
                                                      0,
                                                      $this->_options['timezone'],
                                                      true);

            return $this->_prepareValue($value, $assoc);
        } else {
            return null;
        }
    }
}
