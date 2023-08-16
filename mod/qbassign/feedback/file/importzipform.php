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
 * @package   qbassignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/qbassign/feedback/file/importziplib.php');

/**
 * Import zip form
 *
 * @package   qbassignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbassignfeedback_file_import_zip_form extends moodleform implements renderable {

    /**
     * Create this grade import form
     */
    public function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;
        $params = $this->_customdata;

        $renderer = $PAGE->get_renderer('qbassign');

        // Visible elements.
        $qbassignment = $params['qbassignment'];
        $contextid = $qbassignment->get_context()->id;
        $importer = $params['importer'];
        $update = false;

        if (!$importer) {
            throw new \moodle_exception('invalidarguments');
            return;
        }

        $files = $importer->get_import_files($contextid);

        $mform->addElement('header', 'uploadzip', get_string('confirmuploadzip', 'qbassignfeedback_file'));

        $currentgroup = groups_get_activity_group($qbassignment->get_course_module(), true);
        $allusers = $qbassignment->list_participants($currentgroup, false);
        $participants = array();
        foreach ($allusers as $user) {
            $participants[$qbassignment->get_uniqueid_for_user($user->id)] = $user;
        }

        $fs = get_file_storage();

        $updates = array();
        foreach ($files as $unzippedfile) {
            $user = null;
            $plugin = null;
            $filename = '';

            if ($importer->is_valid_filename_for_import($qbassignment, $unzippedfile, $participants, $user, $plugin, $filename)) {
                if ($importer->is_file_modified($qbassignment, $user, $plugin, $filename, $unzippedfile)) {
                    // Get a string we can show to identify this user.
                    $userdesc = fullname($user, has_capability('moodle/site:viewfullnames', $qbassignment->get_context()));
                    $path = pathinfo($filename);
                    if ($qbassignment->is_blind_marking()) {
                        $userdesc = get_string('hiddenuser', 'qbassign') .
                                    $qbassignment->get_uniqueid_for_user($user->id);
                    }
                    $grade = $qbassignment->get_user_grade($user->id, false);

                    $exists = false;
                    if ($grade) {
                        $exists = $fs->file_exists($contextid,
                                                   'qbassignfeedback_file',
                                                   qbassignFEEDBACK_FILE_FILEAREA,
                                                   $grade->id,
                                                   $path['dirname'],
                                                   $path['basename']);
                    }

                    if (!$grade || !$exists) {
                        $updates[] = get_string('feedbackfileadded', 'qbassignfeedback_file',
                                            array('filename'=>$filename, 'student'=>$userdesc));
                    } else {
                        $updates[] = get_string('feedbackfileupdated', 'qbassignfeedback_file',
                                            array('filename'=>$filename, 'student'=>$userdesc));
                    }
                }
            }
        }

        if (count($updates)) {
            $mform->addElement('html', $renderer->list_block_contents(array(), $updates));
        } else {
            $mform->addElement('html', get_string('nochanges', 'qbassignfeedback_file'));
        }

        $mform->addElement('hidden', 'id', $qbassignment->get_course_module()->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'viewpluginpage');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'confirm', 'true');
        $mform->setType('confirm', PARAM_BOOL);
        $mform->addElement('hidden', 'plugin', 'file');
        $mform->setTYpe('plugin', PARAM_PLUGIN);
        $mform->addElement('hidden', 'pluginsubtype', 'qbassignfeedback');
        $mform->setTYpe('pluginsubtype', PARAM_PLUGIN);
        $mform->addElement('hidden', 'pluginaction', 'uploadzip');
        $mform->setType('pluginaction', PARAM_ALPHA);
        if (count($updates)) {
            $this->add_action_buttons(true, get_string('confirm'));
        } else {
            $mform->addElement('cancel');
            $mform->closeHeaderBefore('cancel');
        }
    }
}

