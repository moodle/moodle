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
 * Form used to select a file and file format for the import
 *
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/**
 * Form used to select a file and file format for the import
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson_import_form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        $mform->addElement('select', 'format', get_string('fileformat', 'lesson'), $this->_customdata['formats']);
        $mform->setDefault('format', 'gift');
        $mform->setType('format', 'text');
        $mform->addRule('format', null, 'required');

        //Using filemanager as filepicker
        $mform->addElement('filepicker', 'questionfile', get_string('upload'));
        $mform->addRule('questionfile', null, 'required', null, 'client');

        $this->add_action_buttons(null, get_string("import"));
    }

    /**
     * Checks that a file has been uploaded, and that it is of a plausible type.
     * @param array $data the submitted data.
     * @param array $errors the errors so far.
     * @return array the updated errors.
     * @throws moodle_exception
     */
    protected function validate_uploaded_file($data, $errors) {
        global $CFG;

        if (empty($data['questionfile'])) {
            $errors['questionfile'] = get_string('required');
            return $errors;
        }

        $files = $this->get_draft_files('questionfile');
        if (!is_array($files) || count($files) < 1) {
            $errors['questionfile'] = get_string('required');
            return $errors;
        }

        $formatfile = $CFG->dirroot.'/question/format/'.$data['format'].'/format.php';
        if (!is_readable($formatfile)) {
            throw new moodle_exception('formatnotfound', 'lesson', '', $data['format']);
        }

        require_once($formatfile);

        $classname = 'qformat_' . $data['format'];
        $qformat = new $classname();

        return $errors;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $errors = $this->validate_uploaded_file($data, $errors);
        return $errors;
    }
}