<?php
require_once('HTML/QuickForm/file.php');

/**
 * HTML class for a form element to upload a file
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_file extends HTML_QuickForm_file{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    function MoodleQuickForm_file($elementName=null, $elementLabel=null, $attributes=null) {
        parent::HTML_QuickForm_file($elementName, $elementLabel, $attributes);
    }
    /**
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        if (!is_array($helpbuttonargs)){
            $helpbuttonargs=array($helpbuttonargs);
        }else{
            $helpbuttonargs=$helpbuttonargs;
        }
        //we do this to to return html instead of printing it
        //without having to specify it in every call to make a button.
        if ('helpbutton' == $function){
            $defaultargs=array('', '', 'moodle', true, false, '', true);
            $helpbuttonargs=$helpbuttonargs + $defaultargs ;
        }
        $this->_helpbutton=call_user_func_array($function, $helpbuttonargs);
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

    /**
     * Override createElement event to add max files
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ($event == 'createElement') {
            $className = get_class($this);
            $this->$className($arg[0], $arg[1].' ('.get_string('maxsize', '', display_size($caller->getMaxFileSize())).')', $arg[2]);
            return true;
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
?>