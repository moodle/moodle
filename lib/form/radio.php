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
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }
   /**
    * Automatically generates and assigns an 'id' attribute for the element.
    *
    * Currently used to ensure that labels work on radio buttons and
    * checkboxes. Per idea of Alexander Radivanovich.
    * Overriden in moodleforms to remove qf_ prefix.
    *
    * @access private
    * @return void
    */
    function _generateId()
    {
        static $idx = 1;

        if (!$this->getAttribute('id')) {
            $this->updateAttributes(array('id' => 'id_'.substr(md5(microtime() . $idx++), 0, 6)));
        }
    } // end func _generateId
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
    function toHtml()
    {
        return '<span>' . parent::toHtml() . '</span>';
    }
}
?>