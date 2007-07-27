<?php
require_once('HTML/QuickForm/advcheckbox.php');

/**
 * HTML class for a advcheckbox type element
 *
 * default behavior special for Moodle is to return '0' if not checked
 * '1' for checked.
 *
 * * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_advcheckbox extends HTML_QuickForm_advcheckbox{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    /**
     * Class constructor
     *
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field label
     * @param     string    $text           (optional)Text to put after the checkbox
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string
     *                                      or an associative array
     * @param     mixed     $values         (optional)Values to pass if checked or not checked
     *
     * @since     1.0
     * @access    public
     * @return    void
     */
    function MoodleQuickForm_advcheckbox($elementName=null, $elementLabel=null, $text=null, $attributes=null, $values=null)
    {
        if ($values === null){
            $values = array(0, 1);
        }
        parent::HTML_QuickForm_advcheckbox($elementName, $elementLabel, $text, $attributes, $values);
    } //end constructor


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
    * advcheckboxes. Per idea of Alexander Radivanovich.
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
     * Slightly different container template when frozen. Don't want to use a label tag
     * with a for attribute in that case for the element label but instead use a div.
     * Templates are defined in renderer constructor.
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