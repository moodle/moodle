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
 * @package dataformview
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

defined('MOODLE_INTERNAL') or die;

require_once("$CFG->libdir/formslib.php");

/**
 *
 */
class dataformviewform extends \moodleform {
    protected $_view = null;

    public function __construct($view, $action = null, $customdata = null, $method = 'post',
                    $target = '', $attributes = null, $editable = true) {
        $this->_view = $view;

        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }

    /**
     *
     */
    public function definition() {
        global $CFG;

        $view = $this->_view;
        $mform = &$this->_form;
        $editoroptions = $view->editoroptions;
        $paramtext = !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEAN;

        // Buttons.
        $this->add_action_buttons();

        // General.
        $mform->addElement('header', 'general', get_string('viewgeneral', 'dataform'));

        // Name.
        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', $paramtext);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setDefault('name', $this->get_default_view_name());
        $mform->addHelpButton('name', 'viewname', 'dataform');

        // Description.
        $mform->addElement('textarea', 'description', get_string('description'));
        $mform->setType('description', $paramtext);
        $mform->addHelpButton('description', 'viewdescription', 'dataform');

        // Visibility.
        $mform->addElement('select', 'visible', get_string('viewvisibility', 'dataform'), $view::get_visibility_modes());
        $mform->addHelpButton('visible', 'viewvisibility', 'dataform');
        $mform->setDefault('visible', 1);

        // Filter.
        if (!$filtersmenu = \mod_dataform_filter_manager::instance($view->dataid)->get_filters(null, true)) {
            $filtersmenu = array(0 => get_string('filtersnonedefined', 'dataform'));
        } else {
            $filtersmenu = array(0 => get_string('choose')) + $filtersmenu;
        }
        $mform->addElement('select', 'filterid', get_string('viewfilter', 'dataform'), $filtersmenu);
        $mform->addHelpButton('filterid', 'viewfilter', 'dataform');
        $mform->setDefault('filterid', 0);

        // Entries per page.
        $options = array(
            0 => get_string('choose'),
            1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 15 => 15,
            20 => 20, 30 => 30, 40 => 40, 50 => 50,
            100 => 100, 200 => 200, 300 => 300, 400 => 400, 500 => 500, 1000 => 1000
        );
        $mform->addElement('select', 'perpage', get_string('viewperpage', 'dataform'), $options);
        $mform->addHelpButton('perpage', 'viewperpage', 'dataform');

        // Group by.
        if (!$fieldsmenu = $view->df->field_manager->get_fields_menu(array('exclude' => array('entry')))) {
            $fieldsmenu = array('' => get_string('fieldsnonedefined', 'dataform'));
        } else {
            $fieldsmenu = array('' => get_string('choose')) + $fieldsmenu;
        }
        // TODO $mform->addElement('select', 'groupby', get_string('viewgroupby', 'dataform'), $fieldsmenu);.
        // TODO $mform->addHelpButton('groupby', 'viewgroupby', 'dataform');.

        // View specific definition.
        $this->definition_view_specific();

        // Remove elements.
        $this->definition_remove_elements();

        // Buttons.
        $this->add_action_buttons();
    }

    /**
     *
     */
    protected function definition_view_specific() {
        // View template.
        $this->definition_view_template();

        // Submission settings.
        $this->definition_view_submission();
    }

    /**
     *
     */
    protected function definition_view_template() {
        $mform = &$this->_form;
        $view = $this->_view;

        $editoroptions = $view->editoroptions;

        // Header.
        $mform->addElement('header', 'viewtemplatehdr', get_string('viewtemplate', 'dataform'));
        $mform->addHelpButton('viewtemplatehdr', 'viewtemplate', 'dataform');

        // Editor.
        $mform->addElement('editor', 'section_editor', get_string('viewtemplate', 'dataform'), null, $editoroptions);
        $this->add_patterns_selectors('section_editor', array('view'));
    }

