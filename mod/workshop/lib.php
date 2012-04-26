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
 * Library of workshop module functions needed by Moodle core and other subsystems
 *
 * All the functions neeeded by Moodle core, gradebook, file subsystem etc
 * are placed here.
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/lib.php');

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information if the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function workshop_supports($feature) {
    switch($feature) {
        case FEATURE_GRADE_HAS_GRADE:   return true;
        case FEATURE_GROUPS:            return true;
        case FEATURE_GROUPINGS:         return true;
        case FEATURE_GROUPMEMBERSONLY:  return true;
        case FEATURE_MOD_INTRO:         return true;
        case FEATURE_BACKUP_MOODLE2:    return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        default:                        return null;
    }
}

/**
 * Saves a new instance of the workshop into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will save a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $workshop An object from the form in mod_form.php
 * @return int The id of the newly inserted workshop record
 */
function workshop_add_instance(stdclass $workshop) {
    global $CFG, $DB;
    require_once(dirname(__FILE__) . '/locallib.php');

    $workshop->phase                = workshop::PHASE_SETUP;
    $workshop->timecreated          = time();
    $workshop->timemodified         = $workshop->timecreated;
    $workshop->useexamples          = (int)!empty($workshop->useexamples);          // unticked checkbox hack
    $workshop->usepeerassessment    = (int)!empty($workshop->usepeerassessment);    // unticked checkbox hack
    $workshop->useselfassessment    = (int)!empty($workshop->useselfassessment);    // unticked checkbox hack
    $workshop->latesubmissions      = (int)!empty($workshop->latesubmissions);      // unticked checkbox hack
    $workshop->evaluation           = 'best';

    // insert the new record so we get the id
    $workshop->id = $DB->insert_record('workshop', $workshop);

    // we need to use context now, so we need to make sure all needed info is already in db
    $cmid = $workshop->coursemodule;
    $DB->set_field('course_modules', 'instance', $workshop->id, array('id' => $cmid));
    $context = get_context_instance(CONTEXT_MODULE, $cmid);

    // process the custom wysiwyg editors
    if ($draftitemid = $workshop->instructauthorseditor['itemid']) {
        $workshop->instructauthors = file_save_draft_area_files($draftitemid, $context->id, 'mod_workshop', 'instructauthors',
                0, workshop::instruction_editors_options($context), $workshop->instructauthorseditor['text']);
        $workshop->instructauthorsformat = $workshop->instructauthorseditor['format'];
    }

    if ($draftitemid = $workshop->instructreviewerseditor['itemid']) {
        $workshop->instructreviewers = file_save_draft_area_files($draftitemid, $context->id, 'mod_workshop', 'instructreviewers',
                0, workshop::instruction_editors_options($context), $workshop->instructreviewerseditor['text']);
        $workshop->instructreviewersformat = $workshop->instructreviewerseditor['format'];
    }

    // re-save the record with the replaced URLs in editor fields
    $DB->update_record('workshop', $workshop);

    // create gradebook items
    workshop_grade_item_update($workshop);
    workshop_grade_item_category_update($workshop);

    // create calendar events
    workshop_calendar_update($workshop, $workshop->coursemodule);

    return $workshop->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $workshop An object from the form in mod_form.php
 * @return bool success
 */
function workshop_update_instance(stdclass $workshop) {
    global $CFG, $DB;
    require_once(dirname(__FILE__) . '/locallib.php');

    $workshop->timemodified         = time();
    $workshop->id                   = $workshop->instance;
    $workshop->useexamples          = (int)!empty($workshop->useexamples);          // unticked checkbox hack
    $workshop->usepeerassessment    = (int)!empty($workshop->usepeerassessment);    // unticked checkbox hack
    $workshop->useselfassessment    = (int)!empty($workshop->useselfassessment);    // unticked checkbox hack
    $workshop->latesubmissions      = (int)!empty($workshop->latesubmissions);      // unticked checkbox hack
    $workshop->evaluation           = 'best';

    // todo - if the grading strategy is being changed, we must replace all aggregated peer grades with nulls
    // todo - if maximum grades are being changed, we should probably recalculate or invalidate them

    $DB->update_record('workshop', $workshop);
    $context = get_context_instance(CONTEXT_MODULE, $workshop->coursemodule);

    // process the custom wysiwyg editors
    if ($draftitemid = $workshop->instructauthorseditor['itemid']) {
        $workshop->instructauthors = file_save_draft_area_files($draftitemid, $context->id, 'mod_workshop', 'instructauthors',
                0, workshop::instruction_editors_options($context), $workshop->instructauthorseditor['text']);
        $workshop->instructauthorsformat = $workshop->instructauthorseditor['format'];
    }

    if ($draftitemid = $workshop->instructreviewerseditor['itemid']) {
        $workshop->instructreviewers = file_save_draft_area_files($draftitemid, $context->id, 'mod_workshop', 'instructreviewers',
                0, workshop::instruction_editors_options($context), $workshop->instructreviewerseditor['text']);
        $workshop->instructreviewersformat = $workshop->instructreviewerseditor['format'];
    }

    // re-save the record with the replaced URLs in editor fields
    $DB->update_record('workshop', $workshop);

    // update gradebook items
    workshop_grade_item_update($workshop);
    workshop_grade_item_category_update($workshop);

    // update calendar events
    workshop_calendar_update($workshop, $workshop->coursemodule);

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function workshop_delete_instance($id) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if (! $workshop = $DB->get_record('workshop', array('id' => $id))) {
        return false;
    }

    // delete all associated aggregations
    $DB->delete_records('workshop_aggregations', array('workshopid' => $workshop->id));

    // get the list of ids of all submissions
    $submissions = $DB->get_records('workshop_submissions', array('workshopid' => $workshop->id), '', 'id');

    // get the list of all allocated assessments
    $assessments = $DB->get_records_list('workshop_assessments', 'submissionid', array_keys($submissions), '', 'id');

    // delete the associated records from the workshop core tables
    $DB->delete_records_list('workshop_grades', 'assessmentid', array_keys($assessments));
    $DB->delete_records_list('workshop_assessments', 'id', array_keys($assessments));
    $DB->delete_records_list('workshop_submissions', 'id', array_keys($submissions));

    // call the static clean-up methods of all available subplugins
    $strategies = get_plugin_list('workshopform');
    foreach ($strategies as $strategy => $path) {
        require_once($path.'/lib.php');
        $classname = 'workshop_'.$strategy.'_strategy';
        call_user_func($classname.'::delete_instance', $workshop->id);
    }

    $allocators = get_plugin_list('workshopallocation');
    foreach ($allocators as $allocator => $path) {
        require_once($path.'/lib.php');
        $classname = 'workshop_'.$allocator.'_allocator';
        call_user_func($classname.'::delete_instance', $workshop->id);
    }

    $evaluators = get_plugin_list('workshopeval');
    foreach ($evaluators as $evaluator => $path) {
        require_once($path.'/lib.php');
        $classname = 'workshop_'.$evaluator.'_evaluation';
        call_user_func($classname.'::delete_instance', $workshop->id);
    }

    // delete the calendar events
    $events = $DB->get_records('event', array('modulename' => 'workshop', 'instance' => $workshop->id));
    foreach ($events as $event) {
        $event = calendar_event::load($event);
        $event->delete();
    }

    // finally remove the workshop record itself
    $DB->delete_records('workshop', array('id' => $workshop->id));

    // gradebook cleanup
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 0, null, array('deleted' => true));
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 1, null, array('deleted' => true));

    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdclass|null
 */
