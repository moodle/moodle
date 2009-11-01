<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

global $CFG;
require_once($CFG->libdir . '/form/group.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/text.php');

/**
 * HTML class for a length of time. For example, 30 minutes of 4 days. The
 * values returned to PHP is the duration in seconds.
 *
 * @package formslib
 */
class MoodleQuickForm_duration extends MoodleQuickForm_group {
   /**
    * Control the fieldnames for form elements
    * optional => if true, show a checkbox beside the element to turn it on (or off)
    */
   protected $_options = array('optional' => false, 'defaultunit' => 60);

   private $_units = null;

   /**
    * Class constructor
    *
    * @access public
    * @param  string $elementName Element's name
    * @param  mixed  $elementLabel Label(s) for an element
    * @param  array  $options Options to control the element's display. Recognised values are
    *       'optional' => true/false - whether to display an 'enabled' checkbox next to the element.
    *       'defaultunit' => 1|60|3600|86400 - the default unit to display when the time is blank. If not specified, minutes is used.
    * @param  mixed  $attributes Either a typical HTML attribute string or an associative array
    */
    function MoodleQuickForm_duration($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'duration';

        // Set the options, do not bother setting bogus ones
        if (!is_array($options)) {
            $options = array();
        }
        $this->_options['optional'] = !empty($options['optional']);
        if (isset($options['defaultunit'])) {
            if (!array_key_exists($options['defaultunit'], $this->get_units())) {
                throw new coding_exception($options['defaultunit'] .
                        ' is not a recognised unit in MoodleQuickForm_duration.');
            }
            $this->_options['defaultunit'] = $options['defaultunit'];
        }
    }

    /**
     * @return array unit length in seconds => string unit name.
     */
    public function get_units() {
        if (is_null($this->_units)) {
            $this->_units = array(
                86400 => get_string('days'),
                3600 => get_string('hours'),
                60 => get_string('minutes'),
                1 => get_string('seconds'),
            );
        }
        return $this->_units;
    }

    /**
     * @param $seconds an amout of time in seconds.
     * @return array($number, $unit) Conver an interval to the best possible unit.
     *      for example 1800 -> array(30, 60) = 30 minutes.
     */
    public function seconds_to_unit($seconds) {
        if ($seconds == 0) {
            return array(0, $this->_options['defaultunit']);
        }
        foreach ($this->get_units() as $unit => $notused) {
            if (fmod($seconds, $unit) == 0) {
                return array($seconds / $unit, $unit);
            }
        }
        return array($seconds, 1);
    }

    // Override of standard quickforms method.
    function _createElements() {
        $attributes = $this->getAttributes();
        if (is_null($attributes)) {
            $attributes = array();
        }
        if (!isset($attributes['size'])) {
            $attributes['size'] = 3;
        }
        $this->_elements = array();
        $this->_elements[] = MoodleQuickForm::createElement('text', 'number', get_string('time', 'form'), $attributes, true);
        unset($attributes['size']);
        $this->_elements[] = MoodleQuickForm::createElement('select', 'timeunit', get_string('timeunit', 'form'), $this->get_units(), $attributes, true);
        // If optional we add a checkbox which the user can use to turn if on
        if($this->_options['optional']) {
            $this->_elements[] = MoodleQuickForm::createElement('checkbox', 'enabled', null, get_string('enable'), $this->getAttributes(), true);
        }
        foreach ($this->_elements as $element){
            if (method_exists($element, 'setHiddenLabel')){
                $element->setHiddenLabel(true);
            }
        }
    }

    // Override of standard quickforms method.
    function onQuickFormEvent($event, $arg, $caller) {
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
                if (!is_array($value)) {
                    list($number, $unit) = $this->seconds_to_unit($value);
                    $value = array('number' => $number, 'timeunit' => $unit);
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
                if ($arg[2]['optional']) {
                    $caller->disabledIf($arg[0], $arg[0] . '[enabled]');
                }
                $caller->setType($arg[0] . '[number]', PARAM_NUMBER);
                return parent::onQuickFormEvent($event, $arg, $caller);
                break;

            default:
                return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    // Override of standard quickforms method.
    function toHtml() {
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer = new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);
        return $renderer->toHtml();
    }

    // Override of standard quickforms method.
    function accept($renderer, $required = false, $error = null) {
        $renderer->renderElement($this, $required, $error);
    }

    /**
     * Output a timestamp. Give it the name of the group.
     * Override of standard quickforms method.
     *
     * @param array $submitValues
     * @param bool $notused Not used.
     * @return array field name => value. The value is the time interval in seconds.
     */
    function exportValue($submitValues, $notused = false) {
        // Get the values from all the child elements.
        $valuearray = array();
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
            return array($this->getName() => 0);
        }
        return array($this->getName() => $valuearray['number'] * $valuearray['timeunit']);
    }
}
