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

var is_ie = (navigator.userAgent.toLowerCase().indexOf("msie") != -1);

document.getElementById("'.$id.'").setAttribute("autocomplete", "off");

var unmaskdiv = document.getElementById("'.$id.'unmaskdiv");

var unmaskchb = document.createElement("input");
unmaskchb.setAttribute("type", "checkbox");
unmaskchb.setAttribute("id", "'.$id.'unmask");
unmaskchb.onchange = function() {unmaskPassword("'.$id.'");};
unmaskdiv.appendChild(unmaskchb);

var unmasklbl = document.createElement("label");
unmasklbl.innerHTML = "'.addslashes_js($unmask).'";
if (is_ie) {
  unmasklbl.setAttribute("htmlFor", "'.$id.'unmask");
} else {
  unmasklbl.setAttribute("for", "'.$id.'unmask");
}
unmaskdiv.appendChild(unmasklbl);

if (is_ie) {
  // ugly hack to work around the famous onchange IE bug
  unmaskchb.onclick = function() {this.blur();};
  unmaskdiv.onclick = function() {this.blur();};
}
//]]>
</script>';
            return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . ' /><div class="unmask" id="'.$id.'unmaskdiv"></div>'.$unmaskjs;
        }
    } //end func toHtml

}
?>