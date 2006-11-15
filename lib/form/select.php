<?php
require_once('HTML/QuickForm/select.php');

/**
 * HTML class for a select type element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_select extends HTML_QuickForm_select{
    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
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
        $defaultargs=array('', '', 'moodle', true, false, '', true);
        $helpbuttonargs=$helpbuttonargs + $defaultargs ;
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
     * Removes an OPTION from the SELECT
     *
     * @param     string    $value      Value for the OPTION to remove
     * @since     1.0
     * @access    public
     * @return    void
     */
    function removeOption($value)
    {
        $key=array_search($value, $this->_values);
        if ($key!==FALSE || $key!==null) {
            unset($this->_values[$key]);
        }
        foreach ($this->_options as $key=>$option){
            if ($option['attr']['value']==$value){
                unset($this->_options[$key]);
                return;
            }
        }
    } // end func removeOption
}
?>