function workshop_user_outline($course, $user, $mod, $workshop) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    $grades = grade_get_grades($course->id, 'mod', 'workshop', $workshop->id, $user->id);

    $submissiongrade = null;
    $assessmentgrade = null;

    $info = '';
    $time = 0;

    if (!empty($grades->items[0]->grades)) {
        $submissiongrade = reset($grades->items[0]->grades);
        $info .= get_string('submissiongrade', 'workshop') . ': ' . $submissiongrade->str_long_grade . html_writer::empty_tag('br');
        $time = max($time, $submissiongrade->dategraded);
    }
    if (!empty($grades->items[1]->grades)) {
        $assessmentgrade = reset($grades->items[1]->grades);
        $info .= get_string('gradinggrade', 'workshop') . ': ' . $assessmentgrade->str_long_grade;
        $time = max($time, $assessmentgrade->dategraded);
    }

    if (!empty($info) and !empty($time)) {
        $return = new stdclass();
        $return->time = $time;
        $return->info = $info;
        return $return;
    }

    return null;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return string HTML
 */
function workshop_user_complete($course, $user, $mod, $workshop) {
    global $CFG, $DB, $OUTPUT;
    require_once(dirname(__FILE__).'/locallib.php');
    require_once($CFG->libdir.'/gradelib.php');

    $workshop   = new workshop($workshop, $mod, $course);
    $grades     = grade_get_grades($course->id, 'mod', 'workshop', $workshop->id, $user->id);

    if (!empty($grades->items[0]->grades)) {
        $submissiongrade = reset($grades->items[0]->grades);
        $info = get_string('submissiongrade', 'workshop') . ': ' . $submissiongrade->str_long_grade;
        echo html_writer::tag('li', $info, array('class'=>'submissiongrade'));
    }
    if (!empty($grades->items[1]->grades)) {
        $assessmentgrade = reset($grades->items[1]->grades);
        $info = get_string('gradinggrade', 'workshop') . ': ' . $assessmentgrade->str_long_grade;
        echo html_writer::tag('li', $info, array('class'=>'gradinggrade'));
    }

    if (has_capability('mod/workshop:viewallsubmissions', $workshop->context)) {
        if ($submission = $workshop->get_submission_by_author($user->id)) {
            $title      = format_string($submission->title);
            $url        = $workshop->submission_url($submission->id);
            $link       = html_writer::link($url, $title);
            $info       = get_string('submission', 'workshop').': '.$link;
            echo html_writer::tag('li', $info, array('class'=>'submission'));
        }
    }

    if (has_capability('mod/workshop:viewallassessments', $workshop->context)) {
        if ($assessments = $workshop->get_assessments_by_reviewer($user->id)) {
            foreach ($assessments as $assessment) {
                $a = new stdclass();
                $a->submissionurl = $workshop->submission_url($assessment->submissionid)->out();
                $a->assessmenturl = $workshop->assess_url($assessment->id)->out();
                $a->submissiontitle = s($assessment->submissiontitle);
                echo html_writer::tag('li', get_string('assessmentofsubmission', 'workshop', $a));
            }
        }
    }
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in workshop activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param stdClass $course
 * @param bool $viewfullnames
 * @param int $timestart
 * @return boolean
 */
function workshop_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;

    $sql = "SELECT s.id AS submissionid, s.title AS submissiontitle, s.timemodified AS submissionmodified,
                   author.id AS authorid, author.lastname AS authorlastname, author.firstname AS authorfirstname,
                   a.id AS assessmentid, a.timemodified AS assessmentmodified,
                   reviewer.id AS reviewerid, reviewer.lastname AS reviewerlastname, reviewer.firstname AS reviewerfirstname,
                   cm.id AS cmid
              FROM {workshop} w
        INNER JOIN {course_modules} cm ON cm.instance = w.id
        INNER JOIN {modules} md ON md.id = cm.module
        INNER JOIN {workshop_submissions} s ON s.workshopid = w.id
        INNER JOIN {user} author ON s.authorid = author.id
         LEFT JOIN {workshop_assessments} a ON a.submissionid = s.id
         LEFT JOIN {user} reviewer ON a.reviewerid = reviewer.id
             WHERE cm.course = ?
                   AND md.name = 'workshop'
                   AND s.example = 0
                   AND (s.timemodified > ? OR a.timemodified > ?)";

    $rs = $DB->get_recordset_sql($sql, array($course->id, $timestart, $timestart));

    $modinfo =& get_fast_modinfo($course); // reference needed because we might load the groups

    $submissions = array(); // recent submissions indexed by submission id
    $assessments = array(); // recent assessments indexed by assessment id
    $users       = array();

    foreach ($rs as $activity) {
        if (!array_key_exists($activity->cmid, $modinfo->cms)) {
            // this should not happen but just in case
            continue;
        }

        $cm = $modinfo->cms[$activity->cmid];
        if (!$cm->uservisible) {
            continue;
        }

        if ($viewfullnames) {
            // remember all user names we can use later
            if (empty($users[$activity->authorid])) {
                $u = new stdclass();
                $u->lastname = $activity->authorlastname;
                $u->firstname = $activity->authorfirstname;
                $users[$activity->authorid] = $u;
            }
            if ($activity->reviewerid and empty($users[$activity->reviewerid])) {
                $u = new stdclass();
                $u->lastname = $activity->reviewerlastname;
                $u->firstname = $activity->reviewerfirstname;
                $users[$activity->reviewerid] = $u;
            }
        }

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $groupmode = groups_get_activity_groupmode($cm, $course);

        if ($activity->submissionmodified > $timestart and empty($submissions[$activity->submissionid])) {
            $s = new stdclass();
            $s->title = $activity->submissiontitle;
            $s->authorid = $activity->authorid;
            $s->timemodified = $activity->submissionmodified;
            $s->cmid = $activity->cmid;
            if (has_capability('mod/workshop:viewauthornames', $context)) {
                $s->authornamevisible = true;
            } else {
                $s->authornamevisible = false;
            }

            // the following do-while wrapper allows to break from deeply nested if-statements
            do {
                if ($s->authorid === $USER->id) {
                    // own submissions always visible
                    $submissions[$activity->submissionid] = $s;
                    break;
                }

                if (has_capability('mod/workshop:viewallsubmissions', $context)) {
                    if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                        if (isguestuser()) {
                            // shortcut - guest user does not belong into any group
                            break;
                        }

                        if (is_null($modinfo->groups)) {
                            $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
                        }

                        // this might be slow - show only submissions by users who share group with me in this cm
                        if (empty($modinfo->groups[$cm->id])) {
                            break;
                        }
                        $authorsgroups = groups_get_all_groups($course->id, $s->authorid, $cm->groupingid);
                        if (is_array($authorsgroups)) {
                            $authorsgroups = array_keys($authorsgroups);
                            $intersect = array_intersect($authorsgroups, $modinfo->groups[$cm->id]);
                            if (empty($intersect)) {
                                break;
                            } else {
                                // can see all submissions and shares a group with the author
                                $submissions[$activity->submissionid] = $s;
                                break;
                            }
                        }

                    } else {
                        // can see all submissions from all groups
                        $submissions[$activity->submissionid] = $s;
                    }
                }
            } while (0);
        }

        if ($activity->assessmentmodified > $timestart and empty($assessments[$activity->assessmentid])) {
            $a = new stdclass();
            $a->submissionid = $activity->submissionid;
            $a->submissiontitle = $activity->submissiontitle;
            $a->reviewerid = $activity->reviewerid;
            $a->timemodified = $activity->assessmentmodified;
            $a->cmid = $activity->cmid;
            if (has_capability('mod/workshop:viewreviewernames', $context)) {
                $a->reviewernamevisible = true;
            } else {
                $a->reviewernamevisible = false;
            }

            // the following do-while wrapper allows to break from deeply nested if-statements
            do {
                if ($a->reviewerid === $USER->id) {
                    // own assessments always visible
                    $assessments[$activity->assessmentid] = $a;
                    break;
                }

                if (has_capability('mod/workshop:viewallassessments', $context)) {
                    if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                        if (isguestuser()) {
                            // shortcut - guest user does not belong into any group
                            break;
                        }

                        if (is_null($modinfo->groups)) {
                            $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
                        }

                        // this might be slow - show only submissions by users who share group with me in this cm
                        if (empty($modinfo->groups[$cm->id])) {
                            break;
                        }
                        $reviewersgroups = groups_get_all_groups($course->id, $a->reviewerid, $cm->groupingid);
                        if (is_array($reviewersgroups)) {
                            $reviewersgroups = array_keys($reviewersgroups);
                            $intersect = array_intersect($reviewersgroups, $modinfo->groups[$cm->id]);
                            if (empty($intersect)) {
                                break;
                            } else {
                                // can see all assessments and shares a group with the reviewer
                                $assessments[$activity->assessmentid] = $a;
                                break;
                            }
                        }

                    } else {
                        // can see all assessments from all groups
                        $assessments[$activity->assessmentid] = $a;
                    }
                }
            } while (0);
        }
    }
    $rs->close();

    $shown = false;

    if (!empty($submissions)) {
        $shown = true;
        echo $OUTPUT->heading(get_string('recentsubmissions', 'workshop'), 3);
        foreach ($submissions as $id => $submission) {
            $link = new moodle_url('/mod/workshop/submission.php', array('id'=>$id, 'cmid'=>$submission->cmid));
            if ($viewfullnames and $submission->authornamevisible) {
                $author = $users[$submission->authorid];
            } else {
                $author = null;
            }
            print_recent_activity_note($submission->timemodified, $author, $submission->title, $link->out(), false, $viewfullnames);
        }
    }

    if (!empty($assessments)) {
        $shown = true;
        echo $OUTPUT->heading(get_string('recentassessments', 'workshop'), 3);
        foreach ($assessments as $id => $assessment) {
            $link = new moodle_url('/mod/workshop/assessment.php', array('asid' => $id));
            if ($viewfullnames and $assessment->reviewernamevisible) {
                $reviewer = $users[$assessment->reviewerid];
            } else {
                $reviewer = null;
            }
            print_recent_activity_note($assessment->timemodified, $reviewer, $assessment->submissiontitle, $link->out(), false, $viewfullnames);
        }
    }

    if ($shown) {
        return true;
    }

    return false;
}

