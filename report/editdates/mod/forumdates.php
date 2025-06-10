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


class report_editdates_mod_forum_date_extractor
        extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'forum');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $forum = $this->mods[$cm->instance];

        $fields = array();
        $fields['duedate'] = new report_editdates_date_setting(
                                           get_string('duedate', 'forum'),
                                           $forum->duedate,
                                           self::DATETIME, true);
        $fields['cutoffdate'] = new report_editdates_date_setting(
                                              get_string('cutoffdate', 'forum'),
                                              $forum->cutoffdate,
                                              self::DATETIME, true);
        if ($forum->assessed) {
            $fields['assesstimestart'] = new report_editdates_date_setting(
                                             get_string('assesstimefrom', 'report_editdates'),
                                             $forum->assesstimestart,
                                             self::DATETIME, true);
            $fields['assesstimefinish'] = new report_editdates_date_setting(
                                              get_string('assesstimeto', 'report_editdates'),
                                              $forum->assesstimefinish,
                                              self::DATETIME, true);
        }
        return $fields;
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        $forum = $this->mods[$cm->instance];
        if ($forum->assessed && $dates['assesstimestart'] != 0 && $dates['assesstimefinish'] != 0 &&
                $dates['assesstimefinish'] < $dates['assesstimestart']) {
            $errors['assesstimefinish'] = get_string('assesstimefinish', 'report_editdates');
        }

        if ($forum->assessed && $dates['assesstimestart'] == 0 && $dates['assesstimefinish'] != 0) {
            $errors['assesstimestart'] = get_string('dependentdate', 'report_editdates');
        }

        if ($forum->assessed && $dates['assesstimefinish'] == 0 && $dates['assesstimestart'] != 0) {
            $errors['assesstimefinish'] = get_string('dependentdate', 'report_editdates');
        }
        return $errors;
    }
}
