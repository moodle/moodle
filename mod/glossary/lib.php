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
 * Library of functions and constants for module glossary
 * (replace glossary with the name of your module and delete this line)
 *
 * @package   mod-glossary
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/rating/lib.php');
require_once($CFG->libdir . '/completionlib.php');

define("GLOSSARY_SHOW_ALL_CATEGORIES", 0);
define("GLOSSARY_SHOW_NOT_CATEGORISED", -1);

define("GLOSSARY_NO_VIEW", -1);
define("GLOSSARY_STANDARD_VIEW", 0);
define("GLOSSARY_CATEGORY_VIEW", 1);
define("GLOSSARY_DATE_VIEW", 2);
define("GLOSSARY_AUTHOR_VIEW", 3);
define("GLOSSARY_ADDENTRY_VIEW", 4);
define("GLOSSARY_IMPORT_VIEW", 5);
define("GLOSSARY_EXPORT_VIEW", 6);
define("GLOSSARY_APPROVAL_VIEW", 7);

/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////
/**
 * @global object
 * @param object $glossary
 * @return int
 */
function glossary_add_instance($glossary) {
    global $DB;
/// Given an object containing all the necessary data,
/// (defined by the form in mod_form.php) this function
/// will create a new instance and return the id number
/// of the new instance.

    if (empty($glossary->ratingtime) or empty($glossary->assessed)) {
        $glossary->assesstimestart  = 0;
        $glossary->assesstimefinish = 0;
    }

    if (empty($glossary->globalglossary) ) {
        $glossary->globalglossary = 0;
    }

    if (!has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
        $glossary->globalglossary = 0;
    }

    $glossary->timecreated  = time();
    $glossary->timemodified = $glossary->timecreated;

    //Check displayformat is a valid one
    $formats = get_list_of_plugins('mod/glossary/formats','TEMPLATE');
    if (!in_array($glossary->displayformat, $formats)) {
        print_error('unknowformat', '', '', $glossary->displayformat);
    }

    $returnid = $DB->insert_record("glossary", $glossary);
    $glossary->id = $returnid;
    glossary_grade_item_update($glossary);

    return $returnid;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @global object
 * @param object $glossary
 * @return bool
 */
function glossary_update_instance($glossary) {
    global $CFG, $DB;

    if (empty($glossary->globalglossary)) {
        $glossary->globalglossary = 0;
    }

    if (!has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
        // keep previous
        unset($glossary->globalglossary);
    }

    $glossary->timemodified = time();
    $glossary->id           = $glossary->instance;

    if (empty($glossary->ratingtime) or empty($glossary->assessed)) {
        $glossary->assesstimestart  = 0;
        $glossary->assesstimefinish = 0;
    }

    //Check displayformat is a valid one
    $formats = get_list_of_plugins('mod/glossary/formats','TEMPLATE');
    if (!in_array($glossary->displayformat, $formats)) {
        print_error('unknowformat', '', '', $glossary->displayformat);
    }

    $DB->update_record("glossary", $glossary);
    if ($glossary->defaultapproval) {
        $DB->execute("UPDATE {glossary_entries} SET approved = 1 where approved <> 1 and glossaryid = ?", array($glossary->id));
    }
    glossary_grade_item_update($glossary);

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id glossary id
 * @return bool success
 */
function glossary_delete_instance($id) {
    global $DB, $CFG;

    if (!$glossary = $DB->get_record('glossary', array('id'=>$id))) {
        return false;
    }

    if (!$cm = get_coursemodule_from_instance('glossary', $id)) {
        return false;
    }

    if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        return false;
    }

    $fs = get_file_storage();

    if ($glossary->mainglossary) {
        // unexport entries
        $sql = "SELECT ge.id, ge.sourceglossaryid, cm.id AS sourcecmid
                  FROM {glossary_entries} ge
                  JOIN {modules} m ON m.name = 'glossary'
                  JOIN {course_modules} cm ON (cm.module = m.id AND cm.instance = ge.sourceglossaryid)
                 WHERE ge.glossaryid = ? AND ge.sourceglossaryid > 0";

        if ($exported = $DB->get_records_sql($sql, array($id))) {
            foreach ($exported as $entry) {
                $entry->glossaryid = $entry->sourceglossaryid;
                $entry->sourceglossaryid = 0;
                $newcontext = get_context_instance(CONTEXT_MODULE, $entry->sourcecmid);
                if ($oldfiles = $fs->get_area_files($context->id, 'mod_glossary', 'attachment', $entry->id)) {
                    foreach ($oldfiles as $oldfile) {
                        $file_record = new stdClass();
                        $file_record->contextid = $newcontext->id;
                        $fs->create_file_from_storedfile($file_record, $oldfile);
                    }
                    $fs->delete_area_files($context->id, 'mod_glossary', 'attachment', $entry->id);
                    $entry->attachment = '1';
                } else {
                    $entry->attachment = '0';
                }
                $DB->update_record('glossary_entries', $entry);
            }
        }
    } else {
        // move exported entries to main glossary
        $sql = "UPDATE {glossary_entries}
                   SET sourceglossaryid = 0
                 WHERE sourceglossaryid = ?";
        $DB->execute($sql, array($id));
    }

    // Delete any dependent records
    $entry_select = "SELECT id FROM {glossary_entries} WHERE glossaryid = ?";
    $DB->delete_records_select('comments', "contextid=? AND commentarea=? AND itemid IN ($entry_select)", array($id, 'glossary_entry', $context->id));
    $DB->delete_records_select('glossary_alias',    "entryid IN ($entry_select)", array($id));

    $category_select = "SELECT id FROM {glossary_categories} WHERE glossaryid = ?";
    $DB->delete_records_select('glossary_entries_categories', "categoryid IN ($category_select)", array($id));
    $DB->delete_records('glossary_categories', array('glossaryid'=>$id));
    $DB->delete_records('glossary_entries', array('glossaryid'=>$id));

    // delete all files
    $fs->delete_area_files($context->id);

    glossary_grade_item_delete($glossary);

    return $DB->delete_records('glossary', array('id'=>$id));
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $glossary
 * @return object|null
 */
function glossary_user_outline($course, $user, $mod, $glossary) {
    global $CFG;

    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'glossary', $glossary->id, $user->id);
    if (empty($grades->items[0]->grades)) {
        $grade = false;
    } else {
        $grade = reset($grades->items[0]->grades);
    }

    if ($entries = glossary_get_user_entries($glossary->id, $user->id)) {
        $result = new stdClass();
        $result->info = count($entries) . ' ' . get_string("entries", "glossary");

        $lastentry = array_pop($entries);
        $result->time = $lastentry->timemodified;

        if ($grade) {
            $result->info .= ', ' . get_string('grade') . ': ' . $grade->str_long_grade;
        }
        return $result;
    } else if ($grade) {
        $result = new stdClass();
        $result->info = get_string('grade') . ': ' . $grade->str_long_grade;

        //datesubmitted == time created. dategraded == time modified or time overridden
        //if grade was last modified by the user themselves use date graded. Otherwise use date submitted
        //TODO: move this copied & pasted code somewhere in the grades API. See MDL-26704
        if ($grade->usermodified == $user->id || empty($grade->datesubmitted)) {
            $result->time = $grade->dategraded;
        } else {
            $result->time = $grade->datesubmitted;
        }

        return $result;
    }
    return NULL;
}

/**
 * @global object
 * @param int $glossaryid
 * @param int $userid
 * @return array
 */
function glossary_get_user_entries($glossaryid, $userid) {
/// Get all the entries for a user in a glossary
    global $DB;

    return $DB->get_records_sql("SELECT e.*, u.firstname, u.lastname, u.email, u.picture
                                   FROM {glossary} g, {glossary_entries} e, {user} u
                             WHERE g.id = ?
                               AND e.glossaryid = g.id
                               AND e.userid = ?
                               AND e.userid = u.id
                          ORDER BY e.timemodified ASC", array($glossaryid, $userid));
}

/**
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $glossary
 */
function glossary_user_complete($course, $user, $mod, $glossary) {
    global $CFG, $OUTPUT;
    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'glossary', $glossary->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        echo $OUTPUT->container(get_string('grade').': '.$grade->str_long_grade);
        if ($grade->str_feedback) {
            echo $OUTPUT->container(get_string('feedback').': '.$grade->str_feedback);
        }
    }

    if ($entries = glossary_get_user_entries($glossary->id, $user->id)) {
        echo '<table width="95%" border="0"><tr><td>';
        foreach ($entries as $entry) {
            $cm = get_coursemodule_from_instance("glossary", $glossary->id, $course->id);
            glossary_print_entry($course, $cm, $glossary, $entry,"","",0);
            echo '<p>';
        }
        echo '</td></tr></table>';
    }
}
/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in glossary activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @global object
 * @global object
 * @global object
 * @param object $course
 * @param object $viewfullnames
 * @param int $timestart
 * @return bool
 */
function glossary_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;

    //TODO: use timestamp in approved field instead of changing timemodified when approving in 2.0
    if (!defined('GLOSSARY_RECENT_ACTIVITY_LIMIT')) {
        define('GLOSSARY_RECENT_ACTIVITY_LIMIT', 50);
    }

    $modinfo = get_fast_modinfo($course);
    $ids = array();

    foreach ($modinfo->cms as $cm) {
        if ($cm->modname != 'glossary') {
            continue;
        }
        if (!$cm->uservisible) {
            continue;
        }
        $ids[$cm->instance] = $cm->id;
    }

    if (!$ids) {
        return false;
    }

    // generate list of approval capabilities for all glossaries in the course.
    $approvals = array();
    foreach ($ids as $glinstanceid => $glcmid) {
        $context = get_context_instance(CONTEXT_MODULE, $glcmid);
        // get records glossary entries that are approved if user has no capability to approve entries.
        if (has_capability('mod/glossary:approve', $context)) {
            $approvals[] = ' ge.glossaryid = :glsid'.$glinstanceid.' ';
        } else {
            $approvals[] = ' (ge.approved = 1 AND ge.glossaryid = :glsid'.$glinstanceid.') ';
        }
        $params['glsid'.$glinstanceid] = $glinstanceid;
    }

    $selectsql = 'SELECT ge.id, ge.concept, ge.approved, ge.timemodified, ge.glossaryid,
                                        '.user_picture::fields('u',null,'userid');
    $countsql = 'SELECT COUNT(*)';

    $joins = array(' FROM {glossary_entries} ge ');
    $joins[] = 'JOIN {user} u ON u.id = ge.userid ';
    $fromsql = implode($joins, "\n");

    $params['timestart'] = $timestart;
    $clausesql = ' WHERE ge.timemodified > :timestart AND (';
    $approvalsql = implode($approvals, ' OR ');

    $ordersql = ') ORDER BY ge.timemodified ASC';

    $entries = $DB->get_records_sql($selectsql.$fromsql.$clausesql.$approvalsql.$ordersql, $params, 0, (GLOSSARY_RECENT_ACTIVITY_LIMIT+1));

    if (empty($entries)) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newentries', 'glossary').':');

    $strftimerecent = get_string('strftimerecent');
    $entrycount = 0;
    foreach ($entries as $entry) {
        if ($entrycount < GLOSSARY_RECENT_ACTIVITY_LIMIT) {
            if ($entry->approved) {
                $dimmed = '';
                $urlparams = array('g' => $entry->glossaryid, 'mode' => 'entry', 'hook' => $entry->id);
            } else {
                $dimmed = ' dimmed_text';
                $urlparams = array('id' => $ids[$entry->glossaryid], 'mode' => 'approval', 'hook' => format_text($entry->concept, true));
            }
            $link = new moodle_url($CFG->wwwroot.'/mod/glossary/view.php' , $urlparams);
            echo '<div class="head'.$dimmed.'">';
            echo '<div class="date">'.userdate($entry->timemodified, $strftimerecent).'</div>';
            echo '<div class="name">'.fullname($entry, $viewfullnames).'</div>';
            echo '</div>';
            echo '<div class="info"><a href="'.$link.'">'.format_text($entry->concept, true).'</a></div>';
            $entrycount += 1;
        } else {
            $numnewentries = $DB->count_records_sql($countsql.$joins[0].$clausesql.$approvalsql.')', $params);
            echo '<div class="head"><div class="activityhead">'.get_string('andmorenewentries', 'glossary', $numnewentries - GLOSSARY_RECENT_ACTIVITY_LIMIT).'</div></div>';
            break;
        }
    }

    return true;
}

