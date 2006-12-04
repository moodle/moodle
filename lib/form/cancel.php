<?php
require_once('HTML/QuickForm/submit.php');

/**
 * HTML class for a submit type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_cancel extends HTML_QuickForm_submit
{
    // {{{ constructor

    /**
     * Class constructor
     *
     * @since     1.0
     * @access    public
     * @return    void
     */
    function MoodleQuickForm_cancel($elementName=null, $value=null, $attributes=null)
    {
        if ($elementName==null){
            $elementName='cancel';
        }
        if ($value==null){
            $value=get_string('cancel');
        }
        HTML_QuickForm_submit::HTML_QuickForm_submit($elementName, $value, $attributes);
        $this->updateAttributes(array('onclick'=>'return this.form.submit();'));
    } //end constructor
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $className = get_class($this);
                $this->$className($arg[0], $arg[1], $arg[2]);
                $caller->_registerCancelButton($this->getName());
                return true;
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    } // end func onQuickFormEvent

    // }}}
} //end class HTML_QuickForm_submit
?>