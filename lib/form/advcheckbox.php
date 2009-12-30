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
     * Group to which this checkbox belongs (for select all/select none button)
     * @var string $_group
     */
    var $_group;

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

        if (!is_null($attributes['group'])) {

            $this->_group = 'checkboxgroup' . $attributes['group'];
            unset($attributes['group']);
            if (is_null($attributes)) {
                $attributes = array();
                $attributes['class'] .= " $this->_group";
            } elseif (is_array($attributes)) {
                if (isset($attributes['class'])) {
                    $attributes['class'] .= " $this->_group";
                } else {
                    $attributes['class'] = $this->_group;
                }
            } elseif ($strpos = stripos($attributes, 'class="')) {
                $attributes = str_ireplace('class="', 'class="' . $this->_group . ' ', $attributes);
            } else {
                $attributes .= ' class="' . $this->_group . '"';
            }
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