/**
 * @global object
 * @param object $log
 */
function glossary_log_info($log) {
    global $DB;

    return $DB->get_record_sql("SELECT e.*, u.firstname, u.lastname
                                  FROM {glossary_entries} e, {user} u
                                 WHERE e.id = ? AND u.id = ?", array($log->info, $log->userid));
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 * @return bool
 */
function glossary_cron () {
    return true;
}

/**
 * Return grade for given user or all users.
 *
 * @global object
 * @param int $glossaryid id of glossary
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function glossary_get_user_grades($glossary, $userid=0) {
    global $CFG;

    require_once($CFG->dirroot.'/rating/lib.php');
    $rm = new rating_manager();

    $ratingoptions = new stdclass();

    //need these to work backwards to get a context id. Is there a better way to get contextid from a module instance?
    $ratingoptions->modulename = 'glossary';
    $ratingoptions->moduleid   = $glossary->id;

    $ratingoptions->userid = $userid;
    $ratingoptions->aggregationmethod = $glossary->assessed;
    $ratingoptions->scaleid = $glossary->scale;
    $ratingoptions->itemtable = 'glossary_entries';
    $ratingoptions->itemtableusercolumn = 'userid';

    return $rm->get_user_grades($ratingoptions);
}

/**
 * Return rating related permissions
 * @param string $options the context id
 * @return array an associative array of the user's rating permissions
 */
function glossary_rating_permissions($options) {
    $contextid = $options;
    $context = get_context_instance_by_id($contextid);

    if (!$context) {
        print_error('invalidcontext');
        return null;
    } else {
        return array('view'=>has_capability('mod/glossary:viewrating',$context), 'viewany'=>has_capability('mod/glossary:viewanyrating',$context), 'viewall'=>has_capability('mod/glossary:viewallratings',$context), 'rate'=>has_capability('mod/glossary:rate',$context));
    }
}

/**
 * Validates a submitted rating
 * @param array $params submitted data
 *            context => object the context in which the rated items exists [required]
 *            itemid => int the ID of the object being rated
 *            scaleid => int the scale from which the user can select a rating. Used for bounds checking. [required]
 *            rating => int the submitted rating
 *            rateduserid => int the id of the user whose items have been rated. NOT the user who submitted the ratings. 0 to update all. [required]
 *            aggregation => int the aggregation method to apply when calculating grades ie RATING_AGGREGATE_AVERAGE [optional]
 * @return boolean true if the rating is valid. Will throw rating_exception if not
 */
function glossary_rating_validate($params) {
    global $DB, $USER;

    if (!array_key_exists('itemid', $params)
            || !array_key_exists('context', $params)
            || !array_key_exists('rateduserid', $params)
            || !array_key_exists('scaleid', $params)) {
        throw new rating_exception('missingparameter');
    }

    $glossarysql = "SELECT g.id as gid, g.scale, e.userid as userid, e.approved, e.timecreated, g.assesstimestart, g.assesstimefinish
                      FROM {glossary_entries} e
                      JOIN {glossary} g ON e.glossaryid = g.id
                     WHERE e.id = :itemid";
    $glossaryparams = array('itemid'=>$params['itemid']);
    if (!$info = $DB->get_record_sql($glossarysql, $glossaryparams)) {
        //item doesn't exist
        throw new rating_exception('invaliditemid');
    }

    if ($info->scale != $params['scaleid']) {
        //the scale being submitted doesnt match the one in the database
        throw new rating_exception('invalidscaleid');
    }

    if ($info->userid == $USER->id) {
        //user is attempting to rate their own glossary entry
        throw new rating_exception('nopermissiontorate');
    }

    if ($info->userid != $params['rateduserid']) {
        //supplied user ID doesnt match the user ID from the database
        throw new rating_exception('invaliduserid');
    }

    //check that the submitted rating is valid for the scale

    // lower limit
    if ($params['rating'] < 0  && $params['rating'] != RATING_UNSET_RATING) {
        throw new rating_exception('invalidnum');
    }

    // upper limit
    if ($info->scale < 0) {
        //its a custom scale
        $scalerecord = $DB->get_record('scale', array('id' => -$params['scaleid']));
        if ($scalerecord) {
            $scalearray = explode(',', $scalerecord->scale);
            if ($params['rating'] > count($scalearray)) {
                throw new rating_exception('invalidnum');
            }
        } else {
            throw new rating_exception('invalidscaleid');
        }
    } else if ($params['rating'] > $info->scale) {
        //if its numeric and submitted rating is above maximum
        throw new rating_exception('invalidnum');
    }

    if (!$info->approved) {
        //item isnt approved
        throw new rating_exception('nopermissiontorate');
    }

    //check the item we're rating was created in the assessable time window
    if (!empty($info->assesstimestart) && !empty($info->assesstimefinish)) {
        if ($info->timecreated < $info->assesstimestart || $info->timecreated > $info->assesstimefinish) {
            throw new rating_exception('notavailable');
        }
    }

    $glossaryid = $info->gid;

    $cm = get_coursemodule_from_instance('glossary', $glossaryid);
    if (empty($cm)) {
        throw new rating_exception('unknowncontext');
    }
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    //if the supplied context doesnt match the item's context
    if (empty($context) || $context->id != $params['context']->id) {
        throw new rating_exception('invalidcontext');
    }

    return true;
}

/**
 * Update activity grades
 *
 * @global object
 * @global object
 * @param object $glossary null means all glossaries (with extra cmidnumber property)
 * @param int $userid specific user only, 0 means all
 */
function glossary_update_grades($glossary=null, $userid=0, $nullifnone=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if (!$glossary->assessed) {
        glossary_grade_item_update($glossary);

    } else if ($grades = glossary_get_user_grades($glossary, $userid)) {
        glossary_grade_item_update($glossary, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        glossary_grade_item_update($glossary, $grade);

    } else {
        glossary_grade_item_update($glossary);
    }
}

/**
 * Update all grades in gradebook.
 *
 * @global object
 */
function glossary_upgrade_grades() {
    global $DB;

    $sql = "SELECT COUNT('x')
              FROM {glossary} g, {course_modules} cm, {modules} m
             WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id";
    $count = $DB->count_records_sql($sql);

    $sql = "SELECT g.*, cm.idnumber AS cmidnumber, g.course AS courseid
              FROM {glossary} g, {course_modules} cm, {modules} m
             WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id";
    $rs = $DB->get_recordset_sql($sql);
    if ($rs->valid()) {
        $pbar = new progress_bar('glossaryupgradegrades', 500, true);
        $i=0;
        foreach ($rs as $glossary) {
            $i++;
            upgrade_set_timeout(60*5); // set up timeout, may also abort execution
            glossary_update_grades($glossary, 0, false);
            $pbar->update($i, $count, "Updating Glossary grades ($i/$count).");
        }
    }
    $rs->close();
}

/**
 * Create/update grade item for given glossary
 *
 * @global object
 * @param object $glossary object with extra cmidnumber
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int, 0 if ok, error code otherwise
 */
function glossary_grade_item_update($glossary, $grades=NULL) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $params = array('itemname'=>$glossary->name, 'idnumber'=>$glossary->cmidnumber);

    if (!$glossary->assessed or $glossary->scale == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;

    } else if ($glossary->scale > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $glossary->scale;
        $params['grademin']  = 0;

    } else if ($glossary->scale < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$glossary->scale;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/glossary', $glossary->course, 'mod', 'glossary', $glossary->id, 0, $grades, $params);
}

/**
 * Delete grade item for given glossary
 *
 * @global object
 * @param object $glossary object
 */
function glossary_grade_item_delete($glossary) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/glossary', $glossary->course, 'mod', 'glossary', $glossary->id, 0, NULL, array('deleted'=>1));
}

/**
 * Returns the users with data in one glossary
 * (users with records in glossary_entries, students)
 *
 * @global object
 * @param int $glossaryid
 * @return array
 */
function glossary_get_participants($glossaryid) {
    global $DB;

    //Get students
    $students = $DB->get_records_sql("SELECT DISTINCT u.id, u.id
                                        FROM {user} u, {glossary_entries} g
                                 WHERE g.glossaryid = ? AND u.id = g.userid", array($glossaryid));

    //Return students array (it contains an array of unique users)
    return $students;
}

/**
 * @global object
 * @param int $gloassryid
 * @param int $scaleid
 * @return bool
 */
function glossary_scale_used ($glossaryid,$scaleid) {
//This function returns if a scale is being used by one glossary
    global $DB;

    $return = false;

    $rec = $DB->get_record("glossary", array("id"=>$glossaryid, "scale"=>-$scaleid));

    if (!empty($rec)  && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of glossary
 *
 * This is used to find out if scale used anywhere
 *
 * @global object
 * @param int $scaleid
 * @return boolean True if the scale is used by any glossary
 */
function glossary_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('glossary', array('scale'=>-$scaleid))) {
        return true;
    } else {
        return false;
    }
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other glossary functions go here.  Each of them must have a name that
/// starts with glossary_

/**
 * This function return an array of valid glossary_formats records
 * Everytime it's called, every existing format is checked, new formats
 * are included if detected and old formats are deleted and any glossary
 * using an invalid format is updated to the default (dictionary).
 *
 * @global object
 * @global object
 * @return array
 */
function glossary_get_available_formats() {
    global $CFG, $DB;

    //Get available formats (plugin) and insert (if necessary) them into glossary_formats
    $formats = get_list_of_plugins('mod/glossary/formats', 'TEMPLATE');
    $pluginformats = array();
    foreach ($formats as $format) {
        //If the format file exists
        if (file_exists($CFG->dirroot.'/mod/glossary/formats/'.$format.'/'.$format.'_format.php')) {
            include_once($CFG->dirroot.'/mod/glossary/formats/'.$format.'/'.$format.'_format.php');
            //If the function exists
            if (function_exists('glossary_show_entry_'.$format)) {
                //Acummulate it as a valid format
                $pluginformats[] = $format;
                //If the format doesn't exist in the table
                if (!$rec = $DB->get_record('glossary_formats', array('name'=>$format))) {
                    //Insert the record in glossary_formats
                    $gf = new stdClass();
                    $gf->name = $format;
                    $gf->popupformatname = $format;
                    $gf->visible = 1;
                    $DB->insert_record("glossary_formats",$gf);
                }
            }
        }
    }

    //Delete non_existent formats from glossary_formats table
    $formats = $DB->get_records("glossary_formats");
    foreach ($formats as $format) {
        $todelete = false;
        //If the format in DB isn't a valid previously detected format then delete the record
        if (!in_array($format->name,$pluginformats)) {
            $todelete = true;
        }

        if ($todelete) {
            //Delete the format
            $DB->delete_records('glossary_formats', array('name'=>$format->name));
            //Reasign existing glossaries to default (dictionary) format
            if ($glossaries = $DB->get_records('glossary', array('displayformat'=>$format->name))) {
                foreach($glossaries as $glossary) {
                    $DB->set_field('glossary','displayformat','dictionary', array('id'=>$glossary->id));
                }
            }
        }
    }

    //Now everything is ready in glossary_formats table
    $formats = $DB->get_records("glossary_formats");

    return $formats;
}

/**
 * @param bool $debug
 * @param string $text
 * @param int $br
 */
function glossary_debug($debug,$text,$br=1) {
    if ( $debug ) {
        echo '<font color="red">' . $text . '</font>';
        if ( $br ) {
            echo '<br />';
        }
    }
}

/**
 *
 * @global object
 * @param int $glossaryid
 * @param string $entrylist
 * @param string $pivot
 * @return array
 */
function glossary_get_entries($glossaryid, $entrylist, $pivot = "") {
    global $DB;
    if ($pivot) {
       $pivot .= ",";
    }

    return $DB->get_records_sql("SELECT $pivot id,userid,concept,definition,format
                                   FROM {glossary_entries}
                                  WHERE glossaryid = ?
                                        AND id IN ($entrylist)", array($glossaryid));
}

/**
 * @global object
 * @global object
 * @param object $concept
 * @param string $courseid
 * @return array
 */
function glossary_get_entries_search($concept, $courseid) {
    global $CFG, $DB;

    //Check if the user is an admin
    $bypassadmin = 1; //This means NO (by default)
    if (has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_SYSTEM))) {
        $bypassadmin = 0; //This means YES
    }

    //Check if the user is a teacher
    $bypassteacher = 1; //This means NO (by default)
    if (has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_COURSE, $courseid))) {
        $bypassteacher = 0; //This means YES
    }

    $conceptlower = moodle_strtolower(trim($concept));

    $params = array('courseid1'=>$courseid, 'courseid2'=>$courseid, 'conceptlower'=>$conceptlower, 'concept'=>$concept);

    return $DB->get_records_sql("SELECT e.*, g.name as glossaryname, cm.id as cmid, cm.course as courseid
                                   FROM {glossary_entries} e, {glossary} g,
                                        {course_modules} cm, {modules} m
                                  WHERE m.name = 'glossary' AND
                                        cm.module = m.id AND
                                        (cm.visible = 1 OR  cm.visible = $bypassadmin OR
                                            (cm.course = :courseid1 AND cm.visible = $bypassteacher)) AND
                                        g.id = cm.instance AND
                                        e.glossaryid = g.id  AND
                                        ( (e.casesensitive != 0 AND LOWER(concept) = :conceptlower) OR
                                          (e.casesensitive = 0 and concept = :concept)) AND
                                        (g.course = :courseid2 OR g.globalglossary = 1) AND
                                         e.usedynalink != 0 AND
                                         g.usedynalink != 0", $params);
}

/**
 * @global object
 * @global object
 * @param object $course
 * @param object $course
 * @param object $glossary
 * @param object $entry
 * @param string $mode
 * @param string $hook
 * @param int $printicons
 * @param int $displayformat
 * @param bool $printview
 * @return mixed
 */
function glossary_print_entry($course, $cm, $glossary, $entry, $mode='',$hook='',$printicons = 1, $displayformat  = -1, $printview = false) {
    global $USER, $CFG;
    $return = false;
    if ( $displayformat < 0 ) {
        $displayformat = $glossary->displayformat;
    }
    if ($entry->approved or ($USER->id == $entry->userid) or ($mode == 'approval' and !$entry->approved) ) {
        $formatfile = $CFG->dirroot.'/mod/glossary/formats/'.$displayformat.'/'.$displayformat.'_format.php';
        if ($printview) {
            $functionname = 'glossary_print_entry_'.$displayformat;
        } else {
            $functionname = 'glossary_show_entry_'.$displayformat;
        }

        if (file_exists($formatfile)) {
            include_once($formatfile);
            if (function_exists($functionname)) {
                $return = $functionname($course, $cm, $glossary, $entry,$mode,$hook,$printicons);
            } else if ($printview) {
                //If the glossary_print_entry_XXXX function doesn't exist, print default (old) print format
                $return = glossary_print_entry_default($entry, $glossary, $cm);
            }
        }
    }
    return $return;
}

/**
 * Default (old) print format used if custom function doesn't exist in format
 *
 * @param object $entry
 * @param object $glossary
 * @param object $cm
 * @return void Output is echo'd
 */
function glossary_print_entry_default ($entry, $glossary, $cm) {
    global $CFG;

    require_once($CFG->libdir . '/filelib.php');

    echo '<h3>'. strip_tags($entry->concept) . ': </h3>';

    $definition = $entry->definition;

    $definition = '<span class="nolink">' . strip_tags($definition) . '</span>';

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $definition = file_rewrite_pluginfile_urls($definition, 'pluginfile.php', $context->id, 'mod_glossary', 'entry', $entry->id);

    $options = new stdClass();
    $options->para = false;
    $options->trusted = $entry->definitiontrust;
    $options->context = $context;
    $options->overflowdiv = true;
    $definition = format_text($definition, $entry->definitionformat, $options);
    echo ($definition);
    echo '<br /><br />';
}

/**
 * Print glossary concept/term as a heading &lt;h3>
 * @param object $entry
 */
function  glossary_print_entry_concept($entry, $return=false) {
    global $OUTPUT;
    $options = new stdClass();
    $options->para = false;
    $text = format_text($OUTPUT->heading('<span class="nolink">' . $entry->concept . '</span>', 3, 'nolink'), FORMAT_MOODLE, $options);
    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }

    if ($return) {
        return $text;
    } else {
        echo $text;
    }
}

/**
 *
 * @global moodle_database DB
 * @param object $entry
 * @param object $glossary
 * @param object $cm
 */
function glossary_print_entry_definition($entry, $glossary, $cm) {
    global $DB, $GLOSSARY_EXCLUDECONCEPTS;

    $definition = $entry->definition;

    //Calculate all the strings to be no-linked
    //First, the concept
    $GLOSSARY_EXCLUDECONCEPTS = array($entry->concept);
    //Now the aliases
    if ( $aliases = $DB->get_records('glossary_alias', array('entryid'=>$entry->id))) {
        foreach ($aliases as $alias) {
            $GLOSSARY_EXCLUDECONCEPTS[]=trim($alias->alias);
        }
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $definition = file_rewrite_pluginfile_urls($definition, 'pluginfile.php', $context->id, 'mod_glossary', 'entry', $entry->id);

    $options = new stdClass();
    $options->para = false;
    $options->trusted = $entry->definitiontrust;
    $options->context = $context;
    $options->overflowdiv = true;
    $text = format_text($definition, $entry->definitionformat, $options);

    // Stop excluding concepts from autolinking
    unset($GLOSSARY_EXCLUDECONCEPTS);

    if (!empty($entry->highlight)) {
        $text = highlight($entry->highlight, $text);
    }
    if (isset($entry->footer)) {   // Unparsed footer info
        $text .= $entry->footer;
    }
    echo $text;
}

/**
 *
 * @global object
 * @param object $course
 * @param object $cm
 * @param object $glossary
 * @param object $entry
 * @param string $mode
 * @param string $hook
 * @param string $type
 * @return string|void
 */
function  glossary_print_entry_aliases($course, $cm, $glossary, $entry,$mode='',$hook='', $type = 'print') {
    global $DB;

    $return = '';
    if ( $aliases = $DB->get_records('glossary_alias', array('entryid'=>$entry->id))) {
        foreach ($aliases as $alias) {
            if (trim($alias->alias)) {
                if ($return == '') {
                    $return = '<select style="font-size:8pt">';
                }
                $return .= "<option>$alias->alias</option>";
            }
        }
        if ($return != '') {
            $return .= '</select>';
        }
    }
    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

/**
 *
 * @global object
 * @global object
 * @global object
 * @param object $course
 * @param object $cm
 * @param object $glossary
 * @param object $entry
 * @param string $mode
 * @param string $hook
 * @param string $type
 * @return string|void
 */
function glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode='',$hook='', $type = 'print') {
    global $USER, $CFG, $DB, $OUTPUT;

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $output = false;   //To decide if we must really return text in "return". Activate when needed only!
    $importedentry = ($entry->sourceglossaryid == $glossary->id);
    $ismainglossary = $glossary->mainglossary;


    $return = '<span class="commands">';
    // Differentiate links for each entry.
    $altsuffix = ': '.strip_tags(format_text($entry->concept));

    if (!$entry->approved) {
        $output = true;
        $return .= get_string('entryishidden','glossary');
    }

    if (has_capability('mod/glossary:manageentries', $context) or (isloggedin() and has_capability('mod/glossary:write', $context) and $entry->userid == $USER->id)) {
        // only teachers can export entries so check it out
        if (has_capability('mod/glossary:export', $context) and !$ismainglossary and !$importedentry) {
            $mainglossary = $DB->get_record('glossary', array('mainglossary'=>1,'course'=>$course->id));
            if ( $mainglossary ) {  // if there is a main glossary defined, allow to export the current entry
                $output = true;
                $return .= ' <a title="'.get_string('exporttomainglossary','glossary') . '" href="exportentry.php?id='.$entry->id.'&amp;prevmode='.$mode.'&amp;hook='.urlencode($hook).'"><img src="'.$OUTPUT->pix_url('export', 'glossary').'" class="iconsmall" alt="'.get_string('exporttomainglossary','glossary').$altsuffix.'" /></a>';
            }
        }

        if ( $entry->sourceglossaryid ) {
            $icon = $OUTPUT->pix_url('minus', 'glossary');   // graphical metaphor (minus) for deleting an imported entry
        } else {
            $icon = $OUTPUT->pix_url('t/delete');
        }

        //Decide if an entry is editable:
        // -It isn't a imported entry (so nobody can edit a imported (from secondary to main) entry)) and
        // -The user is teacher or he is a student with time permissions (edit period or editalways defined).
        $ineditperiod = ((time() - $entry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
        if ( !$importedentry and (has_capability('mod/glossary:manageentries', $context) or ($entry->userid == $USER->id and ($ineditperiod and has_capability('mod/glossary:write', $context))))) {
            $output = true;
            $return .= " <a title=\"" . get_string("delete") . "\" href=\"deleteentry.php?id=$cm->id&amp;mode=delete&amp;entry=$entry->id&amp;prevmode=$mode&amp;hook=".urlencode($hook)."\"><img src=\"";
            $return .= $icon;
            $return .= "\" class=\"iconsmall\" alt=\"" . get_string("delete") .$altsuffix."\" /></a> ";

            $return .= " <a title=\"" . get_string("edit") . "\" href=\"edit.php?cmid=$cm->id&amp;id=$entry->id&amp;mode=$mode&amp;hook=".urlencode($hook)."\"><img src=\"" . $OUTPUT->pix_url('t/edit') . "\" class=\"iconsmall\" alt=\"" . get_string("edit") .$altsuffix. "\" /></a>";
        } elseif ( $importedentry ) {
            $return .= " <font size=\"-1\">" . get_string("exportedentry","glossary") . "</font>";
        }
    }
    if (has_capability('mod/glossary:exportentry', $context)
        || ($entry->userid == $USER->id
            && has_capability('mod/glossary:exportownentry', $context))) {
        require_once($CFG->libdir . '/portfoliolib.php');
        $button = new portfolio_add_button();
        $button->set_callback_options('glossary_entry_portfolio_caller',  array('id' => $cm->id, 'entryid' => $entry->id), '/mod/glossary/locallib.php');

        $filecontext = $context;
        if ($entry->sourceglossaryid == $cm->instance) {
            if ($maincm = get_coursemodule_from_instance('glossary', $entry->glossaryid)) {
                $filecontext = get_context_instance(CONTEXT_MODULE, $maincm->id);
            }
        }
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($filecontext->id, 'mod_glossary', 'attachment', $entry->id, "timemodified", false)) {
            $button->set_formats(PORTFOLIO_FORMAT_RICHHTML);
        } else {
            $button->set_formats(PORTFOLIO_FORMAT_PLAINHTML);
        }

        $return .= $button->to_html(PORTFOLIO_ADD_ICON_LINK);
    }
    $return .= "&nbsp;&nbsp;"; // just to make up a little the output in Mozilla ;)

    $return .= '</span>';

    if (has_capability('mod/glossary:comment', $context) and $glossary->allowcomments) {
        $output = true;
        if (!empty($CFG->usecomments)) {
            require_once($CFG->dirroot . '/comment/lib.php');
            $cmt = new stdClass();
            $cmt->component = 'mod_glossary';
            $cmt->context  = $context;
            $cmt->course   = $course;
            $cmt->cm       = $cm;
            $cmt->area     = 'glossary_entry';
            $cmt->itemid   = $entry->id;
            $cmt->showcount = true;
            $comment = new comment($cmt);
            $return .= '<div>'.$comment->output(true).'</div>';
        }
    }

    //If we haven't calculated any REAL thing, delete result ($return)
    if (!$output) {
        $return = '';
    }
    //Print or get
    if ($type == 'print') {
        echo $return;
    } else {
        return $return;
    }
}

/**
 * @param object $course
 * @param object $cm
 * @param object $glossary
 * @param object $entry
 * @param string $mode
 * @param object $hook
 * @param bool $printicons
 * @param bool $aliases
 * @return void
 */
function  glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, $printicons, $aliases=true) {
    if ($aliases) {
        $aliases = glossary_print_entry_aliases($course, $cm, $glossary, $entry, $mode, $hook,'html');
    }
    $icons   = '';
    if ($printicons) {
        $icons   = glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,'html');
    }
    if ($aliases || $icons || !empty($entry->rating)) {
        echo '<table>';
        if ( $aliases ) {
            echo '<tr valign="top"><td class="aliases">' .
                  get_string('aliases','glossary').': '.$aliases . '</td></tr>';
        }
        if ($icons) {
            echo '<tr valign="top"><td class="icons">'.$icons.'</td></tr>';
        }
        if (!empty($entry->rating)) {
            echo '<tr valign="top"><td class="ratings">';
            glossary_print_entry_ratings($course, $entry);
            echo '</td></tr>';
        }
        echo '</table>';
    }
}

/**
 * @todo Document this function
 */
function glossary_print_entry_attachment($entry, $cm, $format=NULL, $align="right", $insidetable=true) {
///   valid format values: html  : Return the HTML link for the attachment as an icon
///                        text  : Return the HTML link for tha attachment as text
///                        blank : Print the output to the screen
    if ($entry->attachment) {
          if ($insidetable) {
              echo "<table border=\"0\" width=\"100%\" align=\"$align\"><tr><td align=\"$align\" nowrap=\"nowrap\">\n";
          }
          echo glossary_print_attachments($entry, $cm, $format, $align);
          if ($insidetable) {
              echo "</td></tr></table>\n";
          }
    }
}

/**
 * @global object
 * @param object $cm
 * @param object $entry
 * @param string $mode
 * @param string $align
 * @param bool $insidetable
 */
function  glossary_print_entry_approval($cm, $entry, $mode, $align="right", $insidetable=true) {
    global $CFG, $OUTPUT;

    if ($mode == 'approval' and !$entry->approved) {
        if ($insidetable) {
            echo '<table class="glossaryapproval" align="'.$align.'"><tr><td align="'.$align.'">';
        }
        echo '<a title="'.get_string('approve','glossary').'" href="approve.php?eid='.$entry->id.'&amp;mode='.$mode.'&amp;sesskey='.sesskey().'"><img align="'.$align.'" src="'.$OUTPUT->pix_url('i/approve') . '" style="border:0px; width:34px; height:34px" alt="'.get_string('approve','glossary').'" /></a>';
        if ($insidetable) {
            echo '</td></tr></table>';
        }
    }
}

/**
 * It returns all entries from all glossaries that matches the specified criteria
 *  within a given $course. It performs an $extended search if necessary.
 * It restrict the search to only one $glossary if the $glossary parameter is set.
 *
 * @global object
 * @global object
 * @param object $course
 * @param array $searchterms
 * @param int $extended
 * @param object $glossary
 * @return array
 */
function glossary_search($course, $searchterms, $extended = 0, $glossary = NULL) {
    global $CFG, $DB;

    if ( !$glossary ) {
        if ( $glossaries = $DB->get_records("glossary", array("course"=>$course->id)) ) {
            $glos = "";
            foreach ( $glossaries as $glossary ) {
                $glos .= "$glossary->id,";
            }
            $glos = substr($glos,0,-1);
        }
    } else {
        $glos = $glossary->id;
    }

    if (!has_capability('mod/glossary:manageentries', get_context_instance(CONTEXT_COURSE, $glossary->course))) {
        $glossarymodule = $DB->get_record("modules", array("name"=>"glossary"));
        $onlyvisible = " AND g.id = cm.instance AND cm.visible = 1 AND cm.module = $glossarymodule->id";
        $onlyvisibletable = ", {course_modules} cm";
    } else {

        $onlyvisible = "";
        $onlyvisibletable = "";
    }

    if ($DB->sql_regex_supported()) {
        $REGEXP    = $DB->sql_regex(true);
        $NOTREGEXP = $DB->sql_regex(false);
    }

    $searchcond = array();
    $params     = array();
    $i = 0;

    $concat = $DB->sql_concat('e.concept', "' '", 'e.definition');


    foreach ($searchterms as $searchterm) {
        $i++;

        $NOT = false; /// Initially we aren't going to perform NOT LIKE searches, only MSSQL and Oracle
                   /// will use it to simulate the "-" operator with LIKE clause

    /// Under Oracle and MSSQL, trim the + and - operators and perform
    /// simpler LIKE (or NOT LIKE) queries
        if (!$DB->sql_regex_supported()) {
            if (substr($searchterm, 0, 1) == '-') {
                $NOT = true;
            }
            $searchterm = trim($searchterm, '+-');
        }

        // TODO: +- may not work for non latin languages

        if (substr($searchterm,0,1) == '+') {
            $searchterm = trim($searchterm, '+-');
            $searchterm = preg_quote($searchterm, '|');
            $searchcond[] = "$concat $REGEXP :ss$i";
            $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = trim($searchterm, '+-');
            $searchterm = preg_quote($searchterm, '|');
            $searchcond[] = "$concat $NOTREGEXP :ss$i";
            $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

        } else {
            $searchcond[] = $DB->sql_like($concat, ":ss$i", false, true, $NOT);
            $params['ss'.$i] = "%$searchterm%";
        }
    }

    if (empty($searchcond)) {
        $totalcount = 0;
        return array();
    }

    $searchcond = implode(" AND ", $searchcond);

    $sql = "SELECT e.*
              FROM {glossary_entries} e, {glossary} g $onlyvisibletable
             WHERE $searchcond
               AND (e.glossaryid = g.id or e.sourceglossaryid = g.id) $onlyvisible
               AND g.id IN ($glos) AND e.approved <> 0";

    return $DB->get_records_sql($sql, $params);
}

/**
 * @global object
 * @param array $searchterms
 * @param object $glossary
 * @param bool $extended
 * @return array
 */
function glossary_search_entries($searchterms, $glossary, $extended) {
    global $DB;

    $course = $DB->get_record("course", array("id"=>$glossary->course));
    return glossary_search($course,$searchterms,$extended,$glossary);
}

/**
 * if return=html, then return a html string.
 * if return=text, then return a text-only string.
 * otherwise, print HTML for non-images, and return image HTML
 *     if attachment is an image, $align set its aligment.
 *
 * @global object
 * @global object
 * @param object $entry
 * @param object $cm
 * @param string $type html, txt, empty
 * @param string $align left or right
 * @return string image string or nothing depending on $type param
 */
function glossary_print_attachments($entry, $cm, $type=NULL, $align="left") {
    global $CFG, $DB, $OUTPUT;

    if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        return '';
    }

    if ($entry->sourceglossaryid == $cm->instance) {
        if (!$maincm = get_coursemodule_from_instance('glossary', $entry->glossaryid)) {
            return '';
        }
        $filecontext = get_context_instance(CONTEXT_MODULE, $maincm->id);

    } else {
        $filecontext = $context;
    }

    $strattachment = get_string('attachment', 'glossary');

    $fs = get_file_storage();

    $imagereturn = '';
    $output = '';

    if ($files = $fs->get_area_files($filecontext->id, 'mod_glossary', 'attachment', $entry->id, "timemodified", false)) {
        foreach ($files as $file) {
            $filename = $file->get_filename();
            $mimetype = $file->get_mimetype();
            $iconimage = '<img src="'.$OUTPUT->pix_url(file_mimetype_icon($mimetype)).'" class="icon" alt="'.$mimetype.'" />';
            $path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$context->id.'/mod_glossary/attachment/'.$entry->id.'/'.$filename);

            if ($type == 'html') {
                $output .= "<a href=\"$path\">$iconimage</a> ";
                $output .= "<a href=\"$path\">".s($filename)."</a>";
                $output .= "<br />";

            } else if ($type == 'text') {
                $output .= "$strattachment ".s($filename).":\n$path\n";

            } else {
                if (in_array($mimetype, array('image/gif', 'image/jpeg', 'image/png'))) {
                    // Image attachments don't get printed as links
                    $imagereturn .= "<br /><img src=\"$path\" alt=\"\" />";
                } else {
                    $output .= "<a href=\"$path\">$iconimage</a> ";
                    $output .= format_text("<a href=\"$path\">".s($filename)."</a>", FORMAT_HTML, array('context'=>$context));
                    $output .= '<br />';
                }
            }
        }
    }

    if ($type) {
        return $output;
    } else {
        echo $output;
        return $imagereturn;
    }
}

