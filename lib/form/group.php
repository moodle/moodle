<?php
require_once("HTML/QuickForm/group.php");

/**
 * HTML class for a form element group
 *
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class MoodleQuickForm_group extends HTML_QuickForm_group{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    function MoodleQuickForm_group($elementName=null, $elementLabel=null, $elements=null, $separator=null, $appendName = true) {
        parent::HTML_QuickForm_group($elementName, $elementLabel, $elements, $separator, $appendName);
    }
    //would cause problems with client side validation so will leave for now
    //var $_elementTemplateType='fieldset';
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
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            if ($this->getGroupType() == 'submit'){
                return 'nodisplay';
            } else {
                return 'static';
            }
        } else {
            return 'fieldset';
        }
    }

    function setElements($elements){
        parent::setElements($elements);
        foreach ($this->_elements as $element){
            if (method_exists($element, 'setHiddenLabel')){
                $element->setHiddenLabel(true);
            }
        }
    }
}
