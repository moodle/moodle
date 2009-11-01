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
        parent::MoodleQuickForm_password($elementName, $elementLabel, $attributes);
    }

    function toHtml() {
        global $PAGE;
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $id = $this->getAttribute('id');
            $unmask = get_string('unmaskpassword', 'form');
            $unmaskjs = $PAGE->requires->data_for_js('punmask',Array('id'=>$id, 'unmaskstr'=>$unmask))->asap();
            $unmaskjs .= $PAGE->requires->js('lib/form/passwordunmask.js')->asap();
            return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . ' /><div class="unmask" id="'.$id.'unmaskdiv"></div>'.$unmaskjs;
        }
    } //end func toHtml

}
