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
 * Submit an assignment or edit the already submitted work
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->dirroot.'/lib/formslib.php');

class workshop_submission_form extends moodleform {

    function definition() {

        $mform = $this->_form;

        $current            = $this->_customdata['current'];
        $workshop           = $this->_customdata['workshop'];
        $cm                 = $this->_customdata['cm'];
        $dataoptions        = $this->_customdata['dataoptions'];
        $attachmentoptions  = $this->_customdata['attachmentoptions'];

        $mform->addElement('header', 'general', get_string('submission', 'workshop'));

        $mform->addElement('text', 'title', get_string('submissiontitle', 'workshop'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('editor', 'data_editor', get_string('submissiondata', 'workshop'), null, $dataoptions);
        $mform->setType('data_editor', PARAM_RAW);

        if ($workshop->nattachments > 0) {
            $mform->addElement('static', 'filemanagerinfo', get_string('nattachments', 'workshop'), $workshop->nattachments);
            $mform->addElement('filemanager', 'attachment_filemanager', get_string('submissionattachment', 'workshop'),
                                null, $attachmentoptions);
        }

        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'cmid');

        $this->add_action_buttons();

        $this->set_data($current);
    }

    function validation($data, $files) {
        global $CFG, $USER, $DB;

        $errors = parent::validation($data, $files);
        return $errors;
    }
}
