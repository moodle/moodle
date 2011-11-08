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
 * Element-container for advanced grading custom input
 *
 * @copyright  2011 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once("HTML/QuickForm/element.php");
require_once($CFG->dirroot.'/grade/grading/form/lib.php');

if (class_exists('HTML_QuickForm')) {
    HTML_QuickForm::registerRule('gradingvalidated', 'callback', '_validate', 'MoodleQuickForm_grading');
}

/**
 * HTML class for a grading element. This is a wrapper for advanced grading plugins.
 * When adding the 'grading' element to the form, developer must pass an object of
 * class gradingform_instance as $attributes['gradinginstance']. Otherwise an exception will be
 * thrown.
 * This object is responsible for implementing functions to render element html and validate it
 *
 * @author       Marina Glancy
 * @access       public
 */
class MoodleQuickForm_grading extends HTML_QuickForm_input{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';

    /**
     * Stores attributes passed to the element
     * @var array
     */
    private $gradingattributes;

    function MoodleQuickForm_grading($elementName=null, $elementLabel=null, $attributes=null) {
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->gradingattributes = $attributes;
    }

    /**
     * Helper function to retrieve gradingform_instance passed in element attributes
     *
     * @return gradingform_instance
     */
    function get_gradinginstance() {
        if (is_array($this->gradingattributes) && array_key_exists('gradinginstance', $this->gradingattributes)) {
            return $this->gradingattributes['gradinginstance'];
        } else {
            return null;
        }
    }

    /**
     * Returns the input field in HTML
     *
     * @return    string
     */
    function toHtml(){
        global $PAGE;
        return $this->get_gradinginstance()->render_grading_element($PAGE, $this);
    }

    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }

    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * @return string
     */
    function getElementTemplateType(){
        return 'default';
    }

    /**
     * Adds necessary rules to the element
     */
    function onQuickFormEvent($event, $arg, &$caller) {
        if ($event == 'createElement') {
            $attributes = $arg[2];
            if (!is_array($attributes) || !array_key_exists('gradinginstance', $attributes) || !($attributes['gradinginstance'] instanceof gradingform_instance)) {
                throw new moodle_exception('exc_gradingformelement', 'grading');
            }
        }

        $name = $this->getName();
        if ($name && $caller->elementExists($name)) {
            $caller->addRule($name, $this->get_gradinginstance()->default_validation_error_message(), 'gradingvalidated', $this->gradingattributes);
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Function registered as rule for this element and is called when this element is being validated.
     * This is a wrapper to pass the validation to the method gradingform_instance::validate_grading_element
     */
    static function _validate($elementValue, $attributes = null) {
        return $attributes['gradinginstance']->validate_grading_element($elementValue);
    }
}
