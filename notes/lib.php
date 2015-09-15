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
 * Library of functions and constants for notes
 *
 * @package    core_notes
 * @copyright  2007 onwards Yu Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Constants for states.
 */
define('NOTES_STATE_DRAFT', 'draft');
define('NOTES_STATE_PUBLIC', 'public');
define('NOTES_STATE_SITE', 'site');

/**
 * Constants for note parts (flags used by note_print and note_print_list).
 */
define('NOTES_SHOW_FULL', 0x07);
define('NOTES_SHOW_HEAD', 0x02);
define('NOTES_SHOW_BODY', 0x01);
define('NOTES_SHOW_FOOT', 0x04);

/**
 * Retrieves a list of note objects with specific atributes.
 *
 * @param int    $courseid id of the course in which the notes were posted (0 means any)
 * @param int    $userid id of the user to which the notes refer (0 means any)
 * @param string $state state of the notes (i.e. draft, public, site) ('' means any)
 * @param int    $author id of the user who modified the note last time (0 means any)
 * @param string $order an order to sort the results in
 * @param int    $limitfrom number of records to skip (offset)
 * @param int    $limitnum number of records to fetch
 * @return array of note objects
 */
function note_list($courseid=0, $userid=0, $state = '', $author = 0, $order='lastmodified DESC', $limitfrom=0, $limitnum=0) {
    global $DB;

    // Setup filters.
    $selects = array();
    $params = array();
    if ($courseid) {
        $selects[] = 'courseid=?';
        $params[]  = $courseid;
    }
    if ($userid) {
        $selects[] = 'userid=?';
        $params[]  = $userid;
    }
    if ($author) {
        $selects[] = 'usermodified=?';
        $params[]  = $author;
    }
    if ($state) {
        $selects[] = 'publishstate=?';
        $params[]  = $state;
    }
    $selects[] = "module=?";
    $params[]  = 'notes';

    $select = implode(' AND ', $selects);
    $fields = 'id,courseid,userid,content,format,created,lastmodified,usermodified,publishstate';

    return $DB->get_records_select('post', $select, $params, $order, $fields, $limitfrom, $limitnum);
}

/**
 * Retrieves a note object based on its id.
 *
 * @param int $noteid ID of the note to retrieve
 * @return stdClass object
 */
function note_load($noteid) {
    global $DB;

    $fields = 'id,courseid,userid,content,format,created,lastmodified,usermodified,publishstate';
    return $DB->get_record('post', array('id' => $noteid, 'module' => 'notes'), $fields);
}

/**
 * Saves a note object. The note object is passed by reference and its fields (i.e. id)
 * might change during the save.
 *
 * @param stdClass   $note object to save
 * @return boolean true if the object was saved; false otherwise
 */
function note_save(&$note) {
    global $USER, $DB;

    // Setup & clean fields.
    $note->module       = 'notes';
    $note->lastmodified = time();
    $note->usermodified = $USER->id;
    if (empty($note->format)) {
        $note->format = FORMAT_PLAIN;
    }
    if (empty($note->publishstate)) {
        $note->publishstate = NOTES_STATE_PUBLIC;
    }
    // Save data.
    if (empty($note->id)) {
        // Insert new note.
        $note->created = $note->lastmodified;
        $id = $DB->insert_record('post', $note);
        $note = note_load($id);

        // Trigger event.
        $event = \core\event\note_created::create(array(
            'objectid' => $note->id,
            'courseid' => $note->courseid,
            'relateduserid' => $note->userid,
            'userid' => $note->usermodified,
            'context' => context_course::instance($note->courseid),
            'other' => array('publishstate' => $note->publishstate)
        ));
        $event->trigger();
    } else {
        // Update old note.
        $DB->update_record('post', $note);
        $note = note_load($note->id);

        // Trigger event.
        $event = \core\event\note_updated::create(array(
            'objectid' => $note->id,
            'courseid' => $note->courseid,
            'relateduserid' => $note->userid,
            'userid' => $note->usermodified,
            'context' => context_course::instance($note->courseid),
            'other' => array('publishstate' => $note->publishstate)
        ));
        $event->trigger();
    }
    unset($note->module);
    return true;
}

