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
 * Functions used by some stages in workshop db/upgrade.php
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Prepares the inital workshop 2.0 core tables
 */
function workshop_upgrade_prepare_20_tables() {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if (!$dbman->table_exists('workshop')) {
        $table = new xmldb_table('workshop');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('instructauthors', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('instructauthorsformat', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('instructreviewers', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('instructreviewersformat', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('phase', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('useexamples', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('usepeerassessment', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('useselfassessment', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, '80');
        $table->add_field('gradinggrade', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, '20');
        $table->add_field('strategy', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_field('gradedecimals', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('nattachments', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('latesubmissions', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('maxbytes', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '100000');
        $table->add_field('examplesmode', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('submissionstart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('submissionend', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('assessmentstart', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('assessmentend', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('course_fk', XMLDB_KEY_FOREIGN, array('course'), 'course', array('id'));
        $dbman->create_table($table);
    }

    if (!$dbman->table_exists('workshop_submissions')) {
        $table = new xmldb_table('workshop_submissions');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('workshopid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('example', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('authorid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('contentformat', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('contenttrust', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('attachment', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradeover', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradeoverby', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('feedbackauthor', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('feedbackauthorformat', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('timegraded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('workshop_fk', XMLDB_KEY_FOREIGN, array('workshopid'), 'workshop', array('id'));
        $table->add_key('overriddenby_fk', XMLDB_KEY_FOREIGN, array('gradeoverby'), 'user', array('id'));
        $table->add_key('author_fk', XMLDB_KEY_FOREIGN, array('authorid'), 'user', array('id'));
        $dbman->create_table($table);
    }

    if (!$dbman->table_exists('workshop_assessments')) {
        $table = new xmldb_table('workshop_assessments');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('submissionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('reviewerid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('weight', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradinggrade', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradinggradeover', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradinggradeoverby', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('feedbackauthor', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('feedbackauthorformat', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0');
        $table->add_field('feedbackreviewer', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('feedbackreviewerformat', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('submission_fk', XMLDB_KEY_FOREIGN, array('submissionid'), 'workshop_submissions', array('id'));
        $table->add_key('overriddenby_fk', XMLDB_KEY_FOREIGN, array('gradinggradeoverby'), 'user', array('id'));
        $table->add_key('reviewer_fk', XMLDB_KEY_FOREIGN, array('reviewerid'), 'user', array('id'));
        $dbman->create_table($table);
    }

    if (!$dbman->table_exists('workshop_grades')) {
        $table = new xmldb_table('workshop_grades');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('assessmentid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('strategy', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null);
        $table->add_field('dimensionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('grade', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('peercomment', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('peercommentformat', XMLDB_TYPE_INTEGER, '3', null, null, null, '0');
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('assessment_fk', XMLDB_KEY_FOREIGN, array('assessmentid'), 'workshop_assessments', array('id'));
        $table->add_key('formfield_uk', XMLDB_KEY_UNIQUE, array('assessmentid', 'strategy', 'dimensionid'));
        $dbman->create_table($table);
    }

    if (!$dbman->table_exists('workshop_aggregations')) {
        $table = new xmldb_table('workshop_aggregations');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('workshopid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('gradinggrade', XMLDB_TYPE_NUMBER, '10, 5', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timegraded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('workshop_fk', XMLDB_KEY_FOREIGN, array('workshopid'), 'workshop', array('id'));
        $table->add_key('user_fk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('workshopuser', XMLDB_KEY_UNIQUE, array('workshopid', 'userid'));
        $dbman->create_table($table);
    }
}

/**
 * Copies the records from workshop_old into workshop table
 *
 * @return void
 */
function workshop_upgrade_module_instances() {
    global $CFG, $DB;

    upgrade_set_timeout();
    $moduleid = $DB->get_field('modules', 'id', array('name' => 'workshop'), MUST_EXIST);
    $rs = $DB->get_recordset_select('workshop_old', 'newid IS NULL', null, 'id');
    foreach ($rs as $old) {
        $new = workshop_upgrade_transform_instance($old);
        $new->id = $old->id;
        $DB->import_record('workshop', $new);
        $DB->set_field('workshop_old', 'newplugin', 'workshop', array('id' => $old->id));
        $DB->set_field('workshop_old', 'newid', $new->id, array('id' => $old->id));
    }
    $rs->close();
}

/**
 * Given a record containing data from 1.9 workshop table, returns object containing data as should be saved in 2.0 workshop table
 *
 * @param stdClass $old record from 1.9 workshop table
 * @return stdClass
 */
function workshop_upgrade_transform_instance(stdClass $old) {
    global $CFG;
    require_once(dirname(dirname(__FILE__)) . '/locallib.php');

    $new                = new stdClass();
    $new->course        = $old->course;
    $new->name          = $old->name;
    $new->intro         = $old->description;
    $new->introformat   = $old->format;
    $new->nattachments  = $old->nattachments;
    $new->maxbytes      = $old->maxbytes;
    $new->grade         = $old->grade;
    $new->gradinggrade  = $old->gradinggrade;
    $new->phase         = workshop::PHASE_CLOSED;
    $new->timemodified  = time();
    if ($old->ntassessments > 0) {
        $new->useexamples = 1;
    } else {
        $new->useexamples = 0;
    }
    $new->usepeerassessment = 1;
    $new->useselfassessment = $old->includeself;
    switch ($old->gradingstrategy) {
    case 0: // 'notgraded' - renamed
        $new->strategy = 'comments';
        break;
    case 1: // 'accumulative'
        $new->strategy = 'accumulative';
        break;
    case 2: // 'errorbanded' - renamed
        $new->strategy = 'numerrors';
        break;
    case 3: // 'criterion' - will be migrated into 'rubric'
        $new->strategy = 'rubric';
        break;
    case 4: // 'rubric'
        $new->strategy = 'rubric';
        break;
    }
    if ($old->submissionstart < $old->submissionend) {
        $new->submissionstart = $old->submissionstart;
        $new->submissionend   = $old->submissionend;
    }
    if ($old->assessmentstart < $old->assessmentend) {
        $new->assessmentstart = $old->assessmentstart;
        $new->assessmentend   = $old->assessmentend;
    }

    return $new;
}

/**
 * Copies records from workshop_submissions_old into workshop_submissions. Can be called after all workshop module instances
 * were correctly migrated and new ids are filled in workshop_old
 *
 * @return void
 */
function workshop_upgrade_submissions() {
    global $CFG, $DB;

    upgrade_set_timeout();

    // list of teachers in every workshop: array of (int)workshopid => array of (int)userid => notused
    $workshopteachers = array();

    $rs = $DB->get_recordset_select('workshop_submissions_old', 'newid IS NULL');
    foreach ($rs as $old) {
        if (!isset($workshopteachers[$old->workshopid])) {
            $cm = get_coursemodule_from_instance('workshop', $old->workshopid, 0, false, MUST_EXIST);
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $workshopteachers[$old->workshopid] = get_users_by_capability($context, 'mod/workshop:manage', 'u.id');
        }
        $new = workshop_upgrade_transform_submission($old, $old->workshopid, $workshopteachers[$old->workshopid]);
        $newid = $DB->insert_record('workshop_submissions', $new, true, true);
        $DB->set_field('workshop_submissions_old', 'newplugin', 'submissions', array('id' => $old->id));
        $DB->set_field('workshop_submissions_old', 'newid', $newid, array('id' => $old->id));
    }
    $rs->close();
}

/**
 * Given a record from 1.x workshop_submissions_old, returns data for 2.0 workshop_submissions
 *
 * @param stdClass $old
 * @param int $newworkshopid new workshop id
 * @param array $legacyteachers $userid => notused the list of legacy workshop teachers for the submission's workshop
 * @return stdClass
 */
function workshop_upgrade_transform_submission(stdClass $old, $newworkshopid, array $legacyteachers) {

    $new                = new stdclass(); // new submission record to be returned
    $new->workshopid    = $newworkshopid;

    if (isset($legacyteachers[$old->userid])) {
        // the author of the submission was teacher = had mod/workshop:manage. this is the only way how we can
        // recognize the submission should be treated as example submission (ach jo...)
        $new->example   = 1;
    } else {
        $new->example   = 0;
    }

    $new->authorid      = $old->userid;
    $new->timecreated   = $old->timecreated;
    $new->timemodified  = $old->timecreated;
    $new->title         = $old->title;
    $new->content       = $old->description;
    $new->contentformat = FORMAT_HTML;
    $new->contenttrust  = 0;
    $new->published     = 0;

    return $new;
}

/**
 * Returns the list of new submission instances ids
 *
 * @return array (int)oldid => (int)newid
 */
function workshop_upgrade_submission_id_mappings() {
    global $DB;

    $oldrecords = $DB->get_records('workshop_submissions_old', null, 'id', 'id,newid');
    $newids = array();
    foreach ($oldrecords as $oldid => $oldrecord) {
        if ($oldrecord->id and $oldrecord->newid) {
            $newids[$oldid] = $oldrecord->newid;
        }
    }
    return $newids;
}

/**
 * Returns the list of teacherweight values as were set in legacy workshop instances
 *
 * @return array (int)oldid => (int)teacherweight
 */
function workshop_upgrade_legacy_teacher_weights() {
    global $DB;

    $oldrecords = $DB->get_records('workshop_old', null, 'id', 'id,teacherweight');
    $weights = array();
    foreach ($oldrecords as $oldid => $oldrecord) {
        if (is_null($oldrecord->teacherweight)) {
            $weights[$oldid] = 1;
        } else {
            $weights[$oldid] = $oldrecord->teacherweight;
        }
    }
    return $weights;
}

/**
 * Copies all assessments from workshop_assessments_old to workshop_assessments. Can be called after all
 * submissions were migrated.
 *
 * @return void
 */
function workshop_upgrade_assessments() {
    global $CFG, $DB, $OUTPUT;

    upgrade_set_timeout();

    $newsubmissionids   = workshop_upgrade_submission_id_mappings();
    $teacherweights     = workshop_upgrade_legacy_teacher_weights();

    // list of teachers in every workshop: array of (int)workshopid => array of (int)userid => notused
    $workshopteachers   = array();

    // get the list of ids of the new example submissions
    $examplesubmissions = $DB->get_records('workshop_submissions', array('example' => 1), '', 'id');

    $rs = $DB->get_recordset_select('workshop_assessments_old', 'newid IS NULL');
    foreach ($rs as $old) {
        if (!isset($workshopteachers[$old->workshopid])) {
            $cm = get_coursemodule_from_instance('workshop', $old->workshopid, 0, false, MUST_EXIST);
            $context = get_context_instance(CONTEXT_MODULE, $cm->id);
            $workshopteachers[$old->workshopid] = get_users_by_capability($context, 'mod/workshop:manage', 'u.id');
        }
        $ofexample = isset($examplesubmissions[$newsubmissionids[$old->submissionid]]);
        $new = workshop_upgrade_transform_assessment($old, $newsubmissionids[$old->submissionid],
                                                     $workshopteachers[$old->workshopid], $teacherweights[$old->workshopid], $ofexample);
        $newid = $DB->insert_record('workshop_assessments', $new, true, true);
        $DB->set_field('workshop_assessments_old', 'newplugin', 'assessments', array('id' => $old->id));
        $DB->set_field('workshop_assessments_old', 'newid', $newid, array('id' => $old->id));
    }
    $rs->close();
}

/**
 * Given a record from workshop_assessments_old, returns record to be stored in workshop_assessment
 *
 * @param stdClass $old                 record from workshop_assessments_old,
 * @param int      $newsubmissionid     new submission id
 * @param array    $legacyteachers      (int)userid => notused the list of legacy workshop teachers for the submission's workshop
 * @param int      $legacyteacherweight weight of teacher's assessment in legacy workshop
 * @param bool     $ofexample           is this the assessment of an example submission?
 * @return stdClass
 */
function workshop_upgrade_transform_assessment(stdClass $old, $newsubmissionid, array $legacyteachers, $legacyteacherweight, $ofexample) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $new                            = new stdclass();
    $new->submissionid              = $newsubmissionid;
    $new->reviewerid                = $old->userid;

    if ($ofexample) {
        // this is the assessment of an example submission
        if (isset($legacyteachers[$old->userid])) {
            // this is probably the reference assessment of the example submission
            $new->weight            = 1;
        } else {
            $new->weight            = 0;
        }

    } else {
        if (isset($legacyteachers[$old->userid])) {
            $new->weight            = $legacyteacherweight;
        } else {
            $new->weight            = 1;
        }
    }

    if ($old->grade < 0) {
        // in workshop 1.x, this is just allocated assessment that has not been touched yet, having timecreated one year in the future :-/
        $new->timecreated           = time();
    } else {
        $new->grade                 = grade_floatval($old->grade);
        if ($old->teachergraded) {
            $new->gradinggradeover  = grade_floatval($old->gradinggrade);
        } else {
            $new->gradinggrade      = grade_floatval($old->gradinggrade);
        }
        $new->feedbackauthor        = $old->generalcomment;
        $new->feedbackauthorformat  = FORMAT_HTML;
        $new->feedbackreviewer      = $old->teachercomment;
        $new->feedbackreviewerformat = FORMAT_HTML;
        $new->timecreated           = $old->timecreated;
        $new->timemodified          = $old->timegraded;
    }

    return $new;
}

/**
 * Returns the list of new assessment ids
 *
 * @return array (int)oldid => (int)newid
 */
function workshop_upgrade_assessment_id_mappings() {
    global $DB;

    $oldrecords = $DB->get_records('workshop_assessments_old', null, 'id', 'id,newid');
    $newids = array();
    foreach ($oldrecords as $oldid => $oldrecord) {
        if ($oldrecord->id and $oldrecord->newid) {
            $newids[$oldid] = $oldrecord->newid;
        }
    }
    return $newids;
}

/**
 * Returns the list of new element (dimension) ids
 *
 * @param string $strategy the name of strategy subplugin that the element was migrated into
 * @return array (int)workshopid => array (int)elementno => stdclass ->(int)newid {->(string)type} {->(int)maxscore}
 */
function workshop_upgrade_element_id_mappings($strategy) {
    global $DB;

    $oldrecords = $DB->get_records('workshop_elements_old', array('newplugin' => $strategy),
                                   'workshopid,elementno', 'id,workshopid,elementno,scale,maxscore,newid');
    $newids = array();
    foreach ($oldrecords as $old) {
        if (!isset($newids[$old->workshopid])) {
            $newids[$old->workshopid] = array();
        }
        $info = new stdclass();
        $info->newid = $old->newid;
        if ($strategy == 'accumulative') {
            if ($old->scale >= 0 and $old->scale <= 6) {
                $info->type = 'scale';
            } else {
                $info->type = 'value';
            }
        }
        if ($strategy == 'rubric_levels') {
            $info->maxscore = $old->maxscore;
        }
        $newids[$old->workshopid][$old->elementno] = $info;
    }
    return $newids;
}
