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
 * Hidden type form element
 *
 * Contains HTML class for a hidden type element
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/hidden.php');

/**
 * Hidden type form element
 *
 * HTML class for a hidden type element
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_hidden extends HTML_QuickForm_hidden{
    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the hidden element
     * @param string $value (optional) value of the element
     * @param mixed  $attributes (optional) Either a typical HTML attribute string
     *               or an associative array
     */
    function MoodleQuickForm_hidden($elementName=null, $value='', $attributes=null) {
        parent::HTML_QuickForm_hidden($elementName, $value, $attributes);
    }

    /**
     * set html for help button
     *
     * @param array $helpbuttonargs array of arguments to make a help button
     * @param string $function function name to call to get html
     * @deprecated since Moodle 2.0. Please do not call this function any more.
     * @todo MDL-34508 this api will be removed.
     * @see MoodleQuickForm::addHelpButton()
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('setHelpButton() is deprecated, please use $mform->addHelpButton() instead');
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    function getHelpButton(){
        return '';
    }
}
