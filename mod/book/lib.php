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
 * Book module core interaction API
 *
 * @package    mod_book
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Returns list of available numbering types
 * @return array
 */
function book_get_numbering_types() {
    global $CFG; // required for the include

    require_once(dirname(__FILE__).'/locallib.php');

    return array (
        BOOK_NUM_NONE       => get_string('numbering0', 'mod_book'),
        BOOK_NUM_NUMBERS    => get_string('numbering1', 'mod_book'),
        BOOK_NUM_BULLETS    => get_string('numbering2', 'mod_book'),
        BOOK_NUM_INDENTED   => get_string('numbering3', 'mod_book')
    );
}

/**
 * Returns all other caps used in module
 * @return array
 */
function book_get_extra_capabilities() {
    // used for group-members-only
    return array('moodle/site:accessallgroups');
}

/**
 * Add book instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return int new book instance id
 */
function book_add_instance($data, $mform) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    if (!isset($data->customtitles)) {
        $data->customtitles = 0;
    }

    return $DB->insert_record('book', $data);
}

/**
 * Update book instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return bool true
 */
function book_update_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;
    if (!isset($data->customtitles)) {
        $data->customtitles = 0;
    }

    $DB->update_record('book', $data);

    $book = $DB->get_record('book', array('id'=>$data->id));
    $DB->set_field('book', 'revision', $book->revision+1, array('id'=>$book->id));

    return true;
}

/**
 * Delete book instance by activity id
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
 * Given a course and a time, this module should find recent activity
 * that has occurred in book activities and print it out.
 *
 * @param stdClass $course
 * @param bool $viewfullnames
 * @param int $timestart
 * @return bool true if there was output, or false is there was none
 */
function book_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function book_reset_userdata($data) {
    return array();
}

/**
 * No cron in book.
 *
 * @return bool
 */
function book_cron () {
    return true;
}

/**
 * No grading in book.
 *
 * @param int $bookid
 * @return null
 */
function book_grades($bookid) {
    return null;
}

/**
 * This function returns if a scale is being used by one book
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See book, glossary or journal modules
 * as reference.
 *
 * @param int $bookid
 * @param int $scaleid
 * @return boolean True if the scale is used by any journal
 */
function book_scale_used($bookid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of book
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $scaleid
 * @return bool true if the scale is used by any book
 */
function book_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Return read actions.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function book_get_view_actions() {
    global $CFG; // necessary for includes

    $return = array('view', 'view all');

    $plugins = core_component::get_plugin_list('booktool');
    foreach ($plugins as $plugin => $dir) {
        if (file_exists("$dir/lib.php")) {
            require_once("$dir/lib.php");
        }
        $function = 'booktool_'.$plugin.'_get_view_actions';
        if (function_exists($function)) {
            if ($actions = $function()) {
                $return = array_merge($return, $actions);
            }
        }
    }

    return $return;
}

/**
 * Return write actions.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function book_get_post_actions() {
    global $CFG; // necessary for includes

    $return = array('update');

    $plugins = core_component::get_plugin_list('booktool');
    foreach ($plugins as $plugin => $dir) {
        if (file_exists("$dir/lib.php")) {
            require_once("$dir/lib.php");
        }
        $function = 'booktool_'.$plugin.'_get_post_actions';
        if (function_exists($function)) {
            if ($actions = $function()) {
                $return = array_merge($return, $actions);
            }
        }
    }

    return $return;
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
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settingsnav The settings navigation object
 * @param navigation_node $booknode The node to add module settings to
 * @return void
 */
function book_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $booknode) {
    global $USER, $PAGE;

    $plugins = core_component::get_plugin_list('booktool');
    foreach ($plugins as $plugin => $dir) {
        if (file_exists("$dir/lib.php")) {
            require_once("$dir/lib.php");
        }
        $function = 'booktool_'.$plugin.'_extend_settings_navigation';
        if (function_exists($function)) {
            $function($settingsnav, $booknode);
        }
    }

    $params = $PAGE->url->params();

    if (!empty($params['id']) and !empty($params['chapterid']) and has_capability('mod/book:edit', $PAGE->cm->context)) {
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
 * Lists all browsable file areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @return array
 */
function book_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['chapter'] = get_string('chapters', 'mod_book');
    return $areas;
}

/**
 * File browsing support for book module chapter area.
 * @param object $browser
 * @param object $areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return object file_info instance or null if not found
 */
function book_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB;

    // note: 'intro' area is handled in file_browser automatically

    if (!has_capability('mod/book:read', $context)) {
        return null;
    }

    if ($filearea !== 'chapter') {
        return null;
    }

    require_once(dirname(__FILE__).'/locallib.php');

    if (is_null($itemid)) {
        return new book_file_info($browser, $course, $cm, $context, $areas, $filearea);
    }

    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    if (!$storedfile = $fs->get_file($context->id, 'mod_book', $filearea, $itemid, $filepath, $filename)) {
        return null;
    }

    // modifications may be tricky - may cause caching problems
    $canwrite = has_capability('mod/book:edit', $context);

    $chaptername = $DB->get_field('book_chapters', 'title', array('bookid'=>$cm->instance, 'id'=>$itemid));
    $chaptername = format_string($chaptername, true, array('context'=>$context));

    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $chaptername, true, true, $canwrite, false);
}

/**
 * Serves the book attachments. Implements needed access control ;-)
 *
 * @param stdClass $course course object
 * @param cm_info $cm course module object
 * @param context $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function book_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
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

    // Nasty hack because we do not have file revisions in book yet.
    $lifetime = $CFG->filelifetime;
    if ($lifetime > 60*10) {
        $lifetime = 60*10;
    }

    // finally send the file
    send_stored_file($file, $lifetime, 0, $forcedownload, $options);
}

/**
 * Return a list of page types
 *
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array
 */
function book_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-book-*'=>get_string('page-mod-book-x', 'mod_book'));
    return $module_pagetype;
}
