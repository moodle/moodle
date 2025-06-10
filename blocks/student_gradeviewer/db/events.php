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
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Adam Zapletal, Philip Cali, Jason Peak, Chad Mazilly, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$events = array(
    // System events.
    'user_deleted',
    // UES Meta Viewer events (for person queries).
    'ues_meta_supported_types',
    'sports_grade_data_ui_keys',
    'sports_grade_data_ui_element',
    'academic_grade_data_ui_keys',
    'academic_grade_data_ui_element'
);

$map = function($event) {
    return array(
        'handlerfile' => '/blocks/student_gradeviewer/events/lib.php',
        'handlerfunction' => array('student_gradeviewer_handlers', $event),
        'schedule' => 'instant'
    );
};

$handlers = array_combine($events, array_map($map, $events));
