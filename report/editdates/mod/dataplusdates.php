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


class report_editdates_mod_dataplus_date_extractor
        extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'dataplus');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $data = $this->mods[$cm->instance];

        $datadatesettings = array(
            'timeavailablefrom' => new report_editdates_date_setting(
                                    get_string('availablefromdate', 'dataplus'),
                                    $data->timeavailablefrom,
                                    self::DATE, true),
            'timeavailableto' => new report_editdates_date_setting(
                                    get_string('availabletodate', 'dataplus'),
                                    $data->timeavailableto,
                                    self::DATE, true),
        );
        if ($data->assessed) {
            $datadatesettings['assesstimestart'] = new report_editdates_date_setting(
                                    get_string('from'),
                                    $data->assesstimestart,
                                    self::DATETIME, false);
            $datadatesettings['assesstimefinish'] = new report_editdates_date_setting(
                                    get_string('to'),
                                    $data->assesstimefinish,
                                    self::DATETIME, false);
        }
        return $datadatesettings;
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        if ($dates['timeavailablefrom'] != 0 && $dates['timeavailableto'] != 0
                && $dates['timeavailableto'] < $dates['timeavailablefrom']) {
            $errors['timeavailableto'] = get_string('assesstimefinish', 'report_editdates');
        }
        if (isset($dates['assesstimestart']) && isset($dates['assesstimefinish']) &&
                $dates['assesstimestart'] != 0 && $dates['assesstimefinish'] != 0 &&
                $dates['assesstimefinish'] < $dates['assesstimestart']) {

            $errors['assesstimefinish'] = get_string('assesstimefinish', 'report_editdates');
        }
        return $errors;
    }
}
