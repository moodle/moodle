<?php
// This file is part of Book module for Moodle - http://moodle.org/
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
 * Book module core interaction API
 *
 * @package    mod
 * @subpackage book
 * @copyright  2004-2011 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


define('BOOK_NUM_NONE',     '0');
define('BOOK_NUM_NUMBERS',  '1');
define('BOOK_NUM_BULLETS',  '2');
define('BOOK_NUM_INDENTED', '3');

/**
 * Returns list of available numbering types
 * @return array
 */
function book_get_numbering_types() {
    return array (BOOK_NUM_NONE       => get_string('numbering0', 'mod_book'),
                  BOOK_NUM_NUMBERS    => get_string('numbering1', 'mod_book'),
                  BOOK_NUM_BULLETS    => get_string('numbering2', 'mod_book'),
                  BOOK_NUM_INDENTED   => get_string('numbering3', 'mod_book') );
}

/**
 * Add book instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return int new book instance id
 */
function book_add_instance($book) {
    global $DB;

    $book->timecreated = time();
    $book->timemodified = $book->timecreated;
    if (!isset($book->customtitles)) {
        $book->customtitles = 0;
    }

    return $DB->insert_record('book', $book);
}

/**
 * Update book instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return bool true
 */
function book_update_instance($book) {
    global $DB;

    $book->timemodified = time();
    $book->id = $book->instance;
    if (!isset($book->customtitles)) {
        $book->customtitles = 0;
    }

    $DB->update_record('book', $book);

    return true;
}

/**
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id
 * @return bool success
 */
function book_delete_instance($id) {
    global $DB;

    if (!$book = $DB->get_record('book', array('id'=>$id))) {
        return false;
    }

    $DB->delete_records('book_chapters', array('bookid'=>$book->id));
    $DB->delete_records('book', array('id'=>$book->id));

    return true;
}

/**
 * Return use outline
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $page
 * @return object|null
 */
function book_user_outline($course, $user, $mod, $book) {
    global $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'book',
                                              'action'=>'view', 'info'=>$book->id), 'time ASC')) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result = new stdClass();
        $result->info = get_string('numviews', '', $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}

function book_user_complete($course, $user, $mod, $book) {
    // Print a detailed representation of what a  user has done with
    // a given particular instance of this module, for user activity reports.

    return true;
}

function book_print_recent_activity($course, $isteacher, $timestart) {
    // Given a course and a time, this module should find recent activity
    // that has occurred in book activities and print it out.
    // Return true if there was output, or false is there was none.
    return false;  //  True if anything was printed, otherwise false
}

function book_cron () {
    // Function to be run periodically according to the moodle cron
    // This function searches for things that need to be done, such
    // as sending out mail, toggling flags etc ...
    return true;
}

function book_grades($bookid) {
    // Must return an array of grades for a given instance of this module,
    // indexed by user.  It also returns a maximum allowed grade.

    return null;
}

function book_get_participants($bookid) {
    //Must return an array of user records (all data) who are participants
    //for a given instance of book. Must include every user involved
    //in the instance, independent of his role (student, teacher, admin...)
    //See other modules as example.

    return false;
}

/**
 * This function returns if a scale is being used by one book
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See book, glossary or journal modules
 * as reference.
 *
 * @param $bookid int
 * @param $scaleid int
 * @return boolean True if the scale is used by any journal
 */
function book_scale_used($bookid,$scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of book
 *
 * This is used to find out if scale used anywhere
 *
 * @param $scaleid int
 * @return boolean True if the scale is used by any journal
 */
function book_scale_used_anywhere($scaleid) {
    return false;
}

function book_get_view_actions() {
    return array('view', 'view all', 'print');
}

function book_get_post_actions() {
    return array('update');
}

/**
 * Supported features
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function book_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $booknode The node to add module settings to
 * @return void
 */
function book_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $booknode) {
    global $USER, $PAGE, $CFG, $DB, $OUTPUT;

    if ($PAGE->cm->modname !== 'book') {
        return;
    }

    if (empty($PAGE->cm->context)) {
        $PAGE->cm->context = get_context_instance(CONTEXT_MODULE, $PAGE->cm->instance);
    }


    $params = $PAGE->url->params();

    if (empty($params['id']) or empty($params['chapterid'])) {
        return;
    }

    $plugins = get_plugin_list('booktool');
    foreach($plugins as $plugin=>$dir) {
        if (file_exists("$dir/lib.php")) {
            require_once("$dir/lib.php");
        }
        $function = 'booktool_'.$plugin.'_extend_settings_navigation';
        if (function_exists($function)) {
            $function($settingsnav, $booknode);
        }
    }

    if (has_capability('mod/book:edit', $PAGE->cm->context)) {
        if (!empty($USER->editing)) {
            $string = get_string("turneditingoff");
            $edit = '0';
        } else {
            $string = get_string("turneditingon");
            $edit = '1';
        }
        $url = new moodle_url('/mod/book/view.php', array('id'=>$params['id'], 'chapterid'=>$params['chapterid'], 'edit'=>$edit, 'sesskey'=>sesskey()));
        $booknode->add($string, $url, navigation_node::TYPE_SETTING);
    }
}

/**
 * Serves the book attachments. Implements needed access control ;-)
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function book_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea !== 'chapter') {
        return false;
    }

    if (!has_capability('mod/book:read', $context)) {
        return false;
    }

    $chid = (int)array_shift($args);

    if (!$book = $DB->get_record('book', array('id'=>$cm->instance))) {
        return false;
    }

    if (!$chapter = $DB->get_record('book_chapters', array('id'=>$chid, 'bookid'=>$book->id))) {
        return false;
    }

    if ($chapter->hidden and !has_capability('mod/book:viewhiddenchapters', $context)) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_book/chapter/$chid/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 360, 0, false);
}