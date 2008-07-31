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
        global $CFG;
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $ret = get_repository_client();
            return $this->_getTabs() . '<input' . $this->_getAttrString($this->_attributes) . ' onclick=\'openpicker()\' />'.$ret['html'].$ret['js'];
        }
    }
}