/**
 * Returns all activity in course workshops since a given time
 *
 * @param array $activities sequentially indexed array of objects
 * @param int $index
 * @param int $timestart
 * @param int $courseid
 * @param int $cmid
 * @param int $userid defaults to 0
 * @param int $groupid defaults to 0
 * @return void adds items into $activities and increases $index
 */
function workshop_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
    global $CFG, $COURSE, $USER, $DB;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id'=>$courseid));
    }

    $modinfo =& get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];

    $params = array();
    if ($userid) {
        $userselect = "AND (author.id = :authorid OR reviewer.id = :reviewerid)";
        $params['authorid'] = $userid;
        $params['reviewerid'] = $userid;
    } else {
        $userselect = "";
    }

    if ($groupid) {
        $groupselect = "AND (authorgroupmembership.groupid = :authorgroupid OR reviewergroupmembership.groupid = :reviewergroupid)";
        $groupjoin   = "LEFT JOIN {groups_members} authorgroupmembership ON authorgroumembership.userid = author.id
                        LEFT JOIN {groups_members} reviewergroupmembership ON reviewergroumembership.userid = reviewer.id";
        $params['authorgroupid'] = $groupid;
        $params['reviewergroupid'] = $groupid;
    } else {
        $groupselect = "";
        $groupjoin   = "";
    }

    $params['cminstance'] = $cm->instance;
    $params['submissionmodified'] = $timestart;
    $params['assessmentmodified'] = $timestart;

    $sql = "SELECT s.id AS submissionid, s.title AS submissiontitle, s.timemodified AS submissionmodified,
                   author.id AS authorid, author.lastname AS authorlastname, author.firstname AS authorfirstname,
                   author.picture AS authorpicture, author.imagealt AS authorimagealt, author.email AS authoremail,
                   a.id AS assessmentid, a.timemodified AS assessmentmodified,
                   reviewer.id AS reviewerid, reviewer.lastname AS reviewerlastname, reviewer.firstname AS reviewerfirstname,
                   reviewer.picture AS reviewerpicture, reviewer.imagealt AS reviewerimagealt, reviewer.email AS revieweremail
              FROM {workshop_submissions} s
        INNER JOIN {workshop} w ON s.workshopid = w.id
        INNER JOIN {user} author ON s.authorid = author.id
         LEFT JOIN {workshop_assessments} a ON a.submissionid = s.id
         LEFT JOIN {user} reviewer ON a.reviewerid = reviewer.id
        $groupjoin
             WHERE w.id = :cminstance
                   AND s.example = 0
                   $userselect $groupselect
                   AND (s.timemodified > :submissionmodified OR a.timemodified > :assessmentmodified)
          ORDER BY s.timemodified ASC, a.timemodified ASC";

    $rs = $DB->get_recordset_sql($sql, $params);

    $groupmode       = groups_get_activity_groupmode($cm, $course);
    $context         = get_context_instance(CONTEXT_MODULE, $cm->id);
    $grader          = has_capability('moodle/grade:viewall', $context);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $context);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $context);
    $viewauthors     = has_capability('mod/workshop:viewauthornames', $context);
    $viewreviewers   = has_capability('mod/workshop:viewreviewernames', $context);

    if (is_null($modinfo->groups)) {
        $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
    }

    $submissions = array(); // recent submissions indexed by submission id
    $assessments = array(); // recent assessments indexed by assessment id
    $users       = array();

    foreach ($rs as $activity) {

        if ($viewfullnames) {
            // remember all user names we can use later
            if (empty($users[$activity->authorid])) {
                $u = new stdclass();
                $u->id = $activity->authorid;
                $u->lastname = $activity->authorlastname;
                $u->firstname = $activity->authorfirstname;
                $u->picture = $activity->authorpicture;
                $u->imagealt = $activity->authorimagealt;
                $u->email = $activity->authoremail;
                $users[$activity->authorid] = $u;
            }
            if ($activity->reviewerid and empty($users[$activity->reviewerid])) {
                $u = new stdclass();
                $u->id = $activity->reviewerid;
                $u->lastname = $activity->reviewerlastname;
                $u->firstname = $activity->reviewerfirstname;
                $u->picture = $activity->reviewerpicture;
                $u->imagealt = $activity->reviewerimagealt;
                $u->email = $activity->revieweremail;
                $users[$activity->reviewerid] = $u;
            }
        }

        if ($activity->submissionmodified > $timestart and empty($submissions[$activity->submissionid])) {
            $s = new stdclass();
            $s->id = $activity->submissionid;
            $s->title = $activity->submissiontitle;
            $s->authorid = $activity->authorid;
            $s->timemodified = $activity->submissionmodified;
            if (has_capability('mod/workshop:viewauthornames', $context)) {
                $s->authornamevisible = true;
            } else {
                $s->authornamevisible = false;
            }

            // the following do-while wrapper allows to break from deeply nested if-statements
            do {
                if ($s->authorid === $USER->id) {
                    // own submissions always visible
                    $submissions[$activity->submissionid] = $s;
                    break;
                }

                if (has_capability('mod/workshop:viewallsubmissions', $context)) {
                    if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                        if (isguestuser()) {
                            // shortcut - guest user does not belong into any group
                            break;
                        }

                        // this might be slow - show only submissions by users who share group with me in this cm
                        if (empty($modinfo->groups[$cm->id])) {
                            break;
                        }
                        $authorsgroups = groups_get_all_groups($course->id, $s->authorid, $cm->groupingid);
                        if (is_array($authorsgroups)) {
                            $authorsgroups = array_keys($authorsgroups);
                            $intersect = array_intersect($authorsgroups, $modinfo->groups[$cm->id]);
                            if (empty($intersect)) {
                                break;
                            } else {
                                // can see all submissions and shares a group with the author
                                $submissions[$activity->submissionid] = $s;
                                break;
                            }
                        }

                    } else {
                        // can see all submissions from all groups
                        $submissions[$activity->submissionid] = $s;
                    }
                }
            } while (0);
        }

        if ($activity->assessmentmodified > $timestart and empty($assessments[$activity->assessmentid])) {
            $a = new stdclass();
            $a->id = $activity->assessmentid;
            $a->submissionid = $activity->submissionid;
            $a->submissiontitle = $activity->submissiontitle;
            $a->reviewerid = $activity->reviewerid;
            $a->timemodified = $activity->assessmentmodified;
            if (has_capability('mod/workshop:viewreviewernames', $context)) {
                $a->reviewernamevisible = true;
            } else {
                $a->reviewernamevisible = false;
            }

            // the following do-while wrapper allows to break from deeply nested if-statements
            do {
                if ($a->reviewerid === $USER->id) {
                    // own assessments always visible
                    $assessments[$activity->assessmentid] = $a;
                    break;
                }

                if (has_capability('mod/workshop:viewallassessments', $context)) {
                    if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                        if (isguestuser()) {
                            // shortcut - guest user does not belong into any group
                            break;
                        }

                        // this might be slow - show only submissions by users who share group with me in this cm
                        if (empty($modinfo->groups[$cm->id])) {
                            break;
                        }
                        $reviewersgroups = groups_get_all_groups($course->id, $a->reviewerid, $cm->groupingid);
                        if (is_array($reviewersgroups)) {
                            $reviewersgroups = array_keys($reviewersgroups);
                            $intersect = array_intersect($reviewersgroups, $modinfo->groups[$cm->id]);
                            if (empty($intersect)) {
                                break;
                            } else {
                                // can see all assessments and shares a group with the reviewer
                                $assessments[$activity->assessmentid] = $a;
                                break;
                            }
                        }

                    } else {
                        // can see all assessments from all groups
                        $assessments[$activity->assessmentid] = $a;
                    }
                }
            } while (0);
        }
    }
    $rs->close();

    $workshopname = format_string($cm->name, true);

    if ($grader) {
        require_once($CFG->libdir.'/gradelib.php');
        $grades = grade_get_grades($courseid, 'mod', 'workshop', $cm->instance, array_keys($users));
    }

    foreach ($submissions as $submission) {
        $tmpactivity                = new stdclass();
        $tmpactivity->type          = 'workshop';
        $tmpactivity->cmid          = $cm->id;
        $tmpactivity->name          = $workshopname;
        $tmpactivity->sectionnum    = $cm->sectionnum;
        $tmpactivity->timestamp     = $submission->timemodified;
        $tmpactivity->subtype       = 'submission';
        $tmpactivity->content       = $submission;
        if ($grader) {
            $tmpactivity->grade     = $grades->items[0]->grades[$submission->authorid]->str_long_grade;
        }
        if ($submission->authornamevisible and !empty($users[$submission->authorid])) {
            $tmpactivity->user      = $users[$submission->authorid];
        }
        $activities[$index++]       = $tmpactivity;
    }

    foreach ($assessments as $assessment) {
        $tmpactivity                = new stdclass();
        $tmpactivity->type          = 'workshop';
        $tmpactivity->cmid          = $cm->id;
        $tmpactivity->name          = $workshopname;
        $tmpactivity->sectionnum    = $cm->sectionnum;
        $tmpactivity->timestamp     = $assessment->timemodified;
        $tmpactivity->subtype       = 'assessment';
        $tmpactivity->content       = $assessment;
        if ($grader) {
            $tmpactivity->grade     = $grades->items[1]->grades[$assessment->reviewerid]->str_long_grade;
        }
        if ($assessment->reviewernamevisible and !empty($users[$assessment->reviewerid])) {
            $tmpactivity->user      = $users[$assessment->reviewerid];
        }
        $activities[$index++]       = $tmpactivity;
    }
}

