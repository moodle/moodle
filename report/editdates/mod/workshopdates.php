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

require_once($CFG->dirroot.'/mod/quiz/lib.php');


class report_editdates_mod_workshop_date_extractor
        extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'workshop');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $workshop = $this->mods[$cm->instance];
        return array('submissionstart' => new report_editdates_date_setting(
                                        get_string('submissionstart', 'workshop'),
                                        $workshop->submissionstart, self::DATETIME, true),
                     'submissionend' => new report_editdates_date_setting(
                                        get_string('submissionend', 'workshop'),
                                        $workshop->submissionend, self::DATETIME, true),
                     'assessmentstart' => new report_editdates_date_setting(
                                        get_string('assessmentstart', 'workshop'),
                                        $workshop->assessmentstart, self::DATETIME, true),
                     'assessmentend' => new report_editdates_date_setting(
                                        get_string('assessmentend', 'workshop'),
                                        $workshop->assessmentend, self::DATETIME, true),
        );
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();

        // Check the phases borders are valid.
        if ($dates['submissionstart'] > 0 && $dates['submissionend'] > 0 &&
                $dates['submissionstart'] >= $dates['submissionend']) {
            $errors['submissionend'] = get_string('submissionendbeforestart', 'mod_workshop');
        }
        if ($dates['assessmentstart'] > 0 && $dates['assessmentend'] > 0 &&
                $dates['assessmentstart'] >= $dates['assessmentend']) {
            $errors['assessmentend'] = get_string('assessmentendbeforestart', 'mod_workshop');
        }

        // Check the phases do not overlap.
        if (max($dates['submissionstart'], $dates['submissionend']) > 0 &&
                max($dates['assessmentstart'], $dates['assessmentend']) > 0) {
            $phasesubmissionend = max($dates['submissionstart'], $dates['submissionend']);
            $phaseassessmentstart = min($dates['assessmentstart'], $dates['assessmentend']);
            if ($phaseassessmentstart == 0) {
                $phaseassessmentstart = max($dates['assessmentstart'], $dates['assessmentend']);
            }
            if ($phasesubmissionend > 0 && $phaseassessmentstart > 0 && $phaseassessmentstart < $phasesubmissionend) {
                foreach (array('submissionend', 'submissionstart', 'assessmentstart', 'assessmentend') as $f) {
                    if ($dates[$f] > 0) {
                        $errors[$f] = get_string('phasesoverlap', 'mod_workshop');
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    public function save_dates(cm_info $cm, array $dates) {
        parent::save_dates($cm, $dates);

        // Fetch module instance from $mods array.
        $workshop = $this->mods[$cm->instance];

        $workshop->instance = $cm->instance;
        $workshop->coursemodule = $cm->id;

        // Updating date values.
        foreach ($dates as $datetype => $datevalue) {
            $workshop->$datetype = $datevalue;
        }

        // Calling the update event method to change the calender evenrs accordingly.
        workshop_calendar_update($workshop, $cm->id);
    }
}