    /**
     *
     */
    protected function definition_view_submission() {
        $view = $this->_view;
        $mform = &$this->_form;
        $paramtext = !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEAN;

        // EDITING Header.
        $mform->addElement('header', 'vieweditinghdr', get_string('editing', 'dataform'));

        // What to display when editing.
        $options = array(
            '' => get_string('modeeditonly', 'dataform'),
            dataformview::EDIT_SEPARATE => get_string('modeeditseparate', 'dataform'),
            dataformview::EDIT_INLINE => get_string('modeeditinline', 'dataform'),
        );
        $mform->addElement('select', 'submission[display]', get_string('submissiondisplay', 'dataform'), $options);

        // Save buttons.
        $buttons = $view->submission_buttons;
        foreach ($buttons as $name) {
            $grp = array();
            $grp[] = &$mform->createElement('text', "submission[$name]", null);
            $grp[] = &$mform->createElement('checkbox', $name.'buttonenable', null, get_string('enable'));
            $mform->addGroup($grp, $name.'buttongrp', get_string($name.'button', 'dataform'), ' ', false);
            $mform->addHelpButton($name.'buttongrp', $name.'button', 'dataform');
            $mform->setType("submission[$name]", $paramtext);
            $mform->disabledIf("submission[$name]", $name.'buttonenable', 'notchecked');
        }

        // SUBMISSION Header.
        $mform->addElement('header', 'viewsubmissionhdr', get_string('submission', 'dataform'));

        // Redirect view after submission.
        $options = array('' => get_string('choosedots'));
        if ($viewsmenu = \mod_dataform_view_manager::instance($view->df->id)->views_menu) {
            // Remove this view.
            if ($view->id and !empty($viewsmenu[$view->id])) {
                unset($viewsmenu[$view->id]);
            }
            $options = $options + $viewsmenu;
        }
        $label = get_string('submissionredirect', 'dataform');
        $mform->addElement('select', 'submission[redirect]', $label, $options);
        $mform->addHelpButton('submission[redirect]', 'submissionredirect', 'dataform');

        // Response timeout.
        $options = range(0, 20);
        $options[0] = get_string('none');
        $label = get_string('submissiontimeout', 'dataform');
        $mform->addElement('select', 'submission[timeout]', $label, $options);
        $mform->addHelpButton('submission[timeout]', 'submissiontimeout', 'dataform');

        // Response for submission.
        $mform->addElement('textarea', 'submission[message]', get_string('submissionmessage', 'dataform'));
        $mform->setType('submissionmessage', $paramtext);
        $mform->disabledIf('submission[message]', 'submissiontimeout', 'eq', 0);
        $mform->addHelpButton('submission[message]', 'submissionmessage', 'dataform');

        // What to display after submission.
        $label = get_string('submissiondisplayafter', 'dataform');
        $mform->addElement('selectyesno', 'submission[displayafter]', $label);
        $mform->addHelpButton('submission[displayafter]', 'submissiondisplayafter', 'dataform');

        // Set default save and cancel for new views.
        if (!$view->id) {
            $mform->setDefault('savebuttonenable', 1);
            $mform->setDefault('cancelbuttonenable', 1);
            $mform->setDefault('submissiontimeout', 1);
        }
    }