/**
 * Print single activity item prepared by {@see workshop_get_recent_mod_activity()}
 */
function workshop_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
    global $CFG, $OUTPUT;

    if (!empty($activity->user)) {
        echo html_writer::tag('div', $OUTPUT->user_picture($activity->user, array('courseid'=>$courseid)),
                array('style' => 'float: left; padding: 7px;'));
    }

    if ($activity->subtype == 'submission') {
        echo html_writer::start_tag('div', array('class'=>'submission', 'style'=>'padding: 7px; float:left;'));

        if ($detail) {
            echo html_writer::start_tag('h4', array('class'=>'workshop'));
            $url = new moodle_url('/mod/workshop/view.php', array('id'=>$activity->cmid));
            $name = s($activity->name);
            echo html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('icon', $activity->type), 'class'=>'icon', 'alt'=>$name));
            echo ' ' . $modnames[$activity->type];
            echo html_writer::link($url, $name, array('class'=>'name', 'style'=>'margin-left: 5px'));
            echo html_writer::end_tag('h4');
        }

        echo html_writer::start_tag('div', array('class'=>'title'));
        $url = new moodle_url('/mod/workshop/submission.php', array('cmid'=>$activity->cmid, 'id'=>$activity->content->id));
        $name = s($activity->content->title);
        echo html_writer::tag('strong', html_writer::link($url, $name));
        echo html_writer::end_tag('div');

        if (!empty($activity->user)) {
            echo html_writer::start_tag('div', array('class'=>'user'));
            $url = new moodle_url('/user/view.php', array('id'=>$activity->user->id, 'course'=>$courseid));
            $name = fullname($activity->user);
            $link = html_writer::link($url, $name);
            echo get_string('submissionby', 'workshop', $link);
            echo ' - '.userdate($activity->timestamp);
            echo html_writer::end_tag('div');
        } else {
            echo html_writer::start_tag('div', array('class'=>'anonymous'));
            echo get_string('submission', 'workshop');
            echo ' - '.userdate($activity->timestamp);
            echo html_writer::end_tag('div');
        }

        echo html_writer::end_tag('div');
    }

    if ($activity->subtype == 'assessment') {
        echo html_writer::start_tag('div', array('class'=>'assessment', 'style'=>'padding: 7px; float:left;'));

        if ($detail) {
            echo html_writer::start_tag('h4', array('class'=>'workshop'));
            $url = new moodle_url('/mod/workshop/view.php', array('id'=>$activity->cmid));
            $name = s($activity->name);
            echo html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('icon', $activity->type), 'class'=>'icon', 'alt'=>$name));
            echo ' ' . $modnames[$activity->type];
            echo html_writer::link($url, $name, array('class'=>'name', 'style'=>'margin-left: 5px'));
            echo html_writer::end_tag('h4');
        }

        echo html_writer::start_tag('div', array('class'=>'title'));
        $url = new moodle_url('/mod/workshop/assessment.php', array('asid'=>$activity->content->id));
        $name = s($activity->content->submissiontitle);
        echo html_writer::tag('em', html_writer::link($url, $name));
        echo html_writer::end_tag('div');

        if (!empty($activity->user)) {
            echo html_writer::start_tag('div', array('class'=>'user'));
            $url = new moodle_url('/user/view.php', array('id'=>$activity->user->id, 'course'=>$courseid));
            $name = fullname($activity->user);
            $link = html_writer::link($url, $name);
            echo get_string('assessmentbyfullname', 'workshop', $link);
            echo ' - '.userdate($activity->timestamp);
            echo html_writer::end_tag('div');
        } else {
            echo html_writer::start_tag('div', array('class'=>'anonymous'));
            echo get_string('assessment', 'workshop');
            echo ' - '.userdate($activity->timestamp);
            echo html_writer::end_tag('div');
        }

        echo html_writer::end_tag('div');
    }

    echo html_writer::empty_tag('br', array('style'=>'clear:both'));
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function workshop_cron () {
    return true;
}

