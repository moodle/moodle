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
 * Form element group
 *
 * Contains HTML class for group form element
 *
 * @package   core_form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("HTML/QuickForm/group.php");
require_once('templatable_form_element.php');

/**
 * HTML class for a form element group
 *
 * Overloaded {@link HTML_QuickForm_group} with default behavior modified for Moodle.
 *
 * @package   core_form
 * @category  form
 * @copyright 2007 Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_group extends HTML_QuickForm_group implements templatable {
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var MoodleQuickForm */
    protected $_mform = null;

    protected $_renderedfromtemplate = false;

    /**
     * constructor
     *
     * @param string $elementName (optional) name of the group
     * @param string $elementLabel (optional) group label
     * @param array $elements (optional) array of HTML_QuickForm_element elements to group
     * @param string $separator (optional) string to seperate elements.
     * @param string $appendName (optional) string to appened to grouped elements.
     */
    public function __construct($elementName=null, $elementLabel=null, $elements=null, $separator=null, $appendName = true) {
        parent::__construct($elementName, $elementLabel, $elements, $separator, $appendName);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_group($elementName=null, $elementLabel=null, $elements=null, $separator=null, $appendName = true) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $elements, $separator, $appendName);
    }

    /** @var string template type, would cause problems with client side validation so will leave for now */
    //var $_elementTemplateType='fieldset';

    /**
     * set html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * Returns element template, nodisplay/static/fieldset
     *
     * @return string
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            if ($this->getGroupType() == 'submit'){
                return 'nodisplay';
            } else {
                return 'static';
            }
        } else {
            if ($this->getGroupType() == 'submit') {
                return 'actionbuttons';
            }
            return 'fieldset';
        }
    }

    /**
     * Sets the grouped elements and hides label
     *
     * @param array $elements
     */
    function setElements($elements){
        parent::setElements($elements);
        foreach ($this->_elements as $element){
            if (method_exists($element, 'setHiddenLabel')){
                $element->setHiddenLabel(true);
            }
        }
    }

    /**
     * Stores the form this element was added to
     * This object is later used by {@link MoodleQuickForm_group::createElement()}
     * @param null|MoodleQuickForm $mform
     */
    public function setMoodleForm($mform) {
        if ($mform && $mform instanceof MoodleQuickForm) {
            $this->_mform = $mform;
        }
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * If this function is overridden and parent is not called the element must be responsible for
     * storing the MoodleQuickForm object, see {@link MoodleQuickForm_group::setMoodleForm()}
     *
     * @param     string $event Name of event
     * @param     mixed $arg event arguments
     * @param     mixed $caller calling object
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        $this->setMoodleForm($caller);
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Creates an element to add to the group
     * Expects the same arguments as MoodleQuickForm::createElement()
     */
    public function createFormElement() {
        if (!$this->_mform) {
            throw new coding_exception('You can not call createFormElement() on the group element that was not yet added to a form.');
        }
        return call_user_func_array([$this->_mform, 'createElement'], func_get_args());
    }

    public function export_for_template(renderer_base $output) {
        global $OUTPUT;

        $context = $this->export_for_template_base($output);

        $this->_renderedfromtemplate = true;

        include_once('HTML/QuickForm/Renderer/Default.php');

        $elements = [];
        $name = $this->getName();
        $i = 0;
        foreach ($this->_elements as $key => $element) {
            $elementname = '';
            if ($this->_appendName) {
                $elementname = $element->getName();
                if (isset($elementname)) {
                    $element->setName($name . '['. (strlen($elementname) ? $elementname : $key) .']');
                } else {
                    $element->setName($name);
                }
            }
            $element->_generateId();

            $out = $OUTPUT->mform_element($element, false, false, '', true);

            if (empty($out)) {
                $renderer = new HTML_QuickForm_Renderer_Default();
                $renderer->setElementTemplate('{element}');
                $element->accept($renderer);
                $out = $renderer->toHtml();
            }

            // Replicates the separator logic from 'pear/HTML/QuickForm/Renderer/Default.php'.
            $separator = '';
            if ($i > 0) {
                if (is_array($this->_separator)) {
                    $separator = $this->_separator[($i - 1) % count($this->_separator)];
                } else if ($this->_separator === null) {
                    $separator = '&nbsp;';
                } else {
                    $separator = (string) $this->_separator;
                }
            }

            $elements[] = [
                'separator' => $separator,
                'html' => $out
            ];

            // Restore the element's name.
            if ($this->_appendName) {
                $element->setName($elementname);
            }

            $i++;
        }

        $context['groupname'] = $name;
        $context['elements'] = $elements;
        return $context;
    }

    /**
     * Accepts a renderer
     *
     * @param object     An HTML_QuickForm_Renderer object
     * @param bool       Whether a group is required
     * @param string     An error message associated with a group
     * @access public
     * @return void
     */
    public function accept(&$renderer, $required = false, $error = null) {
        $this->_createElementsIfNotExist();
        $renderer->startGroup($this, $required, $error);
        if (!$this->_renderedfromtemplate) {
            // Backwards compatible path - only do this if we didn't render the sub-elements already.
            $name = $this->getName();
            foreach (array_keys($this->_elements) as $key) {
                $element =& $this->_elements[$key];
                $elementname = '';
                if ($this->_appendName) {
                    $elementname = $element->getName();
                    if (isset($elementname)) {
                        $element->setName($name . '['. (strlen($elementname) ? $elementname : $key) .']');
                    } else {
                        $element->setName($name);
                    }
                }

                $required = !$element->isFrozen() && in_array($element->getName(), $this->_required);

                $element->accept($renderer, $required);

                // Restore the element's name.
                if ($this->_appendName) {
                    $element->setName($elementname);
                }
            }
        }
        $renderer->finishGroup($this);
    }
}