/**
 * Lists all browsable file areas
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @return array
 */
function glossary_get_file_areas($course, $cm, $context) {
    $areas = array();
    return $areas;
}

/**
 * Serves the glossary attachments. Implements needed access control ;-)
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - justsend the file
 */
function glossary_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea === 'attachment' or $filearea === 'entry') {
        $entryid = (int)array_shift($args);

        require_course_login($course, true, $cm);

        if (!$entry = $DB->get_record('glossary_entries', array('id'=>$entryid))) {
            return false;
        }

        if (!$glossary = $DB->get_record('glossary', array('id'=>$cm->instance))) {
            return false;
        }

        if ($glossary->defaultapproval and !$entry->approved and !has_capability('mod/glossary:approve', $context)) {
            return false;
        }

        // this trickery here is because we need to support source glossary access

        if ($entry->glossaryid == $cm->instance) {
            $filecontext = $context;

        } else if ($entry->sourceglossaryid == $cm->instance) {
            if (!$maincm = get_coursemodule_from_instance('glossary', $entry->glossaryid)) {
                return false;
            }
            $filecontext = get_context_instance(CONTEXT_MODULE, $maincm->id);

        } else {
            return false;
        }

        $relativepath = implode('/', $args);
        $fullpath = "/$filecontext->id/mod_glossary/$filearea/$entryid/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // finally send the file
        send_stored_file($file, 0, 0, true); // download MUST be forced - security!

    } else if ($filearea === 'export') {
        require_login($course, false, $cm);
        require_capability('mod/glossary:export', $context);

        if (!$glossary = $DB->get_record('glossary', array('id'=>$cm->instance))) {
            return false;
        }

        $cat = array_shift($args);
        $cat = clean_param($cat, PARAM_ALPHANUM);

        $filename = clean_filename(strip_tags(format_string($glossary->name)).'.xml');
        $content = glossary_generate_export_file($glossary, NULL, $cat);

        send_file($content, $filename, 0, 0, true, true);
    }

    return false;
}

