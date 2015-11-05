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
 * Contains HTML class for a select type element with options containing link
 *
 * @package   core_form
 * @copyright 2008 Nicolas Connault <nicolasconnault@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/select.php');

/**
 * select type form element
 *
 * HTML class for a select type element with options containing link
 *
 * @package   core_form
 * @category  form
 * @copyright 2008 Nicolas Connault <nicolasconnault@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_selectwithlink extends HTML_QuickForm_select{
    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel=false;

    /** @var string url to which select option will be posted */
    var $_link=null;

    /** @var string data which will be posted to link */
    var $_linklabel=null;

    /** @var string url return link */
    var $_linkreturn=null;

    /**
     * constructor
     *
     * @param string $elementName Select name attribute
     * @param mixed $elementLabel Label(s) for the select
     * @param array $options Data to be used to populate options
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     * @param bool $linkdata data to be posted
     */
    public function __construct($elementName=null, $elementLabel=null, $options=null, $attributes=null, $linkdata=null) {
        if (!empty($linkdata['link']) && !empty($linkdata['label'])) {
            $this->_link = $linkdata['link'];
            $this->_linklabel = $linkdata['label'];
        }

        if (!empty($linkdata['return'])) {
            $this->_linkreturn = $linkdata['return'];
        }

        parent::__construct($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_selectwithlink($elementName=null, $elementLabel=null, $options=null, $attributes=null, $linkdata=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $options, $attributes, $linkdata);
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
     * Returns the SELECT in HTML
     *
     * @return string
     */
    function toHtml(){
        $retval = '';
        if ($this->_hiddenLabel){
            $this->_generateId();
            $retval = '<label class="accesshide" for="'.$this->getAttribute('id').'" >'.
                        $this->getLabel().'</label>'.parent::toHtml();
        } else {
             $retval = parent::toHtml();
        }

        if (!empty($this->_link)) {
            if (!empty($this->_linkreturn) && is_array($this->_linkreturn)) {
                $appendchar = '?';
                if (strstr($this->_link, '?')) {
                    $appendchar = '&amp;';
                }

                foreach ($this->_linkreturn as $key => $val) {
                    $this->_link .= $appendchar."$key=$val";
                    $appendchar = '&amp;';
                }
            }

            $retval .= '<a style="margin-left: 5px" href="'.$this->_link.'">'.$this->_linklabel.'</a>';
        }

        return $retval;
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
        if (empty($this->_options)) {
            return $this->_prepareValue(null, $assoc);
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
            return $this->_prepareValue(null, $assoc);
        }
        if ($this->getMultiple()) {
            return $this->_prepareValue($cleaned, $assoc);
        } else {
            return $this->_prepareValue($cleaned[0], $assoc);
        }
    }
}
