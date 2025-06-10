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

defined('MOODLE_INTERNAL') || die();

$mapper = function($event) {
    return array(
        'handlerfile' => '/blocks/post_grades/events.php',
        'handlerfunction' => array('post_grades_handler', $event),
        'schedule' => 'instant'
    );
};

$events = array(
    'ues_semester_drop', 'user_deleted'
);

$handlers = array_combine($events, array_map($mapper, $events));

$observers = array(

    // UES.

    array(
        'eventname'   => '\enrol_ues\event\ues_section_dropped',
        'callback'    => 'block_post_grades_observer::ues_section_dropped',
    ),

    array(
        'eventname'   => '\enrol_ues\event\ues_semester_dropped',
        'callback'    => 'block_post_grades_observer::ues_semester_dropped',
    ),

);
