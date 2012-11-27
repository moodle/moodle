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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   assignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/assign/feedback/file/importziplib.php');

/**
 * Import zip form
 *
 * @package   assignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_file_import_zip_form extends moodleform implements renderable {

    /**
     * Create this grade import form
     */
    function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;
        $params = $this->_customdata;

        $renderer = $PAGE->get_renderer('assign');

        // Visible elements.
        $assignment = $params['assignment'];
        $contextid = $assignment->get_context()->id;
        $importer = $params['importer'];
        $update = false;

        if (!$importer) {
            print_error('invalidarguments');
            return;
        }

        $files = $importer->get_import_files($contextid);

        $mform->addElement('header', 'uploadzip', get_string('confirmuploadzip', 'assignfeedback_file'));

        $currentgroup = groups_get_activity_group($assignment->get_course_module(), true);
        $allusers = $assignment->list_participants($currentgroup, false);
        $participants = array();
        foreach ($allusers as $user) {
            $participants[$assignment->get_uniqueid_for_user($user->id)] = $user;
        }

        $fs = get_file_storage();

        $updates = array();
        foreach ($files as $unzippedfile) {
            $user = null;
            $plugin = null;
            $filename = '';

            if ($importer->is_valid_filename_for_import($assignment, $unzippedfile, $participants, $user, $plugin, $filename)) {
                if ($importer->is_file_modified($assignment, $user, $plugin, $filename, $unzippedfile)) {
                    // Get a string we can show to identify this user.
                    $userdesc = fullname($user);
                    if ($assignment->is_blind_marking()) {
                        $userdesc = get_string('hiddenuser', 'assign') .
                                    $assignment->get_uniqueid_for_user($user->id);
                    }
                    $grade = $assignment->get_user_grade($user->id, false);

                    $exists = false;
                    if ($grade) {
                        $exists = $fs->file_exists($contextid,
                                                   'assignfeedback_file',
                                                   ASSIGNFEEDBACK_FILE_FILEAREA,
                                                   $grade->id,
                                                   '/',
                                                   $filename);
                    }

                    if (!$grade || !$exists) {
                        $updates[] = get_string('feedbackfileadded', 'assignfeedback_file',
                                            array('filename'=>$filename, 'student'=>$userdesc));
                    } else {
                        $updates[] = get_string('feedbackfileupdated', 'assignfeedback_file',
                                            array('filename'=>$filename, 'student'=>$userdesc));
                    }
                }
            }
        }

        if (count($updates)) {
            $mform->addElement('html', $renderer->list_block_contents(array(), $updates));
        } else {
            $mform->addElement('html', get_string('nochanges', 'assignfeedback_file'));
        }

        $mform->addElement('hidden', 'id', $assignment->get_course_module()->id);
        $mform->addElement('hidden', 'action', 'viewpluginpage');
        $mform->addElement('hidden', 'confirm', 'true');
        $mform->addElement('hidden', 'plugin', 'file');
        $mform->addElement('hidden', 'pluginsubtype', 'assignfeedback');
        $mform->addElement('hidden', 'pluginaction', 'uploadzip');
        if (count($updates)) {
            $this->add_action_buttons(true, get_string('confirm'));
        } else {
            $mform->addElement('cancel');
            $mform->closeHeaderBefore('cancel');
        }
    }
}

