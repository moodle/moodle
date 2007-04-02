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
    var $_options=array('canUseHtmlEditor'=>'detect','rows'=>10, 'cols'=>45, 'width'=>0,'height'=>0, 'course'=>0);
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
        $this->_canUseHtmlEditor = $this->_options['canUseHtmlEditor'];
    }
    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        if (!$this->_canUseHtmlEditor){
            if ('editorhelpbutton' == $function){
                $key = array_search('richtext', $helpbuttonargs);
                if ($key !== FALSE){
                    array_splice($helpbuttonargs, $key, 1, array('text', 'emoticons'));
                }
            } elseif ('helpbutton' == $function && $helpbuttonargs[0] == 'richtext' && ((!isset($helpbuttonargs[2])) || $helpbuttonargs[2] == 'moodle')){
                //replace single 'richtext' help button with text and emoticon button when htmleditor off.
                return $this->setHelpButton(array('text', 'emoticons'), 'editorhelpbutton');
            }
        }
        return parent::setHelpButton($helpbuttonargs, $function);
    }
    function getElementTemplateType(){
        return $this->_elementTemplateType;
    }
    function toHtml(){
        if ($this->_canUseHtmlEditor && !$this->_flagFrozen){
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
                    print_textarea($this->_canUseHtmlEditor,
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