/**
 *
 */
function glossary_print_tabbed_table_end() {
     echo "</div></div>";
}

/**
 * @param object $cm
 * @param object $glossary
 * @param string $mode
 * @param string $hook
 * @param string $sortkey
 * @param string $sortorder
 */
function glossary_print_approval_menu($cm, $glossary,$mode, $hook, $sortkey = '', $sortorder = '') {
    if ($glossary->showalphabet) {
        echo '<div class="glossaryexplain">' . get_string("explainalphabet","glossary") . '</div><br />';
    }
    glossary_print_special_links($cm, $glossary, $mode, $hook);

    glossary_print_alphabet_links($cm, $glossary, $mode, $hook,$sortkey, $sortorder);

    glossary_print_all_links($cm, $glossary, $mode, $hook);

    glossary_print_sorting_links($cm, $mode, 'CREATION', 'asc');
}
/**
 * @param object $cm
 * @param object $glossary
 * @param string $hook
 * @param string $sortkey
 * @param string $sortorder
 */
function glossary_print_import_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    echo '<div class="glossaryexplain">' . get_string("explainimport","glossary") . '</div>';
}

/**
 * @param object $cm
 * @param object $glossary
 * @param string $hook
 * @param string $sortkey
 * @param string $sortorder
 */
function glossary_print_export_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    echo '<div class="glossaryexplain">' . get_string("explainexport","glossary") . '</div>';
}
/**
 * @param object $cm
 * @param object $glossary
 * @param string $hook
 * @param string $sortkey
 * @param string $sortorder
 */
