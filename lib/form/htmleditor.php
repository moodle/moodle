<?php
global $CFG;
require_once("$CFG->libdir/form/textarea.php");

/**
 * HTML class for htmleditor type element
 * 
 * @author       Jamie Pratt
 * @access       public
 */
class moodleform_htmleditor extends moodleform_textarea{
    var $_type;
    var $_elementTemplateType='default';
    var $_canUseHtmlEditor;
    var $_options=array('course'=>0);
    function moodleform_htmleditor($elementName=null, $elementLabel=null, $attributes=null){
        parent::moodleform_textarea($elementName, $elementLabel, $attributes);
        $this->_canUseHtmlEditor=can_use_html_editor();
        if ($this->_canUseHtmlEditor){
            $this->_type='htmleditor';
            //$this->_elementTemplateType='wide';
        }else{
            $this->_type='textarea';
        }
    }
    function getElementTemplateType(){
        return $this->_elementTemplateType;
    }
    function toHtml(){
        if ($this->_canUseHtmlEditor){
            ob_start();
            use_html_editor($this->getName());
            $script=ob_get_clean();
        } else {
            $script='';
        }
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            return $this->_getTabs() .
                    print_textarea($this->_canUseHtmlEditor, 
                                    $this->getAttribute('rows'),
                                    $this->getAttribute('cols'),
                                    $this->getAttribute('width'),
                                    $this->getAttribute('height'),
                                    $this->getName(),
                                    preg_replace("/(\r\n|\n|\r)/", '&#010;',$this->getValue()),
                                    $this->_options['course'],
                                    true).$script;
        }
    } //end func toHtml

}
?>