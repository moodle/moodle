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
 * Provides the {@link MoodleQuickForm_filetypes} class.
 *
 * @package   core_form
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_form\filetypes_util;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot.'/lib/form/group.php');

/**
 * File types and type groups selection form element.
 *
 * @package   core_form
 * @category  form
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_filetypes extends MoodleQuickForm_group {

    /** @var array Allow selection from these file types only. */
    protected $onlytypes = [];

    /** @var bool Allow selection of 'All file types' (will be stored as '*'). */
    protected $allowall = true;

    /** @var bool Skip implicit validation against known file types. */
    protected $allowunknown = false;

    /** @var core_form\filetypes_util instance to use as a helper. */
    protected $util = null;

    /**
     * Constructor
     *
     * @param string $elementname Element's name
     * @param string $elementlabel Label(s) for an element
     * @param array $options element options:
     *   'onlytypes': Allow selection from these file types only; for example ['onlytypes' => ['web_image']].
     *   'allowall': Allow to select 'All file types', defaults to true. Does not apply with onlytypes are set.
     *   'allowunknown': Skip implicit validation against the list of known file types.
     * @param array|string $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementname = null, $elementlabel = null, $options = null, $attributes = null) {

        parent::__construct($elementname, $elementlabel);
        $this->_type = 'filetypes';

        // Hard-frozen elements do not get the name populated automatically,
        // which leads to PHP notice. Add it explicitly here.
        $this->setAttributes(array('name' => $elementname));
        $this->updateAttributes($attributes);

        if (is_array($options) && $options) {
            if (array_key_exists('onlytypes', $options) && is_array($options['onlytypes'])) {
                $this->onlytypes = $options['onlytypes'];
            }
            if (!$this->onlytypes && array_key_exists('allowall', $options)) {
                $this->allowall = (bool)$options['allowall'];
            }
            if (array_key_exists('allowunknown', $options)) {
                $this->allowunknown = (bool)$options['allowunknown'];
            }
        }

        $this->util = new filetypes_util();
    }

    /**
     * Assemble the elements of the form control.
     */
    public function _createElements() {

        $this->_generateId();

        $this->setElements([
            $this->createFormElement('text', 'filetypes', $this->getLabel(), [
                'id' => $this->getAttribute('id'),
            ]),

            $this->createFormElement('static', 'browser', null,
                '<span data-filetypesbrowser="'.$this->getAttribute('id').'"></span>'),

            $this->createFormElement('static', 'descriptions', null,
                '<div data-filetypesdescriptions="'.$this->getAttribute('id').'"></div>')
        ]);
    }

    /**
     * Return the selected file types.
     *
     * @param array $submitted submitted values
     * @param bool $assoc if true the retured value is associated array
     * @return array
     */
    public function exportValue(&$submitted, $assoc = false) {

        $value = '';
        $filetypeselement = null;

        foreach ($this->_elements as $key => $element) {
            if ($element->_attributes['name'] === 'filetypes') {
                $filetypeselement = $this->_elements[$key];
            }
        }

        if ($filetypeselement) {
            $formval = $filetypeselement->exportValue($submitted[$this->getName()], false);
            if ($formval) {
                $value = $this->util->normalize_file_types($formval);
                if ($value === ['*'] && !$this->allowall) {
                    $value = [];
                }
                $value = implode(',', $value);
            }
        }

        return $this->_prepareValue($value, $assoc);
    }

    /**
     * Accepts a renderer (called shortly before the renderer's toHtml() method).
     *
     * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
     * @param bool $required Whether a group is required
     * @param string $error An error message associated with a group
     */
    public function accept(&$renderer, $required = false, $error = null) {
        global $PAGE;

        $PAGE->requires->js_call_amd('core_form/filetypes', 'init', [
            $this->getAttribute('id'),
            $this->getLabel(),
            $this->onlytypes,
            $this->allowall,
        ]);

        if ($this->isFrozen()) {
            // Don't render the choose button if the control is frozen.
            foreach ($this->_elements as $key => $element) {
                if ($element->_attributes['name'] === 'browser') {
                    unset($this->_elements[$key]);
                }
            }
        }

        parent::accept($renderer, $required, $error);
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        global $OUTPUT;

        switch ($event) {
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    if ($caller->isSubmitted()) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = (string)$this->_findValue($caller->_defaultValues);
                    }
                }
                if (!is_array($value)) {
                    $value = array('filetypes' => $value);
                }
                if ($value['filetypes'] !== null) {
                    $filetypes = $this->util->normalize_file_types($value['filetypes']);
                    if ($filetypes === ['*'] && !$this->allowall) {
                        $filetypes = [];
                    }
                    $value['descriptions'] = '<div data-filetypesdescriptions="'.$this->getAttribute('id').'">' .
                        $OUTPUT->render_from_template('core_form/filetypes-descriptions',
                            $this->util->describe_file_types($filetypes)).'</div>';
                }
                $this->setValue($value);
                return true;
                break;

        }

        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Check that the submitted list contains only known and allowed file types.
     *
     * The validation obeys the element options 'allowall', 'allowunknown' and
     * 'onlytypes' passed when creating the element.
     *
     * @param array $value Submitted value.
     * @return string|null Validation error message or null.
     */
    public function validateSubmitValue($value) {

        $value = $value ?? ['filetypes' => null]; // A null $value can arrive here. Coalesce, creating the default array.

        if (!$this->allowall) {
            // Assert that there is an actual list provided.
            $normalized = $this->util->normalize_file_types($value['filetypes']);
            if (empty($normalized) || $normalized == ['*']) {
                return get_string('filetypesnotall', 'core_form');
            }
        }

        if (!$this->allowunknown) {
            // Assert that all file types are known.
            $unknown = $this->util->get_unknown_file_types($value['filetypes']);

            if ($unknown) {
                return get_string('filetypesunknown', 'core_form', implode(', ', $unknown));
            }
        }

        if ($this->onlytypes) {
            // Assert that all file types are allowed here.
            $notlisted = $this->util->get_not_listed($value['filetypes'], $this->onlytypes);

            if ($notlisted) {
                return get_string('filetypesnotallowed', 'core_form', implode(', ', $notlisted));
            }
        }

        return;
    }
}
