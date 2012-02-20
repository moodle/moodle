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
 * File type form element
 *
 * Contains HTML class for a file type form element
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/file.php');

/**
 * file element
 *
 * HTML class for a form element to upload a file
 *
 * @package    core_form
 * @deprecated since Moodle 2.0 Please do not use this form element.
 * @todo       MDL-31294 remove this element
 * @see        MoodleQuickForm_filepicker
 * @see        MoodleQuickForm_filemanager
 * @copyright  2007 Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_file extends HTML_QuickForm_file{
    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the editor
     * @param string $elementLabel (optional) editor label
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     */
    function MoodleQuickForm_file($elementName=null, $elementLabel=null, $attributes=null) {
        debugging('file forms element is deprecated, please use new filepicker instead');
        parent::HTML_QuickForm_file($elementName, $elementLabel, $attributes);
    }
    /**
     * set html for help button
     *
     * @param array $helpbuttonargs array of arguments to make a help button
     * @param string $function function name to call to get html
     * @deprecated since Moodle 2.0. Please do not call this function any more.
     * @todo MDL-31047 this api will be removed.
     * @see MoodleQuickForm::setHelpButton()
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }
    /**
     * set html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * Override createElement event to add max files
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ($event == 'createElement') {
            $className = get_class($this);
            $this->$className($arg[0], $arg[1].' ('.get_string('maxsize', '', display_size($caller->getMaxFileSize())).')', $arg[2]);
            return true;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Slightly different container template when frozen.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'static';
        } else {
            return 'default';
        }
    }

}