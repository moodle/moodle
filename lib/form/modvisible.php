<?php
global $CFG;
require_once "$CFG->libdir/form/select.php";

/**
 * HTML class for a drop down element to select visibility in an activity mod update form
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_modvisible extends MoodleQuickForm_select{

    /**
     * Class constructor
     *
     * @param     string    $elementName Select name attribute
     * @param     mixed     $elementLabel Label(s) for the select
     * @param     mixed     $attributes Either a typical HTML attribute string or an associative array
     * @param     array     $options ignored
     * @access    public
     * @return    void
     */
    function MoodleQuickForm_modvisible($elementName=null, $elementLabel=null, $attributes=null, $options=null)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes, null);
        $this->_type = 'modvisible';


    } //end constructor

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param     string    $event  Name of event
     * @param     mixed     $arg    event arguments
     * @param     object    $caller calling object
     * @access    public
     * @return    mixed
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $choices=array();
                $choices[1] = get_string('show');
                $choices[0] = get_string('hide');
                $this->load($choices);
                break;

        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }
}
?>