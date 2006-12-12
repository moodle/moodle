<?php
global $CFG;
require_once("$CFG->libdir/form/textarea.php");

/**
 * HTML class for htmleditor type element
 * 
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_htmleditor extends MoodleQuickForm_textarea{
    var $_type;
    var $_elementTemplateType='default';
    var $_canUseHtmlEditor;
    var $_options=array('canUseHtmlEditor'=>'detect','rows'=>10, 'cols'=>65, 'width'=>0,'height'=>0, 'course'=>0);
    function MoodleQuickForm_htmleditor($elementName=null, $elementLabel=null, $options=array(), $attributes=null){
        parent::MoodleQuickForm_textarea($elementName, $elementLabel, $attributes);
        // set the options, do not bother setting bogus ones
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (isset($this->_options[$name])) {
                    if (is_array($value) && is_array($this->_options[$name])) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
        if ($this->_options['canUseHtmlEditor']=='detect'){
            $this->_options['canUseHtmlEditor']=can_use_html_editor();
        }
        if ($this->_options['canUseHtmlEditor']){
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
        if ($this->_options['canUseHtmlEditor'] && !$this->_flagFrozen){
            ob_start();
            use_html_editor($this->getName(), '', $this->getAttribute('id'));
            $script=ob_get_clean();
        } else {
            $script='';
        }
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            return $this->_getTabs() .
                    print_textarea($this->_options['canUseHtmlEditor'], 
                                    $this->_options['rows'],
                                    $this->_options['cols'],
                                    $this->_options['width'],
                                    $this->_options['height'],
                                    $this->getName(),
                                    preg_replace("/(\r\n|\n|\r)/", '&#010;',$this->getValue()),
                                    $this->_options['course'],
                                    true,
                                    $this->getAttribute('id')).$script;
        }
    } //end func toHtml

}
?>