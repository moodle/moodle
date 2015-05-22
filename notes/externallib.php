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
 * @package    core_notes
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . "/notes/lib.php");

/**
 * Notes external functions
 *
 * @package    core_notes
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class core_notes_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
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
                            'format' => new external_format_value('text', VALUE_DEFAULT),
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
     *
     * @param array $notes  An array of notes to create.
     * @return array (success infos and fail infos)
     * @since Moodle 2.2
     */
    public static function create_notes($notes = array()) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::create_notes_parameters(), array('notes' => $notes));

        // Check if note system is enabled.
        if (!$CFG->enablenotes) {
            throw new moodle_exception('notesdisabled', 'notes');
        }

        // Retrieve all courses.
        $courseids = array();
        foreach ($params['notes'] as $note) {
            $courseids[] = $note['courseid'];
        }
        $courses = $DB->get_records_list("course", "id", $courseids);

        // Retrieve all users of the notes.
        $userids = array();
        foreach ($params['notes'] as $note) {
            $userids[] = $note['userid'];
        }
        list($sqluserids, $sqlparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'userid_');
        $users = $DB->get_records_select("user", "id " . $sqluserids . " AND deleted = 0", $sqlparams);

        $resultnotes = array();
        foreach ($params['notes'] as $note) {

            $success = true;
            $resultnote = array(); // The infos about the success of the operation.

            // Check the course exists.
            if (empty($courses[$note['courseid']])) {
                $success = false;
                $errormessage = get_string('invalidcourseid', 'error');
            } else {
                // Ensure the current user is allowed to run this function.
                $context = context_course::instance($note['courseid']);
                self::validate_context($context);
                require_capability('moodle/notes:manage', $context);
            }

            // Check the user exists.
            if (empty($users[$note['userid']])) {
                $success = false;
                $errormessage = get_string('invaliduserid', 'notes', $note['userid']);
            }

            // Build the resultnote.
            if (isset($note['clientnoteid'])) {
                $resultnote['clientnoteid'] = $note['clientnoteid'];
            }

            if ($success) {
                // Now we can create the note.
                $dbnote = new stdClass;
                $dbnote->courseid = $note['courseid'];
                $dbnote->userid = $note['userid'];
                // Need to support 'html' and 'text' format values for backward compatibility.
                switch (strtolower($note['format'])) {
                    case 'html':
                        $textformat = FORMAT_HTML;
                        break;
                    case 'text':
                        $textformat = FORMAT_PLAIN;
                    default:
                        $textformat = external_validate_format($note['format']);
                        break;
                }
                $dbnote->content = $note['text'];
                $dbnote->format = $textformat;

                // Get the state ('personal', 'course', 'site').
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

                // TODO MDL-31119 performance improvement - if possible create a bulk functions for saving multiple notes at once
                if (note_save($dbnote)) { // Note_save attribut an id in case of success.
                    $success = $dbnote->id;
                }

                $resultnote['noteid'] = $success;
            } else {
                // WARNINGS: for backward compatibility we return this errormessage.
                //          We should have thrown exceptions as these errors prevent results to be returned.
                // See http://docs.moodle.org/dev/Errors_handling_in_web_services#When_to_send_a_warning_on_the_server_side .
                $resultnote['noteid'] = -1;
                $resultnote['errormessage'] = $errormessage;
            }

            $resultnotes[] = $resultnote;
        }

        return $resultnotes;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function create_notes_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'clientnoteid' => new external_value(PARAM_ALPHANUMEXT, 'your own id for the note', VALUE_OPTIONAL),
                    'noteid' => new external_value(PARAM_INT, 'ID of the created note when successful, -1 when failed'),
                    'errormessage' => new external_value(PARAM_TEXT, 'error message - if failed', VALUE_OPTIONAL)
                )
            )
        );
    }

    /**
     * Returns description of delete_notes parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_notes_parameters() {
        return new external_function_parameters(
            array(
                "notes"=> new external_multiple_structure(
                    new external_value(PARAM_INT, 'ID of the note to be deleted'), 'Array of Note Ids to be deleted.'
                )
            )
        );
    }

    /**
     * Delete notes about users.
     * Note: code should be matching the /notes/delete.php checks.
     *
     * @param array $notes An array of ids for the notes to delete.
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_notes($notes = array()) {
        global $CFG;

        $params = self::validate_parameters(self::delete_notes_parameters(), array('notes' => $notes));

        // Check if note system is enabled.
        if (!$CFG->enablenotes) {
            throw new moodle_exception('notesdisabled', 'notes');
        }
        $warnings = array();
        foreach ($params['notes'] as $noteid) {
            $note = note_load($noteid);
            if (isset($note->id)) {
                // Ensure the current user is allowed to run this function.
                $context = context_course::instance($note->courseid);
                self::validate_context($context);
                require_capability('moodle/notes:manage', $context);
                if (!note_delete($note)) {
                    $warnings[] = array(array('item' => 'note',
                                              'itemid' => $noteid,
                                              'warningcode' => 'savedfailed',
                                              'message' => 'Note could not be modified'));
                }
            } else {
                $warnings[] = array('item'=>'note', 'itemid'=>$noteid, 'warningcode'=>'badid', 'message'=>'Note does not exist');
            }
        }
        return $warnings;
    }

    /**
     * Returns description of delete_notes result value.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function delete_notes_returns() {
        return  new external_warnings('item is always \'note\'',
                            'When errorcode is savedfailed the note could not be modified.' .
                            'When errorcode is badparam, an incorrect parameter was provided.' .
                            'When errorcode is badid, the note does not exist',
                            'errorcode can be badparam (incorrect parameter), savedfailed (could not be modified), or badid (note does not exist)');

    }

    /**
     * Returns description of get_notes parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_notes_parameters() {
        return new external_function_parameters(
            array(
                "notes"=> new external_multiple_structure(
                    new external_value(PARAM_INT, 'ID of the note to be retrieved'), 'Array of Note Ids to be retrieved.'
                )
            )
        );
    }

    /**
     * Get notes about users.
     *
     * @param array $notes An array of ids for the notes to retrieve.
     * @return null
     * @since Moodle 2.5
     */
    public static function get_notes($notes) {
        global $CFG;

        $params = self::validate_parameters(self::get_notes_parameters(), array('notes' => $notes));
        // Check if note system is enabled.
        if (!$CFG->enablenotes) {
            throw new moodle_exception('notesdisabled', 'notes');
        }
        $resultnotes = array();
        foreach ($params['notes'] as $noteid) {
            $resultnote = array();

            $note = note_load($noteid);
            if (isset($note->id)) {
                // Ensure the current user is allowed to run this function.
                $context = context_course::instance($note->courseid);
                self::validate_context($context);
                require_capability('moodle/notes:view', $context);
                list($gotnote['text'], $gotnote['format']) = external_format_text($note->content,
                                                                                  $note->format,
                                                                                  $context->id,
                                                                                  'notes',
                                                                                  '',
                                                                                  '');
                $gotnote['noteid'] = $note->id;
                $gotnote['userid'] = $note->userid;
                $gotnote['publishstate'] = $note->publishstate;
                $gotnote['courseid'] = $note->courseid;
                $resultnotes["notes"][] = $gotnote;
            } else {
                $resultnotes["warnings"][] = array('item' => 'note',
                                                   'itemid' => $noteid,
                                                   'warningcode' => 'badid',
                                                   'message' => 'Note does not exist');
            }
        }
        return $resultnotes;
    }

    /**
     * Returns description of get_notes result value.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function get_notes_returns() {
        return new external_single_structure(
            array(
                'notes' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'noteid' => new external_value(PARAM_INT, 'id of the note', VALUE_OPTIONAL),
                            'userid' => new external_value(PARAM_INT, 'id of the user the note is about', VALUE_OPTIONAL),
                            'publishstate' => new external_value(PARAM_ALPHA, '\'personal\', \'course\' or \'site\'', VALUE_OPTIONAL),
                            'courseid' => new external_value(PARAM_INT, 'course id of the note', VALUE_OPTIONAL),
                            'text' => new external_value(PARAM_RAW, 'the text of the message - text or HTML', VALUE_OPTIONAL),
                            'format' => new external_format_value('text', VALUE_OPTIONAL),
                        ), 'note'
                    )
                 ),
                 'warnings' => new external_warnings('item is always \'note\'',
                        'When errorcode is savedfailed the note could not be modified.' .
                        'When errorcode is badparam, an incorrect parameter was provided.' .
                        'When errorcode is badid, the note does not exist',
                        'errorcode can be badparam (incorrect parameter), savedfailed (could not be modified), or badid (note does not exist)')
            )
        );
    }

    /**
     * Returns description of update_notes parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function update_notes_parameters() {
        return new external_function_parameters(
            array(
                'notes' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'id of the note'),
                            'publishstate' => new external_value(PARAM_ALPHA, '\'personal\', \'course\' or \'site\''),
                            'text' => new external_value(PARAM_RAW, 'the text of the message - text or HTML'),
                            'format' => new external_format_value('text', VALUE_DEFAULT),
                        )
                    ), "Array of Notes", VALUE_DEFAULT, array()
                )
            )
        );
    }

    /**
     * Update notes about users.
     *
     * @param array $notes An array of ids for the notes to update.
     * @return array fail infos.
     * @since Moodle 2.2
     */
    public static function update_notes($notes = array()) {
        global $CFG, $DB;

        $params = self::validate_parameters(self::update_notes_parameters(), array('notes' => $notes));

        // Check if note system is enabled.
        if (!$CFG->enablenotes) {
            throw new moodle_exception('notesdisabled', 'notes');
        }

        $warnings = array();
        foreach ($params['notes'] as $note) {
            $notedetails = note_load($note['id']);
            if (isset($notedetails->id)) {
                // Ensure the current user is allowed to run this function.
                $context = context_course::instance($notedetails->courseid);
                self::validate_context($context);
                require_capability('moodle/notes:manage', $context);

                $dbnote = new stdClass;
                $dbnote->id = $note['id'];
                $dbnote->content = $note['text'];
                $dbnote->format = external_validate_format($note['format']);
                // Get the state ('personal', 'course', 'site').
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
                        $warnings[] = array('item' => 'note',
                                            'itemid' => $note["id"],
                                            'warningcode' => 'badparam',
                                            'message' => 'Provided publishstate incorrect');
                        break;
                }
                if (!note_save($dbnote)) {
                    $warnings[] = array('item' => 'note',
                                        'itemid' => $note["id"],
                                        'warningcode' => 'savedfailed',
                                        'message' => 'Note could not be modified');
                }
            } else {
                $warnings[] = array('item' => 'note',
                                    'itemid' => $note["id"],
                                    'warningcode' => 'badid',
                                    'message' => 'Note does not exist');
            }
        }
        return $warnings;
    }

    /**
     * Returns description of update_notes result value.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function update_notes_returns() {
        return new external_warnings('item is always \'note\'',
                            'When errorcode is savedfailed the note could not be modified.' .
                            'When errorcode is badparam, an incorrect parameter was provided.' .
                            'When errorcode is badid, the note does not exist',
                            'errorcode can be badparam (incorrect parameter), savedfailed (could not be modified), or badid (note does not exist)');
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_course_notes_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id, 0 for SITE'),
                'userid'   => new external_value(PARAM_INT, 'user id', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Create a notes list
     *
     * @param int $courseid ID of the Course
     * @param stdClass $context context object
     * @param int $userid ID of the User
     * @param int $state
     * @param int $author
     * @return array of notes
     * @since Moodle 2.9
     */
    protected static function create_note_list($courseid, $context, $userid, $state, $author = 0) {
        $results = array();
        $notes = note_list($courseid, $userid, $state, $author);
        foreach ($notes as $key => $note) {
            $note = (array)$note;
            list($note['content'], $note['format']) = external_format_text($note['content'],
                                                                           $note['format'],
                                                                           $context->id,
                                                                           '',
                                                                           '',
                                                                           0);
            $results[$key] = $note;
        }
        return $results;
    }

    /**
     * Get a list of course notes
     *
     * @param int $courseid ID of the Course
     * @param int $userid ID of the User
     * @return array of site, course and personal notes and warnings
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_course_notes($courseid, $userid = 0) {
        global $CFG, $USER;

        if (empty($CFG->enablenotes)) {
            throw new moodle_exception('notesdisabled', 'notes');
        }

        $warnings = array();
        $arrayparams = array(
            'courseid' => $courseid,
            'userid'   => $userid,
        );
        $params = self::validate_parameters(self::get_course_notes_parameters(), $arrayparams);

        if (empty($params['courseid'])) {
            $params['courseid'] = SITEID;
        }
        $user = null;
        if (!empty($params['userid'])) {
            $user = core_user::get_user($params['userid'], 'id', MUST_EXIST);
        }

        $course = get_course($params['courseid']);

        if ($course->id == SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($course->id);
        }
        self::validate_context($context);

        $sitenotes = array();
        $coursenotes = array();
        $personalnotes = array();

        if ($course->id != SITEID) {

            require_capability('moodle/notes:view', $context);
            $sitenotes = self::create_note_list($course->id, $context, $params['userid'], NOTES_STATE_SITE);
            $coursenotes = self::create_note_list($course->id, $context, $params['userid'], NOTES_STATE_PUBLIC);
            $personalnotes = self::create_note_list($course->id, $context, $params['userid'], NOTES_STATE_DRAFT,
                                                        $USER->id);
        } else {
            if (has_capability('moodle/notes:view', $context)) {
                $sitenotes = self::create_note_list(0, $context, $params['userid'], NOTES_STATE_SITE);
            }
            // It returns notes only for a specific user!
            if (!empty($user)) {
                $usercourses = enrol_get_users_courses($user->id, true);
                foreach ($usercourses as $c) {
                    // All notes at course level, only if we have capability on every course.
                    if (has_capability('moodle/notes:view', context_course::instance($c->id))) {
                        $coursenotes += self::create_note_list($c->id, $context, $params['userid'], NOTES_STATE_PUBLIC);
                    }
                }
            }
        }

        $results = array(
            'sitenotes'     => $sitenotes,
            'coursenotes'   => $coursenotes,
            'personalnotes' => $personalnotes,
            'warnings'      => $warnings
        );
        return $results;

    }

    /**
     * Returns array of note structure
     *
     * @return external_description
     * @since Moodle 2.9
     */
    protected static function get_note_structure() {
        return array(
                     'id'           => new external_value(PARAM_INT, 'id of this note'),
                     'courseid'     => new external_value(PARAM_INT, 'id of the course'),
                     'userid'       => new external_value(PARAM_INT, 'user id'),
                     'content'      => new external_value(PARAM_RAW, 'the content text formated'),
                     'format'       => new external_format_value('content'),
                     'created'      => new external_value(PARAM_INT, 'time created (timestamp)'),
                     'lastmodified' => new external_value(PARAM_INT, 'time of last modification (timestamp)'),
                     'usermodified' => new external_value(PARAM_INT, 'user id of the creator of this note'),
                     'publishstate' => new external_value(PARAM_ALPHA, "state of the note (i.e. draft, public, site) ")
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_course_notes_returns() {
        return new external_single_structure(
            array(
                  'sitenotes' => new external_multiple_structure(
                      new external_single_structure(
                          self::get_note_structure() , ''
                      ), 'site notes', VALUE_OPTIONAL
                   ),
                   'coursenotes' => new external_multiple_structure(
                      new external_single_structure(
                          self::get_note_structure() , ''
                      ), 'couse notes', VALUE_OPTIONAL
                   ),
                   'personalnotes' => new external_multiple_structure(
                      new external_single_structure(
                          self::get_note_structure() , ''
                      ), 'personal notes', VALUE_OPTIONAL
                   ),
                 'warnings' => new external_warnings()
            ), 'notes'
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_notes_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id, 0 for notes at system level'),
                'userid' => new external_value(PARAM_INT, 'user id, 0 means view all the user notes', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Simulates the web interface view of notes/index.php: trigger events
     *
     * @param int $courseid id of the course
     * @param int $userid id of the user
     * @return array of warnings and status result
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function view_notes($courseid, $userid = 0) {
        global $CFG;
        require_once($CFG->dirroot . "/notes/lib.php");

        if (empty($CFG->enablenotes)) {
            throw new moodle_exception('notesdisabled', 'notes');
        }

        $warnings = array();
        $arrayparams = array(
            'courseid' => $courseid,
            'userid' => $userid
        );
        $params = self::validate_parameters(self::view_notes_parameters(), $arrayparams);

        if (empty($params['courseid'])) {
            $params['courseid'] = SITEID;
        }

        $course = get_course($params['courseid']);

        if ($course->id == SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($course->id);
        }

        // First of all, validate the context before do further permission checks.
        self::validate_context($context);
        require_capability('moodle/notes:view', $context);

        if (!empty($params['userid'])) {
            $user = core_user::get_user($params['userid'], 'id, deleted', MUST_EXIST);

            if ($user->deleted) {
                throw new moodle_exception('userdeleted');
            }

            if (isguestuser($user)) {
                throw new moodle_exception('invaliduserid');
            }

            if ($course->id != SITEID and !is_enrolled($context, $user, '', true)) {
                throw new moodle_exception('notenrolledprofile');
            }
        }

        note_view($context, $params['userid']);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;

    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function view_notes_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

}

/**
 * Deprecated notes external functions
 *
 * @package    core_notes
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.1
 * @deprecated Moodle 2.2 MDL-29106 - Please do not use this class any more.
 * @see core_notes_external
 */
class moodle_notes_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_notes_external::create_notes_parameters()
     */
    public static function create_notes_parameters() {
        return core_notes_external::create_notes_parameters();
    }

    /**
     * Create notes about some users
     * Note: code should be matching the /notes/edit.php checks
     * and the /user/addnote.php checks. (they are similar cheks)
     *
     * @param array $notes  An array of notes to create.
     * @return array (success infos and fail infos)
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_notes_external::create_notes()
     */
    public static function create_notes($notes = array()) {
        return core_notes_external::create_notes($notes);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_notes_external::create_notes_returns()
     */
    public static function create_notes_returns() {
        return core_notes_external::create_notes_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function create_notes_is_deprecated() {
        return true;
    }

}
