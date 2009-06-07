<?php
require_once('HTML/QuickForm/checkbox.php');

/**
 * HTML class for a checkbox type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_checkbox extends HTML_QuickForm_checkbox{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    function MoodleQuickForm_checkbox($elementName=null, $elementLabel=null, $text='', $attributes=null) {
        parent::HTML_QuickForm_checkbox($elementName, $elementLabel, $text, $attributes);
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
        //fixes bug in quickforms which lets previous set value override submitted value if checkbox is not checked
        // and no value is submitted
        switch ($event) {
            case 'updateValue':
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    // if no boxes were checked, then there is no value in the array
                    // yet we don't want to display default value in this case
                    if ($caller->isSubmitted()) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {

                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                //fix here. setChecked should not be conditional
                $this->setChecked($value);
                break;
            default:
                parent::onQuickFormEvent($event, $arg, $caller);
        }
        return true;
    } // end func onQuickFormEvent
    function toHtml()
    {
        return '<span>' . parent::toHtml() . '</span>';
    }

    /**
     * Returns the disabled field. Accessibility: the return "[ ]" from parent
     * class is not acceptable for screenreader users, and we DO want a label.
     * @return    string
     */
    function getFrozenHtml()
    {
        //$this->_generateId();
        $output = '<input type="checkbox" disabled="disabled" id="'.$this->getAttribute('id').'" ';
        if ($this->getChecked()) {
            $output .= 'checked="checked" />'.$this->_getPersistantData();
        } else {
            $output .= '/>';
        }
        return $output;
    } //end func getFrozenHtml
}
?>