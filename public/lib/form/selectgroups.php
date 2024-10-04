<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * select type form element
 *
 * Class to dynamically create an HTML SELECT with all options grouped in optgroups
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/element.php');
require_once('templatable_form_element.php');

/**
 * select type form element
 *
 * Class to dynamically create an HTML SELECT with all options grouped in optgroups
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_selectgroups extends HTML_QuickForm_element implements templatable {

    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /** @var bool add choose option */
    var $showchoose = false;

    /** @var array Contains the select optgroups */
    var $_optGroups = array();

    /** @var array Default values of the SELECT */
    var $_values = null;

    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel=false;

    /**
     * Class constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param array $optgrps Data to be used to populate options
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     * @param bool $showchoose add standard moodle "Choose..." option as first item
     */
    public function __construct($elementName=null, $elementLabel=null, $optgrps=null, $attributes=null, $showchoose=false)
    {
        $this->showchoose = $showchoose;
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'selectgroups';
        if (isset($optgrps)) {
            $this->loadArrayOptGroups($optgrps);
        }
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_selectgroups($elementName=null, $elementLabel=null, $optgrps=null, $attributes=null, $showchoose=false) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $optgrps, $attributes, $showchoose);
    }

    /**
     * Sets the default values of the select box
     *
     * @param mixed $values Array or comma delimited string of selected values
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
    }

    /**
     * Returns an array of the selected values
     *
     * @return array of selected values
     */
    function getSelected()
    {
        return $this->_values;
    }

    /**
     * Sets the input field name
     *
     * @param string $name Input field name attribute
     */
    function setName($name)
    {
        $this->updateAttributes(array('name' => $name));
    }

    /**
     * Returns the element name
     *
     * @return string
     */
    function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * Returns the element name (possibly with brackets appended)
     *
     * @return string
     */
    function getPrivateName()
    {
        if ($this->getAttribute('multiple')) {
            return $this->getName() . '[]';
        } else {
            return $this->getName();
        }
    }

    /**
     * Sets the value of the form element
     *
     * @param mixed $value Array or comma delimited string of selected values
     */
    function setValue($value)
    {
        $this->setSelected($value);
    }

    /**
     * Returns an array of the selected values
     *
     * @return array of selected values
     */
    function getValue()
    {
        return $this->_values;
    }

    /**
     * Sets the select field size, only applies to 'multiple' selects
     *
     * @param int $size Size of select  field
     */
    function setSize($size)
    {
        $this->updateAttributes(array('size' => $size));
    }

    /**
     * Returns the select field size
     *
     * @return int
     */
    function getSize()
    {
        return $this->getAttribute('size');
    }

    /**
     * Sets the select mutiple attribute
     *
     * @param bool $multiple Whether the select supports multi-selections
     */
    function setMultiple($multiple)
    {
        if ($multiple) {
            $this->updateAttributes(array('multiple' => 'multiple'));
        } else {
            $this->removeAttribute('multiple');
        }
    }

    /**
     * Returns the select mutiple attribute
     *
     * @return bool true if multiple select, false otherwise
     */
    function getMultiple()
    {
        return (bool)$this->getAttribute('multiple');
    }

    /**
     * Loads the options from an associative array
     *
     * @param array $arr Associative array of options
     * @param mixed $values (optional) Array or comma delimited string of selected values
     * @return PEAR_Error|bool on error or true
     * @throws PEAR_Error
     */
    function loadArrayOptGroups($arr, $values=null)
    {
        if (!is_array($arr)) {
            return self::raiseError('Argument 1 of HTML_Select::loadArrayOptGroups is not a valid array');
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
     * @param string $text Display text for the OPTION
     * @param string $value Value for the OPTION
     * @param mixed $attributes Either a typical HTML attribute string
     *              or an associative array
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
     * @param string $optgroup name of the options group
     * @param array $arr Associative array of options
     * @param mixed $values (optional) Array or comma delimited string of selected values
     * @return PEAR_Error|bool on error or true
     * @throws PEAR_Error
     */
    function loadArrayOptions($optgroup, $arr, $values=null)
    {
        if (!is_array($arr)) {
            return self::raiseError('Argument 1 of HTML_Select::loadArray is not a valid array');
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
     * @param string $optgroup name of the option group
     * @param string $text Display text for the OPTION
     * @param string $value Value for the OPTION
     * @param mixed $attributes Either a typical HTML attribute string
     *              or an associative array
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
     * @return string
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
    }

    /**
     * Returns the value of field without HTML tags
     *
     * @return string
     */
    function getFrozenHtml()
    {
        $value = array();
        if (is_array($this->_values)) {
            foreach ($this->_values as $key => $val) {
                foreach ($this->_optGroups as $optGroup) {
                    if (empty($optGroup['options'])) {
                        continue;
                    }
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
    }

   /**
    * We check the options and return only the values that _could_ have been
    * selected. We also return a scalar value if select is not "multiple"
    *
    * @param array $submitValues submitted values
    * @param bool $assoc if true the retured value is associated array
    * @return mixed
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

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
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

    /**
     * Sets label to be hidden
     *
     * @param bool $hiddenLabel sets if label should be hidden
     */
    function setHiddenLabel($hiddenLabel){
        $this->_hiddenLabel = $hiddenLabel;
    }

    /**
     * get html for help button
     *
     * @return string html for help button
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

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);
        $optiongroups = [];
        if ($this->showchoose) {
            $optionsgroups[] = [
                'text' => get_string('choosedots')
            ];
        }

        // Standard option attributes.
        $standardoptionattributes = ['text', 'value', 'selected', 'disabled'];
        foreach ($this->_optGroups as $group) {
            $options = [];

            if (empty($group['options'])) {
                continue;
            }
            foreach ($group['options'] as $option) {
                $o = ['value' => (string)$option['attr']['value']];
                if (is_array($this->_values) && in_array($o['value'], $this->_values)) {
                    $o['selected'] = true;
                } else {
                    $o['selected'] = false;
                }
                $o['text'] = $option['text'];
                $o['disabled'] = !empty($option['attr']['disabled']);
                // Set other attributes.
                $otheroptionattributes = [];
                foreach ($option['attr'] as $attr => $value) {
                    if (!in_array($attr, $standardoptionattributes) && $attr != 'class' && !is_object($value)) {
                        $otheroptionattributes[] = $attr . '="' . s($value) . '"';
                    }
                }
                $o['optionattributes'] = implode(' ', $otheroptionattributes);
                $options[] = $o;
            }

            $og = [
                'text' => $group['attr']['label'],
                'options' => $options
            ];

            $optiongroups[] = $og;
        }
        $context['optiongroups'] = $optiongroups;
        // If the select is a static element and does not allow the user to change the value (Ex: Auth method),
        // we need to change the label to static.
        $context['staticlabel'] = $this->_flagFrozen;

        return $context;
    }
}
