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
    var $_helpbutton='';
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
            $itemid = substr(hexdec(uniqid()), 0, 9)+rand(1,100);
            if(empty($COURSE->context)) {
                $ctx = get_context_instance(CONTEXT_SYSTEM);
            } else {
                $ctx = $COURSE->context;
            }
            $ret = get_repository_client($ctx);
            $suffix = $ret['suffix'];
            $str = $this->_getTabs();
            $str .= '<input type="hidden" value="'.$itemid.'" name="'.$this->_attributes['name'].'" id="'.$this->_attributes['id'].'_'.$suffix.'" />';
            $id = $this->_attributes['id'];
            $str .= <<<EOD
<script type="text/javascript">
function updatefile(){
    alert('$strsaved');
    document.getElementById('repo_info_$suffix').innerHTML = '$strsaved';
}
function callpicker_$suffix(){
    var el=document.getElementById('${id}_${suffix}');
    openpicker_$suffix({"env":"form", 'itemid': $itemid, 'target':el, 'callback':updatefile})
}
</script>
EOD;
            // $this->_getAttrString($this->_attributes);
            $str .= '<input value ="'.get_string('openpicker', 'repository').'" type="button" onclick=\'callpicker_'.$suffix.'()\' />'.'<span id="repo_info_'.$suffix.'" style="color:green"></span>'.$ret['html'].$ret['js'];
            return $str;
        }
    }
}
