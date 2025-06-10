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

require_once($CFG->dirroot.'/mod/zoom/locallib.php');


class report_editdates_mod_zoom_date_extractor
        extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'zoom');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $zoom = $this->mods[$cm->instance];
        if (!empty($zoom->recurring)) {
            return array();
        } else {
            // Underscores currently don't behave well with this report, so we'll omit them.
            return array(
                'starttime' => new report_editdates_date_setting(
                        get_string('meeting_time', 'zoom'),
                        $zoom->start_time, self::DATETIME, false),
                );
        }
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        $zoom = $this->mods[$cm->instance];

        if (empty($zoom->recurring)) {
            // Only report a validation error if the user actually changed the time.
            if ($dates['starttime'] != $zoom->start_time
                    && $dates['starttime'] < strtotime('today')) {
                $errors['starttime'] = get_string('err_start_time_past', 'zoom');
            }
        }

        return $errors;
    }

    public function save_dates(cm_info $cm, array $dates) {
        // Fetch module instance from $mods array.
        $zoom = $this->mods[$cm->instance];
        $zoom->instance = $cm->instance;
        $zoom->cmidnumber = $cm->id;

        // Updating date values.
        $zoom->start_time = $dates['starttime'];

        // Calling the method.
        zoom_update_instance($zoom);
    }
}