    /**
     *
     */
    public function add_action_buttons($cancel = true, $submit = null) {
        $mform = &$this->_form;

        $buttonarray = array();
        // Save and display.
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        // Save and continue.
        $buttonarray[] = &$mform->createElement('submit', 'submitreturnbutton', get_string('savecont', 'dataform'));
        // Reset to default.
        $buttonarray[] = &$mform->createElement('submit', 'resetdefaultbutton', get_string('viewresettodefault', 'dataform'));
        $mform->registerNoSubmitButton('resetdefaultbutton');
        // Switch editor.
        // Cancel.
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     *
     */
    protected function definition_remove_elements() {
    }

    /**
     *
     */
    protected function add_patterns_selectors($editorname, array $patterntypes) {
        global $PAGE;

        $view = $this->_view;
        $mform = &$this->_form;

        foreach ($patterntypes as $patterntype) {
            switch ($patterntype) {
                case 'view':
                    $label = get_string('viewpatterns', 'dataform');
                    $patterns = $view->renderer->get_menu();
                    $patterns = array('' => array('' => $label)) + $patterns;
                    $selecttype = 'selectgroups';
                    break;

                case 'field':
                    $label = get_string('fieldpatterns', 'dataform');
                    $patterns = $view->df->field_manager->get_field_patterns_menu();
                    $patterns = array('' => array('' => $label)) + $patterns;
                    $selecttype = 'selectgroups';
                    break;

                case 'character':
                    $label = get_string('characterpatterns', 'dataform');
                    $patterns = $view->get_character_patterns_menu();
                    $patterns = array('' => $label) + $patterns;
                    $selecttype = 'select';
                    break;

                default:
                    $patterns = null;
            }

            if (!empty($patterns)) {
                $selectorname = "{$editorname}{$patterntype}patterns";
                $mform->addElement($selecttype, $selectorname, null, $patterns);
                $mform->addHelpButton("{$editorname}{$patterntype}patterns", "{$patterntype}patterns", 'dataform');
                $PAGE->requires->js_init_call('M.mod_dataform.util.init_tags_selector', array($selectorname, $editorname));
            }
        }
    }

    /**
     *
     */
    protected function get_default_view_name() {
        $view = $this->_view;
        $df = $view->df;
        $viewname = get_string('pluginname', "dataformview_$view->type");

        $i = 1;
        while ($df->name_exists('views', $viewname, $view->id)) {
            $viewname = "$viewname $i";
            $i++;
        }

        return $viewname;
    }

    /**
     *
     *
     * @param stdClass $data
     * @return stdClass
     */
    protected function fix_patterns_in_href($data) {
        $view = $this->_view;

        // Get the field patterns.
        $fieldpatterns = array();
        if ($set = $view->get_pattern_set('field')) {
            foreach ($set as $patterns) {
                foreach ($patterns as $pattern) {
                    $key = '%5B%5B'. trim($pattern, '[]'). '%5D%5D';
                    $fieldpatterns[$key] = $pattern;
                }
            }
        }

        // HACK: Add the [[entryid]] pattern.
        $fieldpatterns['%5B%5Bentryid%5D%5D'] = '[[entryid]]';

        // Get the view patterns.
        $viewpatterns = array();
        if ($set = $view->get_pattern_set('view')) {
            foreach ($set as $key => $pattern) {
                $set[$key] = '#%23'. trim($pattern, '#'). '%23%23';
            }
            $viewpatterns = array_flip($set);
        }

        // Fix patterns in each editor.
        foreach ($view->editors as $editor) {
            $editorname = $editor. '_editor';
            if (empty($data->$editorname)) {
                continue;
            }

            if (empty($data->{$editorname}['text'])) {
                continue;
            }
            $content = $data->{$editorname}['text'];

            // We want to process only a tags.
            preg_match_all('/<a [^>]+>/', $content, $matches);
            if (empty($matches[0])) {
                continue;
            }

            // Generate replacements for the a tags.
            $replacements = array();
            foreach ($matches[0] as $match) {
                $replacement = str_replace(array_keys($fieldpatterns), $fieldpatterns, $match);
                $replacement = str_replace(array_keys($viewpatterns), $viewpatterns, $replacement);
                $replacements[$match] = $replacement;
            }

            // Adjust the content of the editor text.
            $content = str_replace(array_keys($replacements), $replacements, $content);
            $data->{$editorname}['text'] = $content;
        }

        return $data;
    }

    /**
     *
     */
    public function data_preprocessing(&$data) {
        $view = $this->_view;

        // Fix patterns in href attributes.
        $this->fix_patterns_in_href($data);
        // Submission settings.
        $submission = $view->submission_settings;
        if (is_array($submission)) {
            $data->submission = $submission;
            $buttons = $view->submission_buttons;
            foreach ($buttons as $name) {
                if (array_key_exists($name, $submission)) {
                    $data->{$name.'buttonenable'} = 1;
                }
            }
        }
    }

    /**
     *
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

    /**
     *
     */
    public function validation($data, $files) {
        $view = $this->_view;
        $df = $view->df;

        $errors = parent::validation($data, $files);

        if ($df->name_exists('views', $data['name'], $view->id)) {
            $errors['name'] = get_string('invalidname', 'dataform', get_string('view', 'dataform'));
        }

        return $errors;
    }
}
