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

// Structure step to restore one turnitintooltwo activity.

require_once($CFG->dirroot."/mod/turnitintooltwo/lib.php");

class restore_turnitintooltwo_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        if (!isset($_SESSION['tii_course_reset'])) {
            unset($_SESSION['assignments_to_create']);
            $_SESSION['tii_course_reset'] = 1;
        }
        $paths = array();

        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('turnitintooltwo_courses', '/activity/turnitintooltwo/course');
        $paths[] = new restore_path_element('turnitintooltwo', '/activity/turnitintooltwo');
        $paths[] = new restore_path_element('turnitintooltwo_parts', '/activity/turnitintooltwo/parts/part');

        if ($userinfo) {
            $paths[] = new restore_path_element('turnitintooltwo_submissions', '/activity/turnitintooltwo/submissions/submission');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    protected function process_turnitintooltwo($data) {
        global $DB;

        $config = turnitintooltwo_admin_config();

        $_SESSION['tii_assignment_reset'] = 1;
        if ($this->get_setting_value('userinfo') == 1) {
            $_SESSION['tii_course_reset'] = 0;
            $_SESSION['tii_assignment_reset'] = 0;
        }

        $data = (object)$data;
        $data->course = $this->get_courseid();

        // Work out whether we are duplicating a module activity or course.
        // If activity then we do not want to reset the course.
        $type = $this->get_task()->get_info()->type;
        if ($type == 'activity') {
            $_SESSION['tii_course_reset'] = 0;
        }

        if ($data->grade < 0) {
            // Scale found, get mapping.
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        if ($config->accountid != $data->tiiaccount) {
            $a = new stdClass();
            $a->backupid = $data->tiiaccount;
            $a->current = $config->accountid;
            turnitintooltwo_print_error('wrongaccountid', 'turnitintooltwo', null, $a);
            return false;
        } else {
            // Insert the activity record.
            $newitemid = $DB->insert_record('turnitintooltwo', $data);
            $_SESSION['assignment_id'] = $newitemid;
            // Immediately after inserting "activity" record, call this.
            $this->apply_activity_instance($newitemid);
        }
    }

    protected function process_turnitintooltwo_courses($data) {
        global $DB, $USER;

        $data = (object)$data;
        $oldid = $data->id;
        $data->courseid = $this->get_courseid();
        $_SESSION['course_id'] = $data->courseid;

        // Deleted user's emails are hashed so we need to grab username which is in the format email.timestamp.
        if (empty($data->owneremail)) {
            $ownerusername = $data->ownerun;
            $ownerusername = explode(".", $ownerusername);
            $data->owneremail = implode('.', array_splice($ownerusername, 0, -1));
        }
        $owner = $DB->get_record('user', array('email' => $data->owneremail));
        if ($owner) {
            $data->ownerid = $owner->id;
        } else {
            // Turnitin class owner not found so use restoring user as owner.
            $data->ownerid = $USER->id;
        }
        $tiiowner = new stdClass();
        $tiiowner->userid = $data->ownerid;
        $tiiowner->turnitin_uid = $data->ownertiiuid;
        if (!$DB->get_record('turnitintooltwo_users', array('userid' => $data->ownerid))) {
            $DB->insert_record('turnitintooltwo_users', $tiiowner);
        }
        if (!$DB->get_records('turnitintooltwo_courses', array('courseid' => $data->courseid, 'course_type' => 'TT'))) {
            $data->course_type = 'TT';
            $newitemid = $DB->insert_record('turnitintooltwo_courses', $data);
            $this->set_mapping('turnitintooltwo_courses', $oldid, $newitemid);
        }
    }

    protected function process_turnitintooltwo_parts($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->turnitintooltwoid = $this->get_new_parentid('turnitintooltwo');

        $newitemid = $DB->insert_record('turnitintooltwo_parts', $data);
        $this->set_mapping('turnitintooltwo_parts', $oldid, $newitemid);
    }

    protected function process_turnitintooltwo_submissions($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->turnitintooltwoid = $this->get_new_parentid('turnitintooltwo');
        $data->submission_part = $this->get_mappingid('turnitintooltwo_parts', $data->submission_part);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->submission_hash = $data->userid.'_'.$data->turnitintooltwoid.'_'.$data->submission_part;

        // Create TII User Account Details.
        if (!$tiiuser = $DB->get_record('turnitintooltwo_users', array('turnitin_uid' => $data->tiiuserid))) {
            $tiiuser = new stdClass();
            $tiiuser->userid = $data->userid;
            $tiiuser->turnitin_uid = $data->tiiuserid;
            $DB->insert_record('turnitintooltwo_users', $tiiuser);
        }

        // Check if this hash already exists.
        if ($DB->get_record('turnitintooltwo_submissions', array('submission_hash' => $data->submission_hash))) {
            $data->submission_hash = turnitintooltwo_genUuid();
        }

        // Insert the submission as we have a unique hash.
        $newitemid = $DB->insert_record('turnitintooltwo_submissions', $data);
        $this->set_mapping('turnitintooltwo_submissions', $oldid, $newitemid);
    }

    protected function after_execute() {

        if (!empty($_SESSION['tii_assignment_reset'])) {
            $_SESSION["assignments_to_create"][] = $_SESSION['assignment_id'];
            unset($_SESSION['assignment_id']);
        }

        // Add turnitin related files, itemid based on mapping 'turnitintooltwo_submissions'.
        $this->add_related_files('mod_turnitintooltwo', 'submissions', 'turnitintooltwo_submissions');
    }
}
