<?php
require_once('HTML/QuickForm/textarea.php');

/**
 * HTML class for a textarea type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_textarea extends HTML_QuickForm_textarea{
    /**
     * Need to store id of form as we may need it for helpbutton
     *
     * @var string
     */
    var $_formid = '';
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';

    var $_hiddenLabel=false;

    function MoodleQuickForm_textarea($elementName=null, $elementLabel=null, $attributes=null) {
        parent::HTML_QuickForm_textarea($elementName, $elementLabel, $attributes);
    }
    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        global $SESSION;
        if (!is_array($helpbuttonargs)) {
            $helpbuttonargs = array($helpbuttonargs);
        } else {
            $helpbuttonargs = $helpbuttonargs;
        }
        //we do this to to return html instead of printing it
        //without having to specify it in every call to make a button.
        if ('helpbutton' == $function){
            $defaultargs = array('', '', 'moodle', true, false, '', true);
            $helpbuttonargs = $helpbuttonargs + $defaultargs ;
            if (in_array($helpbuttonargs[0], array('emoticons2', 'text2', 'richtext2'))) {
                $SESSION->inserttextform = $this->_formid;
                $SESSION->inserttextfield = $this->getAttribute('name');
            }
        } else if ('editorhelpbutton' == $function) {
            $specialhelp = array_intersect($helpbuttonargs, array('emoticons2', 'text2', 'richtext2'));
            if (!empty($specialhelp)) {
                $SESSION->inserttextform = $this->_formid;
                $SESSION->inserttextfield = $this->getAttribute('name');
            }
        }
        $this->_helpbutton = call_user_func_array($function, $helpbuttonargs);
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

    function setHiddenLabel($hiddenLabel){
        $this->_hiddenLabel = $hiddenLabel;
    }

    function toHtml(){
        if ($this->_hiddenLabel){
            $this->_generateId();
            return '<label class="accesshide" for="' . $this->getAttribute('id') . '" >' .
                    $this->getLabel() . '</label>' . parent::toHtml();
        } else {
            return parent::toHtml();
        }
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
                $this->_formid = $caller->getAttribute('id');
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    } // end func onQuickFormEvent
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
}
