<?php  // $Id$

require_once('HTML/QuickForm/element.php');

class MoodleQuickForm_areafiles extends HTML_QuickForm_element {
    var $_helpbutton = '';
    var $_areainfo   = array();

    function MoodleQuickForm_files($elementName=null, $elementLabel=null, $attributes=null) {
        parent::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
    }

    function setName($name) {
        $this->updateAttributes(array('name'=>$name));
    }

    function getName() {
        return $this->getAttribute('name');
    }

    function setValue($value) {
        if (!is_array($value)) {
            $this->_areainfo = array();
        } else {
            $this->_areainfo = $value;
        }
    }

    function getValue() {
        return $this->_areainfo;
    }

    function setHelpButton($helpbuttonargs, $function='helpbutton') {
        if (!is_array($helpbuttonargs)) {
            $helpbuttonargs = array($helpbuttonargs);
        } else {
            $helpbuttonargs = $helpbuttonargs;
        }
        //we do this to to return html instead of printing it
        //without having to specify it in every call to make a button.
        if ('helpbutton' == $function){
            $defaultargs = array('', '', 'moodle', true, false, '', true);
            $helpbuttonargs = $helpbuttonargs + $defaultargs ;
        }
        $this->_helpbutton=call_user_func_array($function, $helpbuttonargs);
    }

    function getHelpButton() {
        return $this->_helpbutton;
    }

    function getElementTemplateType() {
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }

    function toHtml() {
        global $CFG;

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $id     = $this->_attributes['id'];
        $elname = $this->_attributes['name'];

        $value = $this->getValue();

        if (empty($value['contextid'])) {
            // no existing area info provided - let's use fresh new draft area
            require_once("$CFG->libdir/filelib.php");
            $this->setValue(get_new_draftarea());
            $value = $this->getValue();
        }

        $contextid = $value['contextid'];
        $filearea  = $value['filearea'];
        $itemid    = $value['itemid'];

        $str  = '<input type="hidden" name="'.$elname.'[contextid]" value="'.$contextid.'" />';
        $str .= '<input type="hidden" name="'.$elname.'[filearea]" value="'.$filearea.'" />';
        $str .= '<input type="hidden" name="'.$elname.'[itemid]" value="'.$itemid.'" />';

        $url = "$CFG->wwwroot/files/areafiles.php?contextid=$contextid&amp;filearea=$filearea&amp;itemid=$itemid";

        $str .= '<object type="text/html" data="'.$url.'" height="160" width="600" style="border:1px solid #000">Error</object>'; // TODO: localise, fix styles, etc.

        return $str;
    }

    function exportValue(&$submitValues, $assoc = false) {
        return array(
            $this->_attributes['name']['contexid'] => $submitValues[$this->_attributes['name']]['contextid'],
            $this->_attributes['name']['filearea'] => $submitValues[$this->_attributes['name']]['filearea'],
            $this->_attributes['name']['itemid']   => $submitValues[$this->_attributes['name']]['itemid'],
            );
    }
}
