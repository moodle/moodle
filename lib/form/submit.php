<?php

require_once('HTML/QuickForm/submit.php');

/**
 * HTML class for a submit type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_submit extends HTML_QuickForm_submit
{
    // {{{ constructor

    /**
     * Class constructor
     *
     * @param     string    Input field name attribute
     * @param     string    Input field value
     * @param     mixed     Either a typical HTML attribute string or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    function MoodleQuickForm_submit($elementName=null, $value=null, $attributes=null)
    {
        HTML_QuickForm_submit::HTML_QuickForm_submit($elementName, $value, $attributes);
        if ('cancel'==$elementName){
            //bypass form validation js :
            $this->updateAttributes(array('onclick'=>'this.form.submit();'));
        }
    } //end constructor

} //end class HTML_QuickForm_submit
?>
