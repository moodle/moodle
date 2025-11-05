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
 * Class report_editdates_mod_feedback_date_extractor
 *
 * This class is responsible for extracting, validating, and saving date settings
 * for the "Feedback" activity module in Moodle.
 *
 * @package   report_editdates
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_mod_feedback_date_extractor
        extends report_editdates_mod_date_extractor {

    /**
     * Constructor.
     *
     * @param stdClass $course The course database row.
     */
    public function __construct($course) {
        parent::__construct($course, 'feedback');
        parent::load_data();
    }

    #[\Override]
    public function get_settings(cm_info $cm) {
        $feedback = $this->mods[$cm->instance];
        return [
            'timeopen' => new report_editdates_date_setting(
                get_string('feedbackopen', 'feedback'),
                $feedback->timeopen,
                self::DATETIME, true
            ),

            'timeclose' => new report_editdates_date_setting(
                get_string('feedbackclose', 'feedback'),
                $feedback->timeclose,
                self::DATETIME, true
            ),
        ];
    }

    #[\Override]
    public function validate_dates(cm_info $cm, array $dates) {
        $errors = [];
        if (!empty($dates['timeopen']) && !empty($dates['timeclose']) &&
                            $dates['timeclose'] < $dates['timeopen']) {
            $errors['timeclose'] = get_string('timeclose', 'report_editdates');
        }
        return $errors;
    }
}
