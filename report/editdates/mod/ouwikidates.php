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


require_once($CFG->dirroot.'/mod/ouwiki/lib.php');

/**
 * Class report_editdates_mod_ouwiki_date_extractor
 *
 * This class is responsible for extracting and managing date settings
 * for the "OU Wiki" activity module in Moodle.
 *
 * @package   report_editdates
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_mod_ouwiki_date_extractor
        extends report_editdates_mod_date_extractor {

    /**
     * Constructor.
     *
     * @param stdClass $course the course database row.
     */
    public function __construct($course) {
        parent::__construct($course, 'ouwiki');
        parent::load_data();
    }

    #[\Override]
    public function get_settings(cm_info $cm) {
        $mod = $this->mods[$cm->instance];

        return [
            'editbegin' => new report_editdates_date_setting(
                get_string('editbegin', 'ouwiki'),
                $mod->editbegin, self::DATETIME, true
            ),
            'editend' => new report_editdates_date_setting(
                get_string('editend', 'ouwiki'),
                $mod->editend, self::DATETIME, true
            ),
        ];

    }

    #[\Override]
    public function validate_dates(cm_info $cm, array $dates) {
        $errors = [];
        if ($dates['editbegin'] != 0 && $dates['editend'] != 0
                && $dates['editend'] < $dates['editbegin']) {
            $errors['editend'] = get_string('editend', 'report_editdates');
        }
        return $errors;
    }
}
