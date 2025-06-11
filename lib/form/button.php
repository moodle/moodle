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
 * Button form element
 *
 * Contains HTML class for a button type element
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("HTML/QuickForm/button.php");
require_once('templatable_form_element.php');

/**
 * HTML class for a button type element
 *
 * Overloaded {@link HTML_QuickForm_button} to add help button
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_button extends HTML_QuickForm_button implements templatable
{
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var bool if true label will be hidden. */
    protected $_hiddenLabel = false;

    /**
     * Any class apart from 'btn' would be overridden with this content.
     *
     * By default, buttons will utilize the btn-secondary class. However, there are cases where we
     * require a button with different stylings (e.g. btn-primary). In these cases, $customclassoverride will override
     * the defaults mentioned previously and utilize the provided class(es).
     *
     * @var null|string $customclassoverride Custom class override for the input element
     */
    protected $customclassoverride;

    /**
     * constructor
     *
     * @param string $elementName (optional) name for the button
     * @param string $value (optional) value for the button
     * @param mixed $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     * @param array $options Options to further customise the button. Currently accepted options are:
     *                  customclassoverride String The CSS class to use for the button instead of the standard
     *                                             btn-primary and btn-secondary classes.
     */
    public function __construct($elementName=null, $value=null, $attributes=null, $options = []) {
        parent::__construct($elementName, $value, $attributes);

        $this->customclassoverride = $options['customclassoverride'] ?? null;
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_button($elementName=null, $value=null, $attributes=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $value, $attributes);
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
     * Slightly different container template when frozen.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }

    /**
     * Sets label to be hidden
     *
     * @param bool $hiddenLabel sets if label should be hidden
     */
    public function setHiddenLabel($hiddenLabel) {
        $this->_hiddenLabel = $hiddenLabel;
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);

        if ($this->customclassoverride) {
            $context['customclassoverride'] = $this->customclassoverride;
        }
        return $context;
    }
}
