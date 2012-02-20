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
 * Button form element
 *
 * Contains HTML class for a button type element
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page
}

global $CFG;
require_once($CFG->libdir.'/form/submit.php');

/**
 * HTML class for a submit cancel type element
 *
 * Overloaded {@link MoodleQuickForm_submit} with default behavior modified to cancel a form.
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_cancel extends MoodleQuickForm_submit
{
    /**
     * constructor
     *
     * @param string $elementName (optional) name of the checkbox
     * @param string $value (optional) value for the button
     * @param mixed $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     */
    function MoodleQuickForm_cancel($elementName=null, $value=null, $attributes=null)
    {
        if ($elementName==null){
            $elementName='cancel';
        }
        if ($value==null){
            $value=get_string('cancel');
        }
        MoodleQuickForm_submit::MoodleQuickForm_submit($elementName, $value, $attributes);
        $this->updateAttributes(array('onclick'=>'skipClientValidation = true; return true;'));
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
            case 'createElement':
                $className = get_class($this);
                $this->$className($arg[0], $arg[1], $arg[2]);
                $caller->_registerCancelButton($this->getName());
                return true;
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Returns the value of field without HTML tags
     *
     * @return string
     */
    function getFrozenHtml(){
        return HTML_QuickForm_submit::getFrozenHtml();
    }

    /**
     * Freeze the element so that only its value is returned
     *
     * @return bool
     */
    function freeze(){
        return HTML_QuickForm_submit::freeze();
    }
}