/**
 * Returns an array of user ids who are participanting in this workshop
 *
 * Participants are either submission authors or reviewers or both.
 * Authors of the example submissions and their referential assessments
 * are not returned as the example submission is considered non-user
 * data for the purpose of workshop backup.
 *
 * @todo: deprecated - to be deleted in 2.2
 *
 * @param int $workshopid ID of an instance of this module
 * @return array of user ids, empty if there are no participants
 */
function workshop_get_participants($workshopid) {
    global $DB;

    $sql = "SELECT u.id
              FROM {workshop} w
              JOIN {workshop_submissions} s ON s.workshopid = w.id
              JOIN {user} u ON s.authorid = u.id
             WHERE w.id = ? AND s.example = 0

             UNION

            SELECT u.id
              FROM {workshop} w
              JOIN {workshop_submissions} s ON s.workshopid = w.id
              JOIN {workshop_assessments} a ON a.submissionid = s.id
              JOIN {user} u ON a.reviewerid = u.id
             WHERE w.id = ? AND (s.example = 0 OR a.weight = 0)";

    $users = array();
    $rs = $DB->get_recordset_sql($sql, array($workshopid, $workshopid));
    foreach ($rs as $user) {
        if (empty($users[$user->id])) {
            $users[$user->id] = $user;
        }
    }
    $rs->close();

    return $users;
}

