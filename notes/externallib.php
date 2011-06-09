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
 * External notes API
 *
 * @package    moodlecore
 * @subpackage notes
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/externallib.php");

class moodle_notes_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_notes_parameters() {
        return new external_function_parameters(
            array(
                'notes' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_INT, 'id of the user the note is about'),
                            'publishstate' => new external_value(PARAM_ALPHA, '\'personal\', \'course\' or \'site\''),
                            'courseid' => new external_value(PARAM_INT, 'course id of the note (in Moodle a note can only be created into a course, even for site and personal notes)'),
                            'text' => new external_value(PARAM_RAW, 'the text of the message - text or HTML'),
                            'format' => new external_value(PARAM_ALPHA, '\'text\' or \'html\'', VALUE_DEFAULT, 'text'),
                            'clientnoteid' => new external_value(PARAM_ALPHANUMEXT, 'your own client id for the note. If this id is provided, the fail message id will be returned to you', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Create notes about some users
     * Note: code should be matching the /notes/edit.php checks
     * and the /user/addnote.php checks. (they are similar cheks)
     * @param array $notes  An array of notes to create.
     * @return array (success infos and fail infos)
     */
    public static function create_notes($notes = array()) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/notes/lib.php");

        $params = self::validate_parameters(self::create_notes_parameters(), array('notes' => $notes));

        //check if note system is enabled
        if (!$CFG->enablenotes) {
            throw new moodle_exception('notesdisabled', 'notes');
        }

        //retrieve all courses
        $courseids = array();
        foreach($params['notes'] as $note) {
            $courseids[] = $note['courseid'];
        }
        $courses = $DB->get_records_list("course", "id", $courseids);

        //retrieve all users of the notes
        $userids = array();
        foreach($params['notes'] as $note) {
            $userids[] = $note['userid'];
        }
        list($sqluserids, $sqlparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'userid_');
        $users = $DB->get_records_select("user", "id " . $sqluserids . " AND deleted = 0", $sqlparams);

        $resultnotes = array();
        foreach ($params['notes'] as $note) {

            $success = true;
            $resultnote = array(); //the infos about the success of the operation

            //check the course exists
            if (empty($courses[$note['courseid']])) {
                $success = false;
                $errormessage = get_string('invalidcourseid', 'notes', $note['courseid']);
            } else {
                // Ensure the current user is allowed to run this function
                $context = get_context_instance(CONTEXT_COURSE, $note['courseid']);
                self::validate_context($context);
                require_capability('moodle/notes:manage', $context);
            }

            //check the user exists
            if (empty($users[$note['userid']])) {
                $success = false;
                $errormessage = get_string('invaliduserid', 'notes', $note['userid']);
            }

            //build the resultnote
            if (isset($note['clientnoteid'])) {
                $resultnote['clientnoteid'] = $note['clientnoteid'];
            }

            if ($success) {
                //now we can create the note
                $dbnote = new stdClass;
                $dbnote->courseid = $note['courseid'];
                $dbnote->userid = $note['userid'];
                //clean param text and set format accordingly
                switch (strtolower($note['format'])) {
                    case 'html':
                        $dbnote->content = clean_param($note['text'], PARAM_CLEANHTML);
                        $dbnote->format = FORMAT_HTML;
                        break;
                    case 'text':
                    default:
                        $dbnote->content = clean_param($note['text'], PARAM_TEXT);
                        $dbnote->format = FORMAT_PLAIN;
                        break;
                }

                //get the state ('personal', 'course', 'site')
                switch ($note['publishstate']) {
                    case 'personal':
                        $dbnote->publishstate = NOTES_STATE_DRAFT;
                        break;
                    case 'course':
                        $dbnote->publishstate = NOTES_STATE_PUBLIC;
                        break;
                    case 'site':
                        $dbnote->publishstate = NOTES_STATE_SITE;
                        $dbnote->courseid = SITEID;
                        break;
                    default:
                        break;
                }

                //TODO: performance improvement - if possible create a bulk functions for saving multiple notes at once
                if (note_save($dbnote)) { //note_save attribut an id in case of success
                    add_to_log($dbnote->courseid, 'notes', 'add',
                            'index.php?course='.$dbnote->courseid.'&amp;user='.$dbnote->userid
                            . '#note-' . $dbnote->id , 'add note');
                    $success = $dbnote->id;
                }

                $resultnote['noteid'] = $success;
            } else {
                $resultnote['noteid'] = -1;
                $resultnote['errormessage'] = $errormessage;
            }

            $resultnotes[] = $resultnote;
        }

        return $resultnotes;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function create_notes_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'clientnoteid' => new external_value(PARAM_ALPHANUMEXT, 'your own id for the note', VALUE_OPTIONAL),
                    'noteid' => new external_value(PARAM_INT, 'test this to know if it success:  id of the created note when successed, -1 when failed'),
                    'errormessage' => new external_value(PARAM_TEXT, 'error message - if failed', VALUE_OPTIONAL)
                )
            )
        );
    }

}
