<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

global $CFG;
require_once($CFG->libdir.'/form/password.php');

/**
 * HTML class for a password type element with unmask option
 *
 * @author       Petr Skoda
 * @access       public
 */
class MoodleQuickForm_passwordunmask extends MoodleQuickForm_password {

    function MoodleQuickForm_passwordunmask($elementName=null, $elementLabel=null, $attributes=null) {
        global $CFG;
        if (empty($CFG->xmlstrictheaders)) {
            // no standard mform in moodle should allow autocomplete of passwords
            // this is valid attribute in html5, sorry, we have to ignore validation errors in legacy xhtml 1.0
            $attributes = (array)$attributes;
            if (!isset($attributes['autocomplete'])) {
                $attributes['autocomplete'] = 'off';
            }
        }
        parent::MoodleQuickForm_password($elementName, $elementLabel, $attributes);
    }

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
    } //end func toHtml

}
