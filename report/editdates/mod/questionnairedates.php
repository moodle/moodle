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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>..

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/questionnaire/lib.php');


class report_editdates_mod_questionnaire_date_extractor
        extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'questionnaire');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $mod = $this->mods[$cm->instance];
        return array('opendate' => new report_editdates_date_setting(
                                        get_string('opendate', 'questionnaire'),
                                        $mod->opendate, self::DATETIME, true),
                    'closedate' => new report_editdates_date_setting(
                                        get_string('closedate', 'questionnaire'),
                                        $mod->closedate, self::DATETIME, true),
        );
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        if ($dates['opendate'] != 0 && $dates['closedate'] != 0
                && $dates['closedate'] < $dates['opendate']) {
            $errors['closedate'] = get_string('closedate', 'report_editdates');
        }
        return $errors;
    }

    public function save_dates(cm_info $cm, array $dates) {
        global $DB, $COURSE;

        // Fetch module instance from $mods array.
        $questionnaire = $this->mods[$cm->instance];
        $questionnaire->instance = $cm->instance;
        $questionnaire->cmidnumber = $cm->id;

        // Updating date values.
        foreach ($dates as $datetype => $datevalue) {
            $questionnaire->$datetype = $datevalue;
            if ($datevalue != 0) {
                $property = 'use'.$datetype;
                $questionnaire->$property = 1;
            }
        }

        // Method name to udpate the instance and associated events.
        $methodname = $cm->modname.'_update_instance';
        // Calling the method.
        $methodname($questionnaire);
    }
}