function glossary_print_alphabet_menu($cm, $glossary, $mode, $hook, $sortkey='', $sortorder = '') {
    if ( $mode != 'date' ) {
        if ($glossary->showalphabet) {
            echo '<div class="glossaryexplain">' . get_string("explainalphabet","glossary") . '</div><br />';
        }

        glossary_print_special_links($cm, $glossary, $mode, $hook);

        glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder);

        glossary_print_all_links($cm, $glossary, $mode, $hook);
    } else {
        glossary_print_sorting_links($cm, $mode, $sortkey,$sortorder);
    }
}

/**
 * @param object $cm
 * @param object $glossary
 * @param string $hook
 * @param string $sortkey
 * @param string $sortorder
 */
function glossary_print_author_menu($cm, $glossary,$mode, $hook, $sortkey = '', $sortorder = '') {
    if ($glossary->showalphabet) {
        echo '<div class="glossaryexplain">' . get_string("explainalphabet","glossary") . '</div><br />';
    }

    glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder);
    glossary_print_all_links($cm, $glossary, $mode, $hook);
    glossary_print_sorting_links($cm, $mode, $sortkey,$sortorder);
}

/**
 * @global object
 * @global object
 * @param object $cm
 * @param object $glossary
 * @param string $hook
 * @param object $category
 */
function glossary_print_categories_menu($cm, $glossary, $hook, $category) {
     global $CFG, $DB, $OUTPUT;

     $context = get_context_instance(CONTEXT_MODULE, $cm->id);

     echo '<table border="0" width="100%">';
     echo '<tr>';

     echo '<td align="center" style="width:20%">';
     if (has_capability('mod/glossary:managecategories', $context)) {
             $options['id'] = $cm->id;
             $options['mode'] = 'cat';
             $options['hook'] = $hook;
             echo $OUTPUT->single_button(new moodle_url("editcategories.php", $options), get_string("editcategories","glossary"), "get");
     }
     echo '</td>';

     echo '<td align="center" style="width:60%">';
     echo '<b>';

     $menu = array();
     $menu[GLOSSARY_SHOW_ALL_CATEGORIES] = get_string("allcategories","glossary");
     $menu[GLOSSARY_SHOW_NOT_CATEGORISED] = get_string("notcategorised","glossary");

     $categories = $DB->get_records("glossary_categories", array("glossaryid"=>$glossary->id), "name ASC");
     $selected = '';
     if ( $categories ) {
          foreach ($categories as $currentcategory) {
                 $url = $currentcategory->id;
                 if ( $category ) {
                     if ($currentcategory->id == $category->id) {
                         $selected = $url;
                     }
                 }
                 $menu[$url] = clean_text($currentcategory->name); //Only clean, not filters
          }
     }
     if ( !$selected ) {
         $selected = GLOSSARY_SHOW_NOT_CATEGORISED;
     }

     if ( $category ) {
        echo format_text($category->name, FORMAT_PLAIN);
     } else {
        if ( $hook == GLOSSARY_SHOW_NOT_CATEGORISED ) {

            echo get_string("entrieswithoutcategory","glossary");
            $selected = GLOSSARY_SHOW_NOT_CATEGORISED;

        } elseif ( $hook == GLOSSARY_SHOW_ALL_CATEGORIES ) {

            echo get_string("allcategories","glossary");
            $selected = GLOSSARY_SHOW_ALL_CATEGORIES;

        }
     }
     echo '</b></td>';
     echo '<td align="center" style="width:20%">';

     $select = new single_select(new moodle_url("/mod/glossary/view.php", array('id'=>$cm->id, 'mode'=>'cat')), 'hook', $menu, $selected, null, "catmenu");
     echo $OUTPUT->render($select);

     echo '</td>';
     echo '</tr>';

     echo '</table>';
}

/**
 * @global object
 * @param object $cm
 * @param object $glossary
 * @param string $mode
 * @param string $hook
 */
function glossary_print_all_links($cm, $glossary, $mode, $hook) {
global $CFG;
     if ( $glossary->showall) {
         $strallentries       = get_string("allentries", "glossary");
         if ( $hook == 'ALL' ) {
              echo "<b>$strallentries</b>";
         } else {
              $strexplainall = strip_tags(get_string("explainall","glossary"));
              echo "<a title=\"$strexplainall\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=$mode&amp;hook=ALL\">$strallentries</a>";
         }
     }
}

/**
 * @global object
 * @param object $cm
 * @param object $glossary
 * @param string $mode
 * @param string $hook
 */
function glossary_print_special_links($cm, $glossary, $mode, $hook) {
global $CFG;
     if ( $glossary->showspecial) {
         $strspecial          = get_string("special", "glossary");
         if ( $hook == 'SPECIAL' ) {
              echo "<b>$strspecial</b> | ";
         } else {
              $strexplainspecial = strip_tags(get_string("explainspecial","glossary"));
              echo "<a title=\"$strexplainspecial\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=$mode&amp;hook=SPECIAL\">$strspecial</a> | ";
         }
     }
}

/**
 * @global object
 * @param object $glossary
 * @param string $mode
 * @param string $hook
 * @param string $sortkey
 * @param string $sortorder
 */
function glossary_print_alphabet_links($cm, $glossary, $mode, $hook, $sortkey, $sortorder) {
global $CFG;
     if ( $glossary->showalphabet) {
          $alphabet = explode(",", get_string('alphabet', 'langconfig'));
          for ($i = 0; $i < count($alphabet); $i++) {
              if ( $hook == $alphabet[$i] and $hook) {
                   echo "<b>$alphabet[$i]</b>";
              } else {
                   echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;mode=$mode&amp;hook=".urlencode($alphabet[$i])."&amp;sortkey=$sortkey&amp;sortorder=$sortorder\">$alphabet[$i]</a>";
              }
              echo ' | ';
          }
     }
}

/**
 * @global object
 * @param object $cm
 * @param string $mode
 * @param string $sortkey
 * @param string $sortorder
 */
function glossary_print_sorting_links($cm, $mode, $sortkey = '',$sortorder = '') {
    global $CFG, $OUTPUT;

    $asc    = get_string("ascending","glossary");
    $desc   = get_string("descending","glossary");
    $bopen  = '<b>';
    $bclose = '</b>';

     $neworder = '';
     $currentorder = '';
     $currentsort = '';
     if ( $sortorder ) {
         if ( $sortorder == 'asc' ) {
             $currentorder = $asc;
             $neworder = '&amp;sortorder=desc';
             $newordertitle = get_string('changeto', 'glossary', $desc);
         } else {
             $currentorder = $desc;
             $neworder = '&amp;sortorder=asc';
             $newordertitle = get_string('changeto', 'glossary', $asc);
         }
         $icon = " <img src=\"".$OUTPUT->pix_url($sortorder, 'glossary')."\" class=\"icon\" alt=\"$newordertitle\" />";
     } else {
         if ( $sortkey != 'CREATION' and $sortkey != 'UPDATE' and
               $sortkey != 'FIRSTNAME' and $sortkey != 'LASTNAME' ) {
             $icon = "";
             $newordertitle = $asc;
         } else {
             $newordertitle = $desc;
             $neworder = '&amp;sortorder=desc';
             $icon = ' <img src="'.$OUTPUT->pix_url('asc', 'glossary').'" class="icon" alt="'.$newordertitle.'" />';
         }
     }
     $ficon     = '';
     $fneworder = '';
     $fbtag     = '';
     $fendbtag  = '';

     $sicon     = '';
     $sneworder = '';

     $sbtag      = '';
     $fbtag      = '';
     $fendbtag      = '';
     $sendbtag      = '';

     $sendbtag  = '';

     if ( $sortkey == 'CREATION' or $sortkey == 'FIRSTNAME' ) {
         $ficon       = $icon;
         $fneworder   = $neworder;
         $fordertitle = $newordertitle;
         $sordertitle = $asc;
         $fbtag       = $bopen;
         $fendbtag    = $bclose;
     } elseif ($sortkey == 'UPDATE' or $sortkey == 'LASTNAME') {
         $sicon = $icon;
         $sneworder   = $neworder;
         $fordertitle = $asc;
         $sordertitle = $newordertitle;
         $sbtag       = $bopen;
         $sendbtag    = $bclose;
     } else {
         $fordertitle = $asc;
         $sordertitle = $asc;
     }

     if ( $sortkey == 'CREATION' or $sortkey == 'UPDATE' ) {
         $forder = 'CREATION';
         $sorder =  'UPDATE';
         $fsort  = get_string("sortbycreation", "glossary");
         $ssort  = get_string("sortbylastupdate", "glossary");

         $currentsort = $fsort;
         if ($sortkey == 'UPDATE') {
             $currentsort = $ssort;
         }
         $sort        = get_string("sortchronogically", "glossary");
     } elseif ( $sortkey == 'FIRSTNAME' or $sortkey == 'LASTNAME') {
         $forder = 'FIRSTNAME';
         $sorder =  'LASTNAME';
         $fsort  = get_string("firstname");
         $ssort  = get_string("lastname");

         $currentsort = $fsort;
         if ($sortkey == 'LASTNAME') {
             $currentsort = $ssort;
         }
         $sort        = get_string("sortby", "glossary");
     }
     $current = '<span class="accesshide">'.get_string('current', 'glossary', "$currentsort $currentorder").'</span>';
     echo "<br />$current $sort: $sbtag<a title=\"$ssort $sordertitle\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;sortkey=$sorder$sneworder&amp;mode=$mode\">$ssort$sicon</a>$sendbtag | ".
                          "$fbtag<a title=\"$fsort $fordertitle\" href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&amp;sortkey=$forder$fneworder&amp;mode=$mode\">$fsort$ficon</a>$fendbtag<br />";
}

