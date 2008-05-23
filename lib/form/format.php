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
     * Whether we are using html editor.
     *
     * @var unknown_type
     */
    var $_useHtmlEditor;
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
        if ($elementName == null){
            $elementName = 'format';
        }
        if ($elementLabel == null){
            $elementLabel = get_string('format');
        }
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_type = 'format';

        $this->_useHtmlEditor=$useHtmlEditor;
        if ($this->_useHtmlEditor === null){
            $this->_useHtmlEditor=can_use_html_editor();
        }

        $this->setPersistantFreeze($this->_useHtmlEditor);
        if ($this->_useHtmlEditor){
            $this->freeze();
        } else {
            $this->unfreeze();
        }
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
                $menu = format_text_menu();
                $this->load($menu);
                $this->setHelpButton(array('textformat', get_string('helpformatting')));
                break;
            case 'updateValue' :
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    $value = $this->_findValue($caller->_submitValues);
                    // Fix for bug #4465 & #5269
                    // XXX: should we push this to element::onQuickFormEvent()?
                    if (null === $value && (!$caller->isSubmitted() || !$this->getMultiple())) {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (null !== $value) {
                    $format=$value;
                }else{
                    $format=FORMAT_MOODLE;
                }
                if ($this->_useHtmlEditor){
                    $this->setValue(array(FORMAT_HTML));
                }else{
                    $this->setValue(array($format));
                }
                return true;
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

}
?>