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
 * Feedback form for project supervision
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

 require_once('../../config.php');
 require_once('../../lib/formslib.php');
 class local_esupervision_submission_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'project_submision_header', 'Projection Submision Form');
        $mform->addElement('textarea', 'project_description', 'Project Description:', ['rows' => '5', 'cols' => '50']);
        $mform->addRule('project_description', null, 'required', null, 'client');

        $mform->addElement('filepicker', 'project_document', 'Project Docoment:', null, ['maxbytes' => 1000000, 'accepted_types' => '*']);
        $mform->addRule('project_document', get_string('error_rquired', 'local_esupervision'), 'required', null, 'client');
        $mform->addElement('submit', 'submitbtn', 'Submit project');     
        $mform->addElement('cancel', 'cancelbtn', 'Cancel');           
    }
    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (empty($data['project_description'])) {
            $errors['project_description'] = get_string('error_required', 'local_esupervision');
        }
        $filemanager = $this->_form->get_file_manager('project_document');
        if (!$filemanager->file_exists()) {
            $errors['project_document'] = get_string('error_required', 'local_esupervision');
        }
        return $errors;
    }

    public function submision($data, $files) {
        $filemanager = $this->_form->get_file_manager('project_document');
        $filemanager->move_file($filemanager->get_new_filename(), '/', false);
    }
 }