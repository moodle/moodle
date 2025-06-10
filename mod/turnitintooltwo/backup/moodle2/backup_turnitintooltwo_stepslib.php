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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Define all the backup steps that will be used by the backup_assignment_activity_task.
// This is the Complete assignment structure for backup, with file and id annotations.

require_once($CFG->dirroot."/mod/turnitintooltwo/lib.php");

class backup_turnitintooltwo_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {
        // Required otherwise Moodle unit test core_calendar_container_testcase calendar/tests/container_test.php will fail.
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            set_config('accountid', 'NULL', 'turnitintooltwo');
        }

        $config = turnitintooltwo_admin_config();

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $turnitintooltwo = new backup_nested_element('turnitintooltwo', array('id'), array(
            'type', 'name', 'grade', 'numparts', 'tiiaccount', 'defaultdtstart', 'defaultdtdue', 'defaultdtpost',
            'anon', 'portfolio', 'allowlate', 'reportgenspeed', 'submitpapersto', 'spapercheck', 'internetcheck',
            'journalcheck', 'institution_check', 'maxfilesize', 'intro', 'introformat', 'timecreated', 'timemodified',
            'studentreports', 'dateformat', 'usegrademark', 'gradedisplay', 'autoupdates', 'commentedittime', 'commentmaxsize',
            'autosubmission', 'shownonsubmission', 'excludebiblio', 'excludequoted', 'excludevalue', 'excludetype', 'erater',
            'erater_handbook', 'erater_dictionary', 'erater_spelling', 'erater_grammar', 'erater_usage', 'erater_mechanics',
            'erater_style', 'transmatch', 'rubric', 'allownonor'
        ));

        $parts = new backup_nested_element('parts');

        $part = new backup_nested_element('part', array('id'), array(
            'turnitintooltwoid', 'partname', 'tiiassignid', 'dtstart', 'dtdue',
            'dtpost', 'maxmarks', 'deleted', 'migrated'));

        $courses = new backup_nested_element('courses');

        $course = new backup_nested_element('course', array('id'), array(
            'courseid', 'ownerid', 'ownertiiuid', 'owneremail', 'ownerfn',
            'ownerln', 'ownerun', 'turnitin_ctl', 'turnitin_cid', 'course_type'));

        $submissions = new backup_nested_element('submissions');

        $submission = new backup_nested_element('submission', array('id'), array(
            'userid', 'submission_part', 'submission_title', 'submission_type', 'submission_filename',
            'submission_objectid', 'submission_score', 'submission_grade', 'submission_gmimaged', 'submission_attempts',
            'submission_modified', 'submission_parent', 'submission_nmuserid', 'submission_nmfirstname',
            'submission_nmlastname', 'submission_unanon', 'submission_anonreason', 'submission_transmatch',
            'submission_orcapable', 'submission_acceptnothing', 'tiiuserid'));

        // Build the tree.
        $submissions->add_child($submission);
        $parts->add_child($part);
        $turnitintooltwo->add_child($parts);
        $turnitintooltwo->add_child($course);
        $turnitintooltwo->add_child($submissions);

        // Define sources.
        $turnitintooltwo->set_source_table('turnitintooltwo', array('id' => backup::VAR_ACTIVITYID));
        $values['tiiaccount'] = $config->accountid;
        $turnitintooltwo->fill_values($values);

        $part->set_source_table('turnitintooltwo_parts', array('turnitintooltwoid' => backup::VAR_ACTIVITYID), 'id');

        $course->set_source_sql("
            SELECT  t.id, t.courseid, t.ownerid, tu.turnitin_uid AS ownertiiuid,
                    u.email AS owneremail, u.firstname AS ownerfn, u.lastname AS ownerln,
                    u.username AS ownerun, t.turnitin_ctl, t.turnitin_cid
              FROM {turnitintooltwo_courses} t, {user} u, {turnitintooltwo_users} tu
             WHERE t.ownerid=u.id AND tu.userid=t.ownerid AND t.courseid = ? AND t.course_type = 'TT'",
            array(backup::VAR_COURSEID));

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $submission->set_source_sql('
            SELECT  s.*, tu.turnitin_uid AS tiiuserid
              FROM {turnitintooltwo_submissions} s, {turnitintooltwo_users} tu
             WHERE s.userid=tu.userid AND s.turnitintooltwoid = ?',
            array(backup::VAR_ACTIVITYID));
        }

        // Define id annotations.
        $submission->annotate_ids('user', 'userid');

        // Define file annotations.
        $turnitintooltwo->annotate_files('mod_turnitintooltwo', 'intro', null); // This file area hasn't itemid.
        $submission->annotate_files('mod_turnitintooltwo', 'submissions', 'id');

        // Return the root element (turnitintooltwo), wrapped into standard activity structure.
        return $this->prepare_activity_structure($turnitintooltwo);
    }
}
