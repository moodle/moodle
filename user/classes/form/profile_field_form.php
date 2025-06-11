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

namespace core_user\form;

use context;
use core_form\dynamic_form;
use moodle_url;
use profile_define_base;

/**
 * Class field_form used for profile fields.
 *
 * @package core_user
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_form extends dynamic_form {

    /** @var profile_define_base $field */
    public $field;
    /** @var \stdClass */
    protected $fieldrecord;

    /**
     * Define the form
     */
    public function definition() {
        global $CFG;
        require_once($CFG->dirroot.'/user/profile/definelib.php');

        $mform = $this->_form;

        // Everything else is dependant on the data type.
        $datatype = $this->get_field_record()->datatype;
        require_once($CFG->dirroot.'/user/profile/field/'.$datatype.'/define.class.php');
        $newfield = 'profile_define_'.$datatype;
        $this->field = new $newfield();

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'editfield');
        $mform->setType('action', PARAM_ALPHANUMEXT);
        $mform->addElement('hidden', 'datatype', $datatype);
        $mform->setType('datatype', PARAM_ALPHA);

        $this->field->define_form($mform);
    }


    /**
     * Alter definition based on existing or submitted data
     */
    public function definition_after_data() {
        $mform = $this->_form;
        $this->field->define_after_data($mform);
    }


    /**
     * Perform some moodle validation.
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        return $this->field->define_validate($data, $files);
    }

    /**
     * Returns the defined editors for the field.
     * @return array
     */
    public function editors(): array {
        $editors = $this->field->define_editors();
        return is_array($editors) ? $editors : [];
    }

    /**
     * Returns context where this form is used
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return \context_system::instance();
    }

    /**
     * Checks if current user has access to this form, otherwise throws exception
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('moodle/site:config', $this->get_context_for_dynamic_submission());
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     */
    public function process_dynamic_submission() {
        global $CFG;
        require_once($CFG->dirroot.'/user/profile/definelib.php');
        profile_save_field($this->get_data(), $this->editors());
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $field = $this->get_field_record();

        // Clean and prepare description for the editor.
        $description = clean_text($field->description, $field->descriptionformat);
        $field->description = ['text' => $description, 'format' => $field->descriptionformat, 'itemid' => 0];
        // Convert the data format for.
        if (is_array($this->editors())) {
            foreach ($this->editors() as $editor) {
                if (isset($field->$editor)) {
                    $editordesc = clean_text($field->$editor, $field->{$editor.'format'});
                    $field->$editor = ['text' => $editordesc, 'format' => $field->{$editor.'format'}, 'itemid' => 0];
                }
            }
        }

        $this->set_data($field);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $id = $this->optional_param('id', 0, PARAM_INT);
        $datatype = $this->optional_param('datatype', 'text', PARAM_PLUGIN);
        return new moodle_url('/user/profile/index.php',
            ['action' => 'editfield', 'id' => $id, 'datatype' => $id ? null : $datatype]);
    }

    /**
     * Record for the field from the database (or generic record for a new field)
     *
     * @return false|mixed|\stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_field_record() {
        global $DB;

        if (!$this->fieldrecord) {
            $id = $this->optional_param('id', 0, PARAM_INT);
            if (!$id || !($this->fieldrecord = $DB->get_record('user_info_field', ['id' => $id]))) {
                $datatype = $this->optional_param('datatype', 'text', PARAM_PLUGIN);
                $this->fieldrecord = new \stdClass();
                $this->fieldrecord->datatype = $datatype;
                $this->fieldrecord->description = '';
                $this->fieldrecord->descriptionformat = FORMAT_HTML;
                $this->fieldrecord->defaultdata = '';
                $this->fieldrecord->defaultdataformat = FORMAT_HTML;
                $this->fieldrecord->categoryid = $this->optional_param('categoryid', 0, PARAM_INT);
            }
            if (!\core_component::get_component_directory('profilefield_'.$this->fieldrecord->datatype)) {
                throw new \moodle_exception('fieldnotfound', 'customfield');
            }
        }

        return $this->fieldrecord;
    }
}


