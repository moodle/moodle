<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Adam Daniel <adaniel1@eesus.jnj.com>                        |
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+

require_once('HTML/QuickForm/element.php');

/**
 * Class to dynamically create an HTML SELECT with all options grouped in optgroups
 *
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class MoodleQuickForm_selectgroups extends HTML_QuickForm_element {

    // {{{ properties

    /** add choose option */
    var $showchoose = false;

    /**
     * Contains the select optgroups
     *
     * @var       array
     * @since     1.0
     * @access    private
     */
    var $_optGroups = array();

    /**
     * Default values of the SELECT
     *
     * @var       string
     * @since     1.0
     * @access    private
     */
    var $_values = null;

    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';
    var $_hiddenLabel=false;

    /**
     * Class constructor
     *
     * @param     string    Select name attribute
     * @param     mixed     Label(s) for the select
     * @param     mixed     Data to be used to populate options
     * @param     mixed     An array whose keys are labels for optgroups and whose values are arrays similar to those passed
     *                          to the select element with keys that are values for options and values are strings for display.
     * @param     mixed     Either a typical HTML attribute string or an associative array
     * @param     bool      add standard moodle "Choose..." option as first item
     * @since     1.0
     * @access    public
     * @return    void
     */
    function MoodleQuickForm_selectgroups($elementName=null, $elementLabel=null, $optgrps=null, $attributes=null, $showchoose=false)
    {
        $this->showchoose = $showchoose;
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'selectgroups';
        if (isset($optgrps)) {
            $this->loadArrayOptGroups($optgrps);
        }
    } //end constructor

    // }}}
    // {{{ apiVersion()


    /**
     * Sets the default values of the select box
     *
     * @param     mixed    $values  Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setSelected($values)
    {
        if (is_string($values) && $this->getMultiple()) {
            $values = preg_split("/[ ]?,[ ]?/", $values);
        }
        if (is_array($values)) {
            $this->_values = array_values($values);
        } else {
            $this->_values = array($values);
        }
    } //end func setSelected

    // }}}
    // {{{ getSelected()

    /**
     * Returns an array of the selected values
     *
     * @since     1.0
     * @access    public
     * @return    array of selected values
     */
    function getSelected()
    {
        return $this->_values;
    } // end func getSelected

    // }}}
    // {{{ setName()

    /**
     * Sets the input field name
     *
     * @param     string    $name   Input field name attribute
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setName($name)
    {
        $this->updateAttributes(array('name' => $name));
    } //end func setName

    // }}}
    // {{{ getName()

    /**
     * Returns the element name
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getName()
    {
        return $this->getAttribute('name');
    } //end func getName

    // }}}
    // {{{ getPrivateName()

    /**
     * Returns the element name (possibly with brackets appended)
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getPrivateName()
    {
        if ($this->getAttribute('multiple')) {
            return $this->getName() . '[]';
        } else {
            return $this->getName();
        }
    } //end func getPrivateName

    // }}}
    // {{{ setValue()

    /**
     * Sets the value of the form element
     *
     * @param     mixed    $values  Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setValue($value)
    {
        $this->setSelected($value);
    } // end func setValue

    // }}}
    // {{{ getValue()

    /**
     * Returns an array of the selected values
     *
     * @since     1.0
     * @access    public
     * @return    array of selected values
     */
    function getValue()
    {
        return $this->_values;
    } // end func getValue

    // }}}
    // {{{ setSize()

    /**
     * Sets the select field size, only applies to 'multiple' selects
     *
     * @param     int    $size  Size of select  field
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setSize($size)
    {
        $this->updateAttributes(array('size' => $size));
    } //end func setSize

    // }}}
    // {{{ getSize()

    /**
     * Returns the select field size
     *
     * @since     1.0
     * @access    public
     * @return    int
     */
    function getSize()
    {
        return $this->getAttribute('size');
    } //end func getSize

    // }}}
    // {{{ setMultiple()

    /**
     * Sets the select mutiple attribute
     *
     * @param     bool    $multiple  Whether the select supports multi-selections
     * @since     1.2
     * @access    public
     * @return    void
     */
    function setMultiple($multiple)
    {
        if ($multiple) {
            $this->updateAttributes(array('multiple' => 'multiple'));
        } else {
            $this->removeAttribute('multiple');
        }
    } //end func setMultiple

    // }}}
    // {{{ getMultiple()

    /**
     * Returns the select mutiple attribute
     *
     * @since     1.2
     * @access    public
     * @return    bool    true if multiple select, false otherwise
     */
    function getMultiple()
    {
        return (bool)$this->getAttribute('multiple');
    } //end func getMultiple

    /**
     * Loads the options from an associative array
     *
     * @param     array    $arr     Associative array of options
     * @param     mixed    $values  (optional) Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function loadArrayOptGroups($arr, $values=null)
    {
        if (!is_array($arr)) {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadArrayOptGroups is not a valid array');
        }
        if (isset($values)) {
            $this->setSelected($values);
        }
        foreach ($arr as $key => $val) {
            // Warning: new API since release 2.3
            $this->addOptGroup($key, $val);
        }
        return true;
    }
    /**
     * Adds a new OPTION to the SELECT
     *
     * @param     string    $text       Display text for the OPTION
     * @param     string    $value      Value for the OPTION
     * @param     mixed     $attributes Either a typical HTML attribute string
     *                                  or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    function addOptGroup($text, $value, $attributes=null)
    {
        if (null === $attributes) {
            $attributes = array('label' => $text);
        } else {
            $attributes = $this->_parseAttributes($attributes);
            $this->_updateAttrArray($attributes, array('label' => $text));
        }
        $index = count($this->_optGroups);
        $this->_optGroups[$index] = array('attr' => $attributes);
        $this->loadArrayOptions($index, $value);
    }

    /**
     * Loads the options from an associative array
     *
     * @param     array    $arr     Associative array of options
     * @param     mixed    $values  (optional) Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function loadArrayOptions($optgroup, $arr, $values=null)
    {
        if (!is_array($arr)) {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadArray is not a valid array');
        }
        if (isset($values)) {
            $this->setSelected($values);
        }
        foreach ($arr as $key => $val) {
            // Warning: new API since release 2.3
            $this->addOption($optgroup, $val, $key);
        }
        return true;
    }

    /**
     * Adds a new OPTION to an optgroup
     *
     * @param     string    $text       Display text for the OPTION
     * @param     string    $value      Value for the OPTION
     * @param     mixed     $attributes Either a typical HTML attribute string
     *                                  or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    function addOption($optgroup, $text, $value, $attributes=null)
    {
        if (null === $attributes) {
            $attributes = array('value' => $value);
        } else {
            $attributes = $this->_parseAttributes($attributes);
            if (isset($attributes['selected'])) {
                // the 'selected' attribute will be set in toHtml()
                $this->_removeAttr('selected', $attributes);
                if (is_null($this->_values)) {
                    $this->_values = array($value);
                } elseif (!in_array($value, $this->_values)) {
                    $this->_values[] = $value;
                }
            }
            $this->_updateAttrArray($attributes, array('value' => $value));
        }
        $this->_optGroups[$optgroup]['options'][] = array('text' => $text, 'attr' => $attributes);
    }

    /**
     * Returns the SELECT in HTML
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $tabs    = $this->_getTabs();
            $strHtml = '';

            if ($this->getComment() != '') {
                $strHtml .= $tabs . '<!-- ' . $this->getComment() . " //-->\n";
            }

            if (!$this->getMultiple()) {
                $attrString = $this->_getAttrString($this->_attributes);
            } else {
                $myName = $this->getName();
                $this->setName($myName . '[]');
                $attrString = $this->_getAttrString($this->_attributes);
                $this->setName($myName);
            }
            $strHtml .= $tabs;
            if ($this->_hiddenLabel){
                $this->_generateId();
                $strHtml .= '<label class="accesshide" for="'.$this->getAttribute('id').'" >'.
                            $this->getLabel().'</label>';
            }
            $strHtml .=  '<select' . $attrString . ">\n";
            if ($this->showchoose) {
                $strHtml .= $tabs . "\t\t<option value=\"\">" . get_string('choose') . "...</option>\n";
            }
            foreach ($this->_optGroups as $optGroup) {
                if (empty($optGroup['options'])) {
                    //xhtml strict
                    continue;
                }
                $strHtml .= $tabs . "\t<optgroup" . ($this->_getAttrString($optGroup['attr'])) . '>';
                foreach ($optGroup['options'] as $option){
                    if (is_array($this->_values) && in_array((string)$option['attr']['value'], $this->_values)) {
                        $this->_updateAttrArray($option['attr'], array('selected' => 'selected'));
                    }
                    $strHtml .= $tabs . "\t\t<option" . $this->_getAttrString($option['attr']) . '>' .
                                $option['text'] . "</option>\n";
                }
                $strHtml .= $tabs . "\t</optgroup>\n";
            }
            return $strHtml . $tabs . '</select>';
        }
    } //end func toHtml

    // }}}
    // {{{ getFrozenHtml()

    /**
     * Returns the value of field without HTML tags
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getFrozenHtml()
    {
        $value = array();
        if (is_array($this->_values)) {
            foreach ($this->_values as $key => $val) {
                foreach ($this->_optGroups as $optGroup) {
                    for ($i = 0, $optCount = count($optGroup['options']); $i < $optCount; $i++) {
                        if ((string)$val == (string)$optGroup['options'][$i]['attr']['value']) {
                            $value[$key] = $optGroup['options'][$i]['text'];
                            break;
                        }
                    }
                }
            }
        }
        $html = empty($value)? '&nbsp;': join('<br />', $value);
        if ($this->_persistantFreeze) {
            $name = $this->getPrivateName();
            // Only use id attribute if doing single hidden input
            if (1 == count($value)) {
                $id     = $this->getAttribute('id');
                $idAttr = isset($id)? array('id' => $id): array();
            } else {
                $idAttr = array();
            }
            foreach ($value as $key => $item) {
                $html .= '<input' . $this->_getAttrString(array(
                             'type'  => 'hidden',
                             'name'  => $name,
                             'value' => $this->_values[$key]
                         ) + $idAttr) . ' />';
            }
        }
        return $html;
    } //end func getFrozenHtml

    // }}}
    // {{{ exportValue()

   /**
    * We check the options and return only the values that _could_ have been
    * selected. We also return a scalar value if select is not "multiple"
    */
    function exportValue(&$submitValues, $assoc = false)
    {
        if (empty($this->_optGroups)) {
            return $this->_prepareValue(null, $assoc);
        }

        $value = $this->_findValue($submitValues);
        if (is_null($value)) {
            $value = $this->getValue();
        }
        $value = (array)$value;

        $cleaned = array();
        foreach ($value as $v) {
            foreach ($this->_optGroups as $optGroup){
                if (empty($optGroup['options'])) {
                    continue;
                }
                foreach ($optGroup['options'] as $option) {
                    if ((string)$option['attr']['value'] === (string)$v) {
                        $cleaned[] = (string)$option['attr']['value'];
                        break;
                    }
                }
            }
        }

        if (empty($cleaned)) {
            return $this->_prepareValue(null, $assoc);
        }
        if ($this->getMultiple()) {
            return $this->_prepareValue($cleaned, $assoc);
        } else {
            return $this->_prepareValue($cleaned[0], $assoc);
        }
    }
    
    // }}}
    // {{{ onQuickFormEvent()

    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ('updateValue' == $event) {
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
                $this->setValue($value);
            }
            return true;
        } else {
            return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }
    function setHiddenLabel($hiddenLabel){
        $this->_hiddenLabel = $hiddenLabel;
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
            $this->updateAttributes(array('id' => 'id_'. substr(md5(microtime() . $idx++), 0, 6)));
        }
    } // end func _generateId
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
}
