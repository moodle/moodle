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
 * Class report_editdates_mod_nanogong_date_extractor
 *
 * This class is responsible for extracting, validating, and saving date settings
 * for the "Nanogong" activity module in Moodle.
 *
 * @package   report_editdates
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_mod_nanogong_date_extractor
        extends report_editdates_mod_date_extractor {

    /**
     * Constructor.
     *
     * @param stdClass $course The course database row.
     */
    public function __construct($course) {
        parent::__construct($course, 'nanogong');
        parent::load_data();
    }

    #[\Override]
    public function get_settings(cm_info $cm) {
        $nanogong = $this->mods[$cm->instance];

        return [
            'timeavailable' => new report_editdates_date_setting(
                get_string('availabledate', 'mod_nanogong'),
                $nanogong->timeavailable,
                self::DATETIME, true
            ),
            'timedue' => new report_editdates_date_setting(
                get_string('duedate', 'mod_nanogong'),
                $nanogong->timedue,
                self::DATETIME, true
            ),
        ];
    }

    #[\Override]
    public function validate_dates(cm_info $cm, array $dates) {
        $errors = [];
        if ($dates['timeavailable'] != 0 && $dates['timedue'] != 0 &&
                $dates['timedue'] < $dates['timeavailable']) {
            $errors['timedue'] = get_string('deadline', 'report_editdates');
        }
        return $errors;
    }
}
