<?php
global $CFG;
require_once "$CFG->libdir/form/select.php";

/**
 * HTML class for a drop down element to select groupmode in an activity mod update form
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_modgroupmode extends MoodleQuickForm_select{
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
    function MoodleQuickForm_modgroupmode($elementName=null, $elementLabel=null, $attributes=null, $options=null)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes, null);
        $this->_type = 'modgroupmode';


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
                $choices = array();
                
                $choices[NOGROUPS] = get_string('groupsnone');
                $choices[SEPARATEGROUPS] = get_string('groupsseparate');
                $choices[VISIBLEGROUPS] = get_string('groupsvisible');
                
                $this->setHelpButton(array('groupmode', get_string('groupmode')));
                $this->load($choices);
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

}
?>
