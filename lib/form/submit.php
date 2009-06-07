<?php
require_once("HTML/QuickForm/submit.php");

/**
 * HTML class for a submit type element
 *
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class MoodleQuickForm_submit extends HTML_QuickForm_submit {
    function MoodleQuickForm_submit($elementName=null, $value=null, $attributes=null) {
        parent::HTML_QuickForm_submit($elementName, $value, $attributes);
    }
    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param     string    $event  Name of event
     * @param     mixed     $arg    event arguments
     * @param     object    $caller calling object
     * @since     1.0
     * @access    public
     * @return    void
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                parent::onQuickFormEvent($event, $arg, $caller);
                if ($caller->isNoSubmitButton($arg[0])){
                    //need this to bypass client validation
                    //for buttons that submit but do not process the
                    //whole form.
                    $onClick = $this->getAttribute('onclick');
                    $skip = 'skipClientValidation = true;';
                    $onClick = ($onClick !== null)?$skip.' '.$onClick:$skip;
                    $this->updateAttributes(array('onclick'=>$onClick));
                }
                return true;
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);

    } // end func onQuickFormEvent
    /**
     * Slightly different container template when frozen. Don't want to display a submit
     * button if the form is frozen.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }
    
    function freeze(){
        $this->_flagFrozen = true;
    }
    
}
?>