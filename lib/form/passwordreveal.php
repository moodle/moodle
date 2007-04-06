<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

global $CFG;
require_once($CFG->libdir.'/form/password.php');

/**
 * HTML class for a password type element with reveal option
 *
 * @author       Petr Skoda
 * @access       public
 */
class MoodleQuickForm_passwordreveal extends MoodleQuickForm_password {

    function toHtml() {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $id = $this->getAttribute('id');
            $reveal = get_string('revealpassword', 'form');
            $revealjs = '<script type="text/javascript">
//<![CDATA[
document.write(\'<div class="reveal"><input id="'.$id.'reveal" value="1" type="checkbox" onclick="revealPassword(\\\''.$id.'\\\')"/><label for="'.$id.'reveal">'.addslashes_js($reveal).'<\/label><\/div>\');
//]]>
</script>';
            return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . ' />'.$revealjs;
        }
    } //end func toHtml

}
?>