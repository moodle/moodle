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
 * Library of functions and constants for module flashcard
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @contributors Valery Fremaux
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/ddllib.php');
require_once($CFG->dirroot.'/mod/flashcard/locallib.php');
require_once($CFG->dirroot.'/mod/flashcard/mailtemplatelib.php');

/**
 * Indicates API features that the forum supports.
 *
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_COMPLETION_HAS_RULES
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function flashcard_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS: {
            return true;
        }
        case FEATURE_GROUPINGS: {
            return false;
        }
        case FEATURE_GROUPMEMBERSONLY: {
            return false;
        }
        case FEATURE_MOD_INTRO: {
            return true;
        }
        case FEATURE_COMPLETION_TRACKS_VIEWS: {
            return true;
        }
        case FEATURE_COMPLETION_HAS_RULES: {
            return true;
        }
        case FEATURE_GRADE_HAS_GRADE: {
            return false;
        }
        case FEATURE_GRADE_OUTCOMES: {
            return false;
        }
        case FEATURE_RATE: {
            return false;
        }
        case FEATURE_BACKUP_MOODLE2: {
            return true;
        }
        case FEATURE_SHOW_DESCRIPTION: {
            return true;
        }
        // 4.1
        case FEATURE_MOD_PURPOSE: {
            return MOD_PURPOSE_CONTENT;
        }

        default: {
            return null;
        }
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 */
function flashcard_add_instance($flashcard) {
    global $DB;

    $flashcard->timemodified = time();

    if (!isset($flashcard->starttimeenable)) {
        $flashcard->starttime = 0;
    }

    if (!isset($flashcard->endtimeenable)) {
        $flashcard->endtime = 0;
    }

    // Saves draft customization image files into definitive filearea.
    $customimages = array('custombackfileid',
                          'customfrontfileid',
                          'customemptyfileid',
                          'customreviewfileid',
                          'customreviewedfileid',
                          'customreviewemptyfileid');
    foreach ($customimages as $ci) {
        flashcard_save_draft_customimage($flashcard, $ci);
    }

    // weird hack
    // Quickform fails getting thios value in $data.
    $flashcard->completionallviewed = clean_param(@$_POST['completionallviewed'], PARAM_INT);

    $newid = $DB->insert_record('flashcard', $flashcard);

    // Import all information from question.
    if (isset($flashcard->forcereload) && $flashcard->forcereload) {
        flashcard_import($flashcard);
    }

    return $newid;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 */
function flashcard_update_instance($flashcard) {
    global $DB;

    $flashcard->timemodified = time();
    $flashcard->id = $flashcard->instance;

    // Update first deck with questions that might be added.

    if (isset($flashcard->forcereload) && $flashcard->forcereload) {
        flashcard_import($flashcard);
    }

    if (!isset($flashcard->starttimeenable)) {
        $flashcard->starttime = 0;
    }

    if (!isset($flashcard->endtimeenable)) {
        $flashcard->endtime = 0;
    }

    // Saves draft customization image files into definitive filearea.
    $customimages = array('custombackfileid',
                          'customfrontfileid',
                          'customemptyfileid',
                          'customreviewfileid',
                          'customreviewedfileid',
                          'customreviewemptyfileid');
    foreach ($customimages as $ci) {
        flashcard_save_draft_customimage($flashcard, $ci);
    }

    // weird hack
    // Quickform fails getting thios value in $data.
    $flashcard->completionallviewed = clean_param(@$_POST['completionallviewed'], PARAM_INT);

    $return = $DB->update_record('flashcard', $flashcard);

    return $return;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 */
function flashcard_delete_instance($id) {
    global $DB;

    if (!$flashcard = $DB->get_record('flashcard', array('id' => $id))) {
        return false;
    }
    if (!$cm = get_coursemodule_from_instance('flashcard', $flashcard->id)) {
        return false;
    }

    $context = context_module::instance($cm->id);

    $result = true;

    // Delete any dependent records here.
    $DB->delete_records('flashcard_deckdata', array('flashcardid' => $flashcard->id));
    $DB->delete_records('flashcard_card', array('flashcardid' => $flashcard->id));

    // Now get rid of all files.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id);

    if (!$DB->delete_records('flashcard', array('id' => $flashcard->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 */
function flashcard_user_outline($course, $user, $mod, $flashcard) {
    global $DB;

    $params = array('userid' => $user->id, 'flashcardid' => $flashcard->id);
    if ($lastaccess = $DB->get_field('flashcard_card', 'MAX(lastaccessed)', $params)) {

        $return->time = $lastaccess;
        $return->info = get_string('lastaccessed', 'flashcard');

        return $return;
    }
    return false;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 * @param object $course
 * @param object $user User object
 * @param object $mod the course module object
 * @param object $flashcard the flaschard instance
 */
function flashcard_user_complete($course, $user, $mod, $flashcard) {
    global $DB;

    $cardsdeck = array();
    $deckaccesscount = array();

    $params = array('userid' => $user->id, 'flashcardid' => $flashcard->id);
    if ($usercards = $DB->get_records('flashcard_card', $params)) {
        foreach ($usercards as $uc) {
            if (!array_key_exists($uc->deck, $cardsdeck)) {
                $cardsdeck[$uc->deck] = 0;
            }
            $cardsdeck[$uc->deck]++;
            if (!array_key_exists($uc->deck, $deckaccesscount)) {
                $deckaccesscount[$uc->deck] = 0;
            }
            $deckaccesscount[$uc->deck] += $uc->accesscount;
        }
    }

    asort($cardsdeck);

    foreach ($cardsdeck as $deckid => $counter) {
        $a = new StdClass();
        $a->count = $counter;
        $a->deck = $deckid;
        $a->cardcount = $deckaccesscount[$deckid];
        echo get_string('userdecksummary', 'flashcard', $a);
        echo '<br/>';
    }

    return true;
}

/**
 * Checks if scale is being used by any instance of assignment
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any assignment
 */
function flashcard_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in flashcard activities and print it out.
 * Return true if there was output, or false is there was none.
 * @param object $course
 * @param bool $isteacher
 * @param int $timestart
 * @uses $CFG
 */
function flashcard_print_recent_activity($course, $isteacher, $timestart) {
    return false; // True if anything was printed, otherwise false.
}

/**
 * Must return an array of grades for a given instance of this module,
 * indexed by user.  It also returns a maximum allowed grade.
 *
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 */
function flashcard_grades($flashcardid) {
    return null;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of flashcard. Must include every user involved
 * in the instance, independent of his role (student, teacher, admin...)
 * See other modules as example.
 * @uses $DB
 */
function flashcard_get_participants($flashcardid) {
    global $DB;

    $sql = "
        SELECT DISTINCT
            userid,
            userid
        FROM
            {flashcard_card}
        WHERE
            flashcardid = ?
    ";
    $userids = $DB->get_records_sql_menu($sql, array('flashcardid' => $flashcardid));
    if ($userids) {
        $users = $DB->get_records_list('user', 'id', array_keys($userids));
    }

    if (!empty($users)) {
        return $users;
    }

    return false;
}

/**
 * This function returns if a scale is being used by one flashcard
 * it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 */
function flashcard_scale_used($flashcardid, $scaleid) {

    $return = false;

    return $return;
}

/**
 * Serves the files included in flashcard. Implements needed access control ;-)
 *
 * There are several situations in general where the files will be sent.
 * 1) filearea = 'questionsoundfile',
 * 2) filearea = 'questionimagefile',
 * 3) filearea = 'questionvideofile',
 * 4) filearea = 'answersoundfile',
 * 5) filearea = 'answerimagefile',
 * 6) filearea = 'answervideofile'
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function flashcard_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $DB;

    require_login($course);

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    $allfileareas = array('intro',
                          'questionsoundfile',
                          'questionimagefile',
                          'questionvideofile',
                          'answersoundfile',
                          'answerimagefile',
                          'answervideofile',
                          'customfront',
                          'customempty',
                          'customback',
                          'customreview',
                          'customreviewed',
                          'customreviewempty');

    if (!in_array($filearea, $allfileareas)) {
        return false;
    }

    $itemid = (int)array_shift($args);

    $fs = get_file_storage();

    if ($files = $fs->get_area_files($context->id, 'mod_flashcard', $filearea, $itemid,
                                     "sortorder, itemid, filepath, filename", false)) {
        $file = array_pop($files);

        // Finally send the file.
        send_stored_file($file, 0, 0, $forcedownload);
    }

    return false;
}

/**
 * Obtains the automatic completion state for this flashcard
 *
 * @global object
 * @global object
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function flashcard_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // Get flashcard details.
    if (!($flashcard = $DB->get_record('flashcard', array('id' => $cm->instance)))) {
        throw new Exception("Can't find flashcard {$cm->instance}");
    }

    $result = $type; // Default return value.

    // Completion condition 1 is have no cards in deck.

    // Count all cards.
    $allcards = $DB->count_records('flashcard_deckdata', array('flashcardid' => $flashcard->id));

    if ($flashcard->completionallgood) {

        // Match any card that are NOT in last deck.
        $sql = "
            SELECT
                COUNT(DISTINCT c.id)
            FROM
                {flashcard_card} c
            WHERE
                c.userid = ? AND
                c.flashcardid = ? AND
                c.deck = ?
        ";
        $good = $DB->count_records_sql($sql, array($userid, $flashcard->id, $flashcard->decks));
        if ($type == COMPLETION_AND) {
            $result = $result && ($good == $allcards);
        } else {
            $result = $result || ($good == $allcards);
        }
    } else if ($flashcard->completionallviewed) {
        // Allgood superseedes allviewed.

        // Match distinct viewed cards.
        $sql = "
            SELECT
                COUNT(DISTINCT c.entryid)
            FROM
                {flashcard_card} c
            WHERE
                c.userid = ? AND
                c.flashcardid = ?
        ";
        $viewed = $DB->count_records_sql($sql, array($userid, $flashcard->id));

        if ($type == COMPLETION_AND) {
            $result = $result && ($viewed >= min($allcards, $flashcard->completionallviewed));
        } else {
            $result = $result || ($viewed >= min($allcards, $flashcard->completionallviewed));
        }
    }

    // Completion condition 2 is : all cards in last deck (easiest).

    return $result;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all posts from the specified forum
 * and clean up any related data.
 *
 * @global object
 * @global object
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function flashcard_reset_userdata($data) {
    global $DB;

    $componentstr = get_string('modulenameplural', 'flashcard');
    $status = array();

    $allflashcardsql = "
        SELECT
            f.id
        FROM
            {flashcard} f
        WHERE
            f.course = ?
    ";

    // Remove all grades from gradebook.

    // Remove all states and usr attempts unconditionally - even for users still enrolled in course.
    if (!empty($data->reset_flashcard_all)) {
        $params = array($data->courseid);
        $DB->delete_records_select('flashcard_card', " flashcardid IN ($allflashcardsql) ", $params);
        $DB->delete_records_select('flashcard_userdeck_state', " flashcardid IN ($allflashcardsql) ", $params);
        $status[] = array('component' => $componentstr,
                          'item' => get_string('resetflashcardstates', 'flashcard'),
                          'error' => false);
    }

    return $status;
}

/**
 * Called by course/reset.php
 *
 * @param $mform form passed by reference
 */
function flashcard_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'flashcardheader', get_string('modulenameplural', 'flashcard'));
    $mform->addElement('checkbox', 'reset_flashcard_all', get_string('resetflashcardstates', 'flashcard'));
}

/**
 * Course reset form defaults.
 * @return array
 */
function flashcard_reset_course_form_defaults($course) {
    return array('reset_flashcard_all' => 1);
}


/**
 * This function allows the tool_dbcleaner to register integrity checks
 */
function flashcard_dbcleaner_add_keys() {
    global $DB;

    $flashcardmoduleid = $DB->get_field('modules', 'id', array('name' => 'flashcard'));

    $keys = array(
        array('flashcard', 'course', 'course', 'id', ''),
        array('flashcard', 'id', 'course_modules', 'instance', ' module = '.$flashcardmoduleid.' '),
        array('flashcard_card', 'flashcardid', 'flashcard', 'id', ''),
        array('flashcard_card', 'userid', 'user', 'id', ''),
        array('flashcard_deckdata', 'flashcardid', 'flashcard', 'id', ''),
        array('flashcard_userdeck_state', 'flashcardid', 'flashcard', 'id', ''),
        array('flashcard_userdeck_state', 'userid', 'user', 'id', ''),
    );

    return $keys;
}

function flashcard_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $flashcardnode) {
    global $DB;

    if (has_capability('mod/flashcard:manage', $settingsnav->get_page()->context)) {

        $params = array('view' => 'summary', 'id' => $settingsnav->get_page()->cm->id, 'page' => 'byusers');
        $reportlink = new moodle_url("/mod/flashcard/view.php", $params);
        $flashcardnode->add(get_string('teachersummary', 'flashcard'), $reportlink, navigation_node::TYPE_CONTAINER);

        $params = array('view' => 'manage', 'id' => $settingsnav->get_page()->cm->id);
        $reportlink = new moodle_url("/mod/flashcard/view.php", $params);
        $flashcardnode->add(get_string('edit', 'flashcard'), $reportlink, navigation_node::TYPE_SETTING);

        $flashcard = $DB->get_record('flashcard', ['id' => $settingsnav->get_page()->cm->instance]);
        if (($flashcard->questionsmediatype == FLASHCARD_MEDIA_TEXT) &&
                ($flashcard->answersmediatype == FLASHCARD_MEDIA_TEXT)) {
            $params = array('what' => 'import', 'view' => 'manage', 'id' => $settingsnav->get_page()->cm->id);
            $reportlink = new moodle_url("/mod/flashcard/view.php", $params);
            $flashcardnode->add(get_string('import', 'flashcard'), $reportlink, navigation_node::TYPE_SETTING);
        }
    }
}