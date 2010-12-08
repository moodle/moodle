<?php

defined('MOODLE_INTERNAL') || die;


/// Library of functions and constants for module 'book'

function book_add_instance($book) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $book->timecreated = time();
    $book->timemodified = $book->timecreated;
    if (!isset($book->customtitles)) {
        $book->customtitles = 0;
    }
    if (!isset($book->disableprinting)) {
        $book->disableprinting = 0;
    }

    return insert_record('book', $book);
}


function book_update_instance($book) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    $book->timemodified = time();
    $book->id = $book->instance;
    if (!isset($book->customtitles)) {
        $book->customtitles = 0;
    }
    if (!isset($book->disableprinting)) {
        $book->disableprinting = 0;
    }

    # May have to add extra stuff in here #

    return update_record('book', $book);
}


function book_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $book = get_record('book', 'id', $id)) {
        return false;
    }

    $result = true;

    delete_records('book_chapters', 'bookid', $book->id);

    if (! delete_records('book', 'id', $book->id)) {
        $result = false;
    }

    return $result;
}


function book_get_types() {
    global $CFG;

    $types = array();

    $type = new object();
    $type->modclass = MOD_CLASS_RESOURCE;
    $type->type = 'book';
    $type->typestr = get_string('modulename', 'book');
    $types[] = $type;

    return $types;
}
function book_user_outline($course, $user, $mod, $book) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    $return = null;
    return $return;
}

function book_user_complete($course, $user, $mod, $book) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function book_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in book activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG;

    return false;  //  True if anything was printed, otherwise false
}

function book_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function book_grades($bookid) {
/// Must return an array of grades for a given instance of this module,
/// indexed by user.  It also returns a maximum allowed grade.

    return NULL;
}

function book_get_participants($bookid) {
//Must return an array of user records (all data) who are participants
//for a given instance of book. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)
//See other modules as example.

    return false;
}

/**
 * This function returns if a scale is being used by one book
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 * @param $bookid int
 * @param $scaleid int
 * @return boolean True if the scale is used by any journal
 */
function book_scale_used ($bookid,$scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of book
 *
 * This is used to find out if scale used anywhere
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
