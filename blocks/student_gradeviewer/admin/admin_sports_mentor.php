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
 *
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class admin_sports_mentor extends student_mentor_role_assign {
    public function __construct() {
        parent::__construct(
            'sports_mentor',
            optional_param('path', 'NA', PARAM_TEXT),
            array('block/student_gradeviewer:sportsadmin')
        );
    }

    public function ui_filters() {
        global $OUTPUT;

        $nosports = get_string('na_sports', 'block_student_gradeviewer');
        $sports = array('NA' => $nosports) + sports_mentor::all_sports();

        $url = new moodle_url('/blocks/student_gradeviewer/admin.php', array(
            'type' => $this->get_type()
        ));

        $assigningto = get_string(
            'assigning_to', 'block_student_gradeviewer', $sports[$this->path]
        );

        return $OUTPUT->single_select($url, 'path', $sports, $this->path) .
            $OUTPUT->heading($assigningto, 3);
    }
}
