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
 * Form element group
 *
 * Contains HTML class for group form element
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("HTML/QuickForm/group.php");

/**
 * HTML class for a form element group
 *
 * Overloaded {@link HTML_QuickForm_group} with default behavior modified for Moodle.
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_group extends HTML_QuickForm_group{
    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /**
     * constructor
     *
     * @param string $elementName (optional) name of the group
     * @param string $elementLabel (optional) group label
     * @param array $elements (optional) array of HTML_QuickForm_element elements to group
     * @param string $separator (optional) string to seperate elements.
     * @param string $appendName (optional) string to appened to grouped elements.
     */
    public function __construct($elementName=null, $elementLabel=null, $elements=null, $separator=null, $appendName = true) {
        parent::__construct($elementName, $elementLabel, $elements, $separator, $appendName);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_group($elementName=null, $elementLabel=null, $elements=null, $separator=null, $appendName = true) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $elements, $separator, $appendName);
    }

    /** @var string template type, would cause problems with client side validation so will leave for now */
    //var $_elementTemplateType='fieldset';

    /**
     * set html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * Returns element template, nodisplay/static/fieldset
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            if ($this->getGroupType() == 'submit'){
                return 'nodisplay';
            } else {
                return 'static';
            }
        } else {
            if ($this->getGroupType() == 'submit') {
                return 'actionbuttons';
            }
            return 'fieldset';
        }
    }

    /**
     * Sets the grouped elements and hides label
     *
     * @param array $elements
     */
    function setElements($elements){
        parent::setElements($elements);
        foreach ($this->_elements as $element){
            if (method_exists($element, 'setHiddenLabel')){
                $element->setHiddenLabel(true);
            }
        }
    }
}
