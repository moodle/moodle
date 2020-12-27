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
 * Contains HTML class for a select type element
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/select.php');
require_once('templatable_form_element.php');

/**
 * select type form element
 *
 * HTML class for a select type element
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_select extends HTML_QuickForm_select implements templatable {

    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel=false;

    /**
     * constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param mixed $options Data to be used to populate options
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementName=null, $elementLabel=null, $options=null, $attributes=null) {
        parent::__construct($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_select($elementName=null, $elementLabel=null, $options=null, $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $options, $attributes);
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
     * Returns HTML for select form element.
     *
     * @return string
     */
    function toHtml(){
        $html = '';
        if ($this->getMultiple()) {
            // Adding an hidden field forces the browser to send an empty data even though the user did not
            // select any element. This value will be cleaned up in self::exportValue() as it will not be part
            // of the select options.
            $html .= '<input type="hidden" name="'.$this->getName().'" value="_qf__force_multiselect_submission">';
        }
        if ($this->_hiddenLabel){
            $this->_generateId();
            $html .= '<label class="accesshide" for="'.$this->getAttribute('id').'" >'.$this->getLabel().'</label>';
        }
        $html .= parent::toHtml();
        return $html;
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
     * Removes an OPTION from the SELECT
     *
     * @param string $value Value for the OPTION to remove
     * @return void
     */
    function removeOption($value)
    {
        $key=array_search($value, $this->_values);
        if ($key!==FALSE and $key!==null) {
            unset($this->_values[$key]);
        }
        foreach ($this->_options as $key=>$option){
            if ($option['attr']['value']==$value){
                unset($this->_options[$key]);
                // we must reindex the options because the ugly code in quickforms' select.php expects that keys are 0,1,2,3... !?!?
                $this->_options = array_merge($this->_options);
                return;
            }
        }
    }

    /**
     * Removes all OPTIONs from the SELECT
     */
    function removeOptions()
    {
        $this->_options = array();
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
        $emptyvalue = $this->getMultiple() ? [] : null;
        if (empty($this->_options)) {
            return $this->_prepareValue($emptyvalue, $assoc);
        }

        $value = $this->_findValue($submitValues);
        if (is_null($value)) {
            $value = $this->getValue();
        }
        $value = (array)$value;

        $cleaned = array();
        foreach ($value as $v) {
            foreach ($this->_options as $option) {
                if ((string)$option['attr']['value'] === (string)$v) {
                    $cleaned[] = (string)$option['attr']['value'];
                    break;
                }
            }
        }

        if (empty($cleaned)) {
            return $this->_prepareValue($emptyvalue, $assoc);
        }
        if ($this->getMultiple()) {
            return $this->_prepareValue($cleaned, $assoc);
        } else {
            return $this->_prepareValue($cleaned[0], $assoc);
        }
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);

        $options = [];
        // Standard option attributes.
        $standardoptionattributes = ['text', 'value', 'selected', 'disabled'];
        foreach ($this->_options as $option) {
            if (is_array($this->_values) && in_array( (string) $option['attr']['value'], $this->_values)) {
                $this->_updateAttrArray($option['attr'], ['selected' => 'selected']);
            }
            $o = [
                'text' => $option['text'],
                'value' => $option['attr']['value'],
                'selected' => !empty($option['attr']['selected']),
                'disabled' => !empty($option['attr']['disabled']),
            ];
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
        $context['options'] = $options;
        $context['nameraw'] = $this->getName();

        return $context;
    }
}