/**
 *
 * @param object $entry0
 * @param object $entry1
 * @return int [-1 | 0 | 1]
 */
function glossary_sort_entries ( $entry0, $entry1 ) {

    if ( moodle_strtolower(ltrim($entry0->concept)) < moodle_strtolower(ltrim($entry1->concept)) ) {
        return -1;
    } elseif ( moodle_strtolower(ltrim($entry0->concept)) > moodle_strtolower(ltrim($entry1->concept)) ) {
        return 1;
    } else {
        return 0;
    }
}


/**
 * @global object
 * @global object
 * @global object
 * @param object $course
 * @param object $entry
 * @return bool
 */
function  glossary_print_entry_ratings($course, $entry) {
    global $OUTPUT;
    if( !empty($entry->rating) ){
        echo $OUTPUT->render($entry->rating);
    }
}

/**
 *
 * @global object
 * @global object
 * @global object
 * @param int $courseid
 * @param array $entries
 * @param int $displayformat
 */
function glossary_print_dynaentry($courseid, $entries, $displayformat = -1) {
    global $USER,$CFG, $DB;

    echo '<div class="boxaligncenter">';
    echo '<table class="glossarypopup" cellspacing="0"><tr>';
    echo '<td>';
    if ( $entries ) {
        foreach ( $entries as $entry ) {
            if (! $glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid))) {
                print_error('invalidid', 'glossary');
            }
            if (! $course = $DB->get_record('course', array('id'=>$glossary->course))) {
                print_error('coursemisconf');
            }
            if (!$cm = get_coursemodule_from_instance('glossary', $entry->glossaryid, $glossary->course) ) {
                print_error('invalidid', 'glossary');
            }

            //If displayformat is present, override glossary->displayformat
            if ($displayformat < 0) {
                $dp = $glossary->displayformat;
            } else {
                $dp = $displayformat;
            }

            //Get popupformatname
            $format = $DB->get_record('glossary_formats', array('name'=>$dp));
            $displayformat = $format->popupformatname;

            //Check displayformat variable and set to default if necessary
            if (!$displayformat) {
                $displayformat = 'dictionary';
            }

            $formatfile = $CFG->dirroot.'/mod/glossary/formats/'.$displayformat.'/'.$displayformat.'_format.php';
            $functionname = 'glossary_show_entry_'.$displayformat;

            if (file_exists($formatfile)) {
                include_once($formatfile);
                if (function_exists($functionname)) {
                    $functionname($course, $cm, $glossary, $entry,'','','','');
                }
            }
        }
    }
    echo '</td>';
    echo '</tr></table></div>';
}

/**
 *
 * @global object
 * @param array $entries
 * @param array $aliases
 * @param array $categories
 * @return string
 */
function glossary_generate_export_csv($entries, $aliases, $categories) {
    global $CFG;
    $csv = '';
    $delimiter = '';
    require_once($CFG->libdir . '/csvlib.class.php');
    $delimiter = csv_import_reader::get_delimiter('comma');
    $csventries = array(0 => array(get_string('concept', 'glossary'), get_string('definition', 'glossary')));
    $csvaliases = array(0 => array());
    $csvcategories = array(0 => array());
    $aliascount = 0;
    $categorycount = 0;

    foreach ($entries as $entry) {
        $thisaliasesentry = array();
        $thiscategoriesentry = array();
        $thiscsventry = array($entry->concept, nl2br($entry->definition));

        if (array_key_exists($entry->id, $aliases) && is_array($aliases[$entry->id])) {
            $thiscount = count($aliases[$entry->id]);
            if ($thiscount > $aliascount) {
                $aliascount = $thiscount;
            }
            foreach ($aliases[$entry->id] as $alias) {
                $thisaliasesentry[] = trim($alias);
            }
        }
        if (array_key_exists($entry->id, $categories) && is_array($categories[$entry->id])) {
            $thiscount = count($categories[$entry->id]);
            if ($thiscount > $categorycount) {
                $categorycount = $thiscount;
            }
            foreach ($categories[$entry->id] as $catentry) {
                $thiscategoriesentry[] = trim($catentry);
            }
        }
        $csventries[$entry->id] = $thiscsventry;
        $csvaliases[$entry->id] = $thisaliasesentry;
        $csvcategories[$entry->id] = $thiscategoriesentry;

    }
    $returnstr = '';
    foreach ($csventries as $id => $row) {
        $aliasstr = '';
        $categorystr = '';
        if ($id == 0) {
            $aliasstr = get_string('alias', 'glossary');
            $categorystr = get_string('category', 'glossary');
        }
        $row = array_merge($row, array_pad($csvaliases[$id], $aliascount, $aliasstr), array_pad($csvcategories[$id], $categorycount, $categorystr));
        $returnstr .= '"' . implode('"' . $delimiter . '"', $row) . '"' . "\n";
    }
    return $returnstr;
}

/**
 *
 * @param object $glossary
 * @param string $ignored invalid parameter
 * @param int|string $hook
 * @return string
 */
function glossary_generate_export_file($glossary, $ignored = "", $hook = 0) {
    global $CFG, $DB;

    $co  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

    $co .= glossary_start_tag("GLOSSARY",0,true);
    $co .= glossary_start_tag("INFO",1,true);
        $co .= glossary_full_tag("NAME",2,false,$glossary->name);
        $co .= glossary_full_tag("INTRO",2,false,$glossary->intro);
        $co .= glossary_full_tag("INTROFORMAT",2,false,$glossary->introformat);
        $co .= glossary_full_tag("ALLOWDUPLICATEDENTRIES",2,false,$glossary->allowduplicatedentries);
        $co .= glossary_full_tag("DISPLAYFORMAT",2,false,$glossary->displayformat);
        $co .= glossary_full_tag("SHOWSPECIAL",2,false,$glossary->showspecial);
        $co .= glossary_full_tag("SHOWALPHABET",2,false,$glossary->showalphabet);
        $co .= glossary_full_tag("SHOWALL",2,false,$glossary->showall);
        $co .= glossary_full_tag("ALLOWCOMMENTS",2,false,$glossary->allowcomments);
        $co .= glossary_full_tag("USEDYNALINK",2,false,$glossary->usedynalink);
        $co .= glossary_full_tag("DEFAULTAPPROVAL",2,false,$glossary->defaultapproval);
        $co .= glossary_full_tag("GLOBALGLOSSARY",2,false,$glossary->globalglossary);
        $co .= glossary_full_tag("ENTBYPAGE",2,false,$glossary->entbypage);

        if ( $entries = $DB->get_records("glossary_entries", array("glossaryid"=>$glossary->id))) {
            $co .= glossary_start_tag("ENTRIES",2,true);
            foreach ($entries as $entry) {
                $permissiongranted = 1;
                if ( $hook ) {
                    switch ( $hook ) {
                    case "ALL":
                    case "SPECIAL":
                    break;
                    default:
                        $permissiongranted = ($entry->concept[ strlen($hook)-1 ] == $hook);
                    break;
                    }
                }
                if ( $hook ) {
                    switch ( $hook ) {
                    case GLOSSARY_SHOW_ALL_CATEGORIES:
                    break;
                    case GLOSSARY_SHOW_NOT_CATEGORISED:
                        $permissiongranted = !$DB->record_exists("glossary_entries_categories", array("entryid"=>$entry->id));
                    break;
                    default:
                        $permissiongranted = $DB->record_exists("glossary_entries_categories", array("entryid"=>$entry->id, "categoryid"=>$hook));
                    break;
                    }
                }
                if ( $entry->approved and $permissiongranted ) {
                    $co .= glossary_start_tag("ENTRY",3,true);
                    $co .= glossary_full_tag("CONCEPT",4,false,trim($entry->concept));
                    $co .= glossary_full_tag("DEFINITION",4,false,$entry->definition);
                    $co .= glossary_full_tag("FORMAT",4,false,$entry->definitionformat); // note: use old name for BC reasons
                    $co .= glossary_full_tag("USEDYNALINK",4,false,$entry->usedynalink);
                    $co .= glossary_full_tag("CASESENSITIVE",4,false,$entry->casesensitive);
                    $co .= glossary_full_tag("FULLMATCH",4,false,$entry->fullmatch);
                    $co .= glossary_full_tag("TEACHERENTRY",4,false,$entry->teacherentry);

                    if ( $aliases = $DB->get_records("glossary_alias", array("entryid"=>$entry->id))) {
                        $co .= glossary_start_tag("ALIASES",4,true);
                        foreach ($aliases as $alias) {
                            $co .= glossary_start_tag("ALIAS",5,true);
                                $co .= glossary_full_tag("NAME",6,false,trim($alias->alias));
                            $co .= glossary_end_tag("ALIAS",5,true);
                        }
                        $co .= glossary_end_tag("ALIASES",4,true);
                    }
                    if ( $catentries = $DB->get_records("glossary_entries_categories", array("entryid"=>$entry->id))) {
                        $co .= glossary_start_tag("CATEGORIES",4,true);
                        foreach ($catentries as $catentry) {
                            $category = $DB->get_record("glossary_categories", array("id"=>$catentry->categoryid));

                            $co .= glossary_start_tag("CATEGORY",5,true);
                                $co .= glossary_full_tag("NAME",6,false,$category->name);
                                $co .= glossary_full_tag("USEDYNALINK",6,false,$category->usedynalink);
                            $co .= glossary_end_tag("CATEGORY",5,true);
                        }
                        $co .= glossary_end_tag("CATEGORIES",4,true);
                    }

                    $co .= glossary_end_tag("ENTRY",3,true);
                }
            }
            $co .= glossary_end_tag("ENTRIES",2,true);

        }


    $co .= glossary_end_tag("INFO",1,true);
    $co .= glossary_end_tag("GLOSSARY",0,true);

    return $co;
}
/// Functions designed by Eloy Lafuente
/// Functions to create, open and write header of the xml file

/**
 * Read import file and convert to current charset
 *
 * @global object
 * @param string $file
 * @return string
 */
function glossary_read_imported_file($file_content) {
    require_once "../../lib/xmlize.php";
    global $CFG;

    return xmlize($file_content, 0);
}

