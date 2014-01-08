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
 * Group of date input element
 *
 * Contains class for a group of elements used to input a date.
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/group.php');
require_once($CFG->libdir . '/formslib.php');

/**
 * Class for a group of elements used to input a date.
 *
 * Emulates moodle print_date_selector function
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_date_selector extends MoodleQuickForm_group
{
    /**
     * Control the fieldnames for form elements
     * startyear => int start of range of years that can be selected
     * stopyear => int last year that can be selected
     * timezone => int|float|string (optional) timezone modifier used for edge case only.
     *      If not specified, then date is caclulated based on current user timezone.
     *      Note: dst will be calculated for string timezones only
     *      {@link http://docs.moodle.org/dev/Time_API#Timezone}
     * optional => if true, show a checkbox beside the date to turn it on (or off)
     * @var array
     */
    protected $_options = array('startyear' => 1970, 'stopyear' => 2020,
            'timezone' => 99, 'optional' => false);

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
     * constructor
     *
     * @param string $elementName Element's name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    function MoodleQuickForm_date_selector($elementName = null, $elementLabel = null, $options = array(), $attributes = null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'date_selector';
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
        form_init_date_js();
    }

    /**
     * This will create date group element constisting of day, month and year.
     *
     * @access private
     */
    function _createElements()
    {
        global $OUTPUT;

        $this->_elements = array();
        for ($i=1; $i<=31; $i++) {
            $days[$i] = $i;
        }
        for ($i=1; $i<=12; $i++) {
            $months[$i] = userdate(gmmktime(12,0,0,$i,15,2000), "%B");
        }
        for ($i=$this->_options['startyear']; $i<=$this->_options['stopyear']; $i++) {
            $years[$i] = $i;
        }
        // E_STRICT creating elements without forms is nasty because it internally uses $this
        $this->_elements[] = @MoodleQuickForm::createElement('select', 'day', get_string('day', 'form'), $days, $this->getAttributes(), true);
        $this->_elements[] = @MoodleQuickForm::createElement('select', 'month', get_string('month', 'form'), $months, $this->getAttributes(), true);
        $this->_elements[] = @MoodleQuickForm::createElement('select', 'year', get_string('year', 'form'), $years, $this->getAttributes(), true);
        $image = $OUTPUT->pix_icon('i/calendar', get_string('calendar', 'calendar'), 'moodle');
        $this->_elements[] = @MoodleQuickForm::createElement('link', 'calendar',
                null, '#', $image,
                array('class' => 'visibleifjs'));

        // If optional we add a checkbox which the user can use to turn if on
        if($this->_options['optional']) {
            $this->_elements[] = @MoodleQuickForm::createElement('checkbox', 'enabled', null, get_string('enable'), $this->getAttributes(), true);
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
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'updateValue':
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    // if no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case
                    if ($caller->isSubmitted()) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                $requestvalue=$value;
                if ($value == 0) {
                    $value = time();
                }
                if (!is_array($value)) {
                    $currentdate = usergetdate($value, $this->_options['timezone']);
                    $value = array(
                        'day' => $currentdate['mday'],
                        'month' => $currentdate['mon'],
                        'year' => $currentdate['year']);
                    // If optional, default to off, unless a date was provided
                     if($this->_options['optional']) {
                        $value['enabled'] = $requestvalue != 0;
                    }
                } else {
                    $value['enabled'] = isset($value['enabled']);
                }
                if (null !== $value){
                    $this->setValue($value);
                }
                break;
            case 'createElement':
                // Optional is an optional param, if its set we need to add a disabledIf rule.
                // If its empty or not specified then its not an optional dateselector.
                if (!empty($arg[2]['optional']) && !empty($arg[0])) {
                    // When using the function addElement, rather than createElement, we still
                    // enter this case, making this check necessary.
                    if ($this->_usedcreateelement) {
                        $caller->disabledIf($arg[0] . '[day]', $arg[0] . '[enabled]');
                        $caller->disabledIf($arg[0] . '[month]', $arg[0] . '[enabled]');
                        $caller->disabledIf($arg[0] . '[year]', $arg[0] . '[enabled]');
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
    function toHtml()
    {
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer = new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);

        $html = $this->_wrap[0];
        if ($this->_usedcreateelement) {
            $html .= html_writer::tag('span', $renderer->toHtml(), array('class' => 'fdate_selector'));
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
    function accept(&$renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    /**
     * Output a timestamp. Give it the name of the group.
     *
     * @param array $submitValues values submitted.
     * @param bool $assoc specifies if returned array is associative
     * @return array
     */
    function exportValue(&$submitValues, $assoc = false)
    {
        $value = null;
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
                    $value[$this->getName()] = 0;
                    return $value;
                }
            }

            $value[$this->getName()] = make_timestamp($valuearray['year'],
                                   $valuearray['month'],
                                   $valuearray['day'],
                                   0, 0, 0,
                                   $this->_options['timezone'],
                                   true);

            return $value;
        } else {
            return null;
        }
    }
}
