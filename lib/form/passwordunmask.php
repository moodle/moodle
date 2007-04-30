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

    function toHtml() {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $id = $this->getAttribute('id');
            $unmask = get_string('unmaskpassword', 'form');
            $unmaskjs = '<script type="text/javascript">
//<![CDATA[
document.write(\'<div class="unmask"><input id="'.$id.'unmask" value="1" type="checkbox" onclick="unmaskPassword(\\\''.$id.'\\\')"/><label for="'.$id.'unmask">'.addslashes_js($unmask).'<\/label><\/div>\');
//]]>
</script>';
            return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . ' />'.$unmaskjs;
        }
    } //end func toHtml

}
?>