/**
 * Return the xml start tag
 *
 * @param string $tag
 * @param int $level
 * @param bool $endline
 * @return string
 */
function glossary_start_tag($tag,$level=0,$endline=false) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."<".strtoupper($tag).">".$endchar;
}

/**
 * Return the xml end tag
 * @param string $tag
 * @param int $level
 * @param bool $endline
 * @return string
 */
function glossary_end_tag($tag,$level=0,$endline=true) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."</".strtoupper($tag).">".$endchar;
}

/**
 * Return the start tag, the contents and the end tag
 *
 * @global object
 * @param string $tag
 * @param int $level
 * @param bool $endline
 * @param string $content
 * @return string
 */
function glossary_full_tag($tag,$level=0,$endline=true,$content) {
        global $CFG;

        $st = glossary_start_tag($tag,$level,$endline);
        $co = preg_replace("/\r\n|\r/", "\n", s($content));
        $et = glossary_end_tag($tag,0,true);
        return $st.$co.$et;
}

/**
 * How many unrated entries are in the given glossary for a given user?
 *
 * @global object
 * @param int $glossaryid
 * @param int $userid
 * @return int
 */
function glossary_count_unrated_entries($glossaryid, $userid) {
    global $DB;
    if ($entries = $DB->get_record_sql("SELECT count('x') as num
                                          FROM {glossary_entries}
                                         WHERE glossaryid = ? AND userid <> ?", array($glossaryid, $userid))) {

        if (!$cm = get_coursemodule_from_instance('glossary', $glossaryid)) {
            return 0;
        }
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        if ($rated = $DB->get_record_sql("SELECT count(*) as num
                                            FROM {glossary_entries} e, {ratings} r
                                           WHERE e.glossaryid = :glossaryid AND e.id = r.itemid
                                                 AND r.userid = :userid and r.contextid = :contextid",
                array('glossaryid'=>$glossaryid, 'userid'=>$userid, 'contextid'=>$context->id))) {

            $difference = $entries->num - $rated->num;
            if ($difference > 0) {
                return $difference;
            } else {
                return 0;    // Just in case there was a counting error
            }
        } else {
            return $entries->num;
        }
    } else {
        return 0;
    }
}

/**
 *
 * Returns the html code to represent any pagging bar. Paramenters are:
 *
 * The function dinamically show the first and last pages, and "scroll" over pages.
 * Fully compatible with Moodle's print_paging_bar() function. Perhaps some day this
 * could replace the general one. ;-)
 *
 * @param int $totalcount total number of records to be displayed
 * @param int $page page currently selected (0 based)
 * @param int $perpage number of records per page
 * @param string $baseurl url to link in each page, the string 'page=XX' will be added automatically.
 *
 * @param int $maxpageallowed Optional maximum number of page allowed.
 * @param int $maxdisplay Optional maximum number of page links to show in the bar
 * @param string $separator Optional string to be used between pages in the bar
 * @param string $specialtext Optional string to be showed as an special link
 * @param string $specialvalue Optional value (page) to be used in the special link
 * @param bool $previousandnext Optional to decide if we want the previous and next links
 * @return string
 */
function glossary_get_paging_bar($totalcount, $page, $perpage, $baseurl, $maxpageallowed=99999, $maxdisplay=20, $separator="&nbsp;", $specialtext="", $specialvalue=-1, $previousandnext = true) {

    $code = '';

    $showspecial = false;
    $specialselected = false;

    //Check if we have to show the special link
    if (!empty($specialtext)) {
        $showspecial = true;
    }
    //Check if we are with the special link selected
    if ($showspecial && $page == $specialvalue) {
        $specialselected = true;
    }

    //If there are results (more than 1 page)
    if ($totalcount > $perpage) {
        $code .= "<div style=\"text-align:center\">";
        $code .= "<p>".get_string("page").":";

        $maxpage = (int)(($totalcount-1)/$perpage);

        //Lower and upper limit of page
        if ($page < 0) {
            $page = 0;
        }
        if ($page > $maxpageallowed) {
            $page = $maxpageallowed;
        }
        if ($page > $maxpage) {
            $page = $maxpage;
        }

        //Calculate the window of pages
        $pagefrom = $page - ((int)($maxdisplay / 2));
        if ($pagefrom < 0) {
            $pagefrom = 0;
        }
        $pageto = $pagefrom + $maxdisplay - 1;
        if ($pageto > $maxpageallowed) {
            $pageto = $maxpageallowed;
        }
        if ($pageto > $maxpage) {
            $pageto = $maxpage;
        }

        //Some movements can be necessary if don't see enought pages
        if ($pageto - $pagefrom < $maxdisplay - 1) {
            if ($pageto - $maxdisplay + 1 > 0) {
                $pagefrom = $pageto - $maxdisplay + 1;
            }
        }

        //Calculate first and last if necessary
        $firstpagecode = '';
        $lastpagecode = '';
        if ($pagefrom > 0) {
            $firstpagecode = "$separator<a href=\"{$baseurl}page=0\">1</a>";
            if ($pagefrom > 1) {
                $firstpagecode .= "$separator...";
            }
        }
        if ($pageto < $maxpage) {
            if ($pageto < $maxpage -1) {
                $lastpagecode = "$separator...";
            }
            $lastpagecode .= "$separator<a href=\"{$baseurl}page=$maxpage\">".($maxpage+1)."</a>";
        }

        //Previous
        if ($page > 0 && $previousandnext) {
            $pagenum = $page - 1;
            $code .= "&nbsp;(<a  href=\"{$baseurl}page=$pagenum\">".get_string("previous")."</a>)&nbsp;";
        }

        //Add first
        $code .= $firstpagecode;

        $pagenum = $pagefrom;

        //List of maxdisplay pages
        while ($pagenum <= $pageto) {
            $pagetoshow = $pagenum +1;
            if ($pagenum == $page && !$specialselected) {
                $code .= "$separator<b>$pagetoshow</b>";
            } else {
                $code .= "$separator<a href=\"{$baseurl}page=$pagenum\">$pagetoshow</a>";
            }
            $pagenum++;
        }

        //Add last
        $code .= $lastpagecode;

        //Next
        if ($page < $maxpage && $page < $maxpageallowed && $previousandnext) {
            $pagenum = $page + 1;
            $code .= "$separator(<a href=\"{$baseurl}page=$pagenum\">".get_string("next")."</a>)";
        }

        //Add special
        if ($showspecial) {
            $code .= '<br />';
            if ($specialselected) {
                $code .= "<b>$specialtext</b>";
            } else {
                $code .= "$separator<a href=\"{$baseurl}page=$specialvalue\">$specialtext</a>";
            }
        }

        //End html
        $code .= "</p>";
        $code .= "</div>";
    }

    return $code;
}
/**
 * @return array
 */
function glossary_get_view_actions() {
    return array('view','view all','view entry');
}
/**
 * @return array
 */
function glossary_get_post_actions() {
    return array('add category','add entry','approve entry','delete category','delete entry','edit category','update entry');
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the glossary.
 * @param object $mform form passed by reference
 */
function glossary_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'glossaryheader', get_string('modulenameplural', 'glossary'));
    $mform->addElement('checkbox', 'reset_glossary_all', get_string('resetglossariesall','glossary'));

    $mform->addElement('select', 'reset_glossary_types', get_string('resetglossaries', 'glossary'),
                       array('main'=>get_string('mainglossary', 'glossary'), 'secondary'=>get_string('secondaryglossary', 'glossary')), array('multiple' => 'multiple'));
    $mform->setAdvanced('reset_glossary_types');
    $mform->disabledIf('reset_glossary_types', 'reset_glossary_all', 'checked');

    $mform->addElement('checkbox', 'reset_glossary_notenrolled', get_string('deletenotenrolled', 'glossary'));
    $mform->disabledIf('reset_glossary_notenrolled', 'reset_glossary_all', 'checked');

    $mform->addElement('checkbox', 'reset_glossary_ratings', get_string('deleteallratings'));
    $mform->disabledIf('reset_glossary_ratings', 'reset_glossary_all', 'checked');

    $mform->addElement('checkbox', 'reset_glossary_comments', get_string('deleteallcomments'));
    $mform->disabledIf('reset_glossary_comments', 'reset_glossary_all', 'checked');
}

/**
 * Course reset form defaults.
 * @return array
 */
function glossary_reset_course_form_defaults($course) {
    return array('reset_glossary_all'=>0, 'reset_glossary_ratings'=>1, 'reset_glossary_comments'=>1, 'reset_glossary_notenrolled'=>0);
}

/**
 * Removes all grades from gradebook
 *
 * @global object
 * @param int $courseid
 * @param string optional type
 */
