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
 * Textarea type form element
 *
 * Contains HTML class for a textarea type element
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/textarea.php');

/**
 * Textarea type form element
 *
 * HTML class for a textarea type element
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_textarea extends HTML_QuickForm_textarea{
    /** @var string Need to store id of form as we may need it for helpbutton */
    var $_formid = '';

    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel=false;

    /**
     * constructor
     *
     * @param string $elementName (optional) name of the text field
     * @param string $elementLabel (optional) text field label
     * @param string $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null) {
        parent::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_textarea($elementName=null, $elementLabel=null, $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * Sets label to be hidden
     *
     * @param bool $hiddenLabel sets if label should be hidden
     */
    function setHiddenLabel($hiddenLabel){
        $this->_hiddenLabel = $hiddenLabel;
    }

    /**
     * Returns HTML for this form element.
     *
     * @return string
     */
    function toHtml(){
        if ($this->_hiddenLabel){
            $this->_generateId();
            return '<label class="accesshide" for="' . $this->getAttribute('id') . '" >' .
                    $this->getLabel() . '</label>' . parent::toHtml();
        } else {
            return parent::toHtml();
        }
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $this->_formid = $caller->getAttribute('id');
                break;
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