/**
 * Deletes a note object based on its id.
 *
 * @param int|object    $note id of the note to delete, or a note object which is to be deleted.
 * @return boolean true always
 */
function note_delete($note) {
    global $DB;
    if (is_int($note)) {
        $noteid = $note;
    } else {
        $noteid = $note->id;
    }
    // Get the full record, note_load doesn't return everything.
    $note = $DB->get_record('post', array('id' => $noteid), '*', MUST_EXIST);
    $return = $DB->delete_records('post', array('id' => $note->id, 'module' => 'notes'));

    // Trigger event.
    $event = \core\event\note_deleted::create(array(
        'objectid' => $note->id,
        'courseid' => $note->courseid,
        'relateduserid' => $note->userid,
        'userid' => $note->usermodified,
        'context' => context_course::instance($note->courseid),
        'other' => array('publishstate' => $note->publishstate)
    ));
    $event->add_record_snapshot('post', $note);
    $event->trigger();

    return $return;
}

/**
 * Converts a state value to its corespondent name
 *
 * @param string  $state state value to convert
 * @return string corespondent state name
 */
function note_get_state_name($state) {
    // Cache state names.
    static $states;
    if (empty($states)) {
        $states = note_get_state_names();
    }
    if (isset($states[$state])) {
        return $states[$state];
    } else {
        return null;
    }
}

/**
 * Returns an array of mappings from state values to state names
 *
 * @return array of mappings
 */
function note_get_state_names() {
    return array(
        NOTES_STATE_DRAFT => get_string('personal', 'notes'),
        NOTES_STATE_PUBLIC => get_string('course', 'notes'),
        NOTES_STATE_SITE => get_string('site', 'notes'),
    );
}

/**
 * Prints a note object
 *
 * @param note  $note the note object to print
 * @param int   $detail OR-ed NOTES_SHOW_xyz flags that specify which note parts to print
 */
function note_print($note, $detail = NOTES_SHOW_FULL) {
    global $CFG, $USER, $DB, $OUTPUT;

    if (!$user = $DB->get_record('user', array('id' => $note->userid))) {
        debugging("User $note->userid not found");
        return;
    }
    if (!$author = $DB->get_record('user', array('id' => $note->usermodified))) {
        debugging("User $note->usermodified not found");
        return;
    }
    $context = context_course::instance($note->courseid);
    $systemcontext = context_system::instance();

    $authoring = new stdClass();
    $authoring->name = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $author->id .
        '&amp;course='.$note->courseid . '">' . fullname($author) . '</a>';
    $authoring->date = userdate($note->lastmodified);

    echo '<div class="notepost '. $note->publishstate . 'notepost' .
        ($note->usermodified == $USER->id ? ' ownnotepost' : '')  .
        '" id="note-' . $note->id . '">';

    // Print note head (e.g. author, user refering to, etc).
    if ($detail & NOTES_SHOW_HEAD) {
        echo '<div class="header">';
        echo '<div class="user">';
        echo $OUTPUT->user_picture($user, array('courseid' => $note->courseid));
        echo fullname($user) . '</div>';
        echo '<div class="info">' .
            get_string('bynameondate', 'notes', $authoring) .
            ' (' . get_string('created', 'notes') . ': ' . userdate($note->created) . ')</div>';
        echo '</div>';
    }

    // Print note content.
    if ($detail & NOTES_SHOW_BODY) {
        echo '<div class="content">';
        echo format_text($note->content, $note->format, array('overflowdiv' => true));
        echo '</div>';
    }

    // Print note options (e.g. delete, edit).
    if ($detail & NOTES_SHOW_FOOT) {
        if (has_capability('moodle/notes:manage', $systemcontext) && $note->publishstate == NOTES_STATE_SITE ||
            has_capability('moodle/notes:manage', $context) &&
            ($note->publishstate == NOTES_STATE_PUBLIC || $note->usermodified == $USER->id)) {
            echo '<div class="footer"><p>';
            echo '<a href="' . $CFG->wwwroot . '/notes/edit.php?id=' . $note->id. '">' . get_string('edit') . '</a> | ';
            echo '<a href="' . $CFG->wwwroot . '/notes/delete.php?id=' . $note->id. '">' . get_string('delete') . '</a>';
            echo '</p></div>';
        }
    }
    echo '</div>';
}

