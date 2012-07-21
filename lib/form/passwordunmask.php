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
 * Password type form element with unmask option
 *
 * Contains HTML class for a password type element with unmask option
 *
 * @package   core_form
 * @copyright 2009 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

global $CFG;
require_once($CFG->libdir.'/form/password.php');

/**
 * Password type form element with unmask option
 *
 * HTML class for a password type element with unmask option
 *
 * @package   core_form
 * @category  form
 * @copyright 2009 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_passwordunmask extends MoodleQuickForm_password {
    /**
     * constructor
     *
     * @param string $elementName (optional) name of the password element
     * @param string $elementLabel (optional) label for password element
     * @param mixed $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     */
    function MoodleQuickForm_passwordunmask($elementName=null, $elementLabel=null, $attributes=null) {
        global $CFG;
        // no standard mform in moodle should allow autocomplete of passwords
        if (empty($attributes)) {
            $attributes = array('autocomplete'=>'off');
        } else if (is_array($attributes)) {
            $attributes['autocomplete'] = 'off';
        } else {
            if (strpos($attributes, 'autocomplete') === false) {
                $attributes .= ' autocomplete="off" ';
            }
        }

        parent::MoodleQuickForm_password($elementName, $elementLabel, $attributes);
    }

    /**
     * Returns HTML for password form element.
     *
     * @return string
     */
    function toHtml() {
        global $PAGE;

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $unmask = get_string('unmaskpassword', 'form');
            //Pass id of the element, so that unmask checkbox can be attached.
            $PAGE->requires->yui_module('moodle-form-passwordunmask', 'M.form.passwordunmask',
                    array(array('formid' => $this->getAttribute('id'), 'checkboxname' => $unmask)));
            return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . ' />';
        }
    }

}
