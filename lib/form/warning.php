<?php
require_once("HTML/QuickForm/static.php");

/**
 * HTML class for a text type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_warning extends HTML_QuickForm_static{
    var $_elementTemplateType='warning';
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    var $_class='';

    function MoodleQuickForm_warning($elementName=null, $elementClass='notifyproblem', $text=null) {
        parent::HTML_QuickForm_static($elementName, null, $text);
        $this->_type = 'warning';
        if (is_null($elementClass)) {
            $elementClass = 'notifyproblem';
        }
        $this->_class = $elementClass;
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

    function toHtml() {
        global $OUTPUT;
        return $OUTPUT->notification($this->_text, $this->_class);
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