/**
 * Is a given scale used by the instance of workshop?
 *
 * The function asks all installed grading strategy subplugins. The workshop
 * core itself does not use scales. Both grade for submission and grade for
 * assessments do not use scales.
 *
 * @param int $workshopid id of workshop instance
 * @param int $scaleid id of the scale to check
 * @return bool
 */
function workshop_scale_used($workshopid, $scaleid) {
    global $CFG; // other files included from here

    $strategies = get_plugin_list('workshopform');
    foreach ($strategies as $strategy => $strategypath) {
        $strategylib = $strategypath . '/lib.php';
        if (is_readable($strategylib)) {
            require_once($strategylib);
        } else {
            throw new coding_exception('the grading forms subplugin must contain library ' . $strategylib);
        }
        $classname = 'workshop_' . $strategy . '_strategy';
        if (method_exists($classname, 'scale_used')) {
            if (call_user_func_array(array($classname, 'scale_used'), array($scaleid, $workshopid))) {
                // no need to include any other files - scale is used
                return true;
            }
        }
    }

    return false;
}

/**
 * Is a given scale used by any instance of workshop?
 *
 * The function asks all installed grading strategy subplugins. The workshop
 * core itself does not use scales. Both grade for submission and grade for
 * assessments do not use scales.
 *
 * @param int $scaleid id of the scale to check
 * @return bool
 */
function workshop_scale_used_anywhere($scaleid) {
    global $CFG; // other files included from here

    $strategies = get_plugin_list('workshopform');
    foreach ($strategies as $strategy => $strategypath) {
        $strategylib = $strategypath . '/lib.php';
        if (is_readable($strategylib)) {
            require_once($strategylib);
        } else {
            throw new coding_exception('the grading forms subplugin must contain library ' . $strategylib);
        }
        $classname = 'workshop_' . $strategy . '_strategy';
        if (method_exists($classname, 'scale_used')) {
            if (call_user_func(array($classname, 'scale_used'), $scaleid)) {
                // no need to include any other files - scale is used
                return true;
            }
        }
    }

    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function workshop_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Creates or updates grade items for the give workshop instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php. Also used by
 * {@link workshop_update_grades()}.
 *
 * @param stdClass $workshop instance object with extra cmidnumber and modname property
 * @param stdClass $submissiongrades data for the first grade item
 * @param stdClass $assessmentgrades data for the second grade item
 * @return void
 */
function workshop_grade_item_update(stdclass $workshop, $submissiongrades=null, $assessmentgrades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $a = new stdclass();
    $a->workshopname = clean_param($workshop->name, PARAM_NOTAGS);

    $item = array();
    $item['itemname'] = get_string('gradeitemsubmission', 'workshop', $a);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $workshop->grade;
    $item['grademin']  = 0;
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 0, $submissiongrades , $item);

    $item = array();
    $item['itemname'] = get_string('gradeitemassessment', 'workshop', $a);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $workshop->gradinggrade;
    $item['grademin']  = 0;
    grade_update('mod/workshop', $workshop->course, 'mod', 'workshop', $workshop->id, 1, $assessmentgrades, $item);
}

