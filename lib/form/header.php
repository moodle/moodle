<?php

require_once 'HTML/QuickForm/header.php';

/**
 * A pseudo-element used for adding headers to form
 *
 */
class MoodleQuickForm_header extends HTML_QuickForm_header
{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';

    function MoodleQuickForm_header($elementName = null, $text = null) {
        parent::HTML_QuickForm_header($elementName, $text);
    }

    // {{{ accept()

   /**
    * Accepts a renderer
    *
    * @param object     An HTML_QuickForm_Renderer object
    * @access public
    * @return void
    */
    function accept(&$renderer)
    {
        $this->_text .= $this->getHelpButton();
        $renderer->renderHeader($this);
    } // end func accept

    // }}}
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
} //end class MoodleQuickForm_header
