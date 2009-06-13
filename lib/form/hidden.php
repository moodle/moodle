<?php
require_once('HTML/QuickForm/hidden.php');

/**
 * HTML class for a hidden type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_hidden extends HTML_QuickForm_hidden{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';

    function MoodleQuickForm_hidden($elementName=null, $value='', $attributes=null) {
        parent::HTML_QuickForm_hidden($elementName, $value, $attributes);
    }

    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){

    }
    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton(){
        return '';
    }
}
?>