/**
 * Update workshop grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $workshop instance object with extra cmidnumber and modname property
 * @param int $userid        update grade of specific user only, 0 means all participants
 * @return void
 */
function workshop_update_grades(stdclass $workshop, $userid=0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    $whereuser = $userid ? ' AND authorid = :userid' : '';
    $params = array('workshopid' => $workshop->id, 'userid' => $userid);
    $sql = 'SELECT authorid, grade, gradeover, gradeoverby, feedbackauthor, feedbackauthorformat, timemodified, timegraded
              FROM {workshop_submissions}
             WHERE workshopid = :workshopid AND example=0' . $whereuser;
    $records = $DB->get_records_sql($sql, $params);
    $submissiongrades = array();
    foreach ($records as $record) {
        $grade = new stdclass();
        $grade->userid = $record->authorid;
        if (!is_null($record->gradeover)) {
            $grade->rawgrade = grade_floatval($workshop->grade * $record->gradeover / 100);
            $grade->usermodified = $record->gradeoverby;
        } else {
            $grade->rawgrade = grade_floatval($workshop->grade * $record->grade / 100);
        }
        $grade->feedback = $record->feedbackauthor;
        $grade->feedbackformat = $record->feedbackauthorformat;
        $grade->datesubmitted = $record->timemodified;
        $grade->dategraded = $record->timegraded;
        $submissiongrades[$record->authorid] = $grade;
    }

    $whereuser = $userid ? ' AND userid = :userid' : '';
    $params = array('workshopid' => $workshop->id, 'userid' => $userid);
    $sql = 'SELECT userid, gradinggrade, timegraded
              FROM {workshop_aggregations}
             WHERE workshopid = :workshopid' . $whereuser;
    $records = $DB->get_records_sql($sql, $params);
    $assessmentgrades = array();
    foreach ($records as $record) {
        $grade = new stdclass();
        $grade->userid = $record->userid;
        $grade->rawgrade = grade_floatval($workshop->gradinggrade * $record->gradinggrade / 100);
        $grade->dategraded = $record->timegraded;
        $assessmentgrades[$record->userid] = $grade;
    }

    workshop_grade_item_update($workshop, $submissiongrades, $assessmentgrades);
}

/**
 * Update the grade items categories if they are changed via mod_form.php
 *
 * We must do it manually here in the workshop module because modedit supports only
 * single grade item while we use two.
 *
 * @param stdClass $workshop An object from the form in mod_form.php
 */
function workshop_grade_item_category_update($workshop) {

    $gradeitems = grade_item::fetch_all(array(
        'itemtype'      => 'mod',
        'itemmodule'    => 'workshop',
        'iteminstance'  => $workshop->id,
        'courseid'      => $workshop->course));

    if (!empty($gradeitems)) {
        foreach ($gradeitems as $gradeitem) {
            if ($gradeitem->itemnumber == 0) {
                if ($gradeitem->categoryid != $workshop->gradecategory) {
                    $gradeitem->set_parent($workshop->gradecategory);
                }
            } else if ($gradeitem->itemnumber == 1) {
                if ($gradeitem->categoryid != $workshop->gradinggradecategory) {
                    $gradeitem->set_parent($workshop->gradinggradecategory);
                }
            }
        }
    }
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area workshop_intro for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function workshop_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['instructauthors']          = get_string('areainstructauthors', 'workshop');
    $areas['instructreviewers']        = get_string('areainstructreviewers', 'workshop');
    $areas['submission_content']       = get_string('areasubmissioncontent', 'workshop');
    $areas['submission_attachment']    = get_string('areasubmissionattachment', 'workshop');

    return $areas;
}

/**
 * Serves the files from the workshop file areas
 *
 * Apart from module intro (handled by pluginfile.php automatically), workshop files may be
 * media inserted into submission content (like images) and submission attachments. For these two,
 * the fileareas workshop_submission_content and workshop_submission_attachment are used.
 * The access rights to the files are checked here. The user must be either a peer-reviewer
 * of the submission or have capability ... (todo) to access the submission files.
 * Besides that, areas workshop_instructauthors and mod_workshop instructreviewers contain the media
 * embedded using the mod_form.php.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function workshop_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    if ($filearea === 'instructauthors') {
        // submission instructions may contain sensitive data
        if (!has_any_capability(array('moodle/course:manageactivities', 'mod/workshop:submit'), $context)) {
            send_file_not_found();
        }

        array_shift($args); // we do not use itemids here
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_workshop/$filearea/0/$relativepath"; // beware, slashes are not used here!

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        $lifetime = isset($CFG->filelifetime) ? $CFG->filelifetime : 86400;

        // finally send the file
        send_stored_file($file, $lifetime, 0);
    }

    if ($filearea === 'instructreviewers') {
        // submission instructions may contain sensitive data
        if (!has_any_capability(array('moodle/course:manageactivities', 'mod/workshop:peerassess'), $context)) {
            send_file_not_found();
        }

        array_shift($args); // we do not use itemids here
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_workshop/$filearea/0/$relativepath";

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        $lifetime = isset($CFG->filelifetime) ? $CFG->filelifetime : 86400;

        // finally send the file
        send_stored_file($file, $lifetime, 0);

    } else if ($filearea === 'submission_content' or $filearea === 'submission_attachment') {
        $itemid = (int)array_shift($args);
        if (!$workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
            return false;
        }
        if (!$submission = $DB->get_record('workshop_submissions', array('id' => $itemid, 'workshopid' => $workshop->id))) {
            return false;
        }
        // TODO now make sure the user is allowed to see the file
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_workshop/$filearea/$itemid/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }
        // finally send the file
        // these files are uploaded by students - forcing download for security reasons
        send_stored_file($file, 0, 0, true);
    }

    return false;
}

/**
 * File browsing support for workshop file areas
 *
 * @param stdClass $browser
 * @param stdClass $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return stdclass file_info instance or null if not found
 */
