<?php
// $Id$

require_once("HTML/QuickForm/button.php");
require_once(dirname(dirname(dirname(__FILE__))) . '/repository/lib.php');

/**
 * HTML class for a button type element
 *
 * @author       Dongsheng Cai <dongsheng@cvs.moodle.org>
 * @version      1.0
 * @since        Moodle 2.0
 * @access       public
 */
class MoodleQuickForm_filepicker extends HTML_QuickForm_button
{
    var $_filearea;
    var $_helpbutton='';
    
    function MoodleQuickForm_filepicker($elementName=null, $value=null, $filearea=null, $attributes=null) {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, $value, $attributes); // Set label cause button doesn't
        parent::HTML_QuickForm_button($elementName, $value, $attributes);
        $this->_filearea = $filearea;
    }
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        if (!is_array($helpbuttonargs)){
            $helpbuttonargs=array($helpbuttonargs);
        }else{
            $helpbuttonargs=$helpbuttonargs;
        }
        //we do this to to return html instead of printing it
        //without having to specify it in every call to make a button.
        if ('helpbutton' == $function){
            $defaultargs=array('', '', 'moodle', true, false, '', true);
            $helpbuttonargs=$helpbuttonargs + $defaultargs ;
        }
        $this->_helpbutton=call_user_func_array($function, $helpbuttonargs);
    }
    function getHelpButton(){
        return $this->_helpbutton;
    }
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }
    function toHtml() {
        global $CFG, $COURSE;
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $strsaved = get_string('filesaved', 'repository');
            if(empty($COURSE->context)) {
                $ctx = get_context_instance(CONTEXT_SYSTEM);
            } else {
                $ctx = $COURSE->context;
            }
            $ret = repository_get_client($ctx);
            $suffix = $ret['suffix'];
            $str = $this->_getTabs();
            $str .= '<input type="hidden" value="" name="'.$this->_attributes['name'].'" id="'.$this->_attributes['id'].'_'.$suffix.'" />';
            $id = $this->_attributes['id'];
            $filearea = $this->_filearea;
            $str .= <<<EOD
<script type="text/javascript">
function updatefile_$suffix(str){
    document.getElementById('repo_info_$suffix').innerHTML = str;
}
function callpicker_$suffix(){
    document.body.className += ' yui-skin-sam';
    var picker = document.createElement('DIV');
    picker.id = 'file-picker-$suffix';
    document.body.appendChild(picker);
    var el=document.getElementById('${id}_${suffix}');
    openpicker_$suffix({"env":"form", 'target':el, 'callback':updatefile_$suffix, 'filearea':"${filearea}"})
}
</script>
EOD;
            // $this->_getAttrString($this->_attributes);
            $str .= "<input name=\"filearea\" value=\"$filearea\" type=\"hidden\">\n";
            $str .= '<input value ="'.get_string('openpicker', 'repository').'" type="button" onclick=\'callpicker_'.$suffix.'()\' />'.'<span id="repo_info_'.$suffix.'" style="color:green"></span>'.$ret['css'].$ret['js'];
            return $str;
        }
    }
}
