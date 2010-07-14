<?php
global $CFG;
require_once "$CFG->libdir/form/select.php";

/**
 * HTML class for a editor format drop down element
 *
 * @author       Jamie Pratt
 * @access       public
 */
class MoodleQuickForm_format extends MoodleQuickForm_select{

    /**
     * Class constructor
     *
     * @param     string    Select name attribute
     * @param     mixed     Label(s) for the select
     * @param     mixed     Either a typical HTML attribute string or an associative array
     * @param     mixed     Either a string returned from can_use_html_editor() or false for no html editor
     *                      default 'detect' tells element to use html editor if it is available.
     * @access    public
     * @return    void
     */
    function MoodleQuickForm_format($elementName=null, $elementLabel=null, $attributes=null, $useHtmlEditor=null)
    {
        throw new coding_exception('MFORMS: Coding error, text formats are handled only by new editor element.');
    } //end constructor

}