function workshop_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB;

    if (!has_capability('moodle/course:managefiles', $context)) {
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content' or $filearea === 'attachment') {

        if (is_null($itemid)) {
            require_once($CFG->dirroot . '/mod/workshop/fileinfolib.php');
            return new workshop_file_info_submissions_container($browser, $course, $cm, $context, $areas, $filearea);
        }

        // we are inside the submission container

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        if (!$storedfile = $fs->get_file($context->id, 'mod_workshop', $filearea, $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_workshop', $filearea, $itemid);
            } else {
                // not found
                return null;
            }
        }

        // let us display the author's name instead of itemid (submission id)
        // todo some sort of caching should happen here

        $sql = "SELECT s.id, u.lastname, u.firstname
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.authorid = u.id)
                 WHERE s.workshopid = ?";
        $params         = array($cm->instance);
        $authors        = $DB->get_records_sql($sql, $params);
        $urlbase        = $CFG->wwwroot . '/pluginfile.php';
        $topvisiblename = fullname($authors[$itemid]);
        // do not allow manual modification of any files!
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $topvisiblename, true, true, false, false);
    }

    if ($filearea == 'instructauthors' or $filearea == 'instructreviewers') {
        // always only itemid 0

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_workshop', $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_workshop', $filearea, 0);
            } else {
                // not found
                return null;
            }
        }
        return new file_info_stored($browser, $context, $storedfile, $urlbase, $areas[$filearea], false, true, true, false);
    }
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding workshop nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the workshop module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function workshop_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
    global $CFG;

    if (has_capability('mod/workshop:submit', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        $url = new moodle_url('/mod/workshop/submission.php', array('cmid' => $cm->id));
        $mysubmission = $navref->add(get_string('mysubmission', 'workshop'), $url);
        $mysubmission->mainnavonly = true;
    }
}

/**
 * Extends the settings navigation with the Workshop settings

 * This function is called when the context for the page is a workshop module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $workshopnode {@link navigation_node}
 */
function workshop_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $workshopnode=null) {
    global $PAGE;

    //$workshopobject = $DB->get_record("workshop", array("id" => $PAGE->cm->instance));

    if (has_capability('mod/workshop:editdimensions', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/workshop/editform.php', array('cmid' => $PAGE->cm->id));
        $workshopnode->add(get_string('editassessmentform', 'workshop'), $url, settings_navigation::TYPE_SETTING);
    }
    if (has_capability('mod/workshop:allocate', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/workshop/allocation.php', array('cmid' => $PAGE->cm->id));
        $workshopnode->add(get_string('allocate', 'workshop'), $url, settings_navigation::TYPE_SETTING);
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function workshop_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-workshop-*'=>get_string('page-mod-workshop-x', 'workshop'));
    return $module_pagetype;
}

////////////////////////////////////////////////////////////////////////////////
// Calendar API                                                               //
////////////////////////////////////////////////////////////////////////////////

/**
 * Updates the calendar events associated to the given workshop
 *
 * @param stdClass $workshop the workshop instance record
 * @param int $cmid course module id
 */
function workshop_calendar_update(stdClass $workshop, $cmid) {
    global $DB;

    // get the currently registered events so that we can re-use their ids
    $currentevents = $DB->get_records('event', array('modulename' => 'workshop', 'instance' => $workshop->id));

    // the common properties for all events
    $base = new stdClass();
    $base->description  = format_module_intro('workshop', $workshop, $cmid, false);
    $base->courseid     = $workshop->course;
    $base->groupid      = 0;
    $base->userid       = 0;
    $base->modulename   = 'workshop';
    $base->eventtype    = 'pluginname';
    $base->instance     = $workshop->id;
    $base->visible      = instance_is_visible('workshop', $workshop);
    $base->timeduration = 0;

    if ($workshop->submissionstart) {
        $event = clone($base);
        $event->name = get_string('submissionstartevent', 'mod_workshop', $workshop->name);
        $event->timestart = $workshop->submissionstart;
        if ($reusedevent = array_shift($currentevents)) {
            $event->id = $reusedevent->id;
        } else {
            // should not be set but just in case
            unset($event->id);
        }
        // update() will reuse a db record if the id field is set
        $eventobj = new calendar_event($event);
        $eventobj->update($event, false);
    }

    if ($workshop->submissionend) {
        $event = clone($base);
        $event->name = get_string('submissionendevent', 'mod_workshop', $workshop->name);
        $event->timestart = $workshop->submissionend;
        if ($reusedevent = array_shift($currentevents)) {
            $event->id = $reusedevent->id;
        } else {
            // should not be set but just in case
            unset($event->id);
        }
        // update() will reuse a db record if the id field is set
        $eventobj = new calendar_event($event);
        $eventobj->update($event, false);
    }

    if ($workshop->assessmentstart) {
        $event = clone($base);
        $event->name = get_string('assessmentstartevent', 'mod_workshop', $workshop->name);
        $event->timestart = $workshop->assessmentstart;
        if ($reusedevent = array_shift($currentevents)) {
            $event->id = $reusedevent->id;
        } else {
            // should not be set but just in case
            unset($event->id);
        }
        // update() will reuse a db record if the id field is set
        $eventobj = new calendar_event($event);
        $eventobj->update($event, false);
    }

    if ($workshop->assessmentend) {
        $event = clone($base);
        $event->name = get_string('assessmentendevent', 'mod_workshop', $workshop->name);
        $event->timestart = $workshop->assessmentend;
        if ($reusedevent = array_shift($currentevents)) {
            $event->id = $reusedevent->id;
        } else {
            // should not be set but just in case
            unset($event->id);
        }
        // update() will reuse a db record if the id field is set
        $eventobj = new calendar_event($event);
        $eventobj->update($event, false);
    }

    // delete any leftover events
    foreach ($currentevents as $oldevent) {
        $oldevent = calendar_event::load($oldevent);
        $oldevent->delete();
    }
}
