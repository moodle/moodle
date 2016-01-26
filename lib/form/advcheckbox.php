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
 * Advanced checkbox type form element
 *
 * Contains HTML class for an advcheckbox type form element
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/advcheckbox.php');

/**
 * HTML class for an advcheckbox type element
 *
 * Overloaded {@link HTML_QuickForm_advcheckbox} with default behavior modified for Moodle.
 * This will return '0' if not checked and '1' if checked.
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_advcheckbox extends HTML_QuickForm_advcheckbox{
    /** @var string html for help button, if empty then no help will icon will be dispalyed. */
    var $_helpbutton='';

    /** @var string Group to which this checkbox belongs (for select all/select none button) */
    var $_group;

    /**
     * constructor
     *
     * @param string $elementName (optional) name of the checkbox
     * @param string $elementLabel (optional) checkbox label
     * @param string $text (optional) Text to put after the checkbox
     * @param mixed $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     * @param mixed $values (optional) Values to pass if checked or not checked
     */
    function MoodleQuickForm_advcheckbox($elementName=null, $elementLabel=null, $text=null, $attributes=null, $values=null)
    {
        if ($values === null){
            $values = array(0, 1);
        }

        if (!empty($attributes['group'])) {

            $this->_group = 'checkboxgroup' . $attributes['group'];
            unset($attributes['group']);
            if (is_null($attributes)) {
                $attributes = array();
                $attributes['class'] .= " $this->_group";
            } elseif (is_array($attributes)) {
                if (isset($attributes['class'])) {
                    $attributes['class'] .= " $this->_group";
                } else {
                    $attributes['class'] = $this->_group;
                }
            } elseif ($strpos = stripos($attributes, 'class="')) {
                $attributes = str_ireplace('class="', 'class="' . $this->_group . ' ', $attributes);
            } else {
                $attributes .= ' class="' . $this->_group . '"';
            }
        }

        parent::HTML_QuickForm_advcheckbox($elementName, $elementLabel, $text, $attributes, $values);
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
     * Returns HTML for advchecbox form element.
     *
     * @return string
     */
    function toHtml()
    {
        return '<span>' . parent::toHtml() . '</span>';
    }

    /**
     * Returns the disabled field. Accessibility: the return "[ ]" from parent
     * class is not acceptable for screenreader users, and we DO want a label.
     *
     * @return string
     */
    function getFrozenHtml()
    {
        //$this->_generateId();
        $output = '<input type="checkbox" disabled="disabled" id="'.$this->getAttribute('id').'" ';
        if ($this->getChecked()) {
            $output .= 'checked="checked" />'.$this->_getPersistantData();
        } else {
            $output .= '/>';
        }
        return $output;
    }

}
