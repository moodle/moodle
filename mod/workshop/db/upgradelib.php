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
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        $table->add_field('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null); // id in 1.9 table
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
 * Copies the records from workshop_old into workshop table, keeping the original id in oldid column
 *
 * @return void
 */
function workshop_upgrade_copy_instances() {
    global $CFG, $DB, $OUTPUT;

    upgrade_set_timeout();
    $moduleid = $DB->get_field('modules', 'id', array('name' => 'workshop'), MUST_EXIST);
    $rs = $DB->get_recordset_select('workshop_old', 'newid IS NULL');
    foreach ($rs as $old) {
        $new                = new stdClass();
        $new->oldid         = $old->id;
        $new->course        = $old->course;
        $new->name          = $old->name;
        $new->intro         = $old->description;
        $new->introformat   = FORMAT_HTML;
        $new->nattachments  = $old->nattachments;
        $new->maxbytes      = $old->maxbytes;
        $new->grade         = $old->grade;
        $new->gradinggrade  = $old->gradinggrade;
        $new->phase         = 50;   // defined in locallib.php as const workshop::PHASE_CLOSED
        $new->timemodified  = time();
        if ($old->ntassessments > 0) {
            $new->useexamples = 1;
        } else {
            $new->useexamples = 0;
        }
        $new->usepeerassessment = 1;
        $new->useselfassessment = $old->includeself;
        switch ($old->gradingstrategy) {
        case 0:
            $new->strategy = 'comments';     // 'notgraded'
            break;
        case 1:
            $new->strategy = 'accumulative'; // 'accumulative'
            break;
        case 2:
            $new->strategy = 'numerrors';    // 'errorbanded'
            break;
        case 3:
            $new->strategy = 'rubric';       // 'criterion'
            break;
        case 4:
            $new->strategy = 'rubric';       // 'rubric'
            break;
        }
        $newid = $DB->insert_record('workshop', $new, true, true);
        $DB->set_field('course_modules', 'instance', $newid, array('module' => $moduleid, 'instance' => $old->id));
        $DB->set_field('workshop_old', 'newid', $newid, array('id' => $old->id));
    }
    $rs->close();
}
