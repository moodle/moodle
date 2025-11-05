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
 * Class report_editdates_mod_choice_date_extractor
 *
 * This class is responsible for extracting and managing date settings
 * for the "Choice" activity module in Moodle.
 *
 * @package   report_editdates
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_mod_choice_date_extractor
        extends report_editdates_mod_date_extractor {

    /**
     * Constructor.
     * @param object $course the course database row.
     */
    public function __construct($course) {
        parent::__construct($course, 'choice');
        parent::load_data();
    }

    #[\Override]
    public function get_settings(cm_info $cm) {
        $choice = $this->mods[$cm->instance];
        if ($choice->timeopen != 0 && $choice->timeclose != 0) {
            return [
                'timeopen' => new report_editdates_date_setting(
                    get_string('choiceopen', 'choice'),
                    $choice->timeopen,
                    self::DATETIME, false
                ),

                'timeclose' => new report_editdates_date_setting(
                    get_string('choiceclose', 'choice'),
                    $choice->timeclose,
                    self::DATETIME, false
                ),
            ];
        }
        return null;
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
