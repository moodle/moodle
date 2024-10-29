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
 * prints the forms to choose an item-typ to create items and to choose a template to use
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

//It must be included from a Moodle page
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/formslib.php');

/**
 * The feedback_edit_use_template_form
 *
 * @deprecated since 4.0 new dynamic forms created
 * @todo       MDL-83522 This class will be deleted in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: 'mod_feedback\form\use_template_form',
    since: '4.0',
    mdl: 'MDL-71914',
    reason: 'New dynamic forms have been created instead.'
)]
class feedback_edit_use_template_form extends moodleform {
    public function __construct($action = null, $customdata = null, $method = 'post', $target = '',
            $attributes = null, $editable = true, $ajaxformdata = null) {
        debugging('Class feedback_edit_use_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @return array
     */
    public static function get_js_module() {
        debugging('Class feedback_edit_use_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::get_js_module();
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $simulatedsubmitteddata
     * @param array $simulatedsubmittedfiles
     * @param string $method
     * @param null $formidentifier
     * @return array
     */
    public static function mock_ajax_submit($simulatedsubmitteddata, $simulatedsubmittedfiles = array(),
            $method = 'post', $formidentifier = null) {
        debugging('Class feedback_edit_use_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::mock_ajax_submit($simulatedsubmitteddata, $simulatedsubmittedfiles, $method, $formidentifier);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $data
     * @return array
     */
    public static function mock_generate_submit_keys($data = []) {
        debugging('Class feedback_edit_use_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::mock_generate_submit_keys($data);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $simulatedsubmitteddata
     * @param array $simulatedsubmittedfiles
     * @param string $method
     * @param null $formidentifier
     */
    public static function mock_submit($simulatedsubmitteddata, $simulatedsubmittedfiles = array(),
            $method = 'post', $formidentifier = null) {
        debugging('Class feedback_edit_use_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        parent::mock_submit($simulatedsubmitteddata, $simulatedsubmittedfiles, $method, $formidentifier);
    }

    /**
     * Form definition
     */
    public function definition() {
        $mform =& $this->_form;

        $course = $this->_customdata['course'];

        $elementgroup = array();
        //headline
        $mform->addElement('header', 'using_templates', get_string('using_templates', 'feedback'));
        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // visible elements
        $templates_options = array();
        $owntemplates = feedback_get_template_list($course, 'own');
        $publictemplates = feedback_get_template_list($course, 'public');

        $options = array();
        if ($owntemplates or $publictemplates) {
            $options[''] = array('' => get_string('choosedots'));

            if ($owntemplates) {
                $courseoptions = array();
                foreach ($owntemplates as $template) {
                    $courseoptions[$template->id] = format_string($template->name);
                }
                $options[get_string('course')] = $courseoptions;
            }

            if ($publictemplates) {
                $publicoptions = array();
                foreach ($publictemplates as $template) {
                    $publicoptions[$template->id] = format_string($template->name);
                }
                $options[get_string('public', 'feedback')] = $publicoptions;
            }

            $attributes = [
                'onChange="this.form.submit()"',
                'data-form-change-checker-override="1"',
            ];
            $elementgroup[] = $mform->createElement(
                'selectgroups',
                'templateid',
                get_string('using_templates', 'feedback'),
                $options,
                implode(' ', $attributes)
            );

            $elementgroup[] = $mform->createElement('submit',
                                                     'use_template',
                                                     get_string('use_this_template', 'feedback'),
                                                     array('class' => 'hiddenifjs'));

            $mform->addGroup($elementgroup, 'elementgroup', '', array(' '), false);
        } else {
            $mform->addElement('static', 'info', get_string('no_templates_available_yet', 'feedback'));
        }

        $this->set_data(array('id' => $this->_customdata['id']));
    }
}

/**
 * The feedback_edit_create_template_form
 *
 * @deprecated since 4.0, new dynamic forms have been created instead.
 */
class feedback_edit_create_template_form extends moodleform {
    public function __construct($action = null, $customdata = null, $method = 'post',
            $target = '', $attributes = null, $editable = true, $ajaxformdata = null) {
        debugging('Class feedback_edit_create_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @return array
     */
    public static function get_js_module() {
        debugging('Class feedback_edit_create_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::get_js_module();
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $simulatedsubmitteddata
     * @param array $simulatedsubmittedfiles
     * @param string $method
     * @param null $formidentifier
     * @return array
     */
    public static function mock_ajax_submit($simulatedsubmitteddata, $simulatedsubmittedfiles = array(),
            $method = 'post', $formidentifier = null) {
        debugging('Class feedback_edit_create_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::mock_ajax_submit($simulatedsubmitteddata, $simulatedsubmittedfiles, $method, $formidentifier);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $data
     * @return array
     */
    public static function mock_generate_submit_keys($data = []) {
        debugging('Class feedback_edit_create_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        return parent::mock_generate_submit_keys($data);
    }

    /**
     * Overrides parent static method for deprecation purposes.
     *
     * @deprecated since 4.0
     * @param array $simulatedsubmitteddata
     * @param array $simulatedsubmittedfiles
     * @param string $method
     * @param null $formidentifier
     */
    public static function mock_submit($simulatedsubmitteddata, $simulatedsubmittedfiles = array(),
            $method = 'post', $formidentifier = null) {
        debugging('Class feedback_edit_create_template_form is deprecated. Replaced with dynamic forms.', DEBUG_DEVELOPER);
        parent::mock_submit($simulatedsubmitteddata, $simulatedsubmittedfiles, $method, $formidentifier);
    }

    /**
     * Form definition
     */
    public function definition() {
        $mform =& $this->_form;

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'do_show');
        $mform->setType('do_show', PARAM_ALPHANUMEXT);
        $mform->setConstant('do_show', 'edit');

        // visible elements
        $elementgroup = array();

        $elementgroup[] = $mform->createElement('text',
                                                 'templatename',
                                                 get_string('name', 'feedback'),
                                                 ['maxlength' => '200']);

        if (has_capability('mod/feedback:createpublictemplate', context_system::instance())) {
            $elementgroup[] = $mform->createElement('checkbox',
                                                     'ispublic', '',
                                                     get_string('public', 'feedback'));
        }


        $mform->addGroup($elementgroup,
                         'elementgroup',
                         get_string('name', 'feedback'),
                         array(' '),
                         false);

        // Buttons.
        $mform->addElement('submit', 'create_template', get_string('save_as_new_template', 'feedback'));

        $mform->setType('templatename', PARAM_TEXT);

        $this->set_data(array('id' => $this->_customdata['id']));
    }

    /**
     * Form validation
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!isset($data['templatename']) || trim(strval($data['templatename'])) === '') {
            $errors['elementgroup'] = get_string('name_required', 'feedback');
        }
        return $errors;
    }
}

