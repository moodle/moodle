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


class report_editdates_mod_glossary_date_extractor
extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'glossary');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $mod = $this->mods[$cm->instance];

        if ($mod->assessed && ( $mod->assesstimestart != 0 || $mod->assesstimefinish != 0) ) {
            return array('assesstimestart' => new report_editdates_date_setting(
                                                    get_string('from'),
                                                    $mod->assesstimestart,
                                                    self::DATETIME, false),
                         'assesstimefinish' => new report_editdates_date_setting(
                                                    get_string('to'),
                                                    $mod->assesstimefinish,
                                                    self::DATETIME, false)
            );
        }
        return null;
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        if ($dates['assesstimestart'] != 0 && $dates['assesstimefinish'] != 0
                && $dates['assesstimefinish'] < $dates['assesstimestart']) {
            $errors['assesstimefinish'] = get_string('assesstimefinish', 'report_editdates');
        }
        return $errors;
    }
}
