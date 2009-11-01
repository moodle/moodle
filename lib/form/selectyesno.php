<?php
global $CFG;
require_once "$CFG->libdir/form/select.php";

/**
 * HTML class for a simple yes/ no drop down element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_selectyesno extends MoodleQuickForm_select{


    /**
     * Class constructor
     *
     * @param     string    Select name attribute
     * @param     mixed     Label(s) for the select
     * @param     mixed     Either a typical HTML attribute string or an associative array
     * @param     mixed     $options ignored
     * @access    public
     * @return    void
     */
    function MoodleQuickForm_selectyesno($elementName=null, $elementLabel=null, $attributes=null, $options=null)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes, null);
        $this->_type = 'selectyesno';

    } //end constructor

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param     string    $event  Name of event
     * @param     mixed     $arg    event arguments
     * @param     object    $caller calling object
     * @since     1.0
     * @access    public
     * @return    mixed
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $choices=array();
                $choices[0] = get_string('no');
                $choices[1] = get_string('yes');
                $this->load($choices);
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

}
