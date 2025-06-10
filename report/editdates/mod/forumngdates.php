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


class report_editdates_mod_forumng_date_extractor
        extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'forumng');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $forumng = $this->mods[$cm->instance];
        $forumngdatesettings = array();

        if ($forumng->ratingscale != 0) {
            $forumngdatesettings['ratingfrom'] = new report_editdates_date_setting(
                                                    get_string('ratingfrom', 'forumng'),
                                                    $forumng->ratingfrom,
                                                    self::DATETIME, true);
            $forumngdatesettings['ratinguntil'] = new report_editdates_date_setting(
                                                    get_string('ratinguntil', 'forumng'),
                                                    $forumng->ratinguntil,
                                                    self::DATETIME, true);
        }

        $forumngdatesettings['postingfrom'] = new report_editdates_date_setting(
                                                    get_string('postingfrom', 'forumng'),
                                                    $forumng->postingfrom,
                                                    self::DATETIME, true);
        $forumngdatesettings['postinguntil'] = new report_editdates_date_setting(
                                                    get_string('postinguntil', 'forumng'),
                                                    $forumng->postinguntil,
                                                    self::DATETIME, true);
        return $forumngdatesettings;
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        if (isset($dates['ratingfrom']) && isset($dates['ratinguntil'])
                && $dates['ratingfrom'] != 0 && $dates['ratinguntil'] != 0
                && $dates['ratinguntil'] < $dates['ratingfrom']) {

            $errors['ratinguntil'] = get_string('timeuntil', 'report_editdates');
        }
        if ($dates['postingfrom'] != 0 && $dates['postinguntil'] != 0
                && $dates['postinguntil'] < $dates['postingfrom']) {
            $errors['postinguntil'] = get_string('timeuntil', 'report_editdates');
        }
        return $errors;
    }
}
