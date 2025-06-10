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

defined('MOODLE_INTERNAL') || die;


class report_editdates_mod_turnitintooltwo_date_extractor
extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'turnitintooltwo');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        global $DB;
        $tii = $this->mods[$cm->instance];
        $parts = $DB->get_records_select("turnitintooltwo_parts", "turnitintooltwoid = ?", [$tii->id], 'id ASC');
        $elems = [];

        foreach ($parts as $id => $part) {
            $elems["startdate$id"] = new report_editdates_date_setting(
                $part->partname . " " . "Start Date",
                $part->dtstart,
                self::DATETIME, false, 5
            );
            $elems["duedate$id"] = new report_editdates_date_setting(
                $part->partname . " " . "Due Date",
                $part->dtdue,
                self::DATETIME, false, 5
            );
            $elems["postdate$id"] = new report_editdates_date_setting(
                $part->partname . " " . "Post Date",
                $part->dtpost,
                self::DATETIME, false, 5
            );
        }

        return $elems;
    }

    public function validate_dates(cm_info $cm, array $dates) {
        global $DB;
        $now = new DateTime("now", core_date::get_user_timezone_object());
        $errors = array();
        $parts = $DB->get_records_select("turnitintooltwo_parts", "turnitintooltwoid = ?", [$cm->instance], 'id ASC');
        foreach ($parts as $id => $part) {
            if ($dates["startdate$id"] > $dates["duedate$id"]) {
                $errors["duedate$id"] = "Due date must be after startdate";
            }
            if ($dates["duedate$id"] > $dates["postdate$id"]) {
                $errors["postdate$id"] = "Post date cannot be before duedate";
            }
            if ($now->getTimestamp() - $dates["startdate$id"] > 31536000) { // Start date cannot be more than 1 year in the past.
                $errors["startdate$id"] = "Start date is more than one year ago";
            }
        }

        return $errors;
    }

    public function save_dates(cm_info $cm, array $dates) {
        global $DB, $COURSE, $CFG;

        $parts = $DB->get_records_select("turnitintooltwo_parts", "turnitintooltwoid = ?", [$cm->instance], 'id ASC');
        foreach ($parts as $id => $part) {
            $update = new stdClass();
            $update->id = $id;
            $update->dtstart = $dates["startdate$id"];
            $update->dtdue = $dates["duedate$id"];
            $update->dtpost = $dates["postdate$id"];
            $result = $DB->update_record('turnitintooltwo_parts', $update);
        }

        require_once($CFG->dirroot."/mod/turnitintooltwo/turnitintooltwo_assignment.class.php");
        $tii = new turnitintooltwo_assignment($cm->instance);
        $tii->edit_moodle_assignment(); // Sync changes to TII / Gradebook / Calendar.
    }
}
