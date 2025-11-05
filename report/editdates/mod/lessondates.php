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

/**
 * Class report_editdates_mod_lesson_date_extractor
 *
 * This class is responsible for extracting, validating, and saving date settings
 * for the "Lesson" activity module in Moodle.
 *
 * @package   report_editdates
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_mod_lesson_date_extractor
        extends report_editdates_mod_date_extractor {

    /**
     * Constructor.
     *
     * @param stdClass $course The course database row.
     */
    public function __construct($course) {
        parent::__construct($course, 'lesson');
        parent::load_data();
    }

    #[\Override]
    public function get_settings(cm_info $cm) {
        $mod = $this->mods[$cm->instance];

        return [
            'available' => new report_editdates_date_setting(
                get_string('available', 'lesson'),
                $mod->available, self::DATETIME, true
            ),
            'deadline' => new report_editdates_date_setting(
                get_string('deadline', 'lesson'),
                $mod->deadline, self::DATETIME, true
            ),
        ];
    }

    #[\Override]
    public function validate_dates(cm_info $cm, array $dates) {
        $errors = [];
        if ($dates['available'] != 0 && $dates['deadline'] != 0
                && $dates['deadline'] < $dates['available']) {
            $errors['deadline'] = get_string('deadline', 'report_editdates');
        }
        return $errors;
    }

    #[\Override]
    public function save_dates(cm_info $cm, array $dates) {
        global $DB, $COURSE;

        // Fetch module instance from $mods array.
        $lesson = $this->mods[$cm->instance];

        // Updating date values.
        foreach ($dates as $datetype => $datevalue) {
            $lesson->$datetype = $datevalue;
        }

        $lesson->timemodified = time();
        // Update DB record.
        $DB->update_record('lesson', $lesson);
        // Update associated calender events.
        lesson_process_post_save($lesson);
    }
}
