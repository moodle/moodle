<?php
require_once('HTML/QuickForm/radio.php');

/**
 * HTML class for a radio type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_radio extends HTML_QuickForm_radio{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    function MoodleQuickForm_radio($elementName=null, $elementLabel=null, $text=null, $value=null, $attributes=null) {
        parent::HTML_QuickForm_radio($elementName, $elementLabel, $text, $value, $attributes);
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

    /**
     * Slightly different container template when frozen.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'static';
        } else {
            return 'default';
        }
    }
    /**
     * Returns the disabled field. Accessibility: the return "( )" from parent
     * class is not acceptable for screenreader users, and we DO want a label.
     * @return string
     */
    function getFrozenHtml()
    {
        $output = '<input type="radio" disabled="disabled" id="'.$this->getAttribute('id').'" ';
        if ($this->getChecked()) {
            $output .= 'checked="checked" />'.$this->_getPersistantData();
        } else {
            $output .= '/>';
        }
        return $output;
    }
    function toHtml()
    {
        return '<span>' . parent::toHtml() . '</span>';
    }
}
