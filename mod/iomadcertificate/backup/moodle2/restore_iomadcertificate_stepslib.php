<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * @package   mod_iomadcertificate
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @basedon   mod_certificate by Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_iomadcertificate_activity_task
 */

/**
 * Structure step to restore one iomadcertificate activity
 */
class restore_iomadcertificate_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('iomadcertificate', '/activity/iomadcertificate');

        if ($userinfo) {
            $paths[] = new restore_path_element('iomadcertificate_issue', '/activity/iomadcertificate/issues/issue');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_iomadcertificate($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the iomadcertificate record
        $newitemid = $DB->insert_record('iomadcertificate', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_iomadcertificate_issue($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->iomadcertificateid = $this->get_new_parentid('iomadcertificate');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        if ($data->userid > 0) {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }

        $newitemid = $DB->insert_record('iomadcertificate_issues', $data);
        $this->set_mapping('iomadcertificate_issue', $oldid, $newitemid);
    }

    protected function after_execute() {
        // Add iomadcertificate related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_iomadcertificate', 'issue', 'iomadcertificate_issue');
    }
}