/**
 * Prints a list of note objects
 *
 * @param array  $notes array of note objects to print
 * @param int   $detail OR-ed NOTES_SHOW_xyz flags that specify which note parts to print
 */
function note_print_list($notes, $detail = NOTES_SHOW_FULL) {

    echo '<div class="notelist">';
    foreach ($notes as $note) {
        note_print($note, $detail);
    }
    echo '</div>';
}

/**
 * Retrieves and prints a list of note objects with specific atributes.
 *
 * @param string  $header HTML to print above the list
 * @param int     $addcourseid id of the course for the add notes link (0 hide link)
 * @param boolean $viewnotes true if the notes should be printed; false otherwise (print notesnotvisible string)
 * @param int     $courseid id of the course in which the notes were posted (0 means any)
 * @param int     $userid id of the user to which the notes refer (0 means any)
 * @param string  $state state of the notes (i.e. draft, public, site) ('' means any)
 * @param int     $author id of the user who modified the note last time (0 means any)
 */
function note_print_notes($header, $addcourseid = 0, $viewnotes = true, $courseid = 0, $userid = 0, $state = '', $author = 0) {
    global $CFG;

    if ($header) {
        echo '<h3 class="notestitle">' . $header . '</h3>';
        echo '<div class="notesgroup">';
    }
    if ($addcourseid) {
        if ($userid) {
            echo '<p><a href="' . $CFG->wwwroot . '/notes/edit.php?courseid=' . $addcourseid . '&amp;userid=' . $userid .
                '&amp;publishstate=' . $state . '">' . get_string('addnewnote', 'notes') . '</a></p>';
        } else {
            echo '<p><a href="' . $CFG->wwwroot . '/user/index.php?id=' . $addcourseid. '">' .
                get_string('addnewnoteselect', 'notes') . '</a></p>';
        }
    }
    if ($viewnotes) {
        $notes = note_list($courseid, $userid, $state, $author);
        if ($notes) {
            note_print_list($notes);
        }
    } else {
        echo '<p>' . get_string('notesnotvisible', 'notes') . '</p>';
    }
    if ($header) {
        echo '</div>';  // The notesgroup div.
    }
}

/**
 * Delete all notes about users in course-
 * @param int $courseid
 * @return bool success
 */
function note_delete_all($courseid) {
    global $DB;

    return $DB->delete_records('post', array('module' => 'notes', 'courseid' => $courseid));
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function note_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array('notes-*' => get_string('page-notes-x', 'notes'));
}

/**
 * Trigger notes viewed event
 *
 * @param  stdClass $context context object
 * @param  int $userid  user id (the user we are viewing the notes)
 * @since Moodle 2.9
 */
function note_view($context, $userid) {

    $event = \core\event\notes_viewed::create(array(
        'relateduserid' => $userid,
        'context' => $context
    ));
    $event->trigger();
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function core_notes_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG;

    if (empty($CFG->enablenotes)) {
        // Notes are disabled, nothing to do.
        return false;
    }

    $url = new moodle_url("/notes/index.php", array('user' => $user->id));
    $title = get_string('notes', 'core_notes');
    if (empty($course)) {
        // Site level profile.
        if (!has_capability('moodle/notes:view', context_system::instance())) {
            // No cap, nothing to do.
            return false;
        }
    } else {
        if (!has_capability('moodle/notes:view', context_course::instance($course->id))) {
            // No cap, nothing to do.
            return false;
        }
        $url->param('course', $course->id);
    }
    $notesnode = new core_user\output\myprofile\node('miscellaneous', 'notes', $title, null, $url);
    $tree->add_node($notesnode);
}
