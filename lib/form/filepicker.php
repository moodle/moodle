<?php
// $Id$

require_once("HTML/QuickForm/button.php");
require_once(dirname(dirname(dirname(__FILE__))) . '/repository/lib.php');

/**
 * HTML class for a single filepicker element (based on button)
 *
 * @author       Moodle.com
 * @version      1.0
 * @since        Moodle 2.0
 * @access       public
 */
class MoodleQuickForm_filepicker extends HTML_QuickForm_input {
    var $_helpbutton='';

    function MoodleQuickForm_filepicker($elementName=null, $elementLabel=null, $attributes=null) {
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
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
        global $CFG, $COURSE, $USER;

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $currentfile = '';
        $draftvalue  = '';
        if ($draftid = (int)$this->getValue()) {
            $fs = get_file_storage();
            $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user_draft', $draftid, '', false)) {
                $file = reset($files);
                $currentfile = $file->get_filename();
                $draftvalue = 'value="'.$draftid.'"';
            }
        }
        $strsaved = get_string('filesaved', 'repository');
        if ($COURSE->id == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        }
        $repository_info = repository_get_client($context);
        $suffix = $repository_info['suffix'];

        $id     = $this->_attributes['id'];
        $elname = $this->_attributes['name'];

        $str = $this->_getTabs();
        $str .= '<input type="hidden" name="'.$elname.'" id="'.$id.'" '.$draftvalue.' />';

        $str .= <<<EOD
<script type="text/javascript">
function updatefile_$suffix(str) {
    document.getElementById('repo_info_$suffix').innerHTML = str;
}
function callpicker_$suffix() {
    document.body.className += ' yui-skin-sam';
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-$suffix';
    picker.className = 'file-picker';
    document.body.appendChild(picker);
    var el=document.getElementById('$id');
    openpicker_$suffix({'env':'form', 'target':el, 'callback':updatefile_$suffix})
}
</script>
EOD;
        $str .= '<input value="'.get_string('openpicker', 'repository').'" type="button" onclick=\'callpicker_'.$suffix.'()\' />'.'<span id="repo_info_'.$suffix.'" class="notifysuccess">'.$currentfile.'</span>'.$repository_info['css'].$repository_info['js'];
        return $str;
    }

    function exportValue(&$submitValues, $assoc = false) {
        return array($this->_attributes['name'] => $submitValues[$this->_attributes['name']]);
    }
}