function glossary_reset_gradebook($courseid, $type='') {
    global $DB;

    switch ($type) {
        case 'main'      : $type = "AND g.mainglossary=1"; break;
        case 'secondary' : $type = "AND g.mainglossary=0"; break;
        default          : $type = ""; //all
    }

    $sql = "SELECT g.*, cm.idnumber as cmidnumber, g.course as courseid
              FROM {glossary} g, {course_modules} cm, {modules} m
             WHERE m.name='glossary' AND m.id=cm.module AND cm.instance=g.id AND g.course=? $type";

    if ($glossarys = $DB->get_records_sql($sql, array($courseid))) {
        foreach ($glossarys as $glossary) {
            glossary_grade_item_update($glossary, 'reset');
        }
    }
}
/**
 * Actual implementation of the reset course functionality, delete all the
 * glossary responses for course $data->courseid.
 *
 * @global object
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function glossary_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->dirroot.'/rating/lib.php');

    $componentstr = get_string('modulenameplural', 'glossary');
    $status = array();

    $allentriessql = "SELECT e.id
                        FROM {glossary_entries} e
                             JOIN {glossary} g ON e.glossaryid = g.id
                       WHERE g.course = ?";

    $allglossariessql = "SELECT g.id
                           FROM {glossary} g
                          WHERE g.course = ?";

    $params = array($data->courseid);

    $fs = get_file_storage();

    $rm = new rating_manager();
    $ratingdeloptions = new stdclass();

    // delete entries if requested
    if (!empty($data->reset_glossary_all)
         or (!empty($data->reset_glossary_types) and in_array('main', $data->reset_glossary_types) and in_array('secondary', $data->reset_glossary_types))) {

        $params[] = 'glossary_entry';
        $DB->delete_records_select('comments', "itemid IN ($allentriessql) AND commentarea=?", $params);
        $DB->delete_records_select('glossary_alias',    "entryid IN ($allentriessql)", $params);
        $DB->delete_records_select('glossary_entries', "glossaryid IN ($allglossariessql)", $params);

        // now get rid of all attachments
        if ($glossaries = $DB->get_records_sql($allglossariessql, $params)) {
            foreach ($glossaries as $glossaryid=>$unused) {
                if (!$cm = get_coursemodule_from_instance('glossary', $glossaryid)) {
                    continue;
                }
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                $fs->delete_area_files($context->id, 'mod_glossary', 'attachment');

                //delete ratings
                $ratingdeloptions->contextid = $context->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            glossary_reset_gradebook($data->courseid);
        }

        $status[] = array('component'=>$componentstr, 'item'=>get_string('resetglossariesall', 'glossary'), 'error'=>false);

    } else if (!empty($data->reset_glossary_types)) {
        $mainentriessql         = "$allentries AND g.mainglossary=1";
        $secondaryentriessql    = "$allentries AND g.mainglossary=0";

        $mainglossariessql      = "$allglossariessql AND g.mainglossary=1";
        $secondaryglossariessql = "$allglossariessql AND g.mainglossary=0";

        if (in_array('main', $data->reset_glossary_types)) {
            $params[] = 'glossary_entry';
            $DB->delete_records_select('comments', "itemid IN ($mainentriessql) AND commentarea=?", $params);
            $DB->delete_records_select('glossary_entries', "glossaryid IN ($mainglossariessql)", $params);

            if ($glossaries = $DB->get_records_sql($mainglossariessql, $params)) {
                foreach ($glossaries as $glossaryid=>$unused) {
                    if (!$cm = get_coursemodule_from_instance('glossary', $glossaryid)) {
                        continue;
                    }
                    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                    $fs->delete_area_files($context->id, 'mod_glossary', 'attachment');

                    //delete ratings
                    $ratingdeloptions->contextid = $context->id;
                    $rm->delete_ratings($ratingdeloptions);
                }
            }

            // remove all grades from gradebook
            if (empty($data->reset_gradebook_grades)) {
                glossary_reset_gradebook($data->courseid, 'main');
            }

            $status[] = array('component'=>$componentstr, 'item'=>get_string('resetglossaries', 'glossary'), 'error'=>false);

        } else if (in_array('secondary', $data->reset_glossary_types)) {
            $params[] = 'glossary_entry';
            $DB->delete_records_select('comments', "itemid IN ($secondaryentriessql) AND commentarea=?", $params);
            $DB->delete_records_select('glossary_entries', "glossaryid IN ($secondaryglossariessql)", $params);
            // remove exported source flag from entries in main glossary
            $DB->execute("UPDATE {glossary_entries
                             SET sourceglossaryid=0
                           WHERE glossaryid IN ($mainglossariessql)", $params);

            if ($glossaries = $DB->get_records_sql($secondaryglossariessql, $params)) {
                foreach ($glossaries as $glossaryid=>$unused) {
                    if (!$cm = get_coursemodule_from_instance('glossary', $glossaryid)) {
                        continue;
                    }
                    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                    $fs->delete_area_files($context->id, 'mod_glossary', 'attachment');

                    //delete ratings
                    $ratingdeloptions->contextid = $context->id;
                    $rm->delete_ratings($ratingdeloptions);
                }
            }

            // remove all grades from gradebook
            if (empty($data->reset_gradebook_grades)) {
                glossary_reset_gradebook($data->courseid, 'secondary');
            }

            $status[] = array('component'=>$componentstr, 'item'=>get_string('resetglossaries', 'glossary').': '.get_string('secondaryglossary', 'glossary'), 'error'=>false);
        }
    }

    // remove entries by users not enrolled into course
    if (!empty($data->reset_glossary_notenrolled)) {
        $entriessql = "SELECT e.id, e.userid, e.glossaryid, u.id AS userexists, u.deleted AS userdeleted
                         FROM {glossary_entries} e
                              JOIN {glossary} g ON e.glossaryid = g.id
                              LEFT JOIN {user} u ON e.userid = u.id
                        WHERE g.course = ? AND e.userid > 0";

        $course_context = get_context_instance(CONTEXT_COURSE, $data->courseid);
        $notenrolled = array();
        $rs = $DB->get_recordset_sql($entriessql, $params);
        if ($rs->valid()) {
            foreach ($rs as $entry) {
                if (array_key_exists($entry->userid, $notenrolled) or !$entry->userexists or $entry->userdeleted
                  or !is_enrolled($course_context , $entry->userid)) {
                    $DB->delete_records('comments', array('commentarea'=>'glossary_entry', 'itemid'=>$entry->id));
                    $DB->delete_records('glossary_entries', array('id'=>$entry->id));

                    if ($cm = get_coursemodule_from_instance('glossary', $entry->glossaryid)) {
                        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                        $fs->delete_area_files($context->id, 'mod_glossary', 'attachment', $entry->id);

                        //delete ratings
                        $ratingdeloptions->contextid = $context->id;
                        $rm->delete_ratings($ratingdeloptions);
                    }
                }
            }
            $status[] = array('component'=>$componentstr, 'item'=>get_string('deletenotenrolled', 'glossary'), 'error'=>false);
        }
        $rs->close();
    }

    // remove all ratings
    if (!empty($data->reset_glossary_ratings)) {
        //remove ratings
        if ($glossaries = $DB->get_records_sql($allglossariessql, $params)) {
            foreach ($glossaries as $glossaryid=>$unused) {
                if (!$cm = get_coursemodule_from_instance('glossary', $glossaryid)) {
                    continue;
                }
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);

                //delete ratings
                $ratingdeloptions->contextid = $context->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            glossary_reset_gradebook($data->courseid);
        }
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallratings'), 'error'=>false);
    }

    // remove comments
    if (!empty($data->reset_glossary_comments)) {
        $params[] = 'glossary_entry';
        $DB->delete_records_select('comments', "itemid IN ($allentriessql) AND commentarea= ? ", $params);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallcomments'), 'error'=>false);
    }

    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('glossary', array('assesstimestart', 'assesstimefinish'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;
}

/**
 * Returns all other caps used in module
 * @return array
 */
function glossary_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames', 'moodle/site:trustcontent', 'moodle/rating:view', 'moodle/rating:viewany', 'moodle/rating:viewall', 'moodle/rating:rate', 'moodle/comment:view', 'moodle/comment:post', 'moodle/comment:delete');
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function glossary_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_RATE:                    return true;
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}

/**
 * Obtains the automatic completion state for this glossary based on any conditions
 * in glossary settings.
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
function glossary_get_completion_state($course,$cm,$userid,$type) {
    global $CFG, $DB;

    // Get glossary details
    if (!($glossary=$DB->get_record('glossary',array('id'=>$cm->instance)))) {
        throw new Exception("Can't find glossary {$cm->instance}");
    }

    $result=$type; // Default return value

    if ($glossary->completionentries) {
        $value = $glossary->completionentries <=
                 $DB->count_records('glossary_entries',array('glossaryid'=>$glossary->id, 'userid'=>$userid, 'approved'=>1));
        if ($type == COMPLETION_AND) {
            $result = $result && $value;
        } else {
            $result = $result || $value;
        }
    }

    return $result;
}

function glossary_extend_navigation($navigation, $course, $module, $cm) {
    global $CFG;
    $navigation->add(get_string('standardview', 'glossary'), new moodle_url('/mod/glossary/view.php', array('id'=>$cm->id, 'mode'=>'letter')));
    $navigation->add(get_string('categoryview', 'glossary'), new moodle_url('/mod/glossary/view.php', array('id'=>$cm->id, 'mode'=>'cat')));
    $navigation->add(get_string('dateview', 'glossary'), new moodle_url('/mod/glossary/view.php', array('id'=>$cm->id, 'mode'=>'date')));
    $navigation->add(get_string('authorview', 'glossary'), new moodle_url('/mod/glossary/view.php', array('id'=>$cm->id, 'mode'=>'author')));
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $glossarynode The node to add module settings to
 */
function glossary_extend_settings_navigation(settings_navigation $settings, navigation_node $glossarynode) {
    global $PAGE, $DB, $CFG, $USER;

    $mode = optional_param('mode', '', PARAM_ALPHA);
    $hook = optional_param('hook', 'ALL', PARAM_CLEAN);

    if (has_capability('mod/glossary:import', $PAGE->cm->context)) {
        $glossarynode->add(get_string('importentries', 'glossary'), new moodle_url('/mod/glossary/import.php', array('id'=>$PAGE->cm->id)));
    }

    if (has_capability('mod/glossary:export', $PAGE->cm->context)) {
        $glossarynode->add(get_string('exportentries', 'glossary'), new moodle_url('/mod/glossary/export.php', array('id'=>$PAGE->cm->id, 'mode'=>$mode, 'hook'=>$hook)));
    }

    if (has_capability('mod/glossary:approve', $PAGE->cm->context) && ($hiddenentries = $DB->count_records('glossary_entries', array('glossaryid'=>$PAGE->cm->instance, 'approved'=>0)))) {
        $glossarynode->add(get_string('waitingapproval', 'glossary'), new moodle_url('/mod/glossary/view.php', array('id'=>$PAGE->cm->id, 'mode'=>'approval')));
    }

    if (has_capability('mod/glossary:write', $PAGE->cm->context)) {
        $glossarynode->add(get_string('addentry', 'glossary'), new moodle_url('/mod/glossary/edit.php', array('cmid'=>$PAGE->cm->id)));
    }

    $glossary = $DB->get_record('glossary', array("id" => $PAGE->cm->instance));

    if (!empty($CFG->enablerssfeeds) && !empty($CFG->glossary_enablerssfeeds) && $glossary->rsstype && $glossary->rssarticles) {
        require_once("$CFG->libdir/rsslib.php");

        $string = get_string('rsstype','forum');

        $url = new moodle_url(rss_get_url($PAGE->cm->context->id, $USER->id, 'mod_glossary', $glossary->id));
        $glossarynode->add($string, $url, settings_navigation::TYPE_SETTING, null, null, new pix_icon('i/rss', ''));
    }
}

/**
 * Running addtional permission check on plugin, for example, plugins
 * may have switch to turn on/off comments option, this callback will
 * affect UI display, not like pluginname_comment_validate only throw
 * exceptions.
 * Capability check has been done in comment->check_permissions(), we
 * don't need to do it again here.
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return array
 */
function glossary_comment_permissions($comment_param) {
    return array('post'=>true, 'view'=>true);
}

/**
 * Validate comment parameter before perform other comments actions
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return boolean
 */
function glossary_comment_validate($comment_param) {
    global $DB;
    // validate comment area
    if ($comment_param->commentarea != 'glossary_entry') {
        throw new comment_exception('invalidcommentarea');
    }
    if (!$record = $DB->get_record('glossary_entries', array('id'=>$comment_param->itemid))) {
        throw new comment_exception('invalidcommentitemid');
    }
    if (!$glossary = $DB->get_record('glossary', array('id'=>$record->glossaryid))) {
        throw new comment_exception('invalidid', 'data');
    }
    if (!$course = $DB->get_record('course', array('id'=>$glossary->course))) {
        throw new comment_exception('coursemisconf');
    }
    if (!$cm = get_coursemodule_from_instance('glossary', $glossary->id, $course->id)) {
        throw new comment_exception('invalidcoursemodule');
    }
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if ($glossary->defaultapproval and !$record->approved and !has_capability('mod/glossary:approve', $context)) {
        throw new comment_exception('notapproved', 'glossary');
    }
    // validate context id
    if ($context->id != $comment_param->context->id) {
        throw new comment_exception('invalidcontext');
    }
    // validation for comment deletion
    if (!empty($comment_param->commentid)) {
        if ($comment = $DB->get_record('comments', array('id'=>$comment_param->commentid))) {
            if ($comment->commentarea != 'glossary_entry') {
                throw new comment_exception('invalidcommentarea');
            }
            if ($comment->contextid != $comment_param->context->id) {
                throw new comment_exception('invalidcontext');
            }
            if ($comment->itemid != $comment_param->itemid) {
                throw new comment_exception('invalidcommentitemid');
            }
        } else {
            throw new comment_exception('invalidcommentid');
        }
    }
    return true;
}
