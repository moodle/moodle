<?php
require_once("HTML/QuickForm/static.php");

/**
 * HTML class for a text type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_static extends HTML_QuickForm_static{
    var $_elementTemplateType='static';
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    function MoodleQuickForm_static($elementName=null, $elementLabel=null, $text=null) {
        parent::HTML_QuickForm_static($elementName, $elementLabel, $text);
    }
    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }
    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    function getElementTemplateType(){
        return $this->_elementTemplateType;
    }
}
