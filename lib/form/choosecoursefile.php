<?php //$Id$
global $CFG;
require_once "$CFG->libdir/form/group.php";

/**
 * Class for an element used to choose a file from the course files folder.
 *
 *
 * @author Jamie Pratt <me@jamiep.org>
 * @access public
 */
class MoodleQuickForm_choosecoursefile extends MoodleQuickForm_group
{
    /**
    * Options for element :
    *
    * $url must be relative to home page  eg /mod/survey/stuff.php
    * courseid => int course id if null then uses $COURSE global
    * width => int Height to assign to popup window
    * title => string Text to be displayed as popup page title
    * options => string List of additional options for popup window
    */
    var $_options = array('courseid'=>null,
                         'height'=>500, 'width'=>750, 'options'=>'none');

   /**
    * These complement separators, they are appended to the resultant HTML
    * @access   private
    * @var      array
    */
    var $_wrap = array('', '');

   /**
    * Class constructor
    *
    * @access   public
    * @param    string  Element's name
    * @param    mixed   Label(s) for an element
    * @param    array   Options to control the element's display
    * @param    mixed   Either a typical HTML attribute string or an associative array
    */
    function MoodleQuickForm_choosecoursefile($elementName = null, $elementLabel = null, $options = array(), $attributes = null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_appendName = true;
        $this->_type = 'choosecoursefile';
        // set the options, do not bother setting bogus ones
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (isset($this->_options[$name])) {
                    if (is_array($value) && is_array($this->_options[$name])) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
    }

    // }}}
    // {{{ _createElements()

    function _createElements() {
        global $COURSE;
        $this->_elements = array();

        if (!is_array($this->getAttributes()) || !array_key_exists('size', $this->getAttributes())) {
            $this->updateAttributes(array('size' => 48));
        }

        $this->_elements[0] =& MoodleQuickForm::createElement('text', 'value', '', $this->getAttributes());
        $this->_elements[1] =& MoodleQuickForm::createElement('button', 'popup', get_string('chooseafile', 'resource') .' ...');

        $button =& $this->_elements[1];

        if ($this->_options['courseid']!==null){
            $courseid=$this->_options['courseid'];
        } else {
            $courseid=$COURSE->id;
        }
        // first find out the text field id - this is a bit hacky, is there a better way?
        $choose = 'id_'.str_replace(array('[', ']'), array('_', ''), $this->getElementName(0));
        $url="/files/index.php?id=$courseid&choose=".$choose;

        if ($this->_options['options'] == 'none') {
            $options = 'menubar=0,location=0,scrollbars,resizable,width='. $this->_options['width'] .',height='. $this->_options['height'];
        }else{
            $options = $this->_options['options'];
        }
        $fullscreen = 0;

        $buttonattributes = array('title'=>get_string("chooseafile", "resource"),
                  'onclick'=>"return openpopup('$url', '".$button->getName()."', '$options', $fullscreen);");

        $button->updateAttributes($buttonattributes);
    }

    /**
     * Output a timestamp. Give it the name of the group.
     *
     * @param array $submitValues
     * @param bool $assoc
     * @return array
     */
    function exportValue(&$submitValues, $assoc = false)
    {
        $value = null;
        $valuearray = $this->_elements[0]->exportValue($submitValues[$this->getName()], true);
        $value[$this->getName()]=$valuearray['value'];
        return $value;
    }
    // }}}

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
        switch ($event) {
            case 'updateValue':
                // constant values override both default and submitted ones
                // default values are overriden by submitted
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    $value = $this->_findValue($caller->_submitValues);
                    if (null === $value) {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (!is_array($value)) {
                   $value = array('value' => $value);
                }
                if (null !== $value) {
                    $this->setValue($value);
                }
                return true;
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);

    } // end func onQuickFormEvent

}
?>
