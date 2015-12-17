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
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

class workshop_submission_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        $current        = $this->_customdata['current'];
        $workshop       = $this->_customdata['workshop'];
        $contentopts    = $this->_customdata['contentopts'];
        $attachmentopts = $this->_customdata['attachmentopts'];

        $this->attachmentopts = $attachmentopts;

        $mform->addElement('header', 'general', get_string('submission', 'workshop'));

        $mform->addElement('text', 'title', get_string('submissiontitle', 'workshop'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('editor', 'content_editor', get_string('submissioncontent', 'workshop'), null, $contentopts);
        $mform->setType('content', PARAM_RAW);

        if ($workshop->nattachments > 0) {
            $mform->addElement('static', 'filemanagerinfo', get_string('nattachments', 'workshop'), $workshop->nattachments);
            $mform->addElement('filemanager', 'attachment_filemanager', get_string('submissionattachment', 'workshop'),
                                null, $attachmentopts);
        }

        $mform->addElement('hidden', 'id', $current->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'cmid', $workshop->cm->id);
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('hidden', 'edit', 1);
        $mform->setType('edit', PARAM_INT);

        $mform->addElement('hidden', 'example', 0);
        $mform->setType('example', PARAM_INT);

        $this->add_action_buttons();

        $this->set_data($current);
    }

    function validation($data, $files) {
        global $CFG, $USER, $DB;

        $errors = parent::validation($data, $files);

        if (empty($data['id']) and empty($data['example'])) {
            // make sure there is no submission saved meanwhile from another browser window
            $sql = "SELECT COUNT(s.id)
                      FROM {workshop_submissions} s
                      JOIN {workshop} w ON (s.workshopid = w.id)
                      JOIN {course_modules} cm ON (w.id = cm.instance)
                      JOIN {modules} m ON (m.name = 'workshop' AND m.id = cm.module)
                     WHERE cm.id = ? AND s.authorid = ? AND s.example = 0";

            if ($DB->count_records_sql($sql, array($data['cmid'], $USER->id))) {
                $errors['title'] = get_string('err_multiplesubmissions', 'mod_workshop');
            }
        }

        if (isset ($data['attachment_filemanager'])) {
            $draftitemid = $data['attachment_filemanager'];

            // If we have draft files, then make sure they are the correct ones.
            if ($draftfiles = file_get_drafarea_files($draftitemid)) {

                if (!$validfileextensions = workshop::get_array_of_file_extensions($this->attachmentopts['filetypes'])) {
                    return $errors;
                }
                $wrongfileextensions = null;
                $bigfiles = null;

                // Check the size and type of each file.
                foreach ($draftfiles->list as $file) {
                    $a = new stdClass();
                    $a->maxbytes = $this->attachmentopts['maxbytes'];
                    $a->currentbytes = $file->size;
                    $a->filename = $file->filename;
                    $a->validfileextensions = implode(',', $validfileextensions);

                    // Check whether the extension of uploaded file is in the list.
                    $thisextension = substr(strrchr($file->filename, '.'), 1);
                    if (!in_array($thisextension, $validfileextensions)) {
                        $wrongfileextensions .= get_string('err_wrongfileextension', 'workshop', $a) . '<br/>';
                    }

                    // Check whether the file size exceeds the maximum submission attachment size.
                    if ($file->size > $this->attachmentopts['maxbytes']) {
                        $bigfiles .= get_string('err_maxbytes', 'workshop', $a) . '<br/>';
                    }
                }
                if ($bigfiles || $wrongfileextensions) {
                    $errors['attachment_filemanager'] = $bigfiles . $wrongfileextensions;
                }
            }
        }
        return $errors;
    }
}
