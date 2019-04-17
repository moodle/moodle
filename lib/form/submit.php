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
 * submit type form element
 *
 * Contains HTML class for a submit type element
 *
 * @package   core_form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("HTML/QuickForm/submit.php");
require_once('templatable_form_element.php');

/**
 * submit type form element
 *
 * HTML class for a submit type element
 *
 * @package   core_form
 * @category  form
 * @copyright 2006 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_submit extends HTML_QuickForm_submit implements templatable {
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /**
     * @var bool $primary Is this button a primary button?
     */
    protected $primary;

    /**
     * Any class apart from 'btn' would be overridden with this content.
     *
     * By default, submit buttons will utilize the btn-primary OR btn-secondary classes. However there are cases where we
     * require a submit button with different stylings (e.g. btn-link). In these cases, $customclassoverride will override
     * the defaults mentioned previously and utilize the provided class(es).
     *
     * @var string $customclassoverride Custom class override for the input element
     */
    protected $customclassoverride;

    /**
     * constructor
     *
     * @param string $elementName (optional) name of the field
     * @param string $value (optional) field label
     * @param string $attributes (optional) Either a typical HTML attribute string or an associative array
     * @param bool|null $primary Is this button a primary button?
     * @param array $options Options to further customise the submit button. Currently accepted options are:
     *                  customclassoverride String The CSS class to use for the button instead of the standard
     *                                             btn-primary and btn-secondary classes.
     */
    public function __construct($elementName=null, $value=null, $attributes=null, $primary = null, $options = []) {
        parent::__construct($elementName, $value, $attributes);

        // Fallback to legacy behaviour if no value specified.
        if (is_null($primary)) {
            $this->primary = $this->getName() != 'cancel';
        } else {
            $this->primary = $primary;
        }

        $this->customclassoverride = $options['customclassoverride'] ?? false;
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_submit($elementName=null, $value=null, $attributes=null, $primary = null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $value, $attributes, $primary);
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                parent::onQuickFormEvent($event, $arg, $caller);
                if ($caller->isNoSubmitButton($arg[0])){
                    //need this to bypass client validation
                    //for buttons that submit but do not process the
                    //whole form.
                    $onClick = $this->getAttribute('onclick');
                    $skip = 'skipClientValidation = true;';
                    $onClick = ($onClick !== null)?$skip.' '.$onClick:$skip;
                    $this->updateAttributes(array('onclick'=>$onClick));
                }
                return true;
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);

    }

    /**
     * Slightly different container template when frozen. Don't want to display a submit
     * button if the form is frozen.
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'actionbuttons';
        }
    }

    /**
     * Freeze the element so that only its value is returned
     */
    function freeze(){
        $this->_flagFrozen = true;
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);
        if (!$this->primary) {
            $context['secondary'] = true;
        }

        if ($this->customclassoverride) {
            $context['customclassoverride'] = $this->customclassoverride;
        }
        return $context;
    }
}
