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
 * Page to view the course reports
 *
 * @package    core
 * @subpackage report
 * @copyright  2021 Sujith Haridasan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');

// Course id.
$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url(new moodle_url('/report/view.php', array('courseid' => $courseid)));

// Basic access checks.
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
require_login($course);

// Get the last viewed Page.
if (!isset($USER->course_last_report[$courseid])) {
    $lasturl = new moodle_url('/report/log/index.php', ['id' => $courseid]);
} else {
    $lasturl = $USER->course_last_report[$courseid];
}

redirect($lasturl);
