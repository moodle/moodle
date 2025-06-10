<?php
// This file is part of the honorlockproctoring module for Moodle - http://moodle.org/
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
 * Honorlock proctoring events.
 *
 * @package    local_honorlockproctoring
 * @copyright  2023 Honorlock (https://honorlock.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\mod_quiz\event\course_module_viewed',
        'callback' => 'local_honorlockproctoring\observer::quiz_viewed',
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_viewed',
        'callback' => 'local_honorlockproctoring\observer::quiz_attempt_viewed',
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback' => 'local_honorlockproctoring\observer::quiz_attempt_submitted',
    ],
];
