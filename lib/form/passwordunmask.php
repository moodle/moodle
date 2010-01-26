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
        global $CFG;
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $id = $this->getAttribute('id');
            $unmask = get_string('unmaskpassword', 'form');
            $unmaskjs = html_writer::script(js_writer::set_variable('punmask', array('id'=>$id, 'unmaskstr'=>$unmask)));
            $unmaskjs .= html_writer::script('', $CFG->httpswwwroot.'/lib/form/passwordunmask.js');
            return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . ' /><div class="unmask" id="'.$id.'unmaskdiv"></div>'.$unmaskjs;
        }
    } //end func toHtml

}
