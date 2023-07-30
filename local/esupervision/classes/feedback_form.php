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

 class local_esupervision_feedback_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('header', 'feedback_header', 'Esupervision Feedback Form');
        $mform->addElement('textarea', 'feedback_comments', 'Feedback Comments:', ['rows' => '5', 'cols' => '50']);
        $mform->addRule('feedback_comments', null, 'required', null, 'client');
        $mform->addElement('submit', 'submitbtn', 'Submit Feedback');     
    }
    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (empty($data['feedback_comments'])) {
            $errors['feedback_comments'] = get_string('error_required', 'local_esupervision');
        }
        if ($data['feedback_comments']<10) {
            $errors['feedback_comments'] = get_string('error_min_length', 'local_esupervision');
        }
        return $errors;
    }
 }