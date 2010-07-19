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

/**
 * Class for a group of elements used to input a date and time.
 *
 * Emulates moodle print_date_selector function and also allows you to select a time.
 *
 * @package formslib
 */
class MoodleQuickForm_date_time_selector extends MoodleQuickForm_group{
    /**
    * Options for the element
    *
    * startyear => integer start of range of years that can be selected
    * stopyear => integer last year that can be selected
    * timezone => float/string timezone
    * applydst => apply users daylight savings adjustment?
    * step     => step to increment minutes by
    * optional => if true, show a checkbox beside the date to turn it on (or off)
    */
    var $_options = array('startyear' => 1970, 'stopyear' => 2020,
                    'timezone' => 99, 'applydst' => true, 'step' => 5, 'optional' => false);

   /**
    * These complement separators, they are appended to the resultant HTML
    * @access   private
    * @var      array
    */
    var $_wrap = array('', '');

   /**
    * Class constructor
    *
    * @access   public
    * @param    string  Element's name
    * @param    mixed   Label(s) for an element
    * @param    array   Options to control the element's display
    * @param    mixed   Either a typical HTML attribute string or an associative array
    */
    function MoodleQuickForm_date_time_selector($elementName = null, $elementLabel = null, $options = array(), $attributes = null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
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
        form_init_date_js();
    }

    // }}}
    // {{{ _createElements()

    function _createElements()
    {
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
        for ($i=0; $i<=23; $i++) {
            $hours[$i] = sprintf("%02d",$i);
        }
        for ($i=0; $i<60; $i+=$this->_options['step']) {
            $minutes[$i] = sprintf("%02d",$i);
        }
        $this->_elements[] =& MoodleQuickForm::createElement('select', 'day', get_string('day', 'form'), $days, $this->getAttributes(), true);
        $this->_elements[] =& MoodleQuickForm::createElement('select', 'month', get_string('month', 'form'), $months, $this->getAttributes(), true);
        $this->_elements[] =& MoodleQuickForm::createElement('select', 'year', get_string('year', 'form'), $years, $this->getAttributes(), true);
        if (right_to_left()) {   // Switch order of elements for Right-to-Left
            $this->_elements[] =& MoodleQuickForm::createElement('select', 'minute', get_string('minute', 'form'), $minutes, $this->getAttributes(), true);
            $this->_elements[] =& MoodleQuickForm::createElement('select', 'hour', get_string('hour', 'form'), $hours, $this->getAttributes(), true);
        } else {
            $this->_elements[] =& MoodleQuickForm::createElement('select', 'hour', get_string('hour', 'form'), $hours, $this->getAttributes(), true);
            $this->_elements[] =& MoodleQuickForm::createElement('select', 'minute', get_string('minute', 'form'), $minutes, $this->getAttributes(), true);
        }
        // If optional we add a checkbox which the user can use to turn if on
        if($this->_options['optional']) {
            $this->_elements[] =& MoodleQuickForm::createElement('checkbox', 'enabled', null, get_string('enable'), $this->getAttributes(), true);
        }
        foreach ($this->_elements as $element){
            if (method_exists($element, 'setHiddenLabel')){
                $element->setHiddenLabel(true);
            }
        }

    }

    // }}}
    // {{{ onQuickFormEvent()

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param     string    $event  Name of event
     * @param     mixed     $arg    event arguments
     * @param     object    $caller calling object
     * @since     1.0
     * @access    public
     * @return    void
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
                    $currentdate = usergetdate($value);
                    // Round minutes to the previous multiple of step.
                    $currentdate['minutes'] -= $currentdate['minutes'] % $this->_options['step'];
                    $value = array(
                        'minute' => $currentdate['minutes'],
                        'hour' => $currentdate['hours'],
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
                if($arg[2]['optional']) {
                    $caller->disabledIf($arg[0], $arg[0].'[enabled]');
                }
                return parent::onQuickFormEvent($event, $arg, $caller);
                break;
            default:
                return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    // }}}
    // {{{ toHtml()

    function toHtml()
    {
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer = new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);
        return $this->_wrap[0] . $renderer->toHtml() . $this->_wrap[1];
    }

    // }}}
    // {{{ accept()

    function accept(&$renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    // }}}

    /**
     * Output a timestamp. Give it the name of the group.
     *
     * @param array $submitValues
     * @param bool $assoc
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
            $valuearray=$valuearray + array('year' => 1970, 'month' => 1, 'day' => 1, 'hour' => 0, 'minute' => 0);
            $value[$this->getName()] = make_timestamp(
                                   $valuearray['year'],
                                   $valuearray['month'],
                                   $valuearray['day'],
                                   $valuearray['hour'],
                                   $valuearray['minute'],
                                   0,
                                   $this->_options['timezone'],
                                   $this->_options['applydst']);

            return $value;
        } else {

            return null;
        }
    }

    // }}